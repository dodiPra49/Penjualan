@extends('layouts.app')

@section('title', 'Manajemen Data Pelanggan')

@section('content')
<!-- Page Header -->
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h2 class="fw-bold text-dark mb-1">
            <i class="bi bi-people text-primary me-2"></i>Data Pelanggan
        </h2>
        <p class="text-muted mb-0">Kelola data pelanggan menggunakan RESTful API, Middleware, dan Service Layer Laravel.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <button class="btn btn-primary-gradient" onclick="openCreateModal()">
            <i class="bi bi-plus-circle-fill me-1"></i> Tambah Pelanggan
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-stat p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Pelanggan</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0" id="stat-total">{{ $stats['total'] ?? 0 }}</h3>
                </div>
                <div class="stat-icon-wrapper bg-primary-subtle text-primary">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-stat p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Pelanggan Aktif</span>
                    <h3 class="fw-bold text-success mt-1 mb-0" id="stat-aktif">{{ $stats['aktif'] ?? 0 }}</h3>
                </div>
                <div class="stat-icon-wrapper bg-success-subtle text-success">
                    <i class="bi bi-person-check-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-stat p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Pelanggan Non-Aktif</span>
                    <h3 class="fw-bold text-danger mt-1 mb-0" id="stat-nonaktif">{{ $stats['nonaktif'] ?? 0 }}</h3>
                </div>
                <div class="stat-icon-wrapper bg-danger-subtle text-danger">
                    <i class="bi bi-person-x-fill"></i>
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
                           placeholder="Cari kode, nama, email, no. telp, atau alamat..." onkeyup="debounceSearch()">
                </div>
            </div>
            <div class="col-md-3 col-6">
                <select id="statusFilter" class="form-select" onchange="loadPelanggan()">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Non-Aktif</option>
                </select>
            </div>
            <div class="col-md-4 col-6 text-end">
                <button class="btn btn-outline-secondary btn-sm px-3" onclick="resetFilter()" title="Reset Filter">
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
                    <th style="width: 140px;">Kode</th>
                    <th>Nama Pelanggan</th>
                    <th>Kontak</th>
                    <th>Alamat</th>
                    <th style="width: 110px;" class="text-center">Status</th>
                    <th style="width: 130px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="pelangganTableBody">
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        Memuat data dari REST API...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Card Footer / Pagination Info -->
    <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
        <div class="small text-muted" id="tableInfo">
            Menampilkan data pelanggan
        </div>
        <div class="small text-muted">
            <span class="badge bg-secondary-subtle text-secondary"><i class="bi bi-hdd-network me-1"></i> Data Source: /api/pelanggan</span>
        </div>
    </div>
</div>

<!-- Technical Architecture Info Accordion -->
<div class="accordion mb-4" id="techDetailsAccordion">
    <div class="accordion-item main-card border-0">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed rounded-3 fw-semibold text-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTech">
                <i class="bi bi-info-circle-fill me-2"></i> Informasi Arsitektur & Spesifikasi Modul (REST API, Middleware, Service)
            </button>
        </h2>
        <div id="collapseTech" class="accordion-collapse collapse" data-bs-parent="#techDetailsAccordion">
            <div class="accordion-body bg-light rounded-bottom-3 p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card h-100 border p-3">
                            <h6 class="fw-bold text-dark"><i class="bi bi-cloud-arrow-up text-primary me-1"></i> 1. RESTful API</h6>
                            <p class="small text-muted mb-2">Endpoint terstandarisasi untuk semua operasi CRUD:</p>
                            <ul class="small list-unstyled mb-0 font-monospace">
                                <li><span class="badge bg-success">GET</span> /api/pelanggan</li>
                                <li><span class="badge bg-primary">POST</span> /api/pelanggan</li>
                                <li><span class="badge bg-info text-dark">GET</span> /api/pelanggan/{id}</li>
                                <li><span class="badge bg-warning text-dark">PUT</span> /api/pelanggan/{id}</li>
                                <li><span class="badge bg-danger">DELETE</span> /api/pelanggan/{id}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border p-3">
                            <h6 class="fw-bold text-dark"><i class="bi bi-shield-check text-success me-1"></i> 2. Middleware Laravel</h6>
                            <p class="small text-muted mb-2"><code>PelangganApiMiddleware</code> bertugas:</p>
                            <ul class="small text-muted mb-0 ps-3">
                                <li>Memastikan header request JSON (<code>Accept: application/json</code>)</li>
                                <li>Menambahkan custom header audit (<code>X-Api-Module</code>, <code>X-Execution-Time-Ms</code>)</li>
                                <li>Mencatat log aktivitas akses endpoint API ke sistem log Laravel</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border p-3">
                            <h6 class="fw-bold text-dark"><i class="bi bi-layers text-info me-1"></i> 3. Service Layer</h6>
                            <p class="small text-muted mb-2"><code>PelangganService</code> memisahkan logika bisnis:</p>
                            <ul class="small text-muted mb-0 ps-3">
                                <li>Otomatisasi nomor urut kode pelanggan (<code>PLG-XXXX</code>)</li>
                                <li>Database Transaction & Error handling</li>
                                <li>Query filtering, search scope, dan ringkasan statistik data</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH / EDIT PELANGGAN -->
