@extends('layouts.app')

@section('title', 'Dashboard & Menu Utama - Sistem Manajemen Penjualan')

@section('content')
<!-- Hero Welcome Section -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem; background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%); color: white;">
    <div class="card-body p-4 p-md-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge badge-warning px-3 py-2 text-uppercase font-weight-bold mb-3 shadow-sm" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                    <i class="fas fa-sparkles mr-1"></i> Aplikasi Penjualan v1.0
                </span>
                <h1 class="font-weight-bold mb-2 display-5" style="letter-spacing: -0.5px;">
                    Selamat Datang di Sistem Penjualan
                </h1>
                <p class="lead text-white-50 mb-4" style="font-size: 1.05rem;">
                    Aplikasi manajemen penjualan modern dengan Bootstrap 4. Silakan gunakan menu dropdown navigasi di bagian atas atau tombol pintasan di bawah untuk mengakses modul yang Anda butuhkan.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('pelanggan.index') }}" class="btn btn-light font-weight-bold px-4 py-2 mr-2 mb-2 shadow-sm text-primary">
                        <i class="fas fa-users text-primary mr-1"></i> Kelola Pelanggan
                    </a>
                    <a href="{{ route('kategori.index') }}" class="btn btn-outline-light font-weight-bold px-4 py-2 mb-2">
                        <i class="fas fa-tags mr-1"></i> Kelola Kategori
                    </a>
                </div>
            </div>
            <div class="col-lg-4 text-center d-none d-lg-block">
                <div class="p-3">
                    <i class="fas fa-store-alt text-warning fa-7x" style="opacity: 0.85; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.3));"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card card-stat h-100 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Total Pelanggan</span>
                    <h3 class="font-weight-bold text-dark mt-1 mb-0">{{ $stats['total_pelanggan'] ?? 0 }}</h3>
                    <small class="text-success font-weight-bold">
                        <i class="fas fa-check-circle mr-1"></i>{{ $stats['pelanggan_aktif'] ?? 0 }} Aktif
                    </small>
                </div>
                <div class="stat-icon-wrapper bg-success-subtle text-success">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card card-stat h-100 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Total Kategori</span>
                    <h3 class="font-weight-bold text-dark mt-1 mb-0">{{ $stats['total_kategori'] ?? 0 }}</h3>
                    <small class="text-info font-weight-bold">
                        <i class="fas fa-tags mr-1"></i>Master Kategori
                    </small>
                </div>
                <div class="stat-icon-wrapper bg-info-subtle text-info">
                    <i class="fas fa-tags"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-12 mb-3">
        <div class="card card-stat h-100 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Status Sistem</span>
                    <h5 class="font-weight-bold text-primary mt-1 mb-0">Bootstrap 4 & REST</h5>
                    <small class="text-muted">
                        <i class="fas fa-server mr-1"></i>Laravel Ready
                    </small>
                </div>
                <div class="stat-icon-wrapper bg-primary-subtle text-primary">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Structure Cards Grid -->
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 class="font-weight-bold text-dark mb-0">
            <i class="fas fa-th-large text-primary mr-2"></i>Struktur Menu Navigasi
        </h4>
        <small class="text-muted">Akses langsung ke seluruh menu Master, Transaksi, Laporan, dan Tool</small>
    </div>
</div>

