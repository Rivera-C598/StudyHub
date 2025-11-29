<?php
$pageTitle = 'StudyHub - Login';
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken()) {
        $errors[] = "Invalid security token. Please try again.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $errors[] = "Username and password are required.";
        } elseif (!checkRateLimit($username)) {
            $errors[] = "Too many login attempts. Please try again in 5 minutes.";
        } else {
            try {
                $pdo = getDbConnection();
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u");
                $stmt->execute([':u' => $username]);
                $user = $stmt->fetch();
                if ($user && password_verify($password, $user['password_hash'])) {
                    loginUser($user);
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $errors[] = "Invalid username or password.";
                }
            } catch (PDOException $e) {
                $errors[] = "An error occurred. Please try again later.";
                error_log("Login error: " . $e->getMessage());
            }
        }
    }
}

$registered = isset($_GET['registered']);
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <h1 class="mb-3 text-center">StudyHub</h1>
            <h4 class="mb-3 text-center">Sign in</h4>

            <?php if ($registered): ?>
                <div class="alert alert-success">
                    Registration successful. You can now log in.
                </div>
            <?php endif; ?>

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
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button class="btn btn-primary w-100" type="submit">Login</button>
                <p class="mt-3 text-center">
                    No account yet?
                    <a href="register.php">Register</a>
                </p>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
