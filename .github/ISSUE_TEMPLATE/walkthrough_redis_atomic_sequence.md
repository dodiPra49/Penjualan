---
name: "Walkthrough: Penerapan Atomic Sequence & Atomic Lock Penomoran Otomatis dengan Redis"
about: Dokumentasi walkthrough penerapan Atomic Sequence dan Atomic Lock Redis untuk penomoran otomatis
title: "[WALKTHROUGH] Penerapan Atomic Sequence & Atomic Lock Penomoran Otomatis dengan Redis"
labels: ["enhancement", "documentation", "redis"]
assignees: []
---

# Walkthrough: Penerapan Atomic Sequence & Atomic Lock Penomoran Otomatis dengan Redis

Penerapan mekanisme penomoran kode otomatis berbasis **Redis Atomic Sequence (`INCR`)** dan **Atomic Distributed Lock (`Cache::lock`)** dengan **Dual-Mode Graceful Fallback** telah selesai diimplementasikan pada proyek Penjualan ini.

---

## Ringkasan Perubahan

### 1. Dependensi & Konfigurasi Redis
* **`composer.json`**:
  * Menambahkan paket `predis/predis` agar Laravel dapat terhubung ke Redis secara fleksibel tanpa kewajiban kompilasi C-extension PHP.
* **`.env.example`**:
  * Mengonfigurasi `REDIS_CLIENT=predis`.

---

### 2. Service Layer Penomoran Otomatis
* **`app/Services/SequenceGeneratorService.php`**:
  * **Atomic Lock**: Mengunci proses kalibrasi awal (`lock:sequence:{key}`) agar hanya satu proses yang menyinkronkan nilai urutan terakhir dari DB ke Redis saat inisialisasi.
  * **Atomic Increment (`INCR`)**: Eksekusi penambahan nomor urut secara *in-memory* $O(1)$ yang thread-safe dan bebas dari *race condition* (anti duplikasi kode di lingkungan multi-kasir/multi-user).
  * **Graceful Fallback**: Menangani kondisi jika service Redis offline/down dengan secara otomatis beralih ke database pessimistic locking (`lockForUpdate`).
  * **Reusable**: Siap digunakan untuk kode pelanggan (`PLG-XXXX`), nomor faktur/invoice transaksi penjualan (`INV-XXXX`), maupun nomor retur.

---

### 3. Integrasi pada PelangganService
* **`app/Services/PelangganService.php`**:
  * Menginjeksi `SequenceGeneratorService` ke dalam constructor.
  * Memperbarui method `generateKodePelanggan()` untuk memanfaatkan `SequenceGeneratorService` dengan prefix `PLG-` dan 4 digit padding angka.

---

## Hasil Pengujian & Verifikasi

Unit test dibuat di `tests/Unit/SequenceGeneratorServiceTest.php` dan dijalankan menggunakan PHPUnit:

```bash
php artisan test tests/Unit/SequenceGeneratorServiceTest.php
```

Hasil:
```text
   PASS  Tests\Unit\SequenceGeneratorServiceTest
  ✓ format code properly
  ✓ generate fallback when redis offline
  ✓ generate first sequence
  ✓ generate using redis atomic incr

  Tests:    4 passed (8 assertions)
  Duration: 0.46s
```

Semua skenario (format prefix, inisialisasi awal, eksekusi Redis Atomic INCR, dan fallback aman saat Redis offline) berhasil diverifikasi dan lulus 100%.
