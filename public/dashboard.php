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
?>
<script defer src="../assets/js/main.js"></script>

<div class="container py-4">
    <div class="row g-4">
        <!-- Left: add/edit resource -->
        <div class="col-md-4">
            <h4><?= $editResource ? 'Edit' : 'Add' ?> Study Resource</h4>

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
            <h4>Your Study Resources (<?= $totalResources ?>)</h4>
            
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
                <table class="table table-dark table-striped align-middle">
                    <thead>
                    <tr>
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
                                <?php if ($res['resource_type'] === 'link' && $res['url']): ?>
                                    <a href="<?= htmlspecialchars($res['url']) ?>" target="_blank">
                                        <?= htmlspecialchars($res['title']) ?>
                                    </a>
                                <?php else: ?>
                                    <?= htmlspecialchars($res['title']) ?>
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

<?php include __DIR__ . '/../includes/footer.php'; ?>
