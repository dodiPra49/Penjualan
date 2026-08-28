@extends('layouts.app')

@section('title', 'Manajemen Data Kategori')

@section('content')
<!-- Page Header -->
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h2 class="fw-bold text-dark mb-1">
            <i class="bi bi-tags text-primary me-2"></i>Data Kategori Produk
        </h2>
        <p class="text-muted mb-0">Kelola master data kategori produk menggunakan RESTful API, OpenAPI 3.0, dan Service Layer Laravel.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <button class="btn btn-primary-gradient" onclick="openCreateModal()">
            <i class="bi bi-plus-circle-fill me-1"></i> Tambah Kategori
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card card-stat p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Kategori Terdaftar</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0" id="stat-total">{{ $totalKategori ?? 0 }}</h3>
                </div>
                <div class="stat-icon-wrapper bg-primary-subtle text-primary">
                    <i class="bi bi-tags-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-stat p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Arsitektur & Standarisasi</span>
                    <h5 class="fw-bold text-success mt-1 mb-0">OpenAPI 3.0 & Service Layer</h5>
                </div>
                <div class="stat-icon-wrapper bg-success-subtle text-success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card main-card mb-4">
    <!-- Filter & Search Toolbar -->
    <div class="card-header bg-white border-bottom p-3">
        <div class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" id="searchInput" class="form-control bg-light border-start-0 ps-0" 
                           placeholder="Cari nama kategori atau keterangan..." onkeyup="debounceSearch()">
                </div>
            </div>
            <div class="col-md-3 col-6">
                <select id="sortBy" class="form-select" onchange="loadKategori()">
                    <option value="id_kategori">Urutkan: ID Kategori</option>
                    <option value="nama_kategori">Urutkan: Nama Kategori</option>
                </select>
            </div>
            <div class="col-md-2 col-6">
                <select id="sortOrder" class="form-select" onchange="loadKategori()">
                    <option value="asc">Ascending (A-Z)</option>
                    <option value="desc">Descending (Z-A)</option>
                </select>
            </div>
            <div class="col-md-2 col-12 text-end">
                <button class="btn btn-outline-secondary btn-sm px-3 w-100" onclick="resetFilter()" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Table Body -->
    <div class="table-responsive">
        <table class="table table-custom table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th style="width: 140px;">ID Kategori</th>
                    <th>Nama Kategori</th>
                    <th>Keterangan</th>
                    <th style="width: 140px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="kategoriTableBody">
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        Memuat data kategori dari REST API...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Card Footer / Data Info -->
    <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
        <div class="small text-muted" id="tableInfo">
            Menampilkan data kategori
        </div>
        <div class="small text-muted">
            <span class="badge bg-secondary-subtle text-secondary">
                <i class="bi bi-hdd-network me-1"></i> Data Source: /api/kategori
            </span>
        </div>
    </div>
</div>

