# Panduan Deployment SIMoU

## Prerequisites

Pastikan server sudah terinstall:
- Debian 13
- Nginx
- PHP 8.4 + extensions (mbstring, mysql, xml, curl, zip, gd)
- MySQL 8.0+ / MariaDB 10.6+
- Composer 2.x
- Node.js 20+ & npm
- PM2
- Git

## Setup Server

### 1. Install PHP Extensions

```bash
apt install php8.4-fpm php8.4-mysql php8.4-xml php8.4-curl \
    php8.4-zip php8.4-gd php8.4-mbstring php8.4-intl php8.4-bcmath
```

### 2. Install PM2

```bash
npm install -g pm2
```

### 3. Setup MySQL

```bash
mysql -u root -p
CREATE DATABASE simou_ummada CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'simou_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON simou_ummada.* TO 'simou_user'@'localhost';
FLUSH PRIVILEGES;
```

### 4. Deploy Aplikasi

```bash
# Clone
cd /home/htdocs
git clone https://github.com/deffan10/SIMoU-UMMADA-Cirebon.git simou
cd simou

# Install
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Environment
cp .env.example .env
php artisan key:generate
# Edit .env sesuai konfigurasi server

# Database
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link

# Permissions
chown -R www-data:www-data /home/htdocs/simou/storage /home/htdocs/simou/bootstrap/cache
chmod -R 755 /home/htdocs/simou
chmod -R 775 /home/htdocs/simou/storage /home/htdocs/simou/bootstrap/cache

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Konfigurasi Nginx

```bash
cp /home/htdocs/simou/deployment/nginx.conf /etc/nginx/sites-available/simou.ummada.ac.id
ln -s /etc/nginx/sites-available/simou.ummada.ac.id /etc/nginx/sites-enabled/
nginx -t
systemctl restart nginx
```

### 6. SSL Certificate

```bash
# Using Certbot (Let's Encrypt)
apt install certbot python3-certbot-nginx
certbot --nginx -d simou.ummada.ac.id
```

### 7. Setup Queue Workers (PM2)

```bash
pm2 start /home/htdocs/simou/deployment/pm2.config.js
pm2 save
pm2 startup
```

### 8. Setup Backup Cron

```bash
chmod +x /home/htdocs/simou/deployment/backup.sh
crontab -e
# Add: 0 2 * * * /home/htdocs/simou/deployment/backup.sh
```

## Updating

```bash
cd /home/htdocs/simou
./deployment/deploy.sh update
```

## Monitoring

```bash
# Check PM2 status
pm2 status
pm2 logs

# Check Nginx
systemctl status nginx
tail -f /var/log/nginx/simou-error.log

# Check PHP-FPM
systemctl status php8.4-fpm

# Check Laravel logs
tail -f /var/www/simou/storage/logs/laravel.log
```

## Troubleshooting

### 500 Error
```bash
# Check permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Check logs
tail -50 storage/logs/laravel.log
```

### Queue Not Processing
```bash
pm2 restart simou-queue-default
pm2 logs simou-queue-default
```

### Cache Issues
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```
