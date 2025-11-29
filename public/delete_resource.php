<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

requireLogin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid resource ID.";
    header('Location: dashboard.php');
    exit;
}

$resourceId = (int) $_GET['id'];
$userId = $_SESSION['user_id'];

try {
    $pdo = getDbConnection();
    
    // Check if resource exists and belongs to user
    $checkStmt = $pdo->prepare("SELECT id FROM resources WHERE id = :id AND user_id = :uid");
    $checkStmt->execute([':id' => $resourceId, ':uid' => $userId]);
    
    if (!$checkStmt->fetch()) {
        $_SESSION['error'] = "Resource not found or access denied.";
    } else {
        $stmt = $pdo->prepare("DELETE FROM resources WHERE id = :id AND user_id = :uid");
        $stmt->execute([':id' => $resourceId, ':uid' => $userId]);
        $_SESSION['success'] = "Resource deleted successfully.";
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Failed to delete resource. Please try again.";
    error_log("Delete resource error: " . $e->getMessage());
}

header('Location: dashboard.php');
exit;
