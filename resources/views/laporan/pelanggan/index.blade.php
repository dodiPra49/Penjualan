@extends('layouts.app')

@section('title', 'Laporan Data Pelanggan - JasperReports')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Laporan</li>
                    <li class="breadcrumb-item active font-weight-bold text-primary" aria-current="page">Pelanggan</li>
                </ol>
            </nav>
            <h2 class="font-weight-bold text-dark mb-0">
                <i class="fas fa-file-invoice text-primary mr-2"></i>Laporan Data Pelanggan
            </h2>
            <p class="text-muted mb-0">Cetak dan unduh laporan data pelanggan menggunakan JasperReports engine</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex flex-wrap gap-2">
            <!-- Action Preview PDF in Modal / New Tab -->
            <a href="{{ route('laporan.pelanggan.preview', ['status' => $status]) }}" target="_blank" class="btn btn-primary-gradient mr-2 shadow-sm">
                <i class="fas fa-file-pdf mr-1"></i> Buka Jasper PDF di Tab Baru
            </a>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-success dropdown-toggle shadow-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-download mr-1"></i> Download Laporan
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow border-0">
                    <h6 class="dropdown-header text-uppercase font-weight-bold">Pilih Format File</h6>
                    <a class="dropdown-item py-2" href="{{ route('laporan.pelanggan.download', ['format' => 'pdf', 'status' => $status]) }}">
                        <i class="fas fa-file-pdf text-danger mr-2"></i> Format Dokumen PDF (.pdf)
                    </a>
                    <a class="dropdown-item py-2" href="{{ route('laporan.pelanggan.download', ['format' => 'xlsx', 'status' => $status]) }}">
                        <i class="fas fa-file-excel text-success mr-2"></i> Format Spreadsheet Excel (.xlsx)
                    </a>
                    <a class="dropdown-item py-2" href="{{ route('laporan.pelanggan.download', ['format' => 'csv', 'status' => $status]) }}">
                        <i class="fas fa-file-csv text-info mr-2"></i> Format File CSV (.csv)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-lg shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle mr-2 fa-lg"></i>
                <div>
                    <strong>Pemberitahuan JasperReport:</strong> {{ session('error') }}
                </div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Kartu Statistik Singkat -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-4 mb-3 mb-md-0">
            <div class="card card-stat border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon-wrapper bg-primary-subtle mr-3">
                        <i class="fas fa-users text-primary"></i>
                    </div>
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase">Total Seluruh Pelanggan</div>
                        <div class="h3 font-weight-bold text-dark mb-0">{{ number_format($totalSemua) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-4 mb-3 mb-md-0">
            <div class="card card-stat border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon-wrapper bg-success-subtle mr-3">
                        <i class="fas fa-user-check text-success"></i>
                    </div>
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase">Pelanggan Aktif</div>
                        <div class="h3 font-weight-bold text-success mb-0">{{ number_format($totalAktif) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-4">
            <div class="card card-stat border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon-wrapper bg-danger-subtle mr-3">
                        <i class="fas fa-user-times text-danger"></i>
                    </div>
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase">Pelanggan Nonaktif</div>
                        <div class="h3 font-weight-bold text-danger mb-0">{{ number_format($totalNonaktif) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Kontrol Cetak Jasper -->
    <div class="card main-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form action="{{ route('laporan.pelanggan') }}" method="GET" class="row align-items-end">
                <div class="col-md-4 mb-3 mb-md-0">
                    <label class="font-weight-bold text-dark small mb-2">
                        <i class="fas fa-filter text-primary mr-1"></i> Filter Status Pelanggan:
                    </label>
                    <select name="status" class="custom-select" onchange="this.form.submit()">
                        <option value="semua" {{ $status === 'semua' ? 'selected' : '' }}>Semua Status Pelanggan ({{ $totalSemua }})</option>
                        <option value="aktif" {{ $status === 'aktif' ? 'selected' : '' }}>Hanya Pelanggan Aktif ({{ $totalAktif }})</option>
                        <option value="nonaktif" {{ $status === 'nonaktif' ? 'selected' : '' }}>Hanya Pelanggan Nonaktif ({{ $totalNonaktif }})</option>
                    </select>
                </div>
                <div class="col-md-5 mb-3 mb-md-0">
                    <label class="font-weight-bold text-dark small mb-2">
                        <i class="fas fa-search text-primary mr-1"></i> Cari Data Pelanggan:
                    </label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari kode, nama, telepon, email..." value="{{ $search }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search mr-1"></i> Cari
                            </button>
                            @if(!empty($search) || $status !== 'semua')
                                <a href="{{ route('laporan.pelanggan') }}" class="btn btn-outline-secondary">Reset</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-3 text-md-right">
                    <button type="button" class="btn btn-info btn-block" onclick="refreshPdfPreview()">
                        <i class="fas fa-sync-alt mr-1"></i> Muat Ulang Preview PDF
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tampilan Tab: Pratinjau Tabel Web & Pratinjau Jasper PDF Langsung -->
    <div class="card main-card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs card-header-tabs m-0 px-3 border-0" id="laporanTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold py-3 px-4" id="preview-pdf-tab" data-toggle="tab" href="#preview-pdf" role="tab" aria-controls="preview-pdf" aria-selected="true">
                        <i class="fas fa-file-pdf text-danger mr-2"></i> Pratinjau Jasper PDF (Viewer)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold py-3 px-4" id="tabel-data-tab" data-toggle="tab" href="#tabel-data" role="tab" aria-controls="tabel-data" aria-selected="false">
                        <i class="fas fa-table text-primary mr-2"></i> Tabel Data Pelanggan
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="tab-content" id="laporanTabContent">
                <!-- Tab 1: Embedded Jasper PDF Viewer -->
                <div class="tab-pane fade show active p-3" id="preview-pdf" role="tabpanel" aria-labelledby="preview-pdf-tab">
                    <div class="d-flex justify-content-between align-items-center mb-2 px-2">
                        <span class="small text-muted">
                            <i class="fas fa-info-circle text-primary mr-1"></i> Dokumen PDF di bawah ini dirender secara langsung dari JasperReports Template (<code>laporan_pelanggan.jrxml</code>).
                        </span>
                        <a href="{{ route('laporan.pelanggan.preview', ['status' => $status]) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-external-link-alt mr-1"></i> Buka Fullscreen
                        </a>
                    </div>
                    <div class="border rounded-lg overflow-hidden" style="height: 650px; background: #525659;">
                        <iframe id="pdfPreviewFrame" src="{{ route('laporan.pelanggan.preview', ['status' => $status]) }}" width="100%" height="100%" style="border: none;">
                            <div class="p-4 text-center text-white">
                                <p>Browser Anda tidak mendukung pratinjau PDF langsung.</p>
                                <a href="{{ route('laporan.pelanggan.download', ['format' => 'pdf', 'status' => $status]) }}" class="btn btn-primary">Download PDF</a>
                            </div>
                        </iframe>
                    </div>
                </div>

                <!-- Tab 2: Tabel Data Pelanggan -->
                <div class="tab-pane fade p-3" id="tabel-data" role="tabpanel" aria-labelledby="tabel-data-tab">
                    <div class="table-responsive">
                        <table class="table table-custom table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5%">No</th>
                                    <th width="15%">Kode Pelanggan</th>
                                    <th width="25%">Nama Pelanggan</th>
                                    <th width="15%">No. Telepon</th>
                                    <th width="20%">Email</th>
                                    <th width="15%">Alamat</th>
                                    <th class="text-center" width="10%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pelanggans as $index => $pelanggan)
                                    <tr>
                                        <td class="text-center text-muted font-weight-bold">{{ $pelanggans->firstItem() + $index }}</td>
                                        <td>
                                            <span class="badge badge-code">{{ $pelanggan->kode_pelanggan }}</span>
                                        </td>
                                        <td class="font-weight-bold text-dark">{{ $pelanggan->nama_pelanggan }}</td>
                                        <td>
                                            @if($pelanggan->nomor_telepon)
                                                <a href="tel:{{ $pelanggan->nomor_telepon }}" class="text-decoration-none text-muted">
                                                    <i class="fas fa-phone-alt text-success mr-1"></i>{{ $pelanggan->nomor_telepon }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($pelanggan->email)
                                                <a href="mailto:{{ $pelanggan->email }}" class="text-decoration-none text-muted">
                                                    <i class="fas fa-envelope text-primary mr-1"></i>{{ $pelanggan->email }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $pelanggan->alamat }}">
                                                {{ $pelanggan->alamat ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($pelanggan->status === 'aktif')
                                                <span class="badge badge-status-aktif">
                                                    <i class="fas fa-check-circle mr-1"></i> Aktif
                                                </span>
                                            @else
                                                <span class="badge badge-status-nonaktif">
                                                    <i class="fas fa-times-circle mr-1"></i> Nonaktif
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-users-slash fa-3x mb-3 text-secondary"></i>
                                                <p class="font-weight-bold mb-1">Data Pelanggan Tidak Ditemukan</p>
                                                <p class="small mb-0">Coba ubah kata kunci pencarian atau filter status yang dipilih.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($pelanggans->hasPages())
                        <div class="p-3 border-top d-flex justify-content-between align-items-center">
                            <div class="small text-muted">
                                Menampilkan {{ $pelanggans->firstItem() }} sampai {{ $pelanggans->lastItem() }} dari {{ $pelanggans->total() }} data
                            </div>
                            <div>
                                {{ $pelanggans->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function refreshPdfPreview() {
        const frame = document.getElementById('pdfPreviewFrame');
        if (frame) {
            frame.src = "{{ route('laporan.pelanggan.preview') }}?status={{ $status }}&_t=" + new Date().getTime();
        }
    }
</script>
@endpush
