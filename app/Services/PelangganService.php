<?php

namespace App\Services;

use App\Models\Pelanggan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class PelangganService
{
    /**
     * Mengambil daftar pelanggan dengan filter pencarian dan paginasi/all.
     */
    public function getAll(array $params = []): LengthAwarePaginator|Collection
    {
        $query = Pelanggan::query();

        // Pencarian keyword
        if (!empty($params['search'])) {
            $query->search($params['search']);
        }

        // Filter status (aktif / nonaktif)
        if (!empty($params['status']) && in_array($params['status'], ['aktif', 'nonaktif'])) {
            $query->where('status', $params['status']);
        }

        // Sorting
        $sortBy = $params['sort_by'] ?? 'created_at';
        $sortOrder = $params['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Paginasi jika diminta (default 10 per page jika paginate=true)
        if (isset($params['per_page']) && is_numeric($params['per_page'])) {
            return $query->paginate((int)$params['per_page']);
        }

        return $query->get();
    }

    /**
     * Mengambil data pelanggan berdasarkan ID.
     */
    public function getById(int $id): ?Pelanggan
    {
        return Pelanggan::find($id);
    }

    /**
     * Membuat data pelanggan baru.
     */
    public function create(array $data): Pelanggan
    {
        return DB::transaction(function () use ($data) {
            // Otomatisasi generate kode pelanggan jika tidak diisi manual
            if (empty($data['kode_pelanggan'])) {
                $data['kode_pelanggan'] = $this->generateKodePelanggan();
            }

            // Set default status jika kosong
            if (empty($data['status'])) {
                $data['status'] = 'aktif';
            }

            return Pelanggan::create($data);
        });
    }

    /**
     * Memperbarui data pelanggan yang ada.
     */
    public function update(int $id, array $data): Pelanggan
    {
        $pelanggan = $this->getById($id);

        if (!$pelanggan) {
            throw new Exception("Data pelanggan dengan ID {$id} tidak ditemukan.");
        }

        $pelanggan->update($data);

        return $pelanggan->fresh();
    }

    /**
     * Menghapus data pelanggan.
     */
    public function delete(int $id): bool
    {
        $pelanggan = $this->getById($id);

        if (!$pelanggan) {
            throw new Exception("Data pelanggan dengan ID {$id} tidak ditemukan.");
        }

        return (bool) $pelanggan->delete();
    }

    /**
     * Menghasilkan kode pelanggan berurutan otomatis (Contoh: PLG-0001).
     */
    public function generateKodePelanggan(): string
    {
        $lastPelanggan = Pelanggan::orderBy('id', 'desc')->first();

        if (!$lastPelanggan) {
            return 'PLG-0001';
        }

        $lastNumber = (int) preg_replace('/[^0-9]/', '', $lastPelanggan->kode_pelanggan);
        $nextNumber = $lastNumber + 1;

        return 'PLG-' . str_pad((string)$nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Mengambil statistik ringkas pelanggan.
     */
    public function getStats(): array
    {
        return [
            'total' => Pelanggan::count(),
            'aktif' => Pelanggan::where('status', 'aktif')->count(),
            'nonaktif' => Pelanggan::where('status', 'nonaktif')->count(),
        ];
    }
}
