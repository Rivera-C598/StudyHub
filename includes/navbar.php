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
            <?php if (isLoggedIn()): ?>
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">📊 Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="calendar.php">📅 Calendar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="templates.php">📋 Templates</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" 
                           data-bs-toggle="dropdown">
                            📥 Export
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li><a class="dropdown-item" href="export.php?format=csv">CSV Format</a></li>
                            <li><a class="dropdown-item" href="export.php?format=json">JSON Format</a></li>
                            <li><a class="dropdown-item" href="export.php?format=pdf" target="_blank">PDF Format</a></li>
                        </ul>
                    </li>
                </ul>
            <?php endif; ?>
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
