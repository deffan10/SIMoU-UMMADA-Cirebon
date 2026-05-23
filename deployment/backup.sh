#!/bin/bash
# =============================================================================
# SIMoU UMMADA Cirebon - Database & Files Backup Script
# =============================================================================
# Add to crontab: 0 2 * * * /var/www/simou/deployment/backup.sh
# =============================================================================

set -e

APP_DIR="/home/htdocs/simou"
BACKUP_DIR="/var/backups/simou"
DATE=$(date +%Y%m%d_%H%M%S)
RETENTION_DAYS=30

# Database credentials (from .env)
DB_USER="simou_user"
DB_PASS="your_secure_password"
DB_NAME="simou_ummada"

echo "[$(date)] Starting backup..."

# Create backup directory
mkdir -p $BACKUP_DIR/database
mkdir -p $BACKUP_DIR/storage

# Database backup
echo "[$(date)] Backing up database..."
mysqldump -u $DB_USER -p"$DB_PASS" $DB_NAME | gzip > $BACKUP_DIR/database/db_${DATE}.sql.gz
echo "  Database: $BACKUP_DIR/database/db_${DATE}.sql.gz"

# Storage files backup
echo "[$(date)] Backing up uploaded files..."
tar -czf $BACKUP_DIR/storage/files_${DATE}.tar.gz -C $APP_DIR/storage/app/public .
echo "  Files: $BACKUP_DIR/storage/files_${DATE}.tar.gz"

# Cleanup old backups
echo "[$(date)] Cleaning up old backups (older than ${RETENTION_DAYS} days)..."
find $BACKUP_DIR -name "*.gz" -mtime +$RETENTION_DAYS -delete

echo "[$(date)] Backup completed successfully!"
echo "  Database size: $(du -h $BACKUP_DIR/database/db_${DATE}.sql.gz | cut -f1)"
echo "  Storage size: $(du -h $BACKUP_DIR/storage/files_${DATE}.tar.gz | cut -f1)"
