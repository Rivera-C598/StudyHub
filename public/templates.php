<?php
$pageTitle = 'StudyHub - Resource Templates';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../config/db.php';
requireLogin();

$templates = [
    'math_problem' => [
        'title' => 'Math Problem Set',
        'subject' => 'Mathematics',
        'type' => 'task',
        'notes' => "Problem Set #__\n\nProblems to solve:\n1. \n2. \n3. \n\nDue date: \nDifficulty: \nChapter: "
    ],
    'reading' => [
        'title' => 'Reading Assignment',
        'subject' => 'Literature',
        'type' => 'task',
        'notes' => "Reading: [Book/Article Title]\n\nPages: \nAuthor: \nKey themes:\n- \n- \n\nQuestions to consider:\n1. \n2. "
    ],
    'lab_report' => [
        'title' => 'Lab Report',
        'subject' => 'Science',
        'type' => 'task',
        'notes' => "Lab Report: [Title]\n\nObjective:\n\nMaterials:\n-\n-\n\nProcedure:\n1.\n2.\n\nResults:\n\nConclusion:\n\nDue date: "
    ],
    'essay' => [
        'title' => 'Essay Outline',
        'subject' => 'Writing',
        'type' => 'task',
        'notes' => "Essay: [Title]\n\nThesis:\n\nI. Introduction\n   - Hook:\n   - Background:\n   - Thesis:\n\nII. Body Paragraph 1\n   - Topic:\n   - Evidence:\n\nIII. Body Paragraph 2\n   - Topic:\n   - Evidence:\n\nIV. Conclusion\n   - Summary:\n   - Final thought:\n\nDue date:\nWord count: "
    ],
    'study_session' => [
        'title' => 'Study Session Plan',
        'subject' => 'General',
        'type' => 'task',
        'notes' => "Study Session: [Date]\n\nTopics to cover:\n1. \n2. \n3. \n\nGoals:\n- \n- \n\nMaterials needed:\n- \n- \n\nTime allocation:\n- Topic 1: __ minutes\n- Topic 2: __ minutes\n- Break: __ minutes"
    ],
    'project' => [
        'title' => 'Project Plan',
        'subject' => 'General',
        'type' => 'task',
        'notes' => "Project: [Title]\n\nObjective:\n\nMilestones:\n1. [Date] - \n2. [Date] - \n3. [Date] - \n\nResources needed:\n- \n- \n\nTeam members:\n- \n\nDeadline: "
    ],
    'exam_prep' => [
        'title' => 'Exam Preparation',
        'subject' => 'General',
        'type' => 'task',
        'notes' => "Exam: [Subject]\nDate: \n\nTopics to review:\n1. \n2. \n3. \n\nPractice problems:\n- \n- \n\nStudy materials:\n- Textbook chapters: \n- Notes: \n- Practice tests: \n\nStudy schedule:\n- Week 1: \n- Week 2: "
    ]
];

// Handle template selection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['template'])) {
    if (!validateCsrfToken()) {
        $error = "Invalid security token.";
    } else {
        $templateKey = $_POST['template'];
        if (isset($templates[$templateKey])) {
            $template = $templates[$templateKey];
            
            // Customize with user input
            $title = trim($_POST['custom_title'] ?? $template['title']);
            $subject = trim($_POST['custom_subject'] ?? $template['subject']);
            
            try {
                $pdo = getDbConnection();
                $stmt = $pdo->prepare(
                    "INSERT INTO resources (user_id, title, subject, resource_type, notes, status)
                     VALUES (?, ?, ?, ?, ?, 'todo')"
                );
                $stmt->execute([
                    $_SESSION['user_id'],
                    $title,
                    $subject,
                    $template['type'],
                    $template['notes']
                ]);
                
                $_SESSION['success'] = "Resource created from template!";
                header('Location: dashboard.php');
                exit;
            } catch (PDOException $e) {
                $error = "Failed to create resource.";
                error_log("Template error: " . $e->getMessage());
            }
        }
    }
}
?>

<div class="container py-4">
    <h2 class="mb-4">Resource Templates</h2>
    <p class="text-muted mb-4">Choose a template to quickly create a new resource with pre-filled structure.</p>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <div class="row g-4">
        <?php foreach ($templates as $key => $template): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($template['title']) ?></h5>
                    <p class="card-text">
                        <span class="badge bg-primary"><?= htmlspecialchars($template['subject']) ?></span>
                        <span class="badge bg-secondary"><?= htmlspecialchars($template['type']) ?></span>
                    </p>
                    <button class="btn btn-outline-primary w-100" 
                            onclick="showTemplateModal('<?= $key ?>', <?= htmlspecialchars(json_encode($template)) ?>)">
                        Use Template
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Template Modal -->
<div class="modal fade" id="templateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-light">
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()) ?>">
                <input type="hidden" name="template" id="modal-template-key">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Customize Template</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="custom_title" id="modal-title-input" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="custom_subject" id="modal-subject-input" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Preview</label>
                        <textarea class="form-control" id="modal-preview" rows="8" readonly></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Resource</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showTemplateModal(key, template) {
    document.getElementById('modal-template-key').value = key;
    document.getElementById('modal-title-input').value = template.title;
    document.getElementById('modal-subject-input').value = template.subject;
    document.getElementById('modal-preview').value = template.notes;
    
    const modal = new bootstrap.Modal(document.getElementById('templateModal'));
    modal.show();
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
