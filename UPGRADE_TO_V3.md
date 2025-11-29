# Upgrade to StudyHub v3.0

Quick guide to upgrade from v2.0 to v3.0

---

## Step 1: Database Migration

Run the SQL migration to add new tables:

### Windows (XAMPP):
```cmd
cd C:\xampp\htdocs\studyhub\database
mysql -u root studyhub_db < add_tags_table.sql
```

### Mac (MAMP):
```bash
cd /Applications/MAMP/htdocs/studyhub/database
mysql -u root -proot studyhub_db < add_tags_table.sql
```

### Linux:
```bash
cd /var/www/html/studyhub/database
mysql -u root -p studyhub_db < add_tags_table.sql
```

---

## Step 2: Verify New Files

Make sure these files exist:

**New Pages:**
- ✅ `public/templates.php`
- ✅ `public/calendar.php`
- ✅ `public/export.php`

**New API:**
- ✅ `api/bulk_actions.php`

**New Database:**
- ✅ `database/add_tags_table.sql`

**Updated Files:**
- ✅ `includes/navbar.php` (new navigation)
- ✅ `assets/js/main.js` (bulk actions)
- ✅ `public/dashboard.php` (stats & checkboxes)

---

## Step 3: Test Features

### Test Bulk Actions:
1. Go to Dashboard
2. Check boxes next to resources
3. Try bulk delete
4. Try bulk status change
5. Try bulk tag addition

### Test Export:
1. Click Export in navbar
2. Download CSV
3. Download JSON
4. Open PDF in new tab

### Test Templates:
1. Go to Templates page
2. Click "Use Template"
3. Customize and create
4. Verify resource created

### Test Calendar:
1. Go to Calendar page
2. Navigate months
3. Check resource display
4. Verify color coding

### Test Stats:
1. Check dashboard stats
2. Verify total count
3. Check completed this week
4. Verify streak calculation

---

## Step 4: Clear Browser Cache

```
Ctrl + Shift + Delete (Windows/Linux)
Cmd + Shift + Delete (Mac)
```

Select:
- Cached images and files
- Cookies and site data

---

## Troubleshooting

### Issue: "Table doesn't exist"
**Solution:** Run the database migration again

### Issue: Bulk actions not working
**Solution:** 
1. Clear browser cache
2. Check browser console for errors
3. Verify `api/bulk_actions.php` exists

### Issue: Export downloads empty file
**Solution:**
1. Check if resources exist
2. Verify database connection
3. Check PHP error log

### Issue: Templates page blank
**Solution:**
1. Check `public/templates.php` exists
2. Verify file permissions
3. Check PHP error log

### Issue: Calendar not showing resources
**Solution:**
1. Verify resources have `created_at` dates
2. Check month/year parameters
3. Clear browser cache

### Issue: Stats showing wrong numbers
**Solution:**
1. Verify database has data
2. Check date calculations
3. Refresh page

---

## Rollback (If Needed)

If you need to rollback to v2.0:

### 1. Restore Files:
```bash
# Backup v3 files first
mv public/templates.php public/templates.php.v3
mv public/calendar.php public/calendar.php.v3
mv public/export.php public/export.php.v3
mv api/bulk_actions.php api/bulk_actions.php.v3

# Restore v2 navbar
git checkout includes/navbar.php
```

### 2. Remove Database Tables (Optional):
```sql
DROP TABLE IF EXISTS resource_tags;
DROP TABLE IF EXISTS user_streaks;
ALTER TABLE resources DROP COLUMN deadline;
```

---

## What's New?

See `FEATURES_V3_COMPLETE.md` for full feature list.

**Quick Summary:**
- ✅ Bulk actions
- ✅ Export (CSV/JSON/PDF)
- ✅ 7 templates
- ✅ Calendar view
- ✅ Stats dashboard
- ✅ Tagging system

---

## Support

If you encounter issues:
1. Check `FEATURES_V3_COMPLETE.md`
2. Review `DASHBOARD_ADDITIONS.md`
3. Check PHP error log
4. Verify database migration ran successfully

---

**Upgrade complete!** Enjoy StudyHub v3.0 🎉
