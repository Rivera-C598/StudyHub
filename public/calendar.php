<?php
$pageTitle = 'StudyHub - Calendar';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
require_once __DIR__ . '/../includes/auth.php';
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

// Get resources for the month
$startDate = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
$endDate = date('Y-m-t', strtotime($startDate));

$stmt = $pdo->prepare("
    SELECT * FROM resources 
    WHERE user_id = ? 
    AND DATE(created_at) BETWEEN ? AND ?
    ORDER BY created_at ASC
");
$stmt->execute([$userId, $startDate, $endDate]);
$resources = $stmt->fetchAll();

// Group by date
$resourcesByDate = [];
foreach ($resources as $resource) {
    $date = date('Y-m-d', strtotime($resource['created_at']));
    if (!isset($resourcesByDate[$date])) {
        $resourcesByDate[$date] = [];
    }
    $resourcesByDate[$date][] = $resource;
}

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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Calendar View</h2>
        <div>
            <a href="?month=<?= $prevMonth ?>&year=<?= $prevYear ?>" class="btn btn-outline-primary">
                ← Previous
            </a>
            <a href="?month=<?= date('n') ?>&year=<?= date('Y') ?>" class="btn btn-outline-secondary">
                Today
            </a>
            <a href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?>" class="btn btn-outline-primary">
                Next →
            </a>
        </div>
    </div>
    
    <h3 class="text-center mb-4"><?= $monthName ?></h3>
    
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
                $isToday = $date === date('Y-m-d');
                $hasResources = isset($resourcesByDate[$date]);
                $resourceCount = $hasResources ? count($resourcesByDate[$date]) : 0;
                
                echo '<div class="calendar-cell' . ($isToday ? ' today' : '') . '">';
                echo '<div class="calendar-date">' . $day . '</div>';
                
                if ($hasResources) {
                    echo '<div class="calendar-resources">';
                    echo '<span class="badge bg-primary">' . $resourceCount . ' resource' . ($resourceCount > 1 ? 's' : '') . '</span>';
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
    
    <div class="mt-4">
        <h4>Legend</h4>
        <div class="d-flex gap-3">
            <span><span class="badge bg-secondary">●</span> Todo</span>
            <span><span class="badge bg-warning">●</span> In Progress</span>
            <span><span class="badge bg-success">●</span> Done</span>
        </div>
    </div>
</div>

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
}

.calendar-cell.empty {
    background: transparent;
    border: none;
}

.calendar-cell.today {
    background: rgba(74, 144, 226, 0.2);
    border-color: var(--primary-color);
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
}

@media (max-width: 768px) {
    .calendar-cell {
        min-height: 80px;
        font-size: 0.85rem;
    }
    
    .calendar-resource-item small {
        font-size: 0.75rem;
    }
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
