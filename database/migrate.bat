@echo off
REM StudyHub Database Migration Script for Windows
REM This script creates the database and tables

echo ========================================
echo StudyHub Database Migration (Windows)
echo ========================================
echo.

REM Check if MySQL is accessible
where mysql >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: MySQL command not found!
    echo Please ensure MySQL is installed and added to PATH.
    echo.
    echo Common MySQL locations:
    echo - C:\xampp\mysql\bin
    echo - C:\Program Files\MySQL\MySQL Server 8.0\bin
    echo.
    pause
    exit /b 1
)

echo MySQL found. Starting migration...
echo.

REM Prompt for MySQL credentials
set /p MYSQL_USER="Enter MySQL username (default: root): "
if "%MYSQL_USER%"=="" set MYSQL_USER=root

echo.
echo Enter MySQL password (press Enter if no password):
set "MYSQL_PASS="
set /p MYSQL_PASS=

echo.
echo Running migration...
echo.

REM Run the migration
if "%MYSQL_PASS%"=="" (
    mysql -u %MYSQL_USER% < schema.sql
) else (
    mysql -u %MYSQL_USER% -p%MYSQL_PASS% < schema.sql
)

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo Migration completed successfully!
    echo ========================================
    echo.
    echo Database 'studyhub_db' has been created.
    echo Tables 'users' and 'resources' are ready.
    echo.
) else (
    echo.
    echo ========================================
    echo Migration failed!
    echo ========================================
    echo Please check the error messages above.
    echo.
)

pause
