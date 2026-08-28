<?php

namespace App\Http\Controllers;

use App\Services\KategoriService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KategoriWebController extends Controller
{
    protected KategoriService $kategoriService;

    public function __construct(KategoriService $kategoriService)
    {
        $this->kategoriService = $kategoriService;
    }

    /**
     * Menampilkan halaman manajemen kategori dengan UI Bootstrap 5.
     */
    public function index(): View
    {
        // Mengambil seluruh kategori untuk statistik awal
        $totalKategori = $this->kategoriService->getAll()->count();

        return view('kategori.index', compact('totalKategori'));
    }
}
