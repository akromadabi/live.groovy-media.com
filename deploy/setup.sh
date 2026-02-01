#!/bin/bash

# ===========================================
# Deployment Script untuk TikTok Live Manager
# Server: live.groovy-media.com
# ===========================================

echo "=========================================="
echo "  TikTok Live Manager - Deployment Setup"
echo "=========================================="

# Path configuration
APP_PATH="/home/$(whoami)/tiktok-live-manager"
PUBLIC_PATH="/home/$(whoami)/public_html/live"

echo ""
echo "[1/6] Setting permissions..."
chmod -R 775 $APP_PATH/storage
chmod -R 775 $APP_PATH/bootstrap/cache

echo ""
echo "[2/6] Generating application key..."
cd $APP_PATH
php artisan key:generate --force

echo ""
echo "[3/6] Running database migrations..."
php artisan migrate --force

echo ""
echo "[4/6] Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "[5/6] Creating storage symlink..."
# Custom symlink karena public folder terpisah
if [ ! -L "$PUBLIC_PATH/storage" ]; then
    ln -s $APP_PATH/storage/app/public $PUBLIC_PATH/storage
    echo "Storage symlink created"
else
    echo "Storage symlink already exists"
fi

echo ""
echo "[6/6] Setting final permissions..."
find $APP_PATH/storage -type d -exec chmod 775 {} \;
find $APP_PATH/storage -type f -exec chmod 664 {} \;

echo ""
echo "=========================================="
echo "  Deployment Complete!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Update .env with your database credentials"
echo "2. Visit https://live.groovy-media.com to test"
echo "3. Setup cron job for queue processing"
echo ""
