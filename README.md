# SIMoU - Sistem Informasi MoU UMMADA Cirebon

Repository MoU/Kerjasama Kampus berbasis Web untuk Universitas Muhammadiyah Ahmad Dahlan Cirebon.

## Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Backend | Laravel 12 (PHP 8.4) |
| Frontend | Blade + TailwindCSS (compiled) + Alpine.js |
| Database | MySQL / MariaDB |
| Auth | Custom Admin Guard (session-based) |
| Queue | Laravel Queue (Database/Redis) |
| Scheduler | Laravel Scheduler + PM2 |
| PDF Preview | iframe / PDF.js |
| Charts | Chart.js |
| Icons | Font Awesome 6 |
| Excel Import | PhpSpreadsheet |
| Process Manager | PM2 |
| Web Server | Nginx |
| SSL | Cloudflare Origin Certificate |

## Fitur Utama

### Public Portal (Tanpa Login)
- Landing page dengan statistik kerjasama & grafik per tahun
- Daftar & pencarian kerjasama publik (filter kategori, institusi, tahun, level)
- Detail kerjasama dengan PDF viewer
- Halaman statistik dengan visualisasi Chart.js
- Halaman Tentang (editable dari admin)
- Logo partner institusi
- Responsive mobile

### Admin Panel (Login Required)
- Dashboard dengan statistik & reminder expire (H-90/H-30/H-7)
- CRUD MoU lengkap (tambah/edit/hapus/soft-delete/restore)
- Sistem renewal/perpanjangan dengan histori versi (versioning)
- Manajemen institusi partner (autocomplete search)
- Manajemen kategori & fakultas
- Upload & manajemen file (PDF/DOC/DOCX, drag & drop)
- Import data dari Excel (.xlsx) dengan preview & validasi
- Template import dinamis (include referensi kategori/fakultas/institusi dari DB)
- Activity log & audit trail
- Notifikasi expire otomatis (scheduler)
- Pengaturan:
  - Upload logo website (otomatis generate favicon 32x32)
  - Edit halaman Tentang (HTML editor)
  - Update profil & foto admin
  - Ganti password (with session invalidation)

## Struktur Database

```
admins              - Data admin pengelola
institutions        - Institusi/lembaga partner
categories          - Kategori kerjasama
faculties           - Fakultas
study_programs      - Program studi
mous                - Data MoU utama
mou_renewals        - Histori perpanjangan (versioning)
attachments         - File lampiran
notifications       - Notifikasi sistem (expire reminder)
activity_logs       - Audit trail
import_logs         - Log import data
site_settings       - Pengaturan website (logo, favicon, about page)
```

## Instalasi Development

```bash
# Clone repository
git clone https://github.com/deffan10/SIMoU-UMMADA-Cirebon.git
cd SIMoU-UMMADA-Cirebon

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Edit .env sesuai database lokal
# DB_CONNECTION=mysql
# DB_DATABASE=simou_ummada
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations & seeder
php artisan migrate
php artisan db:seed

# Create storage link
php artisan storage:link

# Build assets (TailwindCSS + Alpine.js)
npm run build

# Run development server
php artisan serve
```

## Instalasi Production (Debian 13 + Nginx)

```bash
# Clone ke server
cd /home/htdocs
git clone https://github.com/deffan10/SIMoU-UMMADA-Cirebon.git mou
cd mou

# Install dengan PHP 8.4
/usr/bin/php8.4 /usr/local/bin/composer install --no-dev --optimize-autoloader
npm install && npm run build

# Setup
cp .env.example .env
/usr/bin/php8.4 artisan key:generate
# Edit .env (database, redis, APP_URL, dll)

# Database
/usr/bin/php8.4 artisan migrate --force
/usr/bin/php8.4 artisan db:seed --force
/usr/bin/php8.4 artisan storage:link

# Permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Optimize
/usr/bin/php8.4 artisan config:cache
/usr/bin/php8.4 artisan route:cache
/usr/bin/php8.4 artisan view:cache

# Nginx
cp deployment/nginx.conf /etc/nginx/sites-available/mou.ummada.ac.id
ln -s /etc/nginx/sites-available/mou.ummada.ac.id /etc/nginx/sites-enabled/
nginx -t && systemctl restart nginx

# Queue Workers (PM2)
pm2 start deployment/pm2.config.js
pm2 save && pm2 startup
```

