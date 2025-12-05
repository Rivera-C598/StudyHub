@echo off
REM Export StudyHub database for InfinityFree deployment
REM This script exports the database schema and structure

echo ========================================
echo  StudyHub Database Export Tool
echo ========================================
echo.

REM Set MySQL credentials (update if your XAMPP settings are different)
set MYSQL_USER=root
set MYSQL_PASS=
set DB_NAME=studyhub_db
set MYSQL_PATH="C:\xampp\mysql\bin\mysqldump.exe"

REM Check if mysqldump exists
if not exist %MYSQL_PATH% (
    echo ERROR: mysqldump not found at %MYSQL_PATH%
    echo Please update MYSQL_PATH in this script
    pause
    exit /b 1
)

echo Exporting database: %DB_NAME%
echo.

REM Export full database (structure + data)
%MYSQL_PATH% -u %MYSQL_USER% --password=%MYSQL_PASS% %DB_NAME% > database\full_export.sql

if %ERRORLEVEL% EQU 0 (
    echo SUCCESS! Database exported to: database\full_export.sql
    echo.
    echo You can now upload this file to InfinityFree phpMyAdmin!
) else (
    echo ERROR: Database export failed
    echo Make sure XAMPP MySQL is running and database exists
)

echo.
pause
