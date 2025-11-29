# Dashboard Additions - Add These Sections

## 1. Add Bulk Actions Bar (after the filter section, before the table)

```php
<!-- Bulk Actions Bar -->
<div id="bulk-actions" style="display: none;" class="mb-3 p-3 bg-secondary bg-opacity-50 rounded">
    <div class="d-flex justify-content-between align-items-center">
        <span><strong><span id="selected-count">0</span></strong> resources selected</span>
        <div class="btn-group">
            <button class="btn btn-sm btn-success" onclick="bulkChangeStatus('done')">
                Mark Done
            </button>
            <button class="btn btn-sm btn-warning" onclick="bulkChangeStatus('in_progress')">
                Mark In Progress
            </button>
            <button class="btn btn-sm btn-secondary" onclick="bulkChangeStatus('todo')">
                Mark Todo
            </button>
            <button class="btn btn-sm btn-info" onclick="showBulkTagModal()">
                Add Tags
            </button>
            <button class="btn btn-sm btn-danger" onclick="bulkDelete()">
                Delete
            </button>
        </div>
    </div>
</div>
```

## 2. Add Bulk Tag Modal (before closing </div> of container)

```php
<!-- Bulk Tag Modal -->
<div class="modal fade" id="bulkTagModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header">
                <h5 class="modal-title">Add Tags to Selected Resources</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Tags (comma-separated)</label>
                <input type="text" id="bulk-tags-input" class="form-control" 
                       placeholder="e.g. important, exam, review">
                <small class="text-muted">Separate multiple tags with commas</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="bulkAddTags()">Add Tags</button>
            </div>
        </div>
    </div>
</div>
```

## 3. Update Quick Actions Section (already exists, add these buttons)

```php
<div class="mb-4">
    <div class="btn-group" role="group">
        <a href="templates.php" class="btn btn-outline-primary">📋 Templates</a>
        <a href="calendar.php" class="btn btn-outline-primary">📅 Calendar</a>
        <a href="export.php?format=csv" class="btn btn-outline-success">📥 CSV</a>
        <a href="export.php?format=json" class="btn btn-outline-success">📥 JSON</a>
        <a href="export.php?format=pdf" class="btn btn-outline-success" target="_blank">📥 PDF</a>
    </div>
</div>
```

## 4. Add to Navbar (in includes/navbar.php)

```php
<div class="collapse navbar-collapse" id="navbarContent">
    <ul class="navbar-nav me-auto">
        <li class="nav-item">
            <a class="nav-link" href="dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="calendar.php">Calendar</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="templates.php">Templates</a>
        </li>
    </ul>
    <div class="d-flex align-items-center">
        <?php if (isLoggedIn()): ?>
            <span class="navbar-text me-3">
                Hello, <?= htmlspecialchars($_SESSION['username']) ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        <?php else: ?>
            <a href="login.php" class="btn btn-outline-light btn-sm me-2">Login</a>
            <a href="register.php" class="btn btn-primary btn-sm">Register</a>
        <?php endif; ?>
    </div>
</div>
```

## 5. Run Database Migration

```bash
# Windows
cd database
mysql -u root -p studyhub_db < add_tags_table.sql

# Mac/Linux
cd database
mysql -u root -p studyhub_db < add_tags_table.sql
```

This adds:
- resource_tags table
- deadline column
- user_streaks table

## Features Now Available:

✅ **Bulk Actions**
- Select multiple resources with checkboxes
- Bulk delete
- Bulk status change
- Bulk tag addition

✅ **Export**
- Export to CSV
- Export to JSON
- Export to PDF (print-friendly)

✅ **Templates**
- 7 pre-made templates
- Math Problem Set
- Reading Assignment
- Lab Report
- Essay Outline
- Study Session Plan
- Project Plan
- Exam Preparation

✅ **Calendar View**
- Monthly calendar
- Resources by date
- Color-coded by status
- Navigation (prev/next month)

✅ **Quick Stats**
- Total resources
- Completed this week
- Study streak
- Top subject

✅ **Tagging System** (Backend ready)
- Add tags via bulk actions
- Tag storage in database
- Ready for filtering (add UI later)