<div class="modal fade" id="pelangganModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalTitle">
                    <i class="bi bi-person-plus text-primary me-2"></i>Tambah Pelanggan Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="pelangganForm" onsubmit="savePelanggan(event)">
                <div class="modal-body p-4">
                    <input type="hidden" id="pelangganId">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Kode Pelanggan <span class="badge bg-light text-secondary">Otomatis / Kustom</span></label>
                        <input type="text" class="form-control" id="kode_pelanggan" placeholder="Contoh: PLG-0001 (Kosongkan untuk otomatis)">
                        <div class="invalid-feedback" id="err-kode_pelanggan"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Nama Pelanggan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_pelanggan" placeholder="Masukkan nama lengkap pelanggan" required>
                        <div class="invalid-feedback" id="err-nama_pelanggan"></div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="nama@email.com">
                            <div class="invalid-feedback" id="err-email"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Nomor Telepon / WA</label>
                            <input type="text" class="form-control" id="nomor_telepon" placeholder="08xxxxxxxxxx">
                            <div class="invalid-feedback" id="err-nomor_telepon"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Alamat Lengkap</label>
                        <textarea class="form-control" id="alamat" rows="2" placeholder="Masukkan alamat lengkap"></textarea>
                        <div class="invalid-feedback" id="err-alamat"></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small text-muted">Status Pelanggan <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="statusAktif" value="aktif" checked>
                                <label class="form-check-label text-success fw-semibold" for="statusAktif">
                                    <i class="bi bi-check-circle me-1"></i> Aktif
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="statusNonaktif" value="nonaktif">
                                <label class="form-check-label text-danger fw-semibold" for="statusNonaktif">
                                    <i class="bi bi-x-circle me-1"></i> Non-Aktif
                                </label>
                            </div>
                        </div>
                        <div class="invalid-feedback d-block" id="err-status"></div>
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

