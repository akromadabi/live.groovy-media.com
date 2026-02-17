#!/bin/bash

# ===========================================
# Git Pull Deploy Script
# Server: live.groovy-media.com
# Usage: bash deploy.sh
# ===========================================

cd ~/live.groovy-media.com || exit 1

echo "🔄 Pulling latest changes..."
git pull origin main

echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "🔧 Running migrations..."
php artisan migrate --force

echo "🗑️ Clearing caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "📁 Setting permissions..."
chmod -R 775 storage bootstrap/cache

echo ""
echo "✅ Deploy selesai!"
echo "🌐 Cek: https://live.groovy-media.com"
