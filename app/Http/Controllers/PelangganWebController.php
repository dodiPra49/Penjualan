<?php

namespace App\Http\Controllers;

use App\Services\PelangganService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PelangganWebController extends Controller
{
    protected PelangganService $pelangganService;

    public function __construct(PelangganService $pelangganService)
    {
        $this->pelangganService = $pelangganService;
    }

    /**
     * Menampilkan halaman manajemen pelanggan dengan UI Bootstrap 5.
     */
    public function index(): View
    {
        $stats = $this->pelangganService->getStats();
        return view('pelanggan.index', compact('stats'));
    }
}
