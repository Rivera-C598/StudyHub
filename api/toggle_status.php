<?php
// api/toggle_status.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Read JSON body
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

$resourceId = $data['id'] ?? null;
if (!$resourceId || !is_numeric($resourceId)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid resource id']);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $pdo = getDbConnection();

    // Check ownership + current status
    $stmt = $pdo->prepare(
        "SELECT id, status FROM resources WHERE id = :id AND user_id = :uid"
    );
    $stmt->execute([
        ':id'  => $resourceId,
        ':uid' => $userId
    ]);
    $resource = $stmt->fetch();

    if (!$resource) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Resource not found']);
        exit;
    }

    $current = $resource['status'];
    // Cycle through todo -> in_progress -> done -> todo
    $next = match ($current) {
        'todo'        => 'in_progress',
        'in_progress' => 'done',
        'done'        => 'todo',
        default       => 'todo',
    };

    $update = $pdo->prepare(
        "UPDATE resources SET status = :status WHERE id = :id AND user_id = :uid"
    );
    $update->execute([
        ':status' => $next,
        ':id'     => $resourceId,
        ':uid'    => $userId
    ]);

    echo json_encode([
        'success'    => true,
        'new_status' => $next
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Database error: ' . $e->getMessage()
    ]);
}
