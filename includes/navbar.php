<?php
// includes/navbar.php
require_once __DIR__ . '/auth.php'; // make sure auth helpers are available
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">StudyHub</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarContent" aria-controls="navbarContent"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <div class="ms-auto d-flex align-items-center">
                <?php if (isLoggedIn()): ?>
                    <span class="navbar-text me-3">
                        Hello, <?= htmlspecialchars($_SESSION['username']) ?>
                    </span>
                    <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light btn-sm me-2">Login</a>
                    <a href="register.php" class="btn btn-primary btn-sm">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
