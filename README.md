# StudyHub

A simple and intuitive web application for students to organize study materials, track progress, and stay motivated.

## Features

### Core Features
- **User Authentication**: Secure registration and login with password hashing
- **Resource Management**: Add, edit, and delete study resources (notes, links, tasks)
- **Progress Tracking**: Track status of resources (todo → in_progress → done)
- **Search & Filter**: Find resources by title, subject, type, or status
- **Pagination**: Browse through resources efficiently
- **Responsive Design**: Works on desktop and mobile devices

### New in v3.0 🎉
- **Bulk Actions**: Select and manage multiple resources at once
- **Export Data**: Download your resources in CSV, JSON, or PDF format
- **Resource Templates**: 7 pre-made templates for quick resource creation
- **Calendar View**: See your resources in a monthly calendar layout
- **Quick Stats**: Dashboard with total resources, weekly progress, and streaks
- **Tagging System**: Add and manage tags for better organization
- **Motivational Tips**: Get random study tips to stay motivated

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or XAMPP/MAMP
- Modern web browser

## Installation

### 1. Clone or Download

Download the project files to your web server directory:
- XAMPP: `C:\xampp\htdocs\studyhub`
- MAMP: `/Applications/MAMP/htdocs/studyhub`
- Linux: `/var/www/html/studyhub`

### 2. Database Setup

#### Windows (XAMPP):
```cmd
cd database
migrate.bat
```

#### Mac/Linux:
```bash
cd database
chmod +x migrate.sh
./migrate.sh
```

The script will prompt for your MySQL credentials and create the database.

### 3. Configuration

Edit `config/config.php` if your database credentials differ:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'studyhub_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 4. Start the Server

#### Using XAMPP/MAMP:
1. Start Apache and MySQL
2. Navigate to `http://localhost/studyhub`

#### Using PHP Built-in Server:
```bash
php -S localhost:8000 -t public
```
Then visit `http://localhost:8000`

## Usage

### First Time Setup

1. Navigate to the application in your browser
2. Click "Register" to create an account
3. Fill in username, email, and password (minimum 8 characters)
4. Login with your credentials

### Managing Resources

**Add a Resource:**
1. Fill in the form on the left side of the dashboard
2. Enter title, subject, type (note/link/task)
3. Optionally add a URL and notes
4. Click "Save"

**Edit a Resource:**
1. Click "Edit" button on any resource
2. Modify the fields
3. Click "Update"

**Toggle Status:**
1. Click "Toggle" button to cycle through: todo → in_progress → done

**Delete a Resource:**
1. Click "Delete" button
2. Confirm the deletion

### Search and Filter

- Use the search box to find resources by title or subject
- Filter by type (note/link/task)
- Filter by status (todo/in_progress/done)
- Filters work together for precise results

## Security Features

- **CSRF Protection**: All forms include CSRF tokens
- **Password Hashing**: Argon2id or bcrypt
- **SQL Injection Prevention**: Prepared statements
- **XSS Protection**: Output escaping
- **Rate Limiting**: Login attempt throttling
- **Session Security**: Session regeneration on login

## Project Structure

```
studyhub/
├── api/                    # API endpoints
│   ├── motivation.php      # Random study tips
│   └── toggle_status.php   # Toggle resource status
├── assets/                 # Frontend assets
│   ├── css/
│   │   └── style.css       # Custom styles
│   └── js/
│       └── main.js         # JavaScript functionality
├── config/                 # Configuration files
│   ├── config.php          # Database credentials
│   └── db.php              # Database connection
├── database/               # Database scripts
│   ├── schema.sql          # Database schema
│   ├── migrate.bat         # Windows migration script
│   └── migrate.sh          # Mac/Linux migration script
├── includes/               # Shared PHP files
│   ├── auth.php            # Authentication functions
│   ├── footer.php          # Footer template
│   ├── header.php          # Header template
│   ├── navbar.php          # Navigation bar
│   └── security.php        # Security functions
├── public/                 # Public pages
│   ├── dashboard.php       # Main dashboard
│   ├── delete_resource.php # Delete handler
│   ├── index.php           # Entry point
│   ├── login.php           # Login page
│   ├── logout.php          # Logout handler
│   └── register.php        # Registration page
├── index.php               # Root redirect
└── README.md               # This file
```

## Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check credentials in `config/config.php`
- Ensure database exists: `SHOW DATABASES;`

### Page Not Found (404)
- Check web server configuration
- Verify files are in correct directory
- For Apache, ensure `.htaccess` is allowed

### Session Issues
- Check PHP session configuration
- Ensure `session.save_path` is writable
- Clear browser cookies

### Permission Errors
- Ensure web server has read access to files
- Check file permissions: `chmod 755` for directories, `chmod 644` for files

## Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers

## License

This project is open source and available for educational purposes.

## Support

For issues or questions, please check the documentation in the `docs/` folder.