## Login Default

```
Email: admin@ummada.ac.id
Password: password123
```

## Update Deployment

```bash
cd /home/htdocs/mou
git pull origin main
/usr/bin/php8.4 /usr/local/bin/composer install --no-dev --optimize-autoloader
npm run build
/usr/bin/php8.4 artisan migrate --force
/usr/bin/php8.4 artisan config:cache
/usr/bin/php8.4 artisan route:cache
/usr/bin/php8.4 artisan view:cache
pm2 restart all
```

## Deployment Scripts

| File | Fungsi |
|------|--------|
| `deployment/nginx.conf` | Konfigurasi Nginx (SSL Cloudflare, PHP-FPM 8.4) |
| `deployment/pm2.config.js` | PM2 queue workers + scheduler |
| `deployment/deploy.sh` | Script deployment (fresh/update) |
| `deployment/backup.sh` | Backup database + files (cron) |
| `deployment/setup-database.sh` | Script buat database + user MySQL |

## API Endpoints

### Public API (No Auth)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/mous` | List MoU publik (search, filter, paginate) |
| GET | `/api/v1/mous/{id}` | Detail MoU |
| GET | `/api/v1/institutions` | List institusi partner |
| GET | `/api/v1/categories` | List kategori |
| GET | `/api/v1/statistics` | Statistik ringkasan |
| GET | `/api/v1/statistics/yearly` | Statistik per tahun |
| GET | `/api/v1/mous/{id}/renewals` | Histori renewal MoU |

### Admin API (Auth Required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/dashboard/summary` | Dashboard summary |
| GET | `/api/v1/dashboard/chart-data` | Data chart (yearly, category, status) |

## Environment Server

- OS: Debian 13
- Web Server: Nginx + Cloudflare SSL
- PHP: 8.4 (PHP-FPM)
- Database: MySQL 8.0+ / MariaDB 10.6+
- Cache/Session: Redis (opsional) atau Database
- Process Manager: PM2
- Node.js: 20+

## Security

- CSRF Protection (Laravel)
- XSS Protection (Blade auto-escaping + strip_tags whitelist)
- SQL Injection Protection (Eloquent ORM + prepared statements)
- Rate Limit Login (5 attempts/minute)
- Secure file upload validation (mime, size, extension)
- Password hashing (bcrypt)
- Session invalidation on password change
- Audit trail semua aktivitas admin
- File access control (visibility system)
- Security headers via Nginx (HSTS, CSP, X-Frame, etc.)
- Soft delete (data tidak hilang permanen)

## Scheduler (Auto Status Update)

Otomatis update status MoU & generate notifikasi expire setiap hari jam 06:00:

```bash
# Via PM2 (recommended - sudah include di pm2.config.js)
pm2 start deployment/pm2.config.js

# Via crontab (alternative)
* * * * * cd /home/htdocs/mou && /usr/bin/php8.4 artisan schedule:run >> /dev/null 2>&1
```

## Import Excel

- Download template `.xlsx` dari admin panel (sudah include referensi data dari DB)
- Template memiliki 5 sheet: Template, Petunjuk, Daftar Kategori, Daftar Fakultas, Daftar Institusi
- Kolom tanggal sudah di-set sebagai TEXT (tidak auto-format Excel)
- Preview sebelum import
- Validasi duplikat nomor MoU
- Auto-create institusi/kategori/fakultas baru
- Summary hasil import (berhasil/gagal/duplikat)

## License

Proprietary - UMMADA Cirebon
