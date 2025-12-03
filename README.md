# StudyHub 📚

A modern, feature-rich web application for students to organize study materials, track progress, manage deadlines, and stay motivated.

![Version](https://img.shields.io/badge/version-3.1.4-blue)
![PHP](https://img.shields.io/badge/PHP-7.4+-purple)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange)
![License](https://img.shields.io/badge/license-MIT-green)

---

## ✨ Features

### Core Functionality
- **User Authentication** - Secure registration and login with Argon2id/bcrypt password hashing
- **Resource Management** - Create, edit, and delete study resources (notes, links, tasks)
- **Progress Tracking** - Track status: todo → in_progress → done
- **Deadline Management** - Set and track due dates with visual urgency indicators
- **Search & Filter** - Find resources by title, subject, type, or status
- **Pagination** - Browse resources efficiently (10 per page)

### Advanced Features
- **Bulk Actions** - Select and manage multiple resources at once
  - Bulk delete
  - Bulk status change
  - Bulk tag addition
- **Export Data** - Download your resources in CSV, JSON, or PDF format
- **Resource Templates** - 6 pre-made templates for quick resource creation
  - Math Problem Set
  - Reading Assignment
  - Lab Report
  - Essay Outline
  - Study Notes
  - Project Plan
- **Calendar View** - Interactive monthly calendar with deadline tracking
  - Click dates to add resources
  - Color-coded by status
  - Overdue indicators
  - Past date protection (no time travel!)
- **Quick Stats Dashboard** - Real-time statistics
  - Total resources
  - Completed this week
  - Study streak counter
  - Top subject
- **Motivational Tips** - Random study tips to keep you motivated

### Security Features
- **CSRF Protection** - All forms protected with CSRF tokens
- **Rate Limiting** - Login attempt throttling (5 attempts per 5 minutes)
- **Input Validation** - Comprehensive server-side validation
- **SQL Injection Prevention** - Prepared statements throughout
- **XSS Protection** - Output escaping with htmlspecialchars()
- **Session Security** - Session regeneration on login
- **Password Requirements** - Minimum 8 characters

---

## 🚀 Quick Start

See [SETUP.md](SETUP.md) for detailed installation instructions.

**TL;DR:**
```bash
# 1. Clone/download to web server directory
# 2. Run database migration
cd database
./migrate.sh  # or migrate.bat on Windows

# 3. Configure database (if needed)
# Edit config/config.php

# 4. Start server
# XAMPP/MAMP: Start Apache & MySQL
# Or: php -S localhost:8000 -t public

# 5. Open browser
http://localhost/studyhub
```

---

## 📖 Usage Guide

### Getting Started

1. **Register an Account**
   - Click "Register" on the login page
   - Enter username, email, and password (min 8 characters)
   - Login with your credentials

2. **Add Your First Resource**
   - Fill in the form on the left side of the dashboard
   - Enter title, subject, and type (note/link/task)
   - Optionally add URL, notes, and deadline
   - Click "Save"

### Managing Resources

**Edit a Resource:**
- Click "Edit" button on any resource
- Modify fields as needed
- Click "Update"

**Toggle Status:**
- Click "Toggle" button to cycle through statuses
- todo → in_progress → done → todo

**Delete a Resource:**
- Click "Delete" button
- Confirm deletion

**Bulk Actions:**
1. Select multiple resources using checkboxes
2. Choose action from bulk actions bar:
   - Mark Done / In Progress / Todo
   - Delete Selected

### Using the Calendar

**View Deadlines:**
- Navigate to Calendar view from navbar
- See all resources with deadlines
- Color-coded by status (todo/in_progress/done)
- Overdue items highlighted in red

**Add Resource from Calendar:**
1. Click on any date (today or future)
2. Fill in quick-add form
3. Resource created with that deadline
4. Note: Past dates are locked (no time travel!)

**Navigate Months:**
- Use Previous/Next buttons at bottom
- Click "Today" to return to current month

### Search and Filter

**Search:**
- Type in search box to find resources by title or subject
- Real-time filtering as you type

**Filter:**
- Filter by Type: note, link, or task
- Filter by Status: todo, in_progress, or done
- Combine filters for precise results

### Export Your Data

1. Click "Export" dropdown in dashboard
2. Choose format:
   - **CSV** - For spreadsheets (Excel, Google Sheets)
   - **JSON** - For backup or data migration
   - **PDF** - For printing (opens in new tab)

### Using Templates

1. Click "Templates" dropdown when adding a resource
2. Select a template
3. Customize title and subject
4. Template structure is pre-filled in notes
5. Click "Save"

---

## 🎨 User Interface

### Dashboard
- **Left Panel** - Add/Edit resource form with templates
- **Right Panel** - Resource list with search, filter, and bulk actions
- **Top Stats** - Quick overview of your progress
- **Study Tip Card** - Daily motivation

### Calendar
- **Monthly Grid** - All dates with resources
- **Color Coding** - Visual status indicators
- **Interactive** - Click to add resources
- **Navigation** - Easy month switching at bottom

### Responsive Design
- Works on desktop, tablet, and mobile
- Touch-friendly interface
- Adaptive layouts

---

## 🔒 Security

StudyHub implements industry-standard security practices:

### Authentication
- Passwords hashed with Argon2id (or bcrypt fallback)
- Session-based authentication
- Session regeneration on login
- Secure session configuration

### Input Protection
- All user input validated server-side
- Prepared statements prevent SQL injection
- Output escaped to prevent XSS
- CSRF tokens on all forms

### Rate Limiting
- Login attempts limited to 5 per 5 minutes
- Prevents brute force attacks
- Session-based tracking

### Error Handling
- Generic error messages to users
- Detailed errors logged server-side
- No information disclosure

---

## 🗂️ Project Structure

```
studyhub/
├── api/                    # REST API endpoints
│   ├── bulk_actions.php    # Bulk operations
│   ├── motivation.php      # Random study tips
│   ├── search_resources.php # Server-side search
│   └── toggle_status.php   # Status toggling
├── assets/                 # Frontend assets
│   ├── css/
│   │   └── style.css       # Custom styles
│   └── js/
│       └── main.js         # JavaScript functionality
├── config/                 # Configuration
│   ├── config.php          # Database credentials
│   └── db.php              # Database connection
├── database/               # Database files
│   ├── schema.sql          # Database schema
│   ├── migrate.sh          # Linux/Mac migration
│   ├── migrate.bat         # Windows migration
│   ├── migrate_v3.1.sql    # v3.1 migration
│   ├── migrate_v3.1.sh     # v3.1 Linux/Mac
│   └── migrate_v3.1.bat    # v3.1 Windows
├── includes/               # Shared PHP files
│   ├── auth.php            # Authentication functions
│   ├── footer.php          # Footer template
│   ├── header.php          # Header template
│   ├── navbar.php          # Navigation bar
│   └── security.php        # Security functions
├── public/                 # Public pages
│   ├── calendar.php        # Calendar view
│   ├── dashboard.php       # Main dashboard
│   ├── delete_resource.php # Delete handler
│   ├── export.php          # Export functionality
│   ├── index.php           # Entry point
│   ├── login.php           # Login page
│   ├── logout.php          # Logout handler
│   ├── register.php        # Registration page
│   └── templates.php       # Resource templates
├── index.php               # Root redirect
├── README.md               # This file
└── SETUP.md                # Installation guide
```

---

## 🛠️ Technology Stack

### Backend
- **PHP 7.4+** - Server-side logic
- **MySQL 5.7+** - Database
- **PDO** - Database abstraction

### Frontend
- **HTML5** - Structure
- **CSS3** - Styling with custom variables
- **JavaScript (ES6+)** - Interactivity
- **Bootstrap 5.3.3** - UI framework

### Security
- **Argon2id/bcrypt** - Password hashing
- **CSRF Tokens** - Form protection
- **Prepared Statements** - SQL injection prevention
- **htmlspecialchars()** - XSS prevention

---

## 📊 Database Schema

### Users Table
```sql
- id (INT, PRIMARY KEY)
- username (VARCHAR, UNIQUE)
- email (VARCHAR, UNIQUE)
- password_hash (VARCHAR)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

### Resources Table
```sql
- id (INT, PRIMARY KEY)
- user_id (INT, FOREIGN KEY)
- title (VARCHAR)
- subject (VARCHAR)
- resource_type (ENUM: note, link, task)
- url (TEXT, nullable)
- notes (TEXT, nullable)
- status (ENUM: todo, in_progress, done)
- deadline (DATE, nullable)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

### Resource Tags Table
```sql
- id (INT, PRIMARY KEY)
- resource_id (INT, FOREIGN KEY)
- tag (VARCHAR)
- created_at (TIMESTAMP)
```

---

## ⌨️ Keyboard Shortcuts

- **/** (forward slash) - Focus on search box (like GitHub, Reddit)
- **Esc** - Clear search and unfocus

---

## 🐛 Troubleshooting

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

### Toggle Button Not Working
- Clear browser cache (Ctrl+F5)
- Check browser console for JavaScript errors
- Verify main.js is loading

### Calendar Not Showing Resources
- Check that resources have deadlines set
- Verify date format is YYYY-MM-DD
- Check browser console for errors

---

## 🔄 Version History

### v3.1.4 (Current)
- Improved calendar navigation (moved to bottom)
- Enhanced legend with more indicators
- Better button styling with icons

### v3.1.3
- Added past date protection (no time travel)
- Visual indicators for past dates
- Friendly error messages

### v3.1.2
- Fixed POST-Refresh-Duplicate bug
- Implemented Post-Redirect-Get pattern
- Better form submission handling

### v3.1.1
- Fixed toggle button column selector
- Added calendar interactivity
- Quick-add modal for dates

### v3.1.0
- Consolidated JavaScript bulk actions
- Added missing database tables
- Fixed template loading
- Server-side search API
- Performance indexes
- Removed duplicate stats calculations

### v3.0.0
- Bulk actions
- Export functionality
- Resource templates
- Calendar view
- Quick stats dashboard
- Deadline system
- Tagging system

### v2.0.0
- Edit resources
- Search & filter
- Pagination
- CSRF protection
- Rate limiting
- Enhanced security

### v1.0.0
- Initial release
- Basic CRUD operations
- User authentication
- Status tracking

---

## 🤝 Contributing

This is an educational project. Feel free to:
- Fork and modify for your needs
- Report bugs or suggest features
- Improve documentation
- Add new features

---

## 📄 License

This project is open source and available for educational purposes.

---

## 🙏 Acknowledgments

Built with:
- PHP
- MySQL
- Bootstrap 5
- Vanilla JavaScript
- Love for learning ❤️

---

## 📞 Support

For issues or questions:
1. Check the [SETUP.md](SETUP.md) guide
2. Review the troubleshooting section above
3. Check browser console for errors
4. Verify database connection

---

## 🎯 Future Enhancements

Potential features for future versions:
- Email notifications for deadlines
- File attachments
- Rich text editor for notes
- Collaborative study groups
- Statistics and analytics
- Mobile app
- Dark/Light theme toggle
- Import from other apps
- API for third-party integrations

---

**Made with 💙 for students everywhere**

*Study smart, not hard!* 📚✨
