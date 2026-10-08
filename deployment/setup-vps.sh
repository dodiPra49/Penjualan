#!/usr/bin/env bash

# ==============================================================================
# Script Otomasi Setup Awal VPS Ubuntu (20.04 / 22.04 / 24.04) untuk Laravel
# Jalankan dengan sudo / root: sudo bash setup-vps.sh
# ==============================================================================

set -e

if [ "$(id -u)" -ne 0 ]; then
    echo "❌ Harap jalankan script ini dengan hak akses root atau sudo!"
    exit 1
fi

echo "🚀 [1/6] Mengupdate paket sistem Ubuntu..."
apt-get update -y && apt-get upgrade -y
apt-get install -y curl git unzip zip software-properties-common ufw supervisor nginx redis-server

echo "🐘 [2/6] Memasang PHP 8.1 dan ekstensi yang dibutuhkan..."
add-apt-repository -y ppa:ondrej/php
apt-get update -y
apt-get install -y php8.1 php8.1-cli php8.1-fpm php8.1-common php8.1-mysql php8.1-zip \
    php8.1-gd php8.1-mbstring php8.1-curl php8.1-xml php8.1-bcmath php8.1-redis php8.1-intl

echo "📦 [3/6] Memasang Composer..."
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

echo "🟢 [4/6] Memasang Node.js LTS & NPM..."
if ! command -v node &> /dev/null; then
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
    apt-get install -y nodejs
fi

echo "📁 [5/6] Menyiapkan direktori /var/www/penjualan..."
mkdir -p /var/www/penjualan
chown -R www-data:www-data /var/www/penjualan
chmod -R 775 /var/www/penjualan

echo "⚙️ [6/6] Menyesuaikan konfigurasi sudoers untuk reload PHP-FPM tanpa password..."
# Mengizinkan www-data atau user deployer reload php-fpm tanpa prompt password
SUDOERS_FILE="/etc/sudoers.d/laravel-deploy"
echo "%sudo ALL=(ALL) NOPASSWD: /bin/systemctl reload php8.1-fpm, /bin/systemctl reload php8.2-fpm, /usr/bin/systemctl reload php8.1-fpm, /usr/bin/systemctl reload php8.2-fpm" > $SUDOERS_FILE
chmod 0440 $SUDOERS_FILE

echo "=================================================="
echo "✅ Setup Awal VPS Selesai!"
echo "Versi terpasang:"
php -v | head -n 1
composer --version
node -v
npm -v
echo "=================================================="
