#!/bin/bash
# Script Deployment untuk VPS - BMW ULTIMA MONITORING
# Jalankan: bash deploy-vps.sh

echo "=========================================="
echo "DEPLOYMENT SCRIPT - BMW ULTIMA"
echo "=========================================="
echo ""

# Step 1: Masuk ke direktori aplikasi
echo "[1/7] Masuk ke direktori aplikasi..."
cd /var/www/ultima || { echo "Error: Folder /var/www/ultima tidak ditemukan!"; exit 1; }

# Step 2: Setup git safe directory
echo "[2/7] Setup git..."
git config --global --add safe.directory /var/www/ultima

# Step 3: Pull code terbaru
echo "[3/7] Pull code terbaru..."
git fetch --all
git reset --hard origin/main
git clean -fd

# Step 4: Verifikasi file penting
echo "[4/7] Verifikasi file penting..."
if ! grep -q "ManagementUserController" routes/web.php; then
    echo "ERROR: routes/web.php tidak terupdate!"
    echo "Manual fix needed!"
    exit 1
fi
echo "✓ routes/web.php sudah benar"

# Step 5: Install dependencies & rebuild
echo "[5/7] Install dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
npm run build

# Step 6: Clear all caches
echo "[6/7] Clear caches..."
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan optimize:clear

# Clear cached views manually
rm -rf storage/framework/views/*

# Rebuild views
php artisan view:cache

# Step 7: Verify routes
echo "[7/7] Verify routes..."
php artisan route:list --name=management.users

echo ""
echo "=========================================="
echo "DEPLOYMENT SELESAI!"
echo "Silakan refresh browser (Ctrl+F5)"
echo "=========================================="