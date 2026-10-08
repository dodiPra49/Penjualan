#!/usr/bin/env bash

# ==============================================================================
# Script Otomasi Deployment Laravel ke Linux VPS
# ==============================================================================

set -e # Berhenti jika ada error kritis

export PATH="/usr/local/bin:/usr/bin:/bin:/usr/local/sbin:/usr/sbin:/sbin:$HOME/.composer/vendor/bin:$PATH"

echo "=================================================="
echo "🕒 [$(date '+%Y-%m-%d %H:%M:%S')] Memulai proses deployment..."
echo "👤 User aktif: $(whoami) | Direktori: $(pwd)"
echo "=================================================="

# 0. Pastikan Git safe.directory aktif
git config --global --add safe.directory "$(pwd)" 2>/dev/null || true

# 1. Aktifkan Maintenance Mode (dengan pesan ramah)
echo "📦 1. Mengaktifkan maintenance mode..."
php artisan down --render="errors::503" 2>/dev/null || true

# 2. Ambil update terbaru dari branch main
echo "📥 2. Menarik perubahan terbaru dari repository Git..."
git fetch origin main
git reset --hard origin/main

# 3. Pastikan file .env tersedia
if [ ! -f ".env" ]; then
    echo "⚠️ File .env belum ada! Membuat dari .env.example..."
    cp .env.example .env
    php artisan key:generate || true
fi

# 4. Install dependency Composer untuk produksi (tanpa dev package)
echo "🐘 4. Memasang dependensi Composer (Production)..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 5. Build asset frontend (Vite) jika NPM terinstall
if command -v npm &> /dev/null; then
    echo "⚡ 5. Membangun asset frontend dengan Vite..."
    if [ -f "package-lock.json" ]; then
        npm ci --silent 2>/dev/null || npm install --silent 2>/dev/null || npm run build --silent 2>/dev/null || true
    else
        npm install --silent 2>/dev/null || true
    fi
    npm run build --silent 2>/dev/null || true
else
    echo "ℹ️ Node/NPM tidak terdeteksi di server, melewati proses build asset."
fi

# 6. Jalankan migrasi database
echo "🗄️ 6. Menjalankan migrasi database..."
if php artisan migrate --force; then
    echo "✅ Migrasi database berhasil."
else
    echo "⚠️ Peringatan: Migrasi database belum berhasil dijalankan."
    echo "   (Pastikan konfigurasi DB_* di file .env server sudah sesuai dengan MySQL VPS Anda)"
fi

# 7. Bersihkan cache lama & lakukan optimasi cache
echo "⚡ 7. Mengoptimasi cache Laravel..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 8. Pastikan storage symlink sudah terpasang
echo "🔗 8. Memverifikasi storage link..."
php artisan storage:link 2>/dev/null || true

# 9. Set permission folder storage & bootstrap/cache (aman untuk www-data)
echo "🔒 9. Memperbarui permission direktori..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
if [ "$(id -u)" -eq 0 ]; then
    chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
fi

# 10. Restart Worker Queue & Reload PHP-FPM
echo "🔄 10. Memperbarui worker queue & reload PHP-FPM..."
php artisan queue:restart 2>/dev/null || true

# Reload PHP-FPM jika sudo diizinkan tanpa password
if command -v sudo &> /dev/null; then
    sudo systemctl reload php8.1-fpm 2>/dev/null || sudo systemctl reload php8.2-fpm 2>/dev/null || sudo systemctl reload php8.3-fpm 2>/dev/null || true
fi

# 11. Matikan Maintenance Mode
echo "🚀 11. Menonaktifkan maintenance mode..."
php artisan up 2>/dev/null || true

echo "=================================================="
echo "✅ [$(date '+%Y-%m-%d %H:%M:%S')] Deployment berhasil diselesaikan!"
echo "=================================================="
