@extends('layouts.app')

@section('title', ($info['title'] ?? 'Modul') . ' - Sistem Penjualan')

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-md-8 col-lg-6">
        <div class="card main-card text-center p-4 p-md-5">
            <div class="mb-4">
                <div class="stat-icon-wrapper bg-primary-subtle text-primary mx-auto mb-3" style="width: 75px; height: 75px; font-size: 2.2rem; border-radius: 1.25rem;">
                    <i class="{{ $info['icon'] ?? 'fas fa-layer-group' }}"></i>
                </div>
                <span class="badge badge-light border text-uppercase font-weight-bold px-3 py-1 mb-2">
                    {{ $info['category'] ?? 'Menu Penjualan' }}
                </span>
                <h3 class="font-weight-bold text-dark mb-2">{{ $info['title'] ?? 'Modul' }}</h3>
                <p class="text-muted mb-4">
                    Modul ini telah terdaftar dalam struktur navigasi menu dropdown dan siap diintegrasikan dengan database & RESTful API.
                </p>
            </div>

            <div class="card bg-light border-0 p-3 mb-4 text-left small">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted font-weight-bold">Status Modul:</span>
                    <span class="badge badge-info px-2 py-1">Siap Dikembangkan</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted font-weight-bold">Framework UI:</span>
                    <span class="text-dark font-weight-bold">Bootstrap 4.6</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted font-weight-bold">Menu Induk:</span>
                    <span class="text-primary font-weight-bold">{{ $info['category'] ?? 'Master' }}</span>
                </div>
            </div>

            <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('home') }}" class="btn btn-primary-gradient px-4 mr-2">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Menu Awal
                </a>
                @if(isset($info['category']) && $info['category'] === 'Master')
                    <a href="{{ route('pelanggan.index') }}" class="btn btn-outline-secondary px-3">
                        <i class="fas fa-users mr-1"></i> Data Pelanggan
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
