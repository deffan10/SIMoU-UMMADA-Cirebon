#!/bin/bash
# =============================================================================
# SIMoU UMMADA Cirebon - Database Setup Script
# =============================================================================
# Usage: sudo bash setup-database.sh
# =============================================================================

set -e

# ==============================
# KONFIGURASI - Sesuaikan jika perlu
# ==============================
DB_NAME="simou_ummada"
DB_USER="simou_user"
DB_PASS="your_secure_password"
DB_HOST="localhost"
DB_CHARSET="utf8mb4"
DB_COLLATION="utf8mb4_unicode_ci"

# Warna output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo ""
echo "=========================================="
echo " SIMoU - Database Setup"
echo "=========================================="
echo ""

# ==============================
# CEK MYSQL/MARIADB RUNNING
# ==============================
echo -e "${YELLOW}[1/5]${NC} Cek MySQL/MariaDB service..."

if systemctl is-active --quiet mysql 2>/dev/null; then
    echo -e "  ${GREEN}✓${NC} MySQL is running"
elif systemctl is-active --quiet mariadb 2>/dev/null; then
    echo -e "  ${GREEN}✓${NC} MariaDB is running"
else
    echo -e "  ${RED}✗${NC} MySQL/MariaDB tidak berjalan!"
    echo "  Jalankan: systemctl start mysql (atau mariadb)"
    exit 1
fi

# ==============================
# INPUT PASSWORD ROOT MYSQL
# ==============================
echo ""
echo -e "${YELLOW}[2/5]${NC} Masukkan password root MySQL:"
read -sp "  Root Password: " MYSQL_ROOT_PASS
echo ""

# Test koneksi
if ! mysql -u root -p"${MYSQL_ROOT_PASS}" -e "SELECT 1" &>/dev/null; then
    echo -e "  ${RED}✗${NC} Gagal konek ke MySQL! Password root salah."
    exit 1
fi
echo -e "  ${GREEN}✓${NC} Koneksi MySQL berhasil"

# ==============================
# BUAT DATABASE
# ==============================
echo ""
echo -e "${YELLOW}[3/5]${NC} Membuat database '${DB_NAME}'..."

mysql -u root -p"${MYSQL_ROOT_PASS}" <<EOF
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\`
    CHARACTER SET ${DB_CHARSET}
    COLLATE ${DB_COLLATION};
EOF

echo -e "  ${GREEN}✓${NC} Database '${DB_NAME}' berhasil dibuat"

# ==============================
# BUAT USER & GRANT PRIVILEGES
# ==============================
echo ""
echo -e "${YELLOW}[4/5]${NC} Membuat user '${DB_USER}' dan set privileges..."

mysql -u root -p"${MYSQL_ROOT_PASS}" <<EOF
-- Buat user jika belum ada
CREATE USER IF NOT EXISTS '${DB_USER}'@'${DB_HOST}' IDENTIFIED BY '${DB_PASS}';

-- Grant privileges
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'${DB_HOST}';

-- Flush
FLUSH PRIVILEGES;
EOF

echo -e "  ${GREEN}✓${NC} User '${DB_USER}' berhasil dibuat dengan akses ke '${DB_NAME}'"

# ==============================
# VERIFIKASI
# ==============================
echo ""
echo -e "${YELLOW}[5/5]${NC} Verifikasi koneksi dengan user baru..."

if mysql -u "${DB_USER}" -p"${DB_PASS}" -h "${DB_HOST}" -e "USE ${DB_NAME}; SELECT 1" &>/dev/null; then
    echo -e "  ${GREEN}✓${NC} User '${DB_USER}' berhasil konek ke database '${DB_NAME}'"
else
    echo -e "  ${RED}✗${NC} Gagal verifikasi! Cek ulang konfigurasi."
    exit 1
fi

# ==============================
# SUMMARY
# ==============================
echo ""
echo "=========================================="
echo -e " ${GREEN}✓ DATABASE SETUP SELESAI!${NC}"
echo "=========================================="
echo ""
echo " Informasi Database:"
echo " ─────────────────────────────────────────"
echo "  Host      : ${DB_HOST}"
echo "  Database  : ${DB_NAME}"
echo "  Username  : ${DB_USER}"
echo "  Password  : ${DB_PASS}"
echo "  Charset   : ${DB_CHARSET}"
echo "  Collation : ${DB_COLLATION}"
echo ""
echo " Konfigurasi .env:"
echo " ─────────────────────────────────────────"
echo "  DB_CONNECTION=mysql"
echo "  DB_HOST=127.0.0.1"
echo "  DB_PORT=3306"
echo "  DB_DATABASE=${DB_NAME}"
echo "  DB_USERNAME=${DB_USER}"
echo "  DB_PASSWORD=${DB_PASS}"
echo ""
echo " Langkah selanjutnya:"
echo " ─────────────────────────────────────────"
echo "  1. Edit .env sesuai info di atas"
echo "  2. Jalankan: /usr/bin/php8.4 artisan migrate --force"
echo "  3. Jalankan: /usr/bin/php8.4 artisan db:seed --force"
echo ""
