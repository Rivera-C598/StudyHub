#!/bin/bash
# StudyHub v3.1 Migration Script for Mac/Linux
# This script applies database changes for v3.1 improvements

echo "========================================"
echo "StudyHub v3.1 Database Migration"
echo "========================================"
echo ""

# Get database credentials
read -p "MySQL username [root]: " DB_USER
DB_USER=${DB_USER:-root}

read -sp "MySQL password: " DB_PASS
echo ""

DB_NAME="studyhub_db"

# Create backup
echo ""
echo "Creating backup..."
BACKUP_FILE="backup_v3.1_$(date +%Y%m%d_%H%M%S).sql"
mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_FILE"

if [ $? -ne 0 ]; then
    echo "ERROR: Backup failed!"
    exit 1
fi

echo "Backup created: $BACKUP_FILE"
echo ""

# Apply migration
echo "Applying migration..."
mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < migrate_v3.1.sql

if [ $? -ne 0 ]; then
    echo "ERROR: Migration failed!"
    echo "You can restore from backup: $BACKUP_FILE"
    exit 1
fi

echo ""
echo "========================================"
echo "Migration completed successfully!"
echo "========================================"
echo ""
echo "Changes applied:"
echo "- Added deadline column to resources"
echo "- Added performance indexes"
echo "- Created resource_tags table"
echo ""
echo "Backup saved as: $BACKUP_FILE"
echo ""
