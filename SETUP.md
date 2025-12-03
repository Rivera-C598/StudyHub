# StudyHub Setup Guide 🚀

Complete installation guide for setting up StudyHub on any platform.

---

## 📋 Table of Contents

1. [Requirements](#requirements)
2. [Quick Setup](#quick-setup)
3. [Detailed Setup by Platform](#detailed-setup-by-platform)
   - [Windows (XAMPP)](#windows-xampp)
   - [Mac (MAMP)](#mac-mamp)
   - [Linux](#linux)
   - [Docker](#docker)
4. [Database Setup](#database-setup)
5. [Configuration](#configuration)
6. [Verification](#verification)
7. [Troubleshooting](#troubleshooting)
8. [Production Deployment](#production-deployment)

---

## Requirements

### Minimum Requirements
- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher (or MariaDB 10.2+)
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Disk Space**: 50 MB
- **RAM**: 512 MB minimum

### Recommended
- **PHP**: 8.0+
- **MySQL**: 8.0+
- **RAM**: 1 GB+
- **SSL Certificate**: For production

### PHP Extensions Required
- `pdo_mysql` - Database connectivity
- `session` - Session management
- `json` - JSON handling
- `mbstring` - String handling

### Browser Support
- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers

---

## Quick Setup

For experienced users who want to get started quickly:

```bash
# 1. Download/Clone
git clone https://github.com/yourusername/studyhub.git
cd studyhub

# 2. Database Setup
cd database
./migrate.sh  # Linux/Mac
# OR
migrate.bat   # Windows

# 3. Configure (if needed)
nano config/config.php

# 4. Start Server
# XAMPP/MAMP: Start Apache & MySQL
# OR
php -S localhost:8000 -t public

# 5. Open Browser
http://localhost/studyhub
```

---

## Detailed Setup by Platform

### Windows (XAMPP)

#### Step 1: Install XAMPP

1. Download XAMPP from [https://www.apachefriends.org](https://www.apachefriends.org)
2. Run installer (choose PHP 7.4 or higher)
3. Install to `C:\xampp` (default)
4. Start XAMPP Control Panel

#### Step 2: Download StudyHub

**Option A: Download ZIP**
1. Download StudyHub ZIP file
2. Extract to `C:\xampp\htdocs\studyhub`

**Option B: Git Clone**
```cmd
cd C:\xampp\htdocs
git clone https://github.com/yourusername/studyhub.git
```

#### Step 3: Start Services

1. Open XAMPP Control Panel
2. Click "Start" for Apache
3. Click "Start" for MySQL
4. Verify both show "Running" status

#### Step 4: Database Setup

**Option A: Automatic (Recommended)**
```cmd
cd C:\xampp\htdocs\studyhub\database
migrate.bat
```

**Option B: Manual**
1. Open browser: `http://localhost/phpmyadmin`
2. Click "New" to create database
3. Name: `studyhub_db`
4. Collation: `utf8mb4_unicode_ci`
5. Click "SQL" tab
6. Copy contents of `database/schema.sql`
7. Paste and click "Go"

#### Step 5: Configure Database

Edit `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'studyhub_db');
define('DB_USER', 'root');
define('DB_PASS', '');  // Empty for XAMPP default
```

#### Step 6: Access Application

Open browser: `http://localhost/studyhub`

---

### Mac (MAMP)

#### Step 1: Install MAMP

1. Download MAMP from [https://www.mamp.info](https://www.mamp.info)
2. Install MAMP (free version is fine)
3. Open MAMP application

#### Step 2: Download StudyHub

**Option A: Download ZIP**
1. Download StudyHub ZIP
2. Extract to `/Applications/MAMP/htdocs/studyhub`

**Option B: Git Clone**
```bash
cd /Applications/MAMP/htdocs
git clone https://github.com/yourusername/studyhub.git
```

#### Step 3: Start Services

1. Open MAMP
2. Click "Start Servers"
3. Wait for Apache and MySQL to start

#### Step 4: Database Setup

```bash
cd /Applications/MAMP/htdocs/studyhub/database
chmod +x migrate.sh
./migrate.sh
```

Enter MySQL credentials when prompted:
- Username: `root`
- Password: `root` (MAMP default)

#### Step 5: Configure Database

Edit `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'studyhub_db');
define('DB_USER', 'root');
define('DB_PASS', 'root');  // MAMP default
```

#### Step 6: Access Application

Open browser: `http://localhost:8888/studyhub`

---

### Linux

#### Step 1: Install LAMP Stack

**Ubuntu/Debian:**
```bash
sudo apt update
sudo apt install apache2 mysql-server php php-mysql php-mbstring php-json
sudo systemctl start apache2
sudo systemctl start mysql
sudo systemctl enable apache2
sudo systemctl enable mysql
```

**CentOS/RHEL:**
```bash
sudo yum install httpd mariadb-server php php-mysqlnd php-mbstring php-json
sudo systemctl start httpd
sudo systemctl start mariadb
sudo systemctl enable httpd
sudo systemctl enable mariadb
```

#### Step 2: Download StudyHub

```bash
cd /var/www/html
sudo git clone https://github.com/yourusername/studyhub.git
sudo chown -R www-data:www-data studyhub
sudo chmod -R 755 studyhub
```

#### Step 3: Secure MySQL

```bash
sudo mysql_secure_installation
```

Follow prompts:
- Set root password
- Remove anonymous users: Yes
- Disallow root login remotely: Yes
- Remove test database: Yes
- Reload privilege tables: Yes

#### Step 4: Database Setup

```bash
cd /var/www/html/studyhub/database
chmod +x migrate.sh
sudo ./migrate.sh
```

#### Step 5: Configure Database

Edit `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'studyhub_db');
define('DB_USER', 'root');
define('DB_PASS', 'your_mysql_password');
```

#### Step 6: Configure Apache

Create virtual host (optional but recommended):

```bash
sudo nano /etc/apache2/sites-available/studyhub.conf
```

Add:
```apache
<VirtualHost *:80>
    ServerName studyhub.local
    DocumentRoot /var/www/html/studyhub/public
    
    <Directory /var/www/html/studyhub/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/studyhub_error.log
    CustomLog ${APACHE_LOG_DIR}/studyhub_access.log combined
</VirtualHost>
```

Enable site:
```bash
sudo a2ensite studyhub.conf
sudo systemctl reload apache2
```

Add to `/etc/hosts`:
```bash
sudo nano /etc/hosts
# Add line:
127.0.0.1 studyhub.local
```

#### Step 7: Access Application

Open browser: `http://studyhub.local` or `http://localhost/studyhub`

---

### Docker

#### Step 1: Create docker-compose.yml

```yaml
version: '3.8'

services:
  web:
    image: php:8.0-apache
    container_name: studyhub_web
    ports:
      - "8080:80"
    volumes:
      - ./:/var/www/html
    depends_on:
      - db
    environment:
      - DB_HOST=db
      - DB_NAME=studyhub_db
      - DB_USER=studyhub_user
      - DB_PASS=studyhub_pass

  db:
    image: mysql:8.0
    container_name: studyhub_db
    ports:
      - "3306:3306"
    environment:
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_DATABASE: studyhub_db
      MYSQL_USER: studyhub_user
      MYSQL_PASSWORD: studyhub_pass
    volumes:
      - db_data:/var/lib/mysql
      - ./database/schema.sql:/docker-entrypoint-initdb.d/schema.sql

volumes:
  db_data:
```

#### Step 2: Start Containers

```bash
docker-compose up -d
```

#### Step 3: Access Application

Open browser: `http://localhost:8080`

---

## Database Setup

### Automatic Migration

The easiest way to set up the database:

**Windows:**
```cmd
cd database
migrate.bat
```

**Linux/Mac:**
```bash
cd database
chmod +x migrate.sh
./migrate.sh
```

The script will:
1. Prompt for MySQL credentials
2. Create database if it doesn't exist
3. Create all tables
4. Set up indexes
5. Verify installation

### Manual Migration

If automatic migration fails:

1. **Create Database:**
```sql
CREATE DATABASE studyhub_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE studyhub_db;
```

2. **Run Schema:**
```bash
mysql -u root -p studyhub_db < database/schema.sql
```

3. **Verify Tables:**
```sql
SHOW TABLES;
```

You should see:
- `users`
- `resources`
- `resource_tags`

### Upgrading from v3.0 to v3.1

If you have an existing installation:

**Windows:**
```cmd
cd database
migrate_v3.1.bat
```

**Linux/Mac:**
```bash
cd database
chmod +x migrate_v3.1.sh
./migrate_v3.1.sh
```

This adds:
- `deadline` column to resources
- `resource_tags` table
- Performance indexes

---

## Configuration

### Database Configuration

Edit `config/config.php`:

```php
<?php
// Database credentials
define('DB_HOST', 'localhost');     // Database host
define('DB_NAME', 'studyhub_db');   // Database name
define('DB_USER', 'root');          // Database username
define('DB_PASS', '');              // Database password
```

### Common Configurations

**XAMPP (Windows):**
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'studyhub_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

**MAMP (Mac):**
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'studyhub_db');
define('DB_USER', 'root');
define('DB_PASS', 'root');
```

**Linux Production:**
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'studyhub_db');
define('DB_USER', 'studyhub_user');
define('DB_PASS', 'strong_password_here');
```

**Docker:**
```php
define('DB_HOST', 'db');  // Container name
define('DB_NAME', 'studyhub_db');
define('DB_USER', 'studyhub_user');
define('DB_PASS', 'studyhub_pass');
```

### PHP Configuration

Recommended `php.ini` settings:

```ini
; Session settings
session.cookie_httponly = 1
session.cookie_secure = 1  ; If using HTTPS
session.use_strict_mode = 1

; Upload limits (if adding file upload feature)
upload_max_filesize = 10M
post_max_size = 10M

; Error reporting (development)
display_errors = On
error_reporting = E_ALL

; Error reporting (production)
display_errors = Off
error_reporting = E_ALL
log_errors = On
error_log = /path/to/php_errors.log
```

---

## Verification

### 1. Check PHP Version

```bash
php -v
```

Should show 7.4 or higher.

### 2. Check PHP Extensions

```bash
php -m | grep -E 'pdo_mysql|session|json|mbstring'
```

All should be listed.

### 3. Check Database Connection

Create `test_db.php` in root:

```php
<?php
require_once 'config/db.php';
try {
    $pdo = getDbConnection();
    echo "✅ Database connection successful!";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage();
}
```

Access: `http://localhost/studyhub/test_db.php`

Delete file after testing.

### 4. Check File Permissions

**Linux/Mac:**
```bash
# Files should be readable
ls -la

# Directories should be 755
find . -type d -exec chmod 755 {} \;

# Files should be 644
find . -type f -exec chmod 644 {} \;
```

### 5. Test Application

1. Open browser to your StudyHub URL
2. You should see the login page
3. Click "Register"
4. Create a test account
5. Login and verify dashboard loads

---

## Troubleshooting

### Database Connection Failed

**Error:** "Connection refused" or "Access denied"

**Solutions:**
1. Verify MySQL is running:
   ```bash
   # Linux
   sudo systemctl status mysql
   
   # Mac (MAMP)
   # Check MAMP control panel
   
   # Windows (XAMPP)
   # Check XAMPP control panel
   ```

2. Check credentials in `config/config.php`

3. Test MySQL connection:
   ```bash
   mysql -u root -p
   ```

4. Verify database exists:
   ```sql
   SHOW DATABASES;
   ```

### Page Not Found (404)

**Solutions:**
1. Check file location:
   - XAMPP: `C:\xampp\htdocs\studyhub`
   - MAMP: `/Applications/MAMP/htdocs/studyhub`
   - Linux: `/var/www/html/studyhub`

2. Check URL:
   - XAMPP: `http://localhost/studyhub`
   - MAMP: `http://localhost:8888/studyhub`
   - Linux: `http://localhost/studyhub`

3. Verify Apache is running

### Blank White Page

**Solutions:**
1. Enable error display:
   ```php
   // Add to top of index.php temporarily
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```

2. Check PHP error log:
   - XAMPP: `C:\xampp\php\logs\php_error_log`
   - MAMP: `/Applications/MAMP/logs/php_error.log`
   - Linux: `/var/log/apache2/error.log`

3. Check file permissions

### Session Issues

**Error:** "Session could not be started"

**Solutions:**
1. Check session directory is writable:
   ```bash
   # Linux
   sudo chmod 777 /var/lib/php/sessions
   ```

2. Check `php.ini`:
   ```ini
   session.save_path = "/tmp"
   ```

3. Clear browser cookies

### CSS/JS Not Loading

**Solutions:**
1. Check file paths in browser console (F12)

2. Verify files exist:
   ```bash
   ls -la assets/css/style.css
   ls -la assets/js/main.js
   ```

3. Clear browser cache (Ctrl+F5)

4. Check Apache configuration allows `.htaccess`

### Permission Denied Errors

**Linux/Mac:**
```bash
# Fix ownership
sudo chown -R www-data:www-data /var/www/html/studyhub

# Fix permissions
sudo chmod -R 755 /var/www/html/studyhub
```

### MySQL "Too Many Connections"

**Solution:**
Edit MySQL config:
```ini
[mysqld]
max_connections = 200
```

Restart MySQL.

---

## Production Deployment

### Security Checklist

- [ ] Change default database password
- [ ] Disable PHP error display
- [ ] Enable HTTPS (SSL certificate)
- [ ] Set secure session cookies
- [ ] Configure firewall
- [ ] Regular backups
- [ ] Update PHP and MySQL
- [ ] Remove test files
- [ ] Set proper file permissions
- [ ] Configure rate limiting

### HTTPS Setup

**Using Let's Encrypt (Free):**

```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache

# Get certificate
sudo certbot --apache -d yourdomain.com

# Auto-renewal
sudo certbot renew --dry-run
```

### Performance Optimization

**Enable OPcache:**
```ini
; php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
```

**MySQL Optimization:**
```ini
; my.cnf
innodb_buffer_pool_size = 256M
query_cache_size = 64M
```

### Backup Strategy

**Database Backup:**
```bash
# Daily backup script
mysqldump -u root -p studyhub_db > backup_$(date +%Y%m%d).sql

# Automated with cron
0 2 * * * /path/to/backup_script.sh
```

**File Backup:**
```bash
tar -czf studyhub_backup_$(date +%Y%m%d).tar.gz /var/www/html/studyhub
```

### Monitoring

**Log Files to Monitor:**
- Apache access log
- Apache error log
- PHP error log
- MySQL slow query log

**Tools:**
- Uptime monitoring (UptimeRobot, Pingdom)
- Error tracking (Sentry)
- Performance monitoring (New Relic)

---

## Additional Resources

### Useful Commands

**Check PHP Info:**
```bash
php -i | grep -i mysql
```

**Test Database Connection:**
```bash
mysql -u root -p -e "SELECT 1"
```

**View Apache Error Log:**
```bash
tail -f /var/log/apache2/error.log
```

**Restart Services:**
```bash
# Apache
sudo systemctl restart apache2

# MySQL
sudo systemctl restart mysql
```

### Getting Help

1. Check error logs first
2. Review this setup guide
3. Check README.md troubleshooting section
4. Search for specific error messages
5. Check PHP/MySQL documentation

---

## Quick Reference

### Default Credentials

**XAMPP:**
- MySQL User: `root`
- MySQL Pass: `` (empty)

**MAMP:**
- MySQL User: `root`
- MySQL Pass: `root`

**Linux:**
- Set during MySQL installation

### Default Ports

- Apache: `80` (HTTP), `443` (HTTPS)
- MySQL: `3306`
- MAMP Apache: `8888`

### Important Files

- Database config: `config/config.php`
- Database schema: `database/schema.sql`
- Main entry: `public/index.php`
- Apache config: `/etc/apache2/sites-available/`

---

**Setup complete! 🎉**

Visit your StudyHub installation and start organizing your studies!

For questions or issues, refer to the troubleshooting section or check the README.md file.
