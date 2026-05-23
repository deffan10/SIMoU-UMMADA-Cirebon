#!/bin/bash
# =============================================================================
# SIMoU - Queue Worker Restart Script
# =============================================================================

echo "Restarting SIMoU queue workers..."

# Gracefully restart PM2 processes
pm2 restart simou-queue-default
pm2 restart simou-queue-notifications
pm2 restart simou-scheduler

echo "Queue workers restarted successfully!"
pm2 status
