<?php

namespace App\Services;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class KategoriService
{
    /**
     * Mengambil daftar data kategori dengan opsi pencarian, sorting, dan paginasi.
     */
    public function getAll(array $params = []): LengthAwarePaginator|Collection
    {
        $query = Kategori::query();

        // Filter pencarian berdasarkan nama_kategori atau keterangan
        if (!empty($params['search'])) {
            $query->search($params['search']);
        }

        // Sorting fleksibel
        $sortBy = $params['sort_by'] ?? 'id_kategori';
        $sortOrder = $params['sort_order'] ?? 'asc';
        
        // Memastikan kolom yang di-sort valid
        $allowedSortCols = ['id_kategori', 'nama_kategori', 'keterangan'];
        if (in_array($sortBy, $allowedSortCols)) {
            $query->orderBy($sortBy, strtolower($sortOrder) === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('id_kategori', 'asc');
        }

        // Paginasi jika parameter per_page disediakan
        if (isset($params['per_page']) && is_numeric($params['per_page'])) {
            return $query->paginate((int)$params['per_page']);
        }

        return $query->get();
    }

    /**
     * Mengambil detail kategori berdasarkan id_kategori.
     */
    public function getById(int $id): ?Kategori
    {
        return Kategori::find($id);
    }

    /**
     * Membuat rekaman data kategori baru.
     */
    public function create(array $data): Kategori
    {
        return DB::transaction(function () use ($data) {
            return Kategori::create([
                'nama_kategori' => $data['nama_kategori'],
                'keterangan'    => $data['keterangan'] ?? null,
            ]);
        });
    }

    /**
     * Memperbarui data kategori berdasarkan id_kategori.
     */
    public function update(int $id, array $data): Kategori
    {
        $kategori = $this->getById($id);

        if (!$kategori) {
            throw new Exception("Data kategori dengan ID {$id} tidak ditemukan.");
        }

        $kategori->update($data);

        return $kategori->fresh();
    }

    /**
     * Menghapus data kategori berdasarkan id_kategori.
     */
    public function delete(int $id): bool
    {
        $kategori = $this->getById($id);

        if (!$kategori) {
            throw new Exception("Data kategori dengan ID {$id} tidak ditemukan.");
        }

        return (bool) $kategori->delete();
    }
}
