<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
requireLogin();

$format = $_GET['format'] ?? 'csv';
$userId = $_SESSION['user_id'];

try {
    $pdo = getDbConnection();
    
    // Fetch all resources with tags
    $stmt = $pdo->prepare("
        SELECT r.*, GROUP_CONCAT(rt.tag) as tags
        FROM resources r
        LEFT JOIN resource_tags rt ON r.id = rt.resource_id
        WHERE r.user_id = ?
        GROUP BY r.id
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$userId]);
    $resources = $stmt->fetchAll();
    
    switch ($format) {
        case 'csv':
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="studyhub_export_' . date('Y-m-d') . '.csv"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['ID', 'Title', 'Subject', 'Type', 'Status', 'URL', 'Notes', 'Tags', 'Created']);
            
            foreach ($resources as $resource) {
                fputcsv($output, [
                    $resource['id'],
                    $resource['title'],
                    $resource['subject'],
                    $resource['resource_type'],
                    $resource['status'],
                    $resource['url'] ?? '',
                    $resource['notes'] ?? '',
                    $resource['tags'] ?? '',
                    $resource['created_at']
                ]);
            }
            
            fclose($output);
            break;
            
        case 'json':
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="studyhub_export_' . date('Y-m-d') . '.json"');
            
            $export = [
                'export_date' => date('Y-m-d H:i:s'),
                'user_id' => $userId,
                'total_resources' => count($resources),
                'resources' => array_map(function($r) {
                    return [
                        'id' => $r['id'],
                        'title' => $r['title'],
                        'subject' => $r['subject'],
                        'type' => $r['resource_type'],
                        'status' => $r['status'],
                        'url' => $r['url'],
                        'notes' => $r['notes'],
                        'tags' => $r['tags'] ? explode(',', $r['tags']) : [],
                        'created_at' => $r['created_at'],
                        'updated_at' => $r['updated_at']
                    ];
                }, $resources)
            ];
            
            echo json_encode($export, JSON_PRETTY_PRINT);
            break;
            
        case 'pdf':
            // Simple PDF generation (requires FPDF or similar library)
            // For now, we'll create a simple HTML that can be printed to PDF
            header('Content-Type: text/html');
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>StudyHub Export</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    h1 { color: #333; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #4A90E2; color: white; }
                    tr:nth-child(even) { background-color: #f2f2f2; }
                    @media print {
                        button { display: none; }
                    }
                </style>
            </head>
            <body>
                <button onclick="window.print()">Print to PDF</button>
                <h1>StudyHub Resources Export</h1>
                <p>Export Date: <?= date('Y-m-d H:i:s') ?></p>
                <p>Total Resources: <?= count($resources) ?></p>
                
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Tags</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resources as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['title']) ?></td>
                            <td><?= htmlspecialchars($r['subject']) ?></td>
                            <td><?= htmlspecialchars($r['resource_type']) ?></td>
                            <td><?= htmlspecialchars($r['status']) ?></td>
                            <td><?= htmlspecialchars($r['tags'] ?? '') ?></td>
                            <td><?= htmlspecialchars($r['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </body>
            </html>
            <?php
            break;
            
        default:
            header('Location: dashboard.php');
    }
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Export failed. Please try again.";
    error_log("Export error: " . $e->getMessage());
    header('Location: dashboard.php');
}
exit;
