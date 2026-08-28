# Dokumen Rancangan Arsitektur Aplikasi & Proses Bisnis Pelanggan

## 1. Ikhtisar Arsitektur Aplikasi
Aplikasi **Sistem Penjualan** dibangun menggunakan arsitektur berlapis (*Layered Architecture*) berbasis kerangka kerja Laravel. Pemisahan tanggung jawab (*Separation of Concerns*) diterapkan untuk menjamin skalabilitas, keterbacaan kode (*maintainability*), dan kemudahan pengujian (*testability*).

```
+-------------------------------------------------------------+
|                      Client Application                     |
|            (Frontend Web, Mobile App, Postman/Swagger)      |
+-------------------------------------------------------------+
                              | HTTP Request (JSON)
                              v
+-------------------------------------------------------------+
|                     Routing & Middleware                    |
|  - routes/api.php                                           |
|  - App\Http\Middleware\PelangganApiMiddleware               |
|    * Set JSON Header                                        |
|    * Execution Time Tracking (X-Execution-Time-Ms)          |
|    * Custom Header (X-Api-Module: Pelanggan-CRUD)           |
|    * Request/Response Auditing & Logging                    |
+-------------------------------------------------------------+
                              | Validated Request
                              v
+-------------------------------------------------------------+
|                   HTTP Controller & Requests                |
|  - App\Http\Controllers\Api\PelangganApiController          |
|  - App\Http\Controllers\Api\KategoriApiController           |
|  - App\Http\Requests\StorePelangganRequest                  |
|  - App\Http\Requests\UpdatePelangganRequest                 |
|  - App\Http\Requests\StoreKategoriRequest                   |
|  - App\Http\Requests\UpdateKategoriRequest                  |
|  - App\Http\Resources\PelangganResource                     |
|  - App\Http\Resources\KategoriResource                      |
+-------------------------------------------------------------+
                              | Business Operations
                              v
+-------------------------------------------------------------+
|                      Service Layer                          |
|  - App\Services\PelangganService                            |
|  - App\Services\KategoriService                             |
|    * Business Logic, Transactions, Validations              |
|    * Filter, Search, Sorting, & Pagination Logic            |
+-------------------------------------------------------------+
                              | Eloquent Query
                              v
+-------------------------------------------------------------+
|                   Data Access / Model Layer                 |
|  - App\Models\Pelanggan                                     |
|  - App\Models\Kategori                                      |
+-------------------------------------------------------------+
                              | SQL Execution
                              v
+-------------------------------------------------------------+
|                     Database (MySQL / MariaDB)              |
|  - Table: `pelanggans`, `kategori`, dll.                    |
+-------------------------------------------------------------+
```

---

## 2. Alur Proses Bisnis Data Pelanggan

### A. Alur Pembuatan Data Pelanggan (Create - POST `/api/pelanggan`)
1. **Request Masuk**: Klien mengirimkan payload JSON berisi atribut `nama_pelanggan`, `email`, `nomor_telepon`, `alamat`, `status`, dan opsi `kode_pelanggan`.
2. **Validasi (Form Request)**: `StorePelangganRequest` memastikan format data valid (contoh: `nama_pelanggan` wajib, format `email` valid, keunikan `kode_pelanggan`). Jika gagal, mengembalikan HTTP 422.
3. **Logika Bisnis (Service)**:
   - Jika `kode_pelanggan` tidak diisi klien, `PelangganService::generateKodePelanggan()` membuat kode unik berurutan dengan format `PLG-XXXX` (contoh: `PLG-0001`, `PLG-0002`).
   - Status default disetel ke `aktif` jika tidak ditentukan.
4. **Penyimpanan (Model & Database)**: Data disimpan ke tabel `pelanggans` menggunakan database transaction.
5. **Transformasi Respons (API Resource)**: `PelangganResource` memformat response output JSON seragam dengan HTTP status 201.

### B. Alur Pengambilan Data (Read / Search - GET `/api/pelanggan` & GET `/api/pelanggan/{id}`)
1. **Daftar & Pencarian**:
   - Menerima parameter `search` untuk mencocokkan kode, nama, email, nomor telepon, atau alamat via scope Eloquent.
   - Filter berdasarkan status (`aktif` atau `nonaktif`).
   - Sorting dinamis berdasarkan kolom yang dipilih (`created_at`, `nama_pelanggan`, dll.) dan arah (`asc` / `desc`).
   - Paginasi otomatis jika parameter `per_page` disertakan.
