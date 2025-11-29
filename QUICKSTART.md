# StudyHub Quick Start

Get StudyHub running in 5 minutes!

## Windows (XAMPP)

```cmd
1. Install XAMPP from https://www.apachefriends.org
2. Extract StudyHub to C:\xampp\htdocs\studyhub
3. Start Apache and MySQL in XAMPP Control Panel
4. Open Command Prompt:
   cd C:\xampp\htdocs\studyhub\database
   migrate.bat
5. Open browser: http://localhost/studyhub
```

## Mac (MAMP)

```bash
1. Install MAMP from https://www.mamp.info
2. Extract StudyHub to /Applications/MAMP/htdocs/studyhub
3. Start MAMP servers
4. Open Terminal:
   cd /Applications/MAMP/htdocs/studyhub/database
   chmod +x migrate.sh
   ./migrate.sh
5. Open browser: http://localhost:8888/studyhub
```

## Linux

```bash
# Install requirements
sudo apt install apache2 mysql-server php php-mysql

# Copy files
sudo cp -r studyhub /var/www/html/
sudo chown -R www-data:www-data /var/www/html/studyhub

# Start services
sudo systemctl start apache2 mysql

# Create database
cd /var/www/html/studyhub/database
chmod +x migrate.sh
./migrate.sh

# Open browser: http://localhost/studyhub
```

## First Steps

1. Click **Register**
2. Create account (password min 8 chars)
3. **Login**
4. Add your first study resource!

## Need Help?

- Full guide: [README.md](README.md)
- Setup details: [docs/SETUP.md](docs/SETUP.md)
- Security info: [docs/SECURITY.md](docs/SECURITY.md)

## Default Database Credentials

- Host: `localhost`
- Database: `studyhub_db`
- User: `root`
- Password: `` (empty for XAMPP) or `root` (for MAMP)

Change in `config/config.php` if different.