<div class="row">
    <!-- 1. Master Section Card -->
    <div class="col-lg-6 mb-4">
        <div class="card main-card h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="stat-icon-wrapper bg-primary-subtle text-primary mr-3" style="width: 38px; height: 38px; font-size: 1.1rem;">
                        <i class="fas fa-layer-group"></i>
                    </span>
                    <h5 class="font-weight-bold text-dark mb-0">1. Master Data</h5>
                </div>
                <span class="badge badge-primary px-2 py-1">3 Submenu</span>
            </div>
            <div class="card-body p-3">
                <div class="list-group list-group-flush">
                    <!-- Produk -->
                    <a href="{{ route('produk.index') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-3 border rounded mb-2">
                        <div class="d-flex align-items-center">
                            <div class="mr-3 text-primary font-weight-bold" style="font-size: 1.25rem; width: 30px; text-align: center;">
                                <i class="fas fa-box"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-0 text-dark">Produk</h6>
                                <small class="text-muted">Kelola data master barang dan katalog produk</small>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>

                    <!-- Kategori -->
                    <a href="{{ route('kategori.index') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-3 border rounded mb-2">
                        <div class="d-flex align-items-center">
                            <div class="mr-3 text-info font-weight-bold" style="font-size: 1.25rem; width: 30px; text-align: center;">
                                <i class="fas fa-tags"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-0 text-dark">Kategori</h6>
                                <small class="text-muted">Kelola kategori produk dan OpenAPI 3.0</small>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>

                    <!-- Pelanggan -->
                    <a href="{{ route('pelanggan.index') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-3 border rounded">
                        <div class="d-flex align-items-center">
                            <div class="mr-3 text-success font-weight-bold" style="font-size: 1.25rem; width: 30px; text-align: center;">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-0 text-dark">Pelanggan</h6>
                                <small class="text-muted">Kelola database pelanggan aktif, kontak, dan alamat</small>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Transaksi Section Card -->
    <div class="col-lg-6 mb-4">
        <div class="card main-card h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="stat-icon-wrapper bg-success-subtle text-success mr-3" style="width: 38px; height: 38px; font-size: 1.1rem;">
                        <i class="fas fa-shopping-cart"></i>
                    </span>
                    <h5 class="font-weight-bold text-dark mb-0">2. Transaksi</h5>
                </div>
                <span class="badge badge-success px-2 py-1">2 Submenu</span>
            </div>
            <div class="card-body p-3">
                <div class="list-group list-group-flush">
                    <!-- Penjualan -->
                    <a href="{{ route('transaksi.penjualan') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-3 border rounded mb-2">
                        <div class="d-flex align-items-center">
                            <div class="mr-3 text-success font-weight-bold" style="font-size: 1.25rem; width: 30px; text-align: center;">
                                <i class="fas fa-cash-register"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-0 text-dark">Penjualan</h6>
                                <small class="text-muted">Pencatatan transaksi kasir POS & faktur penjualan</small>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>

                    <!-- Retur Penjualan -->
                    <a href="{{ route('transaksi.retur') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-3 border rounded">
                        <div class="d-flex align-items-center">
                            <div class="mr-3 text-danger font-weight-bold" style="font-size: 1.25rem; width: 30px; text-align: center;">
                                <i class="fas fa-undo-alt"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-0 text-dark">Retur Penjualan</h6>
                                <small class="text-muted">Proses pengembalian barang atau pembatalan transaksi</small>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Laporan Section Card -->
    <div class="col-lg-6 mb-4">
        <div class="card main-card h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="stat-icon-wrapper bg-warning-subtle text-warning mr-3" style="width: 38px; height: 38px; font-size: 1.1rem;">
                        <i class="fas fa-chart-bar"></i>
                    </span>
                    <h5 class="font-weight-bold text-dark mb-0">3. Laporan</h5>
                </div>
                <span class="badge badge-warning px-2 py-1">2 Submenu</span>
            </div>
            <div class="card-body p-3">
                <div class="list-group list-group-flush">
                    <!-- Laporan Penjualan -->
                    <a href="{{ route('laporan.penjualan') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-3 border rounded mb-2">
                        <div class="d-flex align-items-center">
                            <div class="mr-3 text-primary font-weight-bold" style="font-size: 1.25rem; width: 30px; text-align: center;">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-0 text-dark">Laporan Penjualan</h6>
                                <small class="text-muted">Rekapitulasi omset, grafik berkala, dan ekspor data</small>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>

                    <!-- Laporan Pelanggan -->
                    <a href="{{ route('laporan.pelanggan') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-3 border rounded">
                        <div class="d-flex align-items-center">
                            <div class="mr-3 text-info font-weight-bold" style="font-size: 1.25rem; width: 30px; text-align: center;">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-0 text-dark">Laporan Pelanggan</h6>
                                <small class="text-muted">Daftar pelanggan, riwayat belanja, dan status</small>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Tool Section Card -->
    <div class="col-lg-6 mb-4">
        <div class="card main-card h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="stat-icon-wrapper bg-info-subtle text-info mr-3" style="width: 38px; height: 38px; font-size: 1.1rem;">
                        <i class="fas fa-tools"></i>
                    </span>
                    <h5 class="font-weight-bold text-dark mb-0">4. Tool</h5>
                </div>
                <span class="badge badge-info px-2 py-1">1 Submenu</span>
            </div>
            <div class="card-body p-3">
                <div class="list-group list-group-flush">
                    <!-- Dashboard Penjualan -->
                    <a href="{{ route('home') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-3 border rounded">
                        <div class="d-flex align-items-center">
                            <div class="mr-3 text-warning font-weight-bold" style="font-size: 1.25rem; width: 30px; text-align: center;">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-0 text-dark">Dashboard Penjualan</h6>
                                <small class="text-muted">Ringkasan analitik, KPI penjualan, dan informasi sistem</small>
                            </div>
                        </div>
                        <span class="badge badge-light border px-2 py-1 font-weight-bold">Aktif</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