2. **Detail Pelanggan**: Mengambil entitas tunggal berdasarkan `id`. Mengembalikan HTTP 404 jika ID tidak ditemukan.
3. **Ringkasan Statistik**: Endpoint `/api/pelanggan/stats/summary` menghitung agregasi total pelanggan, pelanggan aktif, dan pelanggan nonaktif.

### C. Alur Pembaruan Data (Update - PUT/PATCH `/api/pelanggan/{id}`)
1. **Validasi**: `UpdatePelangganRequest` memverifikasi aturan keunikan `kode_pelanggan` dengan mengabaikan ID pelanggan yang sedang diperbarui (*ignore current record*).
2. **Eksekusi Update**: `PelangganService` memastikan data target ada di database, kemudian memperbarui field yang diberikan.
3. **Respons**: Mengembalikan data entitas terbaru dengan HTTP 200.

### D. Alur Penghapusan Data (Delete - DELETE `/api/pelanggan/{id}`)
1. Memeriksa keberadaan data pelanggan.
2. Menghapus rekaman pelanggan secara permanen dari basis data.
3. Mengembalikan pesan konfirmasi keberhasilan dengan HTTP 200.

---

## 3. Spesifikasi OpenAPI 3.0 (Swagger)

Spesifikasi lengkap REST API Pelanggan didefinisikan dalam format OpenAPI 3.0 (JSON) pada berkas:
📄 [`docs/pelanggan-api.json`](file:///d:/DODI%20AGUSRI.S.KOM/2026%202026%202026/FELLOW%20DEVELOPER/LARAVEL/Penjualan/docs/pelanggan-api.json)

### Ringkasan Endpoint CRUD Pelanggan

| Metode | Endpoint | Deskripsi | Query / Body Parameters | Status Response |
| :--- | :--- | :--- | :--- | :--- |
| **GET** | `/api/pelanggan` | Mengambil daftar pelanggan | `search`, `status`, `sort_by`, `sort_order`, `per_page`, `page` | `200`, `500` |
| **POST** | `/api/pelanggan` | Menambah data pelanggan baru | Body JSON: `nama_pelanggan` (req), `kode_pelanggan`, `email`, `nomor_telepon`, `alamat`, `status` | `201`, `422`, `500` |
| **GET** | `/api/pelanggan/stats/summary` | Mengambil statistik ringkas pelanggan | - | `200`, `500` |
| **GET** | `/api/pelanggan/{id}` | Mengambil detail pelanggan | Path: `id` | `200`, `404`, `500` |
| **PUT** | `/api/pelanggan/{id}` | Update data pelanggan (penuh) | Path: `id`, Body JSON: data pelanggan | `200`, `404`, `422`, `500` |
| **PATCH** | `/api/pelanggan/{id}` | Update data pelanggan (parsial) | Path: `id`, Body JSON: field yang diubah | `200`, `404`, `422`, `500` |
| **DELETE** | `/api/pelanggan/{id}` | Menghapus pelanggan | Path: `id` | `200`, `404`, `500` |

---

## 4. Cara Menggunakan & Menguji Spesifikasi OpenAPI

1. **Swagger Editor / Swagger UI**:
   - Buka [Swagger Editor Online](https://editor.swagger.io/).
   - Impor / Paste isi file [`docs/pelanggan-api.json`](file:///d:/DODI%20AGUSRI.S.KOM/2026%202026%202026/FELLOW%20DEVELOPER/LARAVEL/Penjualan/docs/pelanggan-api.json).
2. **Postman**:
   - Buka Postman -> Klik tombol **Import** -> Pilih file `docs/pelanggan-api.json`.
   - Postman akan otomatis meng-generate Collection berisi seluruh endpoint CRUD pelanggan beserta contoh payload dan response-nya.
3. **Dokumentasi Redoc**:
   - File JSON dapat langsung dirender menggunakan Redoc CLI atau Redocly viewer.
