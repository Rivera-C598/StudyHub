#!/bin/bash
# StudyHub Database Migration Script for Mac/Linux
# This script creates the database and tables

echo "========================================"
echo "StudyHub Database Migration (Mac/Linux)"
echo "========================================"
echo ""

# Check if MySQL is accessible
if ! command -v mysql &> /dev/null; then
    echo "ERROR: MySQL command not found!"
    echo "Please ensure MySQL is installed and added to PATH."
    echo ""
    echo "Install MySQL:"
    echo "  Mac: brew install mysql"
    echo "  Linux: sudo apt-get install mysql-server"
    echo ""
    exit 1
fi

echo "MySQL found. Starting migration..."
echo ""

# Prompt for MySQL credentials
read -p "Enter MySQL username (default: root): " MYSQL_USER
MYSQL_USER=${MYSQL_USER:-root}

echo ""
read -sp "Enter MySQL password (press Enter if no password): " MYSQL_PASS
echo ""
echo ""

echo "Running migration..."
echo ""

# Run the migration
if [ -z "$MYSQL_PASS" ]; then
    mysql -u "$MYSQL_USER" < schema.sql
else
    mysql -u "$MYSQL_USER" -p"$MYSQL_PASS" < schema.sql
fi

if [ $? -eq 0 ]; then
    echo ""
    echo "========================================"
    echo "Migration completed successfully!"
    echo "========================================"
    echo ""
    echo "Database 'studyhub_db' has been created."
    echo "Tables 'users' and 'resources' are ready."
    echo ""
else
    echo ""
    echo "========================================"
    echo "Migration failed!"
    echo "========================================"
    echo "Please check the error messages above."
    echo ""
    exit 1
fi
