<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     */
    protected $table = 'kategori';

    /**
     * Primary key kustom.
     */
    protected $primaryKey = 'id_kategori';

    /**
     * Menonaktifkan timestamps bawaan karena tabel kategori tidak memiliki kolom created_at & updated_at.
     */
    public $timestamps = false;

    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'nama_kategori',
        'keterangan',
    ];

    /**
     * Scope filter pencarian berdasarkan kata kunci.
     */
    public function scopeSearch($query, $search)
    {
        if (!empty($search)) {
            return $query->where(function ($q) use ($search) {
                $q->where('nama_kategori', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }
        return $query;
    }
}
