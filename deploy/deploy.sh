#!/bin/bash

# ==============================================================================
# Script Auto Deploy Laravel - Tiktok Live Manager
# Domain : live.groovy-media.com
# Cara Pakai di Terminal Server:
# bash /www/wwwroot/live.groovy-media.com/deploy.sh
# ==============================================================================

# Menghentikan script jika terjadi error fatal
set -e

# Otomatis pindah ke direktori root proyek
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$SCRIPT_DIR"

echo "=================================================="
echo "🚀 Memulai Deployment di: $SCRIPT_DIR"
echo "⏰ Waktu: $(date '+%Y-%m-%d %H:%M:%S')"
echo "=================================================="

# 1. Tarik pembaruan terbaru dari GitHub
echo "🔄 [1/6] Mengambil update dari GitHub (git pull)..."
git pull origin main

# 2. Update dependensi PHP jika ada
echo "📦 [2/6] Memperbarui dependensi Composer..."
composer install --no-dev --optimize-autoloader --no-interaction

# 3. Jalankan migrasi database
echo "🗄️ [3/6] Menjalankan migrasi database..."
php artisan migrate --force

# 4. Bersihkan cache lama
echo "🧹 [4/6] Membersihkan cache lama..."
php artisan optimize:clear

# 5. Bangun cache baru untuk performa maksimal
echo "⚡ [5/6] Membangun cache baru (config, route, view)..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Atur izin akses folder storage dan cache
echo "🔒 [6/6] Menyesuaikan permissions storage & cache..."
chmod -R 775 storage bootstrap/cache

# Jika berjalan di Linux Nginx / aaPanel, sesuaikan kepemilikan user www
if id "www" >/dev/null 2>&1; then
    chown -R www:www storage bootstrap/cache 2>/dev/null || true
fi

echo "=================================================="
echo "✅ DEPLOYMENT BERHASIL SELESAI!"
echo "🌐 Aplikasi siap diakses: https://live.groovy-media.com"
echo "=================================================="
