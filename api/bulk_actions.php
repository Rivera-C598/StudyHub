<?php
// api/bulk_actions.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/security.php';

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

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

$action = $data['action'] ?? null;
$resourceIds = $data['ids'] ?? [];

if (!$action || empty($resourceIds) || !is_array($resourceIds)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $pdo = getDbConnection();
    
    // Verify all resources belong to user
    $placeholders = str_repeat('?,', count($resourceIds) - 1) . '?';
    $stmt = $pdo->prepare(
        "SELECT id FROM resources WHERE id IN ($placeholders) AND user_id = ?"
    );
    $stmt->execute([...$resourceIds, $userId]);
    $validIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($validIds) !== count($resourceIds)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Access denied to some resources']);
        exit;
    }
    
    switch ($action) {
        case 'delete':
            $stmt = $pdo->prepare(
                "DELETE FROM resources WHERE id IN ($placeholders) AND user_id = ?"
            );
            $stmt->execute([...$resourceIds, $userId]);
            echo json_encode([
                'success' => true,
                'message' => count($resourceIds) . ' resources deleted',
                'count' => count($resourceIds)
            ]);
            break;
            
        case 'change_status':
            $newStatus = $data['status'] ?? 'todo';
            if (!isValidStatus($newStatus)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid status']);
                exit;
            }
            
            $stmt = $pdo->prepare(
                "UPDATE resources SET status = ? WHERE id IN ($placeholders) AND user_id = ?"
            );
            $stmt->execute([$newStatus, ...$resourceIds, $userId]);
            echo json_encode([
                'success' => true,
                'message' => count($resourceIds) . ' resources updated',
                'count' => count($resourceIds),
                'new_status' => $newStatus
            ]);
            break;
            
        case 'add_tags':
            $tags = $data['tags'] ?? [];
            if (empty($tags) || !is_array($tags)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'No tags provided']);
                exit;
            }
            
            foreach ($resourceIds as $resourceId) {
                foreach ($tags as $tag) {
                    $stmt = $pdo->prepare(
                        "INSERT IGNORE INTO resource_tags (resource_id, tag) VALUES (?, ?)"
                    );
                    $stmt->execute([$resourceId, trim($tag)]);
                }
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Tags added to ' . count($resourceIds) . ' resources',
                'count' => count($resourceIds)
            ]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
    error_log("Bulk action error: " . $e->getMessage());
}
