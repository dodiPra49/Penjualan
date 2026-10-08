#!/usr/bin/env bash

# ==============================================================================
# Script Otomasi Deployment Laravel ke Linux VPS
# ==============================================================================

set -e # Berhenti jika ada error

echo "=================================================="
echo "🕒 [$(date '+%Y-%m-%d %H:%M:%S')] Memulai proses deployment..."
echo "=================================================="

# 1. Aktifkan Maintenance Mode (dengan pesan ramah)
echo "📦 1. Mengaktifkan maintenance mode..."
php artisan down --render="errors::503" || true

# 2. Ambil update terbaru dari branch main
echo "📥 2. Menarik perubahan terbaru dari repository Git..."
git fetch origin main
git reset --hard origin/main

# 3. Install dependency Composer untuk produksi (tanpa dev package)
echo "🐘 3. Memasang dependensi Composer (Production)..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 4. Build asset frontend (Vite) jika NPM terinstall
if command -v npm &> /dev/null; then
    echo "⚡ 4. Membangun asset frontend dengan Vite..."
    npm ci --silent
    npm run build --silent
else
    echo "ℹ️ Node/NPM tidak terdeteksi di server, melewati proses build asset."
fi

# 5. Jalankan migrasi database
echo "🗄️ 5. Menjalankan migrasi database..."
php artisan migrate --force

# 6. Bersihkan cache lama & lakukan optimasi cache
echo "⚡ 6. Mengoptimasi cache Laravel..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 7. Pastikan storage symlink sudah terpasang
echo "🔗 7. Memverifikasi storage link..."
php artisan storage:link || true

# 8. Set permission folder storage & bootstrap/cache (aman untuk www-data)
echo "🔒 8. Memperbarui permission direktori..."
chmod -R 775 storage bootstrap/cache || true
if [ "$(id -u)" -eq 0 ]; then
    chown -R www-data:www-data storage bootstrap/cache || true
fi

# 9. Restart Worker Queue & Reload PHP-FPM
echo "🔄 9. Memperbarui worker queue & reload PHP-FPM..."
php artisan queue:restart || true

# Reload PHP-FPM jika sudo diizinkan tanpa password
if command -v sudo &> /dev/null; then
    sudo systemctl reload php8.1-fpm 2>/dev/null || sudo systemctl reload php8.2-fpm 2>/dev/null || sudo systemctl reload php8.3-fpm 2>/dev/null || true
fi

# 10. Matikan Maintenance Mode
echo "🚀 10. Menonaktifkan maintenance mode..."
php artisan up

echo "=================================================="
echo "✅ [$(date '+%Y-%m-%d %H:%M:%S')] Deployment berhasil diselesaikan!"
echo "=================================================="
