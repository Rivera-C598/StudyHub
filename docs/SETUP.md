# StudyHub Setup Guide

Complete step-by-step guide to get StudyHub running on your system.

## Prerequisites

Before you begin, ensure you have:

- **PHP 7.4+** installed
- **MySQL 5.7+** or MariaDB
- **Web Server** (Apache, Nginx) or XAMPP/MAMP/WAMP
- **Modern Browser** (Chrome, Firefox, Safari, Edge)

---

## Quick Start (XAMPP - Windows)

### 1. Install XAMPP

Download and install XAMPP from [https://www.apachefriends.org](https://www.apachefriends.org)

### 2. Copy Files

Extract StudyHub to:
```
C:\xampp\htdocs\studyhub
```

### 3. Start Services

1. Open XAMPP Control Panel
2. Start **Apache**
3. Start **MySQL**

### 4. Create Database

Open Command Prompt:
```cmd
cd C:\xampp\htdocs\studyhub\database
migrate.bat
```

Enter your MySQL credentials when prompted (default: root with no password).

### 5. Access Application

Open browser and navigate to:
```
http://localhost/studyhub
```

---

## Quick Start (MAMP - Mac)

### 1. Install MAMP

Download and install MAMP from [https://www.mamp.info](https://www.mamp.info)

### 2. Copy Files

Extract StudyHub to:
```
/Applications/MAMP/htdocs/studyhub
```

### 3. Start Services

1. Open MAMP
2. Click "Start Servers"

### 4. Create Database

Open Terminal:
```bash
cd /Applications/MAMP/htdocs/studyhub/database
chmod +x migrate.sh
./migrate.sh
```

Enter your MySQL credentials when prompted (default: root/root).

### 5. Access Application

Open browser and navigate to:
```
http://localhost:8888/studyhub
```

---

## Manual Setup (Linux)

### 1. Install Requirements

**Ubuntu/Debian:**
```bash
sudo apt update
sudo apt install apache2 mysql-server php php-mysql
```

**CentOS/RHEL:**
```bash
sudo yum install httpd mariadb-server php php-mysqlnd
```

### 2. Copy Files

```bash
sudo cp -r studyhub /var/www/html/
sudo chown -R www-data:www-data /var/www/html/studyhub
```

### 3. Start Services

```bash
sudo systemctl start apache2
sudo systemctl start mysql
```

### 4. Create Database

```bash
cd /var/www/html/studyhub/database
chmod +x migrate.sh
./migrate.sh
```

### 5. Configure Apache (Optional)

Create virtual host:
```bash
sudo nano /etc/apache2/sites-available/studyhub.conf
```

Add:
```apache
<VirtualHost *:80>
    ServerName studyhub.local
    DocumentRoot /var/www/html/studyhub
    
    <Directory /var/www/html/studyhub>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Enable site:
```bash
sudo a2ensite studyhub
sudo systemctl reload apache2
```

Add to `/etc/hosts`:
```
127.0.0.1 studyhub.local
```

### 6. Access Application

```
http://localhost/studyhub
```
or
```
http://studyhub.local
```

---

## Using PHP Built-in Server

For development only (not for production):

```bash
cd studyhub
php -S localhost:8000 -t public
```

Access at: `http://localhost:8000`

**Note:** You still need MySQL running and database created.

---

## Configuration

### Database Settings

Edit `config/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'studyhub_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### PHP Settings

Recommended `php.ini` settings:

```ini
; Development
display_errors = On
error_reporting = E_ALL

; Production
display_errors = Off
error_reporting = E_ALL
log_errors = On
error_log = /path/to/error.log

; Session
session.cookie_httponly = 1
session.cookie_secure = 1  ; Only if using HTTPS
session.cookie_samesite = Strict

; Upload (if implementing file uploads)
upload_max_filesize = 10M
post_max_size = 10M
```

---

## Verification

### Check PHP Version

```bash
php -v
```

Should show 7.4 or higher.

### Check MySQL Connection

```bash
mysql -u root -p
```

Then:
```sql
SHOW DATABASES;
USE studyhub_db;
SHOW TABLES;
```

Should show `users` and `resources` tables.

### Check Web Server

Navigate to:
```
http://localhost/studyhub
```

You should see the login page.

---

## First User

1. Click "Register"
2. Create account:
   - Username: `admin`
   - Email: `admin@example.com`
   - Password: `password123` (minimum 8 characters)
3. Login with credentials
4. Start adding resources!

---

## Troubleshooting

### "Database connection failed"

**Check:**
- MySQL is running
- Credentials in `config/config.php` are correct
- Database `studyhub_db` exists

**Fix:**
```bash
cd database
# Windows
migrate.bat

# Mac/Linux
./migrate.sh
```

### "Page not found" (404)

**Check:**
- Files are in correct directory
- Web server is running
- URL is correct

**Apache:** Ensure `AllowOverride All` is set

### "Permission denied"

**Linux/Mac:**
```bash
sudo chown -R www-data:www-data /var/www/html/studyhub
sudo chmod -R 755 /var/www/html/studyhub
```

### "Session errors"

**Check:**
- Session directory is writable
- PHP session configuration

**Fix:**
```bash
# Linux
sudo chmod 1733 /var/lib/php/sessions
```

### "CSRF token error"

**Fix:**
- Clear browser cookies
- Ensure sessions are working
- Check `session_start()` is called

---

## Security Checklist

Before deploying to production:

- [ ] Change database password
- [ ] Enable HTTPS
- [ ] Set `display_errors = Off`
- [ ] Configure error logging
- [ ] Set secure session cookies
- [ ] Add security headers
- [ ] Regular backups
- [ ] Update PHP and MySQL

---

## Next Steps

- Read [README.md](../README.md) for usage guide
- Review [SECURITY.md](SECURITY.md) for security details
- Check [API.md](API.md) for API documentation
- See [CHANGELOG.md](../CHANGELOG.md) for version history

---

## Getting Help

If you encounter issues:

1. Check error logs
2. Review this guide
3. Verify all prerequisites
4. Check file permissions
5. Test database connection