<!-- Technical Architecture Info Accordion -->
<div class="accordion mb-4" id="techDetailsAccordion">
    <div class="accordion-item main-card border-0">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed rounded-3 fw-semibold text-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTech">
                <i class="bi bi-info-circle-fill me-2"></i> Informasi Teknis Modul Kategori (OpenAPI 3.0 & Service Layer)
            </button>
        </h2>
        <div id="collapseTech" class="accordion-collapse collapse" data-bs-parent="#techDetailsAccordion">
            <div class="accordion-body bg-light rounded-bottom-3 p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card h-100 border p-3">
                            <h6 class="fw-bold text-dark"><i class="bi bi-file-earmark-code text-primary me-1"></i> 1. OpenAPI 3.0 Spec</h6>
                            <p class="small text-muted mb-2">Terdokumentasi penuh pada berkas <code>docs/kategori-api.json</code>:</p>
                            <ul class="small list-unstyled mb-0 font-monospace">
                                <li><span class="badge bg-success">GET</span> /api/kategori</li>
                                <li><span class="badge bg-primary">POST</span> /api/kategori</li>
                                <li><span class="badge bg-info text-dark">GET</span> /api/kategori/{id}</li>
                                <li><span class="badge bg-warning text-dark">PUT/PATCH</span> /api/kategori/{id}</li>
                                <li><span class="badge bg-danger">DELETE</span> /api/kategori/{id}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border p-3">
                            <h6 class="fw-bold text-dark"><i class="bi bi-layers text-success me-1"></i> 2. Service Layer Pattern</h6>
                            <p class="small text-muted mb-2"><code>App\Services\KategoriService</code> menangani operasi:</p>
                            <ul class="small text-muted mb-0 ps-3">
                                <li>Pemisahan logika bisnis dari HTTP Controller</li>
                                <li>Database Transaction handling</li>
                                <li>Query searching, dynamic sorting, dan pagination</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border p-3">
                            <h6 class="fw-bold text-dark"><i class="bi bi-shield-check text-info me-1"></i> 3. Validasi & Resource</h6>
                            <p class="small text-muted mb-2">Standardisasi input & output:</p>
                            <ul class="small text-muted mb-0 ps-3">
                                <li><code>StoreKategoriRequest</code> & <code>UpdateKategoriRequest</code></li>
                                <li><code>KategoriResource</code> untuk output JSON seragam</li>
                                <li>Custom audit header <code>X-Api-Module</code> & <code>X-Execution-Time-Ms</code></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH / EDIT KATEGORI -->
