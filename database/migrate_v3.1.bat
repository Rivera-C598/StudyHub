@echo off
REM StudyHub v3.1 Migration Script for Windows
REM This script applies database changes for v3.1 improvements

echo ========================================
echo StudyHub v3.1 Database Migration
echo ========================================
echo.

REM Backup first
echo Creating backup...
set BACKUP_FILE=backup_v3.1_%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%.sql
set BACKUP_FILE=%BACKUP_FILE: =0%
mysqldump -u root studyhub_db > %BACKUP_FILE%
if %errorlevel% neq 0 (
    echo ERROR: Backup failed!
    pause
    exit /b 1
)
echo Backup created: %BACKUP_FILE%
echo.

REM Apply migration
echo Applying migration...
mysql -u root studyhub_db < migrate_v3.1.sql
if %errorlevel% neq 0 (
    echo ERROR: Migration failed!
    echo You can restore from backup: %BACKUP_FILE%
    pause
    exit /b 1
)

echo.
echo ========================================
echo Migration completed successfully!
echo ========================================
echo.
echo Changes applied:
echo - Added deadline column to resources
echo - Added performance indexes
echo - Created resource_tags table
echo.
echo Backup saved as: %BACKUP_FILE%
echo.
pause
