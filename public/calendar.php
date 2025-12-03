<?php
$pageTitle = 'StudyHub - Calendar';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../config/db.php';
requireLogin();

$pdo = getDbConnection();
$userId = $_SESSION['user_id'];

// Get current month or requested month
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// Validate month/year
if ($month < 1 || $month > 12) $month = date('n');
if ($year < 2020 || $year > 2030) $year = date('Y');

// Get resources for the month (by deadline)
$startDate = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
$endDate = date('Y-m-t', strtotime($startDate));

$stmt = $pdo->prepare("
    SELECT * FROM resources 
    WHERE user_id = ? 
    AND deadline BETWEEN ? AND ?
    ORDER BY deadline ASC
");
$stmt->execute([$userId, $startDate, $endDate]);
$resources = $stmt->fetchAll();

// Group by deadline date
$resourcesByDate = [];
foreach ($resources as $resource) {
    $date = $resource['deadline'];
    if (!isset($resourcesByDate[$date])) {
        $resourcesByDate[$date] = [];
    }
    $resourcesByDate[$date][] = $resource;
}

// Get today's date for comparison
$today = date('Y-m-d');

// Calendar calculations
$firstDay = date('w', strtotime($startDate)); // 0 (Sunday) to 6 (Saturday)
$daysInMonth = date('t', strtotime($startDate));
$monthName = date('F Y', strtotime($startDate));

// Previous/Next month
$prevMonth = $month - 1;
$prevYear = $year;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}

$nextMonth = $month + 1;
$nextYear = $year;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}
?>

<div class="container py-4">
    <div class="text-center mb-4">
        <h2 class="mb-2">Calendar View</h2>
        <h3 class="text-muted"><?= $monthName ?></h3>
    </div>
    
    <div class="calendar-grid">
        <div class="calendar-header">
            <div class="calendar-day-name">Sun</div>
            <div class="calendar-day-name">Mon</div>
            <div class="calendar-day-name">Tue</div>
            <div class="calendar-day-name">Wed</div>
            <div class="calendar-day-name">Thu</div>
            <div class="calendar-day-name">Fri</div>
            <div class="calendar-day-name">Sat</div>
        </div>
        
        <div class="calendar-body">
            <?php
            // Empty cells before first day
            for ($i = 0; $i < $firstDay; $i++) {
                echo '<div class="calendar-cell empty"></div>';
            }
            
            // Days of month
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                $isToday = $date === $today;
                $hasResources = isset($resourcesByDate[$date]);
                $resourceCount = $hasResources ? count($resourcesByDate[$date]) : 0;
                
                // Determine urgency
                $isPast = $date < $today;
                $isThisWeek = $date >= $today && $date <= date('Y-m-d', strtotime('+7 days'));
                
                $cellClass = 'calendar-cell';
                if ($isToday) $cellClass .= ' today';
                if ($isPast) $cellClass .= ' past';
                if ($hasResources && $isPast) $cellClass .= ' overdue';
                if ($hasResources && $isThisWeek && !$isPast) $cellClass .= ' upcoming';
                
                echo '<div class="' . $cellClass . '" data-date="' . $date . '" data-is-past="' . ($isPast ? 'true' : 'false') . '">';
                echo '<div class="calendar-date">' . $day . '</div>';
                
                if ($hasResources) {
                    echo '<div class="calendar-resources">';
                    
                    // Show urgency badge
                    if ($isPast) {
                        $overdueCount = 0;
                        foreach ($resourcesByDate[$date] as $r) {
                            if ($r['status'] !== 'done') $overdueCount++;
                        }
                        if ($overdueCount > 0) {
                            echo '<span class="badge bg-danger mb-1">' . $overdueCount . ' overdue</span>';
                        }
                    } elseif ($isThisWeek) {
                        echo '<span class="badge bg-warning mb-1">' . $resourceCount . ' due soon</span>';
                    } else {
                        echo '<span class="badge bg-info mb-1">' . $resourceCount . ' due</span>';
                    }
                    
                    echo '<div class="calendar-resource-list">';
                    foreach ($resourcesByDate[$date] as $resource) {
                        $statusColor = $resource['status'] === 'done' ? 'success' : 
                                     ($resource['status'] === 'in_progress' ? 'warning' : 'secondary');
                        echo '<div class="calendar-resource-item">';
                        echo '<span class="badge bg-' . $statusColor . ' me-1">●</span>';
                        echo '<small>' . htmlspecialchars(substr($resource['title'], 0, 20)) . '</small>';
                        echo '</div>';
                    }
                    echo '</div>';
                    echo '</div>';
                }
                
                echo '</div>';
            }
            ?>
        </div>
    </div>
    
    <!-- Navigation Controls -->
    <div class="calendar-navigation mt-4">
        <div class="d-flex justify-content-center gap-2">
            <a href="?month=<?= $prevMonth ?>&year=<?= $prevYear ?>" class="btn btn-primary">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: middle;">
                    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                </svg>
                Previous
            </a>
            <a href="?month=<?= date('n') ?>&year=<?= date('Y') ?>" class="btn btn-success">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: middle;">
                    <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                </svg>
                Today
            </a>
            <a href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?>" class="btn btn-primary">
                Next
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: middle;">
                    <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            </a>
        </div>
    </div>
    
    <!-- Legend -->
    <div class="mt-4 text-center">
        <h5 class="mb-3">Legend</h5>
        <div class="d-flex justify-content-center gap-4 flex-wrap">
            <span><span class="badge bg-secondary">●</span> Todo</span>
            <span><span class="badge bg-warning">●</span> In Progress</span>
            <span><span class="badge bg-success">●</span> Done</span>
            <span><span class="badge bg-danger">●</span> Overdue</span>
            <span style="opacity: 0.5;">🔒 Past (locked)</span>
        </div>
    </div>
