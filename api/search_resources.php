<?php
// api/search_resources.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$userId = $_SESSION['user_id'];
$search = $_GET['search'] ?? '';
$type = $_GET['type'] ?? '';
$status = $_GET['status'] ?? '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

try {
    $pdo = getDbConnection();
    
    // Build WHERE clause
    $where = ['user_id = :uid'];
    $params = [':uid' => $userId];
    
    if ($search !== '') {
        $where[] = '(title LIKE :search OR subject LIKE :search)';
        $params[':search'] = '%' . $search . '%';
    }
    
    if ($type !== '') {
        $where[] = 'resource_type = :type';
        $params[':type'] = $type;
    }
    
    if ($status !== '') {
        $where[] = 'status = :status';
        $params[':status'] = $status;
    }
    
    $whereClause = implode(' AND ', $where);
    
    // Count total
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM resources WHERE $whereClause");
    $countStmt->execute($params);
    $total = $countStmt->fetchColumn();
    
    // Get resources
    $stmt = $pdo->prepare(
        "SELECT * FROM resources WHERE $whereClause ORDER BY created_at DESC LIMIT :limit OFFSET :offset"
    );
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $resources = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'resources' => $resources,
        'total' => $total,
        'page' => $page,
        'perPage' => $perPage,
        'totalPages' => ceil($total / $perPage)
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
    error_log("Search error: " . $e->getMessage());
}
