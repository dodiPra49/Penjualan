#!/usr/bin/env bash

# ==============================================================================
# Script Khusus: Instalasi & Konfigurasi MariaDB di VPS Linux (Ubuntu/Debian)
# Jalankan dengan sudo: sudo bash install-mariadb.sh
# ==============================================================================

set -e

# 1. Validasi Hak Akses Root
if [ "$(id -u)" -ne 0 ]; then
    echo "❌ Error: Harap jalankan script ini dengan hak akses sudo / root!"
    echo "Contoh: sudo bash install-mariadb.sh"
    exit 1
fi

echo "=================================================="
echo "🐬 Memulai Instalasi MariaDB Server di VPS..."
echo "=================================================="

# 2. Update Paket Sistem & Pasang MariaDB
echo "📦 1. Mengunduh dan memasang MariaDB Server & Client..."
apt-get update -y
apt-get install -y mariadb-server mariadb-client

# 3. Aktifkan dan Jalankan Layanan MariaDB
echo "⚡ 2. Mengaktifkan dan menjalankan service MariaDB..."
systemctl enable mariadb
systemctl start mariadb

# Verifikasi status layanan
if systemctl is-active --quiet mariadb; then
    echo "✅ Layanan MariaDB berhasil berjalan (Active/Running)."
else
    echo "❌ Gagal menjalankan MariaDB. Silakan periksa 'systemctl status mariadb'."
    exit 1
fi

echo "=================================================="
echo "🗄️ Konfigurasi Database untuk Aplikasi Laravel Penjualan"
echo "=================================================="

# Variabel default database
DB_NAME="penjualan"
DB_USER="penjualan_user"

# Meminta input password database dari pengguna (atau default jika kosong)
read -p "Masukkan password untuk user database '$DB_USER' [Default: RahasiaPenjualan2026!]: " DB_PASS
DB_PASS=${DB_PASS:-RahasiaPenjualan2026!}

echo ""
echo "⚙️ Membuat Database: '$DB_NAME' dan User: '$DB_USER'..."

# Eksekusi SQL untuk membuat database dan user
mysql -e "
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
CREATE USER IF NOT EXISTS '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'127.0.0.1';
FLUSH PRIVILEGES;
"

echo "✅ Database dan User berhasil dibuat!"
echo ""
echo "=================================================="
echo "🎉 INSTALASI DAN KONFIGURASI MARIADB SELESAI"
echo "=================================================="
echo "Versi MariaDB:"
mariadb --version
echo ""
echo "📋 Silakan sesuaikan konfigurasi file .env Anda di VPS:"
echo "--------------------------------------------------"
echo "DB_CONNECTION=mysql"
echo "DB_HOST=127.0.0.1"
echo "DB_PORT=3306"
echo "DB_DATABASE=${DB_NAME}"
echo "DB_USERNAME=${DB_USER}"
echo "DB_PASSWORD=${DB_PASS}"
echo "--------------------------------------------------"
echo "=================================================="
