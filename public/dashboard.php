<?php
$pageTitle = 'StudyHub - Dashboard';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';
requireLogin();

$pdo = getDbConnection();
$userId = $_SESSION['user_id'];
$errors = [];
$success = null;

// Handle new resource POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['edit_id'])) {
    if (!validateCsrfToken()) {
        $errors[] = "Invalid security token. Please try again.";
    } else {
        $title   = trim($_POST['title'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $type    = $_POST['resource_type'] ?? 'note';
        $url     = trim($_POST['url'] ?? '');
        $notes   = trim($_POST['notes'] ?? '');

        if ($title === '' || $subject === '') {
            $errors[] = "Title and subject are required.";
        } elseif (!isValidResourceType($type)) {
            $errors[] = "Invalid resource type.";
        } else {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO resources (user_id, title, subject, resource_type, url, notes)
                     VALUES (:uid, :t, :s, :rt, :url, :notes)"
                );
                $stmt->execute([
                    ':uid'  => $userId,
                    ':t'    => $title,
                    ':s'    => $subject,
                    ':rt'   => $type,
                    ':url'  => $url !== '' ? $url : null,
                    ':notes'=> $notes
                ]);
                $success = "Resource added!";
            } catch (PDOException $e) {
                $errors[] = "Failed to add resource. Please try again.";
                error_log("Add resource error: " . $e->getMessage());
            }
        }
    }
}

// Handle edit resource POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    if (!validateCsrfToken()) {
        $errors[] = "Invalid security token. Please try again.";
    } else {
        $editId  = (int) $_POST['edit_id'];
        $title   = trim($_POST['title'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $type    = $_POST['resource_type'] ?? 'note';
        $url     = trim($_POST['url'] ?? '');
        $notes   = trim($_POST['notes'] ?? '');

        if ($title === '' || $subject === '') {
            $errors[] = "Title and subject are required.";
        } elseif (!isValidResourceType($type)) {
            $errors[] = "Invalid resource type.";
        } else {
            try {
                $stmt = $pdo->prepare(
                    "UPDATE resources SET title = :t, subject = :s, resource_type = :rt, 
                     url = :url, notes = :notes WHERE id = :id AND user_id = :uid"
                );
                $stmt->execute([
                    ':t'    => $title,
                    ':s'    => $subject,
                    ':rt'   => $type,
                    ':url'  => $url !== '' ? $url : null,
                    ':notes'=> $notes,
                    ':id'   => $editId,
                    ':uid'  => $userId
                ]);
                $success = "Resource updated!";
            } catch (PDOException $e) {
                $errors[] = "Failed to update resource. Please try again.";
                error_log("Update resource error: " . $e->getMessage());
            }
        }
    }
}

// Get resource for editing
$editResource = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM resources WHERE id = :id AND user_id = :uid");
    $stmt->execute([':id' => (int)$_GET['edit'], ':uid' => $userId]);
    $editResource = $stmt->fetch();
}

// Handle flash messages from redirects
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    if (!isset($errors)) $errors = [];
    $errors[] = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Calculate stats
$statsStmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
        SUM(CASE WHEN DATE(updated_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND status = 'done' THEN 1 ELSE 0 END) as completed_this_week
    FROM resources WHERE user_id = ?
");
$statsStmt->execute([$userId]);
$stats = $statsStmt->fetch();

// Most studied subject
$subjectStmt = $pdo->prepare("
    SELECT subject, COUNT(*) as count 
    FROM resources 
    WHERE user_id = ? 
    GROUP BY subject 
    ORDER BY count DESC 
    LIMIT 1
");
$subjectStmt->execute([$userId]);
$topSubject = $subjectStmt->fetch();

// Calculate streak
$streakStmt = $pdo->prepare("
    SELECT COUNT(DISTINCT DATE(updated_at)) as streak
    FROM resources
    WHERE user_id = ? 
    AND status = 'done'
    AND DATE(updated_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
");
$streakStmt->execute([$userId]);
$streakData = $streakStmt->fetch();
$streak = $streakData['streak'] ?? 0;

// Pagination
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Count total resources
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM resources WHERE user_id = :uid");
$countStmt->execute([':uid' => $userId]);
$totalResources = $countStmt->fetchColumn();
$totalPages = ceil($totalResources / $perPage);

// Load resources with pagination
$stmt = $pdo->prepare(
    "SELECT * FROM resources WHERE user_id = :uid ORDER BY created_at DESC LIMIT :limit OFFSET :offset"
);
$stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$resources = $stmt->fetchAll();

// Calculate quick stats
$statsStmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
        SUM(CASE WHEN status = 'todo' THEN 1 ELSE 0 END) as todo
    FROM resources 
    WHERE user_id = :uid
");
$statsStmt->execute([':uid' => $userId]);
$stats = $statsStmt->fetch();

// Completed this week
$weekStmt = $pdo->prepare("
    SELECT COUNT(*) as completed_this_week
    FROM resources 
    WHERE user_id = :uid 
    AND status = 'done' 
    AND updated_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
");
$weekStmt->execute([':uid' => $userId]);
$weekStats = $weekStmt->fetch();

// Most studied subject
$subjectStmt = $pdo->prepare("
    SELECT subject, COUNT(*) as count
    FROM resources 
    WHERE user_id = :uid
    GROUP BY subject
    ORDER BY count DESC
    LIMIT 1
");
$subjectStmt->execute([':uid' => $userId]);
$topSubject = $subjectStmt->fetch();

// Calculate streak (consecutive days with activity)
$streakStmt = $pdo->prepare("
    SELECT DATE(created_at) as activity_date
    FROM resources 
    WHERE user_id = :uid
    UNION
    SELECT DATE(updated_at) as activity_date
    FROM resources 
    WHERE user_id = :uid AND status = 'done'
    ORDER BY activity_date DESC
");
$streakStmt->execute([':uid' => $userId]);
$activityDates = $streakStmt->fetchAll(PDO::FETCH_COLUMN);

$streak = 0;
$today = new DateTime();
$today->setTime(0, 0, 0);

foreach ($activityDates as $dateStr) {
    $activityDate = new DateTime($dateStr);
    $activityDate->setTime(0, 0, 0);
    $diff = $today->diff($activityDate)->days;
    
    if ($diff == $streak) {
        $streak++;
    } else {
        break;
    }
}

$stats['completed_this_week'] = $weekStats['completed_this_week'];
$stats['top_subject'] = $topSubject['subject'] ?? 'None';
$stats['streak'] = $streak;
?>
<script defer src="../assets/js/main.js"></script>

<div class="container py-4">
    <!-- Quick Stats Dashboard -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card bg-primary bg-opacity-25 p-3 text-center">
                <h2 class="mb-0"><?= $stats['total'] ?></h2>
                <small class="text-muted">Total Resources</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card bg-success bg-opacity-25 p-3 text-center">
                <h2 class="mb-0"><?= $stats['completed_this_week'] ?></h2>
                <small class="text-muted">Completed This Week</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card bg-warning bg-opacity-25 p-3 text-center">
                <h2 class="mb-0"><?= $stats['streak'] ?> 🔥</h2>
                <small class="text-muted">Day Streak</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card bg-info bg-opacity-25 p-3 text-center">
                <h2 class="mb-0 text-truncate"><?= htmlspecialchars($stats['top_subject']) ?></h2>
                <small class="text-muted">Top Subject</small>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left: add/edit resource -->
        <div class="col-md-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h4 class="mb-0"><?= $editResource ? 'Edit' : 'Add' ?> Study Resource</h4>
                <?php if (!$editResource): ?>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" 
                                data-bs-toggle="dropdown">
                            📋 Templates
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li><a class="dropdown-item" href="#" onclick="loadTemplate('math_problem_set')">Math Problem Set</a></li>
                            <li><a class="dropdown-item" href="#" onclick="loadTemplate('reading_assignment')">Reading Assignment</a></li>
                            <li><a class="dropdown-item" href="#" onclick="loadTemplate('lab_report')">Lab Report</a></li>
                            <li><a class="dropdown-item" href="#" onclick="loadTemplate('essay_outline')">Essay Outline</a></li>
                            <li><a class="dropdown-item" href="#" onclick="loadTemplate('study_notes')">Study Notes</a></li>
                            <li><a class="dropdown-item" href="#" onclick="loadTemplate('project_plan')">Project Plan</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form method="post" class="card bg-secondary bg-opacity-50 p-3">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()) ?>">
                <?php if ($editResource): ?>
                    <input type="hidden" name="edit_id" value="<?= $editResource['id'] ?>">
                <?php endif; ?>
                <div class="mb-2">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" 
                           value="<?= htmlspecialchars($editResource['title'] ?? '') ?>" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-control" 
                           value="<?= htmlspecialchars($editResource['subject'] ?? '') ?>" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Type</label>
                    <select name="resource_type" class="form-select">
                        <option value="note" <?= ($editResource['resource_type'] ?? '') === 'note' ? 'selected' : '' ?>>Note</option>
                        <option value="link" <?= ($editResource['resource_type'] ?? '') === 'link' ? 'selected' : '' ?>>Link</option>
                        <option value="task" <?= ($editResource['resource_type'] ?? '') === 'task' ? 'selected' : '' ?>>Task</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">URL (optional, for links)</label>
                    <input type="url" name="url" class="form-control" 
                           value="<?= htmlspecialchars($editResource['url'] ?? '') ?>">
                </div>
                <div class="mb-2">
                    <label class="form-label">Notes / Description</label>
                    <textarea name="notes" rows="3" class="form-control"><?= htmlspecialchars($editResource['notes'] ?? '') ?></textarea>
                </div>
                <button class="btn btn-primary w-100" type="submit">
                    <?= $editResource ? 'Update' : 'Save' ?>
                </button>
                <?php if ($editResource): ?>
                    <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
                <?php endif; ?>
            </form>

            <div class="card mt-4 bg-secondary bg-opacity-50 p-3">
                <h6>Study Tip</h6>
                <p id="study-tip-text" class="mb-1">Loading tip...</p>
                <button class="btn btn-sm btn-outline-light" id="refresh-tip-btn">New Tip</button>
            </div>
        </div>

        <!-- Right: resource list -->
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Your Study Resources (<?= $totalResources ?>)</h4>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" 
                            data-bs-toggle="dropdown">
                        📥 Export
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                        <li><a class="dropdown-item" href="export.php?format=csv">Export as CSV</a></li>
                        <li><a class="dropdown-item" href="export.php?format=json">Export as JSON</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Search and Filter -->
            <div class="filter-section">
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="text" id="search-input" class="form-control" placeholder="Search by title or subject...">
                    </div>
                    <div class="col-md-3">
                        <select id="filter-type" class="form-select">
                            <option value="">All Types</option>
                            <option value="note">Note</option>
                            <option value="link">Link</option>
                            <option value="task">Task</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filter-status" class="form-select">
                            <option value="">All Status</option>
                            <option value="todo">Todo</option>
                            <option value="in_progress">In Progress</option>
                            <option value="done">Done</option>
                        </select>
                    </div>
                </div>
            </div>

            <?php if (empty($resources)): ?>
                <p class="text-muted">No resources yet. Add your first one!</p>
            <?php else: ?>
                <!-- Bulk Actions Bar -->
                <div id="bulkActionsBar" class="alert alert-info d-none mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span><span id="selectedCount">0</span> resources selected</span>
                        <div>
                            <button class="btn btn-sm btn-success me-2" onclick="bulkUpdateStatus('done')">Mark Done</button>
                            <button class="btn btn-sm btn-warning me-2" onclick="bulkUpdateStatus('in_progress')">Mark In Progress</button>
                            <button class="btn btn-sm btn-secondary me-2" onclick="bulkUpdateStatus('todo')">Mark Todo</button>
                            <button class="btn btn-sm btn-danger" onclick="bulkDelete()">Delete Selected</button>
                        </div>
                    </div>
                </div>

                <table class="table table-dark table-striped align-middle">
                    <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        </th>
                        <th>Title</th>
                        <th>Subject</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($resources as $res): ?>
                        <tr data-id="<?= $res['id'] ?>">
                            <td>
                                <input type="checkbox" class="resource-checkbox" value="<?= $res['id'] ?>" 
                                       onchange="updateBulkActions()">
                            </td>
                            <td>
                                <?php if ($res['resource_type'] === 'link' && $res['url']): ?>
                                    <a href="<?= htmlspecialchars($res['url']) ?>" target="_blank">
                                        <?= htmlspecialchars($res['title']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="resource-title" 
                                          style="cursor: pointer; text-decoration: underline dotted;"
                                          onclick="showNotesModal(<?= htmlspecialchars(json_encode($res)) ?>)">
                                        <?= htmlspecialchars($res['title']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($res['subject']) ?></td>
                            <td><?= htmlspecialchars($res['resource_type']) ?></td>
                            <td>
                                <span class="badge bg-<?=
                                    $res['status'] === 'done' ? 'success' :
                                    ($res['status'] === 'in_progress' ? 'warning' : 'secondary');
                                ?>">
                                    <?= htmlspecialchars($res['status']) ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-success me-1 toggle-status-btn"
                                        data-id="<?= $res['id'] ?>">
                                    Toggle
                                </button>
                                <a href="dashboard.php?edit=<?= $res['id'] ?>"
                                   class="btn btn-sm btn-outline-primary me-1">
                                    Edit
                                </a>
                                <a href="delete_resource.php?id=<?= $res['id'] ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Delete this resource?');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Notes Preview Modal -->
<div class="modal fade" id="notesModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Subject:</strong> <span id="modalSubject"></span></p>
                <p><strong>Type:</strong> <span id="modalType"></span></p>
                <p id="modalUrlContainer" style="display: none;">
                    <strong>URL:</strong> <a id="modalUrl" href="#" target="_blank"></a>
                </p>
                <hr class="border-secondary">
                <p><strong>Notes:</strong></p>
                <p id="modalNotes" class="text-muted"></p>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a id="modalEditBtn" href="#" class="btn btn-primary">Edit</a>
            </div>
        </div>
    </div>
</div>

<!-- Keyboard Shortcuts Help -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1000;">
    <button class="btn btn-sm btn-outline-light" type="button" data-bs-toggle="collapse" data-bs-target="#shortcutsHelp">
        ⌨️ Shortcuts
    </button>
    <div class="collapse mt-2" id="shortcutsHelp">
        <div class="card bg-dark text-light border-secondary p-2" style="font-size: 0.85rem;">
            <div><kbd>Ctrl+N</kbd> New resource</div>
            <div><kbd>Ctrl+F</kbd> Search</div>
            <div><kbd>Ctrl+S</kbd> Save form</div>
            <div><kbd>Esc</kbd> Clear search</div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