<!-- MODAL DETAIL PELANGGAN -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-person-badge text-info me-2"></i>Detail Informasi Pelanggan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="avatar-wrapper bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="bi bi-person"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-0" id="detail-nama">-</h5>
                    <div class="mt-1">
                        <span class="badge-code me-1" id="detail-kode">-</span>
                        <span id="detail-status-badge"></span>
                    </div>
                </div>

                <div class="card bg-light border-0 p-3">
                    <div class="row g-2 small">
                        <div class="col-4 text-muted fw-semibold">Email</div>
                        <div class="col-8 text-dark fw-medium" id="detail-email">-</div>

                        <div class="col-4 text-muted fw-semibold">No. Telepon</div>
                        <div class="col-8 text-dark fw-medium" id="detail-telepon">-</div>

                        <div class="col-4 text-muted fw-semibold">Alamat</div>
                        <div class="col-8 text-dark fw-medium" id="detail-alamat">-</div>

                        <div class="col-4 text-muted fw-semibold">Dibuat Pada</div>
                        <div class="col-8 text-muted" id="detail-created">-</div>

                        <div class="col-4 text-muted fw-semibold">Diperbarui</div>
                        <div class="col-8 text-muted" id="detail-updated">-</div>
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
    const API_URL = '/api/pelanggan';
    let pelangganModalInstance = null;
    let detailModalInstance = null;
    let searchTimeout = null;

    document.addEventListener('DOMContentLoaded', () => {
        pelangganModalInstance = new bootstrap.Modal(document.getElementById('pelangganModal'));
        detailModalInstance = new bootstrap.Modal(document.getElementById('detailModal'));
        loadPelanggan();
        loadStats();
    });

    /**
     * Mengambil daftar pelanggan dari REST API
     */
    async function loadPelanggan() {
        const tableBody = document.getElementById('pelangganTableBody');
        const search = document.getElementById('searchInput').value;
        const status = document.getElementById('statusFilter').value;

        tableBody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Memuat data pelanggan...
                </td>
            </tr>
        `;

        try {
            const queryParams = new URLSearchParams();
            if (search) queryParams.append('search', search);
            if (status) queryParams.append('status', status);

            const response = await fetch(`${API_URL}?${queryParams.toString()}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success && result.data.length > 0) {
                renderTable(result.data);
                document.getElementById('tableInfo').innerHTML = `Total data ditemukan: <strong>${result.data.length}</strong> pelanggan`;
            } else {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            Tidak ada data pelanggan yang ditemukan.
                        </td>
                    </tr>
                `;
                document.getElementById('tableInfo').innerHTML = 'Tidak ada data.';
            }
        } catch (error) {
            console.error('Error fetching pelanggan:', error);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4 text-danger">
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
        const tableBody = document.getElementById('pelangganTableBody');
        let html = '';

        data.forEach((item, index) => {
            const statusBadge = item.status === 'aktif' 
                ? `<span class="badge-status-aktif"><i class="bi bi-check-circle-fill me-1"></i>Aktif</span>`
                : `<span class="badge-status-nonaktif"><i class="bi bi-x-circle-fill me-1"></i>Non-Aktif</span>`;

            html += `
                <tr>
                    <td class="text-muted fw-semibold">${index + 1}</td>
                    <td><span class="badge-code">${item.kode_pelanggan}</span></td>
                    <td>
                        <div class="fw-bold text-dark">${escapeHtml(item.nama_pelanggan)}</div>
                    </td>
                    <td>
                        <div class="small"><i class="bi bi-envelope text-muted me-1"></i>${escapeHtml(item.email)}</div>
                        <div class="small"><i class="bi bi-telephone text-muted me-1"></i>${escapeHtml(item.nomor_telepon)}</div>
                    </td>
                    <td>
                        <div class="small text-muted" style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${escapeHtml(item.alamat)}">
                            ${escapeHtml(item.alamat)}
                        </div>
                    </td>
                    <td class="text-center">${statusBadge}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <button class="btn btn-outline-info btn-action" onclick="showDetail(${item.id})" title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-outline-warning btn-action" onclick="openEditModal(${item.id})" title="Edit Data">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-action" onclick="deletePelanggan(${item.id}, '${escapeHtml(item.nama_pelanggan)}')" title="Hapus Data">
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
     * Memuat ringkasan statistik
     */
    async function loadStats() {
        try {
            const res = await fetch(`${API_URL}/stats/summary`, {
                headers: { 'Accept': 'application/json' }
            });
            const result = await res.json();
            if (result.success) {
                document.getElementById('stat-total').innerText = result.data.total;
                document.getElementById('stat-aktif').innerText = result.data.aktif;
                document.getElementById('stat-nonaktif').innerText = result.data.nonaktif;
            }
        } catch (e) {
            console.error('Error fetching stats:', e);
        }
    }

    /**
     * Debounce pencarian
     */
    function debounceSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadPelanggan();
        }, 300);
    }

    /**
     * Reset filter
     */
    function resetFilter() {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = '';
        loadPelanggan();
    }

    /**
     * Buka modal Tambah
     */
    function openCreateModal() {
        clearValidationErrors();
        document.getElementById('pelangganForm').reset();
        document.getElementById('pelangganId').value = '';
        document.getElementById('modalTitle').innerHTML = '<i class="bi bi-person-plus text-primary me-2"></i>Tambah Pelanggan Baru';
        document.getElementById('statusAktif').checked = true;
        pelangganModalInstance.show();
    }

    /**
     * Buka modal Edit dan isi data via REST API GET /api/pelanggan/{id}
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
                const p = result.data;
                document.getElementById('pelangganId').value = p.id;
                document.getElementById('kode_pelanggan').value = p.kode_pelanggan;
                document.getElementById('nama_pelanggan').value = p.nama_pelanggan;
                document.getElementById('email').value = p.email === '-' ? '' : p.email;
                document.getElementById('nomor_telepon').value = p.nomor_telepon === '-' ? '' : p.nomor_telepon;
                document.getElementById('alamat').value = p.alamat === '-' ? '' : p.alamat;

                if (p.status === 'aktif') {
                    document.getElementById('statusAktif').checked = true;
                } else {
                    document.getElementById('statusNonaktif').checked = true;
                }

                document.getElementById('modalTitle').innerHTML = '<i class="bi bi-pencil-square text-warning me-2"></i>Edit Data Pelanggan';
                pelangganModalInstance.show();
            } else {
                Swal.fire('Error', result.message || 'Gagal memuat data pelanggan', 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'Terjadi kesalahan saat mengambil data.', 'error');
        }
    }

    /**
     * Simpan data pelanggan (POST untuk create, PUT untuk update)
     */
    async function savePelanggan(event) {
        event.preventDefault();
        clearValidationErrors();

        const id = document.getElementById('pelangganId').value;
        const isUpdate = !!id;

        const payload = {
            kode_pelanggan: document.getElementById('kode_pelanggan').value.trim() || undefined,
            nama_pelanggan: document.getElementById('nama_pelanggan').value.trim(),
            email: document.getElementById('email').value.trim() || null,
            nomor_telepon: document.getElementById('nomor_telepon').value.trim() || null,
            alamat: document.getElementById('alamat').value.trim() || null,
            status: document.querySelector('input[name="status"]:checked')?.value || 'aktif'
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
                pelangganModalInstance.hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.message,
                    timer: 1800,
                    showConfirmButton: false
                });
                loadPelanggan();
                loadStats();
            } else if (res.status === 422 && result.errors) {
                // Tampilkan error validasi
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
     * Lihat detail pelanggan di modal
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
                const p = result.data;
                document.getElementById('detail-nama').innerText = p.nama_pelanggan;
                document.getElementById('detail-kode').innerText = p.kode_pelanggan;
                document.getElementById('detail-email').innerText = p.email;
                document.getElementById('detail-telepon').innerText = p.nomor_telepon;
                document.getElementById('detail-alamat').innerText = p.alamat;
                document.getElementById('detail-created').innerText = p.created_at || '-';
                document.getElementById('detail-updated').innerText = p.updated_at || '-';

                const statusBadge = p.status === 'aktif'
                    ? `<span class="badge-status-aktif"><i class="bi bi-check-circle-fill me-1"></i>Aktif</span>`
                    : `<span class="badge-status-nonaktif"><i class="bi bi-x-circle-fill me-1"></i>Non-Aktif</span>`;
                document.getElementById('detail-status-badge').innerHTML = statusBadge;

                detailModalInstance.show();
            } else {
                Swal.fire('Error', result.message || 'Detail tidak ditemukan', 'error');
            }
        } catch (e) {
            Swal.fire('Error', 'Gagal memuat detail pelanggan.', 'error');
        }
    }

    /**
     * Hapus data pelanggan via REST API DELETE /api/pelanggan/{id}
     */
    async function deletePelanggan(id, name) {
        const confirm = await Swal.fire({
            title: 'Hapus Pelanggan?',
            html: `Apakah Anda yakin ingin menghapus data pelanggan <strong>${name}</strong>? Tindakan ini tidak dapat dibatalkan.`,
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
                    loadPelanggan();
                    loadStats();
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