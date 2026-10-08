# Panduan Implementasi CI/CD Laravel ke VPS Linux

Panduan ini menjelaskan arsitektur dan langkah-langkah implementasi **Continuous Integration & Continuous Deployment (CI/CD)** menggunakan **GitHub Actions** untuk aplikasi Laravel ke server **VPS Linux (Ubuntu/Debian)**.

---

## 1. Arsitektur CI/CD

```
+-------------------------------------------------------------+
| Developer Push ke branch `main`                             |
+------------------------------+------------------------------+
                               |
                               v
+-------------------------------------------------------------+
| GitHub Actions: Job 1 - CI (Test & Build Check)             |
|  - Setup PHP 8.1 & Ekstensi                                 |
|  - Cache & Composer Install                                 |
|  - Node 20 & NPM Install & Vite Build                       |
|  - PHPUnit Test (Database In-Memory SQLite)                 |
+------------------------------+------------------------------+
                               | (Jika lolos pengujian)
                               v
+-------------------------------------------------------------+
| GitHub Actions: Job 2 - CD (SSH Deploy ke VPS)              |
|  - Menghubungkan ke VPS via SSH Key                         |
|  - Masuk ke direktori target (/var/www/penjualan)           |
|  - Menjalankan `./deploy.sh`                                |
+------------------------------+------------------------------+
                               |
                               v
+-------------------------------------------------------------+
| Eksekusi di VPS Linux (deploy.sh)                           |
|  1. php artisan down                                        |
|  2. git fetch & git reset --hard origin/main                |
|  3. composer install --no-dev --optimize-autoloader         |
|  4. npm ci && npm run build                                 |
|  5. php artisan migrate --force                             |
|  6. php artisan optimize:clear && cache config/route/view   |
|  7. php artisan queue:restart                               |
|  8. reload PHP-FPM                                          |
|  9. php artisan up                                          |
+-------------------------------------------------------------+
```

---

## 2. Persiapan Server VPS Linux

### A. Setup Environment Awal
Jika server VPS Anda masih baru (Ubuntu 20.04 / 22.04 / 24.04), Anda dapat menggunakan script otomasi yang telah disediakan:
```bash
# Upload atau buat file setup-vps.sh di server, lalu jalankan:
sudo bash deployment/setup-vps.sh
```
Atau pastikan paket-paket berikut sudah terpasang:
- **Nginx**
- **PHP 8.1 / 8.2** (`cli`, `fpm`, `mysql`, `xml`, `mbstring`, `curl`, `zip`, `bcmath`, `redis`, `intl`)
- **Composer**
- **Node.js 20 & NPM**
- **MySQL / MariaDB** & **Redis**
- **Git**

---

### B. Siapkan Direktori Aplikasi & Clone Project
Masuk ke VPS via SSH, lalu clone repository ke `/var/www/penjualan`:

```bash
# Buat direktori dan atur kepemilikan user
sudo mkdir -p /var/www/penjualan
sudo chown -R $USER:$USER /var/www/penjualan

# Clone repository
git clone https://github.com/dodiPra49/Penjualan.git /var/www/penjualan
cd /var/www/penjualan

# Setup environment file (.env)
cp .env.example .env
nano .env   # Sesuaikan APP_ENV=production, APP_DEBUG=false, DB_*, REDIS_*, APP_URL

# Install dependensi awal & generate APP_KEY
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan storage:link

# Jalankan migrasi database
php artisan migrate --force

# Beri permission ke folder cache & storage untuk web server (www-data)
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Buat deploy script executable
chmod +x deploy.sh
```

---

### C. Konfigurasi Nginx
Salin template Nginx yang telah disiapkan:
```bash
sudo cp deployment/nginx.conf /etc/nginx/sites-available/penjualan.conf
sudo nano /etc/nginx/sites-available/penjualan.conf  # Ganti yourdomain.com dengan domain atau IP VPS Anda
sudo ln -s /etc/nginx/sites-available/penjualan.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 3. Konfigurasi SSH Key untuk GitHub Actions

Agar GitHub Actions dapat melakukan deployment tanpa password, buat SSH Key pair khusus untuk deployment.

### Langkah 1: Generate SSH Key di VPS atau di Lokal
Jalankan perintah ini di VPS (atau komputer lokal Anda):
```bash
ssh-keygen -t ed25519 -C "github-actions-deploy" -f ~/.ssh/github_deploy -N ""
```
Perintah di atas menghasilkan 2 file:
1. `~/.ssh/github_deploy` (Private Key - simpan rahasia)
2. `~/.ssh/github_deploy.pub` (Public Key)

### Langkah 2: Daftarkan Public Key di VPS
Tambahkan isi `github_deploy.pub` ke file `~/.ssh/authorized_keys` di VPS:
```bash
cat ~/.ssh/github_deploy.pub >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
chmod 700 ~/.ssh
```

### Langkah 3: Ambil Private Key
Tampilkan isi private key untuk disalin ke GitHub:
```bash
cat ~/.ssh/github_deploy
```
*(Salin seluruh isinya, mulai dari `-----BEGIN OPENSSH PRIVATE KEY-----` hingga `-----END OPENSSH PRIVATE KEY-----`)*.

---

## 4. Konfigurasi GitHub Repository Secrets

Buka repository Anda di GitHub:
1. Klik **Settings** > **Secrets and variables** > **Actions**.
2. Klik tombol **New repository secret**.
3. Tambahkan 5 secret berikut:

| Nama Secret | Wajib / Opsional | Deskripsi / Nilai Contoh |
|---|---|---|
| `SSH_HOST` | **Wajib** | IP Publik VPS Anda (contoh: `103.123.45.67`) atau domain server |
| `SSH_USER` | **Wajib** | Username SSH untuk login ke VPS (contoh: `ubuntu`, `deployer`, atau `root`) |
| `SSH_KEY` | **Pilihan A** | Isi **Private Key SSH** (diawali `-----BEGIN OPENSSH PRIVATE KEY-----` s/d `-----END OPENSSH PRIVATE KEY-----`) |
| `SSH_PASSWORD` | **Pilihan B** | Password akun SSH VPS (jika tidak menggunakan Private Key) |
| `SSH_PORT` | Opsional | Port SSH server (default: `22` jika tidak diisi) |
| `WORK_DIR` | Opsional | Path direktori proyek di VPS (default: `/var/www/penjualan`) |

> 💡 **Pilih salah satu metode autentikasi**: Gunakan `SSH_KEY` (Sangat Direkomendasikan demi keamanan) ATAU gunakan `SSH_PASSWORD`.

---

## 5. Konfigurasi Izin Reload PHP-FPM (Tanpa Password Sudo)

Agar GitHub Actions dapat mereload PHP-FPM secara otomatis setelah update tanpa terhenti prompt password `sudo`:

Jalankan di VPS:
```bash
sudo visudo -f /etc/sudoers.d/laravel-deploy
```
Tambahkan baris berikut (sesuaikan nama user Anda, misal `ubuntu`):
```text
ubuntu ALL=(ALL) NOPASSWD: /bin/systemctl reload php8.1-fpm, /usr/bin/systemctl reload php8.1-fpm
```
Simpan dan keluar.

---

## 6. Uji Coba Deployment

Setelah rahasia GitHub Secrets terpasang:
1. Lakukan perubahan kecil atau jalankan perintah commit & push:
   ```bash
   git add .
   git commit -m "feat: implementasi CI/CD GitHub Actions ke Linux VPS"
   git push origin main
   ```
2. Buka tab **Actions** di repository GitHub Anda:
   - Anda akan melihat workflow **Laravel CI/CD to Linux VPS** berjalan otomatis.
   - **Job 1 (CI - Test)** akan memvalidasi dependensi, build asset Vite, dan menjalankan test PHPUnit.
   - **Job 2 (CD - Deploy)** akan terpicu otomatis setelah Job 1 berhasil, menghubungkan ke VPS via SSH dan mengeksekusi `./deploy.sh`.
3. Buka URL web aplikasi Anda di browser untuk memastikan aplikasi telah terupdate secara otomatis!
