<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Kategori;
use Illuminate\Http\Request;

class DashboardWebController extends Controller
{
    /**
     * Menampilkan tampilan awal aplikasi / Dashboard Penjualan
     */
    public function index()
    {
        $totalPelanggan = 0;
        $pelangganAktif = 0;
        $totalKategori = 0;

        try {
            $totalPelanggan = Pelanggan::count();
            $pelangganAktif = Pelanggan::where('status', 'aktif')->count();
        } catch (\Exception $e) {
            // Abaikan jika tabel belum siap
        }

        try {
            $totalKategori = Kategori::count();
        } catch (\Exception $e) {
            // Abaikan jika tabel belum siap
        }

        $stats = [
            'total_pelanggan' => $totalPelanggan,
            'pelanggan_aktif' => $pelangganAktif,
            'total_kategori'  => $totalKategori,
            'total_produk'    => 0, // Placeholder modul produk
            'total_penjualan' => 0, // Placeholder modul transaksi
        ];

        return view('home', compact('stats'));
    }

    /**
     * Tampilan placeholder modul yang sedang dalam pengembangan
     */
    public function placeholder(Request $request, $module = 'Modul')
    {
        $moduleNames = [
            'produk'           => ['title' => 'Master Produk', 'icon' => 'fas fa-box', 'category' => 'Master'],
            'penjualan'        => ['title' => 'Transaksi Penjualan', 'icon' => 'fas fa-cash-register', 'category' => 'Transaksi'],
            'retur-penjualan'  => ['title' => 'Retur Penjualan', 'icon' => 'fas fa-undo-alt', 'category' => 'Transaksi'],
            'laporan-penjualan'=> ['title' => 'Laporan Penjualan', 'icon' => 'fas fa-file-invoice-dollar', 'category' => 'Laporan'],
            'laporan-pelanggan'=> ['title' => 'Laporan Pelanggan', 'icon' => 'fas fa-file-alt', 'category' => 'Laporan'],
        ];

        $info = $moduleNames[$module] ?? [
            'title' => ucwords(str_replace('-', ' ', $module)),
            'icon'  => 'fas fa-folder',
            'category' => 'Sistem Penjualan'
        ];

        return view('placeholder', compact('info', 'module'));
    }
}
