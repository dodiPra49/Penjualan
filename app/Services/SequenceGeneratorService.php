<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Throwable;

class SequenceGeneratorService
{
    /**
     * Menghasilkan nomor/kode berurutan unik menggunakan Redis Atomic Sequence (INCR) & Atomic Lock.
     * Dilengkapi graceful fallback ke database locking jika Redis sedang tidak tersedia.
     *
     * @param string $prefix Awalan kode (contoh: 'PLG-', 'INV-')
     * @param string $sequenceKey Nama unik key urutan di Redis (contoh: 'pelanggan', 'penjualan:20260926')
     * @param callable $maxDbResolver Callback fungsi untuk membaca nomor urut terbesar saat ini dari DB jika Redis belum ter-inisialisasi
     * @param int $padLength Panjang digit angka (default 4 digit, contoh: 0001)
     * @return string Kode lengkap terformat (contoh: 'PLG-0001')
     */
    public function generate(
        string $prefix,
        string $sequenceKey,
        callable $maxDbResolver,
        int $padLength = 4
    ): string {
        $redisKey = "sequence:{$sequenceKey}";
        $lockKey = "lock:sequence:{$sequenceKey}";

        try {
            // 1. Periksa ketersediaan key di Redis
            if (!Redis::exists($redisKey)) {
                // Gunakan Atomic Lock untuk kalibrasi awal dari DB agar terhindar dari race condition
                $lock = Cache::lock($lockKey, 10);
                try {
                    // Tunggu perolehan lock maksimal 5 detik
                    $lock->block(5);

                    // Pengecekan ulang (double-check) setelah lock didapatkan
                    if (!Redis::exists($redisKey)) {
                        $currentMax = (int) $maxDbResolver();
                        Redis::set($redisKey, $currentMax);
                    }
                } finally {
                    optional($lock)->release();
                }
            }

            // 2. Operasi Atomic Increment in-memory (O(1) thread-safe)
            $nextSequence = (int) Redis::incr($redisKey);

            return $this->formatCode($prefix, $nextSequence, $padLength);

        } catch (Throwable $e) {
            // 3. Fallback jika Redis offline / koneksi bermasalah
            Log::warning("Redis Sequence unavailable for [{$sequenceKey}]. Falling back to Database Lock: " . $e->getMessage());

            return $this->fallbackDatabaseGenerate($prefix, $lockKey, $maxDbResolver, $padLength);
        }
    }

    /**
     * Fallback aman jika Redis offline menggunakan Cache Lock / DB Lock.
     */
    protected function fallbackDatabaseGenerate(
        string $prefix,
        string $lockKey,
        callable $maxDbResolver,
        int $padLength
    ): string {
        try {
            // Gunakan Cache::lock (driver fallback seperti file/db jika redis offline)
            return Cache::lock("fallback:{$lockKey}", 5)->block(3, function () use ($prefix, $maxDbResolver, $padLength) {
                $currentMax = (int) $maxDbResolver();
                $nextSequence = $currentMax + 1;
                return $this->formatCode($prefix, $nextSequence, $padLength);
            });
        } catch (Throwable $fallbackError) {
            Log::error("Sequence fallback lock failed: " . $fallbackError->getMessage());
            // Last resort jika lock cache juga gagal: langsung eksekusi DB resolver
            $currentMax = (int) $maxDbResolver();
            $nextSequence = $currentMax + 1;
            return $this->formatCode($prefix, $nextSequence, $padLength);
        }
    }

    /**
     * Format prefix dan padding angka.
     */
    public function formatCode(string $prefix, int $number, int $padLength = 4): string
    {
        return $prefix . str_pad((string)$number, $padLength, '0', STR_PAD_LEFT);
    }

    /**
     * Reset / setel ulang nilai sequence tertentu di Redis.
     */
    public function resetSequence(string $sequenceKey, int $value = 0): bool
    {
        try {
            Redis::set("sequence:{$sequenceKey}", $value);
            return true;
        } catch (Throwable $e) {
            Log::warning("Failed to reset Redis sequence for [{$sequenceKey}]: " . $e->getMessage());
            return false;
        }
    }
}
