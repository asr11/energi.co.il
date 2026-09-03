#!/bin/bash
# ==========================================================
# HUB WP Migrator - One-Click Deployment Script
# Target: Hostinger / Cloudways / Any Modern PHP 8.2+ Server
# Site: energi.co.il
# ==========================================================

set -e

# Default Target Credentials (Override via env or prompt)
TARGET_SSH_USER="${1:-$TARGET_USER}"
TARGET_SSH_HOST="${2:-$TARGET_HOST}"
TARGET_SITE_PATH="${3:-$TARGET_PATH}"
TARGET_DOMAIN="${4:-energi.co.il}"

if [ -z "$TARGET_SSH_USER" ] || [ -z "$TARGET_SSH_HOST" ] || [ -z "$TARGET_SITE_PATH" ]; then
    echo "=========================================================="
    echo "🚀 HUB One-Click Deployer - energi.co.il"
    echo "=========================================================="
    echo "Usage: ./deploy.sh <SSH_USER> <SSH_HOST> <SITE_PATH> [TARGET_DOMAIN]"
    echo "Example: ./deploy.sh energi 65.108.89.58 /var/www/energi.co.il energi.co.il"
    echo "=========================================================="
    exit 1
fi

echo "🚀 [1/4] Syncing clean wp-content to $TARGET_SSH_HOST:$TARGET_SITE_PATH..."
rsync -avz --delete -e "ssh -o StrictHostKeyChecking=no" ./wp-content/ ${TARGET_SSH_USER}@${TARGET_SSH_HOST}:${TARGET_SITE_PATH}/wp-content/

echo "🗄️ [2/4] Uploading cleaned DB dump..."
rsync -avz -e "ssh -o StrictHostKeyChecking=no" ./backup/cleaned_db.sql ${TARGET_SSH_USER}@${TARGET_SSH_HOST}:${TARGET_SITE_PATH}/cleaned_db.sql

echo "⚡ [3/4] Importing cleaned database on target server..."
ssh -o StrictHostKeyChecking=no ${TARGET_SSH_USER}@${TARGET_SSH_HOST} "cd ${TARGET_SITE_PATH} && wp db import cleaned_db.sql && rm cleaned_db.sql"

echo "🔄 [4/4] Updating URLs to https://${TARGET_DOMAIN} & flushing cache..."
ssh -o StrictHostKeyChecking=no ${TARGET_SSH_USER}@${TARGET_SSH_HOST} "cd ${TARGET_SITE_PATH} && wp search-replace 'http://localhost:8085' 'https://${TARGET_DOMAIN}' --all-tables && wp cache flush 2>/dev/null || true"

echo "✅ Deployment completed successfully for ${TARGET_DOMAIN}!"
