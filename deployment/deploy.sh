#!/bin/bash
# =============================================================================
# SIMoU UMMADA Cirebon - Deployment Script
# =============================================================================
# Usage: ./deploy.sh [fresh|update]
# =============================================================================

set -e

APP_DIR="/home/htdocs/simou"
BACKUP_DIR="/var/backups/simou"
DATE=$(date +%Y%m%d_%H%M%S)

echo "=========================================="
echo " SIMoU Deployment - $(date)"
echo "=========================================="

# Function: Deploy fresh
deploy_fresh() {
    echo "[1/8] Cloning repository..."
    git clone https://github.com/deffan10/SIMoU-UMMADA-Cirebon.git $APP_DIR

    echo "[2/8] Installing dependencies..."
    cd $APP_DIR
    composer install --no-dev --optimize-autoloader --no-interaction
    npm install && npm run build

    echo "[3/8] Setting up environment..."
    cp .env.example .env
    php artisan key:generate

    echo "[4/8] Setting permissions..."
    chown -R www-data:www-data $APP_DIR/storage $APP_DIR/bootstrap/cache
    chmod -R 755 $APP_DIR
    chmod -R 775 $APP_DIR/storage $APP_DIR/bootstrap/cache

    echo "[5/8] Running migrations..."
    php artisan migrate --force
    php artisan db:seed --force

    echo "[6/8] Optimizing..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan storage:link

    echo "[7/8] Setting up Nginx..."
    cp $APP_DIR/deployment/nginx.conf /etc/nginx/sites-available/simou.ummada.ac.id
    ln -sf /etc/nginx/sites-available/simou.ummada.ac.id /etc/nginx/sites-enabled/
    nginx -t

    echo "[8/8] Starting services..."
    pm2 start $APP_DIR/deployment/pm2.config.js
    pm2 save
    pm2 startup
    systemctl restart php8.4-fpm
    systemctl restart nginx

    echo "=========================================="
    echo " Fresh deployment completed!"
    echo "=========================================="
}

# Function: Update deployment
deploy_update() {
    echo "[1/7] Creating backup..."
    mkdir -p $BACKUP_DIR
    mysqldump -u simou_user -p simou_ummada > $BACKUP_DIR/db_backup_$DATE.sql
    echo "  Database backed up to: $BACKUP_DIR/db_backup_$DATE.sql"

    echo "[2/7] Pulling latest code..."
    cd $APP_DIR
    git pull origin main

    echo "[3/7] Installing dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction

    echo "[4/7] Running migrations..."
    php artisan migrate --force

    echo "[5/7] Clearing & rebuilding cache..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    echo "[6/7] Restarting queue workers..."
    pm2 restart all

    echo "[7/7] Restarting PHP-FPM..."
    systemctl restart php8.4-fpm

    echo "=========================================="
    echo " Update deployment completed!"
    echo "=========================================="
}

# Main
case "${1:-update}" in
    fresh)
        deploy_fresh
        ;;
    update)
        deploy_update
        ;;
    *)
        echo "Usage: $0 [fresh|update]"
        exit 1
        ;;
esac