<div class="modal fade" id="kategoriModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalTitle">
                    <i class="bi bi-plus-circle text-primary me-2"></i>Tambah Kategori Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="kategoriForm" onsubmit="saveKategori(event)">
                <div class="modal-body p-4">
                    <input type="hidden" id="id_kategori">

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_kategori" maxlength="50" placeholder="Contoh: Elektronik, Pakaian, Makanan..." required>
                        <div class="invalid-feedback" id="err-nama_kategori"></div>
                        <div class="form-text small">Maksimal 50 karakter.</div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small text-muted">Keterangan</label>
                        <textarea class="form-control" id="keterangan" rows="3" maxlength="150" placeholder="Deskripsi atau catatan singkat kategori produk (opsional)"></textarea>
                        <div class="invalid-feedback" id="err-keterangan"></div>
                        <div class="form-text small">Maksimal 150 karakter.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-gradient" id="btnSubmit">
                        <i class="bi bi-save me-1"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL DETAIL KATEGORI -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-tag text-info me-2"></i>Detail Informasi Kategori
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="avatar-wrapper bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="bi bi-tag"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-0" id="detail-nama">-</h5>
                    <div class="mt-1">
                        <span class="badge-code" id="detail-id">-</span>
                    </div>
                </div>

                <div class="card bg-light border-0 p-3">
                    <div class="row g-2 small">
                        <div class="col-4 text-muted fw-semibold">ID Kategori</div>
                        <div class="col-8 text-dark fw-medium" id="detail-id-text">-</div>

                        <div class="col-4 text-muted fw-semibold">Nama Kategori</div>
                        <div class="col-8 text-dark fw-medium" id="detail-nama-text">-</div>

                        <div class="col-4 text-muted fw-semibold">Keterangan</div>
                        <div class="col-8 text-dark fw-medium" id="detail-keterangan">-</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const API_URL = '/api/kategori';
    let kategoriModalInstance = null;
    let detailModalInstance = null;
    let searchTimeout = null;

    document.addEventListener('DOMContentLoaded', () => {
        kategoriModalInstance = new bootstrap.Modal(document.getElementById('kategoriModal'));
        detailModalInstance = new bootstrap.Modal(document.getElementById('detailModal'));
        loadKategori();
    });

    /**
     * Mengambil daftar kategori dari REST API
     */
    async function loadKategori() {
        const tableBody = document.getElementById('kategoriTableBody');
        const search = document.getElementById('searchInput').value;
        const sortBy = document.getElementById('sortBy').value;
        const sortOrder = document.getElementById('sortOrder').value;

        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Memuat data kategori...
                </td>
            </tr>
        `;

        try {
            const queryParams = new URLSearchParams();
            if (search) queryParams.append('search', search);
            if (sortBy) queryParams.append('sort_by', sortBy);
            if (sortOrder) queryParams.append('sort_order', sortOrder);

            const response = await fetch(`${API_URL}?${queryParams.toString()}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success && result.data.length > 0) {
                renderTable(result.data);
                document.getElementById('tableInfo').innerHTML = `Total data ditemukan: <strong>${result.data.length}</strong> kategori`;
                document.getElementById('stat-total').innerText = result.meta?.total || result.data.length;
            } else {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            Tidak ada data kategori yang ditemukan.
                        </td>
                    </tr>
                `;
                document.getElementById('tableInfo').innerHTML = 'Tidak ada data.';
            }
        } catch (error) {
            console.error('Error fetching kategori:', error);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4 text-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Gagal memuat data dari REST API.
                    </td>
                </tr>
            `;
        }
    }

    /**
     * Render baris data ke tabel HTML
     */
    function renderTable(data) {
        const tableBody = document.getElementById('kategoriTableBody');
        let html = '';

        data.forEach((item, index) => {
            html += `
                <tr>
                    <td class="text-muted fw-semibold">${index + 1}</td>
                    <td><span class="badge-code">ID: ${item.id_kategori}</span></td>
                    <td>
                        <div class="fw-bold text-dark">${escapeHtml(item.nama_kategori)}</div>
                    </td>
                    <td>
                        <div class="small text-muted" style="max-width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${escapeHtml(item.keterangan || '-')}">
                            ${escapeHtml(item.keterangan || '-')}
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <button class="btn btn-outline-info btn-action" onclick="showDetail(${item.id_kategori})" title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-outline-warning btn-action" onclick="openEditModal(${item.id_kategori})" title="Edit Data">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-action" onclick="deleteKategori(${item.id_kategori}, '${escapeHtml(item.nama_kategori)}')" title="Hapus Data">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        tableBody.innerHTML = html;
    }

    /**
     * Debounce pencarian
     */
    function debounceSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadKategori();
        }, 300);
    }

    /**
     * Reset filter
     */
    function resetFilter() {
        document.getElementById('searchInput').value = '';
        document.getElementById('sortBy').value = 'id_kategori';
        document.getElementById('sortOrder').value = 'asc';
        loadKategori();
    }

    /**
     * Buka modal Tambah Kategori
     */
    function openCreateModal() {
        clearValidationErrors();
        document.getElementById('kategoriForm').reset();
        document.getElementById('id_kategori').value = '';
        document.getElementById('modalTitle').innerHTML = '<i class="bi bi-plus-circle text-primary me-2"></i>Tambah Kategori Baru';
        kategoriModalInstance.show();
    }

    /**
     * Buka modal Edit dan isi data via REST API GET /api/kategori/{id}
     */
    async function openEditModal(id) {
        clearValidationErrors();
        try {
            Swal.fire({
                title: 'Memuat data...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            const res = await fetch(`${API_URL}/${id}`, {
                headers: { 'Accept': 'application/json' }
            });
            const result = await res.json();
            Swal.close();

            if (result.success) {
                const k = result.data;
                document.getElementById('id_kategori').value = k.id_kategori;
                document.getElementById('nama_kategori').value = k.nama_kategori;
                document.getElementById('keterangan').value = k.keterangan || '';

                document.getElementById('modalTitle').innerHTML = '<i class="bi bi-pencil-square text-warning me-2"></i>Edit Data Kategori';
                kategoriModalInstance.show();
            } else {
                Swal.fire('Error', result.message || 'Gagal memuat data kategori', 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'Terjadi kesalahan saat mengambil data.', 'error');
        }
    }

    /**
     * Simpan data kategori (POST untuk create, PUT untuk update)
     */
    async function saveKategori(event) {
        event.preventDefault();
        clearValidationErrors();

        const id = document.getElementById('id_kategori').value;
        const isUpdate = !!id;

        const payload = {
            nama_kategori: document.getElementById('nama_kategori').value.trim(),
            keterangan: document.getElementById('keterangan').value.trim() || null
        };

        const url = isUpdate ? `${API_URL}/${id}` : API_URL;
        const method = isUpdate ? 'PUT' : 'POST';

        const btnSubmit = document.getElementById('btnSubmit');
        const originalBtnText = btnSubmit.innerHTML;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';
        btnSubmit.disabled = true;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const result = await res.json();
            btnSubmit.innerHTML = originalBtnText;
            btnSubmit.disabled = false;

            if (res.ok && result.success) {
                kategoriModalInstance.hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.message,
                    timer: 1800,
                    showConfirmButton: false
                });
                loadKategori();
            } else if (res.status === 422 && result.errors) {
                // Tampilkan error validasi form request
                displayValidationErrors(result.errors);
            } else {
                Swal.fire('Gagal', result.message || 'Gagal menyimpan data.', 'error');
            }
        } catch (error) {
            btnSubmit.innerHTML = originalBtnText;
            btnSubmit.disabled = false;
            Swal.fire('Error', 'Terjadi kesalahan pada server/jaringan.', 'error');
        }
    }

    /**
     * Lihat detail kategori di modal
     */
    async function showDetail(id) {
        try {
            Swal.fire({
                title: 'Memuat detail...',
                didOpen: () => Swal.showLoading()
            });

            const res = await fetch(`${API_URL}/${id}`, {
                headers: { 'Accept': 'application/json' }
            });
            const result = await res.json();
            Swal.close();

            if (result.success) {
                const k = result.data;
                document.getElementById('detail-nama').innerText = k.nama_kategori;
                document.getElementById('detail-id').innerText = `ID: ${k.id_kategori}`;
                document.getElementById('detail-id-text').innerText = k.id_kategori;
                document.getElementById('detail-nama-text').innerText = k.nama_kategori;
                document.getElementById('detail-keterangan').innerText = k.keterangan || '-';

                detailModalInstance.show();
            } else {
                Swal.fire('Error', result.message || 'Detail tidak ditemukan', 'error');
            }
        } catch (e) {
            Swal.fire('Error', 'Gagal memuat detail kategori.', 'error');
        }
    }

    /**
     * Hapus data kategori via REST API DELETE /api/kategori/{id}
     */
    async function deleteKategori(id, name) {
        const confirm = await Swal.fire({
            title: 'Hapus Kategori?',
            html: `Apakah Anda yakin ingin menghapus kategori <strong>${name}</strong>? Tindakan ini tidak dapat dibatalkan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal'
        });

        if (confirm.isConfirmed) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch(`${API_URL}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const result = await res.json();
                if (res.ok && result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: result.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    loadKategori();
                } else {
                    Swal.fire('Gagal', result.message || 'Gagal menghapus data.', 'error');
                }
            } catch (e) {
                Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
            }
        }
    }

    /**
     * Tampilkan pesan error validasi Form Request
     */
    function displayValidationErrors(errors) {
        for (const [field, messages] of Object.entries(errors)) {
            const inputEl = document.getElementById(field);
            const errEl = document.getElementById(`err-${field}`);
            if (inputEl) inputEl.classList.add('is-invalid');
            if (errEl) errEl.innerText = messages[0];
        }
    }

    /**
     * Bersihkan pesan error validasi Form
     */
    function clearValidationErrors() {
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.innerText = '');
    }

    /**
     * Escape string HTML helper
     */
    function escapeHtml(text) {
        if (!text) return '-';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
    }
</script>
@endpush