</div>

<!-- Quick Add Modal -->
<div class="modal fade" id="quickAddModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-light">
            <form method="post" action="dashboard.php">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()) ?>">
                <input type="hidden" name="deadline" id="modal-deadline">
                
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Add Resource for <span id="modal-date-display"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="resource_type" class="form-select">
                            <option value="task">Task</option>
                            <option value="note">Note</option>
                            <option value="link">Link</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" rows="3" class="form-control"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Resource</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Make calendar cells clickable
document.addEventListener('DOMContentLoaded', () => {
    const calendarCells = document.querySelectorAll('.calendar-cell:not(.empty)');
    
    calendarCells.forEach(cell => {
        cell.addEventListener('click', (e) => {
            // Don't open modal if clicking on a resource item
            if (e.target.closest('.calendar-resource-item')) {
                return;
            }
            
            // Check if date is in the past
            const isPast = cell.getAttribute('data-is-past') === 'true';
            const date = cell.getAttribute('data-date');
            if (!date) return;
            
            if (isPast) {
                // Show friendly message for past dates
                alert('⏰ Time travel not supported! You can only add resources for today or future dates.\n\nTo add a resource that was due in the past, go to the Dashboard and leave the deadline empty or set it to today.');
                return;
            }
            
            // Format date for display
            const dateObj = new Date(date + 'T00:00:00');
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const formattedDate = dateObj.toLocaleDateString('en-US', options);
            
            // Set modal values
            document.getElementById('modal-deadline').value = date;
            document.getElementById('modal-date-display').textContent = formattedDate;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('quickAddModal'));
            modal.show();
            
            // Focus on title input after modal opens
            setTimeout(() => {
                document.querySelector('#quickAddModal input[name="title"]').focus();
            }, 500);
        });
    });
    
    // Make resource items clickable to go to dashboard
    const resourceItems = document.querySelectorAll('.calendar-resource-item');
    resourceItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.stopPropagation();
            window.location.href = 'dashboard.php';
        });
    });
});
</script>

<style>
.calendar-grid {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 1rem;
}

.calendar-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.calendar-day-name {
    text-align: center;
    font-weight: 600;
    padding: 0.5rem;
    color: var(--primary-color);
}

.calendar-body {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0.5rem;
}

.calendar-cell {
    min-height: 100px;
    padding: 0.5rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    position: relative;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}

.calendar-cell.empty {
    background: transparent;
    border: none;
    cursor: default;
}

.calendar-cell.empty:hover {
    transform: none;
    box-shadow: none;
}

.calendar-cell.today {
    background: rgba(102, 126, 234, 0.2);
    border-color: var(--primary-color);
    box-shadow: 0 0 10px rgba(102, 126, 234, 0.3);
}

.calendar-cell.overdue {
    background: rgba(239, 68, 68, 0.1);
    border-color: #ef4444;
}

.calendar-cell.upcoming {
    background: rgba(245, 158, 11, 0.1);
    border-color: #f59e0b;
}

.calendar-cell.past {
    opacity: 0.5;
    cursor: not-allowed;
}

.calendar-cell.past .calendar-date {
    color: var(--text-muted);
}

.calendar-cell:not(.empty):not(.past):hover {
    transform: scale(1.05) translateY(-2px);
    z-index: 10;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    border-color: var(--primary-color);
    background: rgba(102, 126, 234, 0.15);
}

.calendar-cell.today:hover {
    box-shadow: 0 8px 30px rgba(102, 126, 234, 0.6);
}

.calendar-cell.overdue:hover {
    box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
    border-color: #ef4444;
}

.calendar-cell.upcoming:hover {
    box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
    border-color: #f59e0b;
}

.calendar-cell.past:hover {
    transform: none;
    box-shadow: none;
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.1);
}

.calendar-date {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.calendar-resources {
    margin-top: 0.5rem;
}

.calendar-resource-list {
    margin-top: 0.5rem;
}

.calendar-resource-item {
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    padding: 0.25rem;
    border-radius: 4px;
    transition: all 0.2s ease;
    cursor: pointer;
}

.calendar-resource-item:hover {
    background: rgba(102, 126, 234, 0.2);
    transform: translateX(3px);
    padding-left: 0.5rem;
}

/* Calendar Navigation */
.calendar-navigation {
    padding: 1.5rem 0;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.calendar-navigation .btn {
    min-width: 120px;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.calendar-navigation .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.calendar-navigation .btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border: none;
    font-weight: 600;
}

.calendar-navigation .btn-success:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.5);
}

.calendar-navigation svg {
    margin: 0 4px;
}

@media (max-width: 768px) {
    .calendar-cell {
        min-height: 80px;
        font-size: 0.85rem;
    }
    
    .calendar-resource-item small {
        font-size: 0.75rem;
    }
    
    .calendar-navigation .btn {
        min-width: 90px;
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }
    
    .calendar-navigation .gap-2 {
        gap: 0.5rem !important;
    }
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
