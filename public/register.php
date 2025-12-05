<?php
$pageTitle = 'StudyHub - Register';
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken()) {
        $errors[] = "Invalid security token. Please try again.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if ($username === '' || $email === '' || $password === '') {
            $errors[] = "All fields are required.";
        } elseif (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        } elseif ($password !== $confirm) {
            $errors[] = "Passwords do not match.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email address.";
        }

        if (empty($errors)) {
            try {
                $pdo = getDbConnection();

                // Check duplicates
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u OR email = :e");
                $stmt->execute([':u' => $username, ':e' => $email]);
                if ($stmt->fetch()) {
                    $errors[] = "Username or email already exists.";
                } else {
                    $hash = hashPassword($password);
                    $stmt = $pdo->prepare(
                        "INSERT INTO users (username, email, password_hash)
                         VALUES (:u, :e, :p)"
                    );
                    $stmt->execute([
                        ':u' => $username,
                        ':e' => $email,
                        ':p' => $hash
                    ]);
                    header('Location: login.php?registered=1');
                    exit;
                }
            } catch (PDOException $e) {
                $errors[] = "An error occurred. Please try again later.";
                error_log("Registration error: " . $e->getMessage());
            }
        }
    }
}
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <h1 class="mb-3 text-center">StudyHub</h1>
            <h4 class="mb-3 text-center">Create your account</h4>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" class="card bg-secondary bg-opacity-50 p-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()) ?>">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control"
                           value="<?= htmlspecialchars($username ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($email ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" minlength="8" required>
                    <small class="form-text text-muted">Minimum 8 characters</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button class="btn btn-primary w-100" type="submit">Register</button>
                <p class="mt-3 text-center text-light">
                    Already have an account?
                    <a href="login.php">Log in</a>
                </p>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
