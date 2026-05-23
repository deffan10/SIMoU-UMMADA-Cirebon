# SIMoU - Sistem Informasi MoU UMMADA Cirebon

Repository MoU/Kerjasama Kampus berbasis Web untuk Universitas Muhammadiyah Ahmad Dahlan Cirebon.

## Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Backend | Laravel 12 |
| Frontend | Blade + TailwindCSS + Alpine.js |
| Database | MySQL / MariaDB |
| Auth | Laravel Breeze (Custom Admin Guard) |
| Queue | Laravel Queue (Database Driver) |
| Scheduler | Laravel Scheduler + PM2 |
| PDF Preview | PDF.js / iframe |
| Charts | Chart.js |
| Icons | Font Awesome 6 |
| Process Manager | PM2 |
| Web Server | Nginx |

## Fitur Utama

### Public Portal (Tanpa Login)
- Landing page dengan statistik kerjasama
- Daftar & pencarian kerjasama publik
- Detail kerjasama dengan PDF viewer
- Halaman statistik dengan visualisasi data
- Logo partner institusi
- Filter kategori, institusi, tahun, level

### Admin Panel (Login Required)
- Dashboard dengan statistik & reminder expire (H-90/H-30/H-7)
- CRUD MoU lengkap (tambah/edit/hapus/restore)
- Sistem renewal/perpanjangan dengan histori versi
- Manajemen institusi partner
- Manajemen kategori & fakultas
- Upload & manajemen file (PDF/DOC)
- Import data dari Excel
- Activity log & audit trail
- Notifikasi expire otomatis
- Pengaturan profil & password

## Struktur Database

```
admins              - Data admin pengelola
institutions        - Institusi/lembaga partner
categories          - Kategori kerjasama
faculties           - Fakultas
study_programs      - Program studi
mous                - Data MoU utama
mou_renewals        - Histori perpanjangan
attachments         - File lampiran
notifications       - Notifikasi sistem
activity_logs       - Audit trail
import_logs         - Log import data
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

# Build assets
npm run build

# Run development server
php artisan serve
```

## Login Default

```
Email: admin@ummada.ac.id
Password: password123
```

## Deployment Production

Lihat file `deployment/` untuk konfigurasi:
- `deployment/nginx.conf` - Konfigurasi Nginx
- `deployment/pm2.config.js` - PM2 queue workers
- `deployment/deploy.sh` - Script deployment
- `deployment/backup.sh` - Script backup database

```bash
# Fresh deployment
sudo ./deployment/deploy.sh fresh

# Update deployment
sudo ./deployment/deploy.sh update
```

## API Endpoints

### Public API (No Auth)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/mous` | List MoU publik |
| GET | `/api/v1/mous/{id}` | Detail MoU |
| GET | `/api/v1/institutions` | List institusi |
| GET | `/api/v1/categories` | List kategori |
| GET | `/api/v1/statistics` | Statistik |
| GET | `/api/v1/statistics/yearly` | Statistik per tahun |
| GET | `/api/v1/mous/{id}/renewals` | Histori renewal |

### Admin API (Auth Required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/dashboard/summary` | Dashboard summary |
| GET | `/api/v1/dashboard/chart-data` | Data chart |

## Environment Server

- OS: Debian 13
- Web Server: Nginx
- PHP: 8.4+ (PHP-FPM)
- Database: MySQL 8.0+ / MariaDB 10.6+
- Process Manager: PM2
- Node.js: 20+

## Security

- CSRF Protection (Laravel default)
- XSS Protection (Blade auto-escaping)
- SQL Injection Protection (Eloquent ORM)
- Rate Limit Login (5 attempts/minute)
- Secure file upload validation
- Password hashing (bcrypt)
- Audit trail semua aktivitas
- File access control
- Security headers via Nginx

## Scheduler

Jalankan status update otomatis setiap hari jam 06:00:
```bash
# Via PM2 (recommended)
pm2 start deployment/pm2.config.js

# Via crontab (alternative)
* * * * * cd /var/www/simou && php artisan schedule:run >> /dev/null 2>&1
```

## License

Proprietary - UMMADA Cirebon
