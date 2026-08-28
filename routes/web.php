<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardWebController;
use App\Http\Controllers\PelangganWebController;
use App\Http\Controllers\KategoriWebController;
use App\Http\Controllers\LaporanPelangganController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Tampilan awal aplikasi akan membuka Dashboard / Menu Utama Penjualan
*/

// Tampilan Awal Aplikasi
Route::get('/', [DashboardWebController::class, 'index'])->name('home');
Route::get('/dashboard', [DashboardWebController::class, 'index'])->name('dashboard.index');

// Master Data
Route::get('/master/produk', [DashboardWebController::class, 'placeholder'])->defaults('module', 'produk')->name('produk.index');
Route::get('/kategori', [KategoriWebController::class, 'index'])->name('kategori.index');
Route::get('/pelanggan', [PelangganWebController::class, 'index'])->name('pelanggan.index');

// Transaksi
Route::get('/transaksi/penjualan', [DashboardWebController::class, 'placeholder'])->defaults('module', 'penjualan')->name('transaksi.penjualan');
Route::get('/transaksi/retur-penjualan', [DashboardWebController::class, 'placeholder'])->defaults('module', 'retur-penjualan')->name('transaksi.retur');

// Laporan
Route::get('/laporan/penjualan', [DashboardWebController::class, 'placeholder'])->defaults('module', 'laporan-penjualan')->name('laporan.penjualan');
Route::get('/laporan/pelanggan', [LaporanPelangganController::class, 'index'])->name('laporan.pelanggan');
Route::get('/laporan/pelanggan/preview', [LaporanPelangganController::class, 'preview'])->name('laporan.pelanggan.preview');
Route::get('/laporan/pelanggan/download', [LaporanPelangganController::class, 'download'])->name('laporan.pelanggan.download');

