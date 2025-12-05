# 🚀 Deploy StudyHub to InfinityFree

This guide will help you deploy StudyHub to **InfinityFree** so your students can access it online for FREE!

---

## 📋 Step-by-Step Deployment Guide

### **Step 1: Sign Up for InfinityFree**

1. Go to: https://infinityfree.net
2. Click **"Sign Up Now"**
3. Create a free account (use your email)
4. Choose a subdomain (e.g., `studyhub.infinityfreeapp.com`) or use your own domain

---

### **Step 2: Get Your Database Credentials**

1. After signup, go to **Control Panel** (cPanel)
2. Find **"MySQL Databases"** section
3. Click **"Create Database"**
4. Database name will be something like: `epiz_12345678_studyhub`
5. **IMPORTANT**: Write down these credentials:
   - **MySQL Hostname**: (e.g., `sql123.infinityfreeapp.com`)
   - **Database Name**: (e.g., `epiz_12345678_studyhub`)
   - **Username**: (e.g., `epiz_12345678`)
   - **Password**: (your chosen password)

---

### **Step 3: Import Your Database**

1. In cPanel, find **"phpMyAdmin"** and click it
2. Click on your database name (left sidebar)
3. Click **"Import"** tab at the top
4. Click **"Choose File"** and select: `database/schema.sql`
5. Click **"Go"** button at the bottom
6. Wait for success message ✅

If you have migration files:
7. Import `database/migrate_v3.1.sql` (if exists)
8. Import `database/add_tags_table.sql` (if exists)

---

### **Step 4: Configure Production Database**

1. In your StudyHub folder, go to `config/` directory
2. Copy the file: `config.production.php.example`
3. Rename the copy to: `config.production.php`
4. Open `config.production.php` and fill in YOUR credentials from Step 2:

```php
define('DB_HOST', 'sql123.infinityfreeapp.com');  // Your MySQL hostname
define('DB_NAME', 'epiz_12345678_studyhub');      // Your database name
define('DB_USER', 'epiz_12345678');               // Your username
define('DB_PASS', 'your_actual_password');        // Your password
```

5. **SAVE** the file

---

### **Step 5: Prepare Files for Upload**

**Option A: Using File Manager (Easier)**
1. In cPanel, click **"File Manager"**
2. Navigate to `htdocs/` folder
3. Delete any existing files in there
4. Click **"Upload"**
5. Select ALL files from your StudyHub folder
6. Wait for upload to complete

**Option B: Using ZIP (Faster for many files)**
1. On your computer, select ALL StudyHub files and folders:
   - `api/`
   - `assets/`
   - `config/` (including the new `config.production.php`)
   - `database/`
   - `docs/`
   - `includes/`
   - `public/`
   - `.htaccess`
   - `index.php`
   - All other files

2. **Create a ZIP file** (right-click → Send to → Compressed folder)

3. In InfinityFree cPanel, go to **"File Manager"**

4. Navigate to `htdocs/` folder

5. Click **"Upload"** and upload your ZIP file

6. After upload, click on the ZIP file → **"Extract"**

7. Delete the ZIP file after extraction

---

### **Step 6: Set File Permissions** (Important!)

1. In File Manager, right-click on `config/` folder
2. Select **"Change Permissions"**
3. Make sure it's set to **755** (or leave default)
4. Click on `config/config.production.php`
5. Set permissions to **644** (read-only for security)

---

### **Step 7: Test Your Site!** 🎉

1. Open your browser
2. Go to: `https://yourdomain.infinityfreeapp.com`
3. You should see the StudyHub login page!
4. Try creating an account and logging in

---

## 🔧 Troubleshooting

### **"Database connection failed"**
- Double-check credentials in `config.production.php`
- Make sure database was imported successfully
- Verify MySQL hostname is correct

### **"404 Not Found" or blank page**
- Make sure files are in `htdocs/` folder
- Check that `.htaccess` file was uploaded
- Verify `index.php` exists in root

### **"500 Internal Server Error"**
- Check file permissions (folders: 755, files: 644)
- Look at error logs in cPanel → "Error Logs"

### **CSS/JS not loading**
- Clear browser cache (Ctrl+F5)
- Check `assets/` folder was uploaded correctly

---

## 📱 Share With Students

Once deployed, simply share this URL with your students:
```
https://yourdomain.infinityfreeapp.com
```

They can:
- Register an account
- Start adding study resources
- Track their tasks and deadlines
- Access it from any device!

---

## 🔒 Security Notes

- **NEVER** commit `config.production.php` to GitHub (it has your password!)
- Consider adding `.gitignore` file with:
  ```
  config/config.production.php
  ```
- Change default passwords for any test accounts
- InfinityFree provides free SSL certificates - enable it in cPanel!

---

## 💡 Next Steps (Optional)

1. **Enable SSL**: In cPanel → SSL/TLS → Enable free SSL
2. **Custom Domain**: Point your own domain to InfinityFree
3. **Email Setup**: Configure email forwarding for notifications
4. **Backups**: Regularly backup database via phpMyAdmin

---

## ❓ Need Help?

- InfinityFree Forum: https://forum.infinityfree.net
- InfinityFree Docs: https://infinityfree.net/support

---

**You're all set! Your students can now access StudyHub online! 🎓✨**
