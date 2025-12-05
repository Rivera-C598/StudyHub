<?php
// config/config.php

// Detect environment (local XAMPP vs InfinityFree hosting)
if (file_exists(__DIR__ . '/config.production.php')) {
    // Production environment (InfinityFree)
    require_once __DIR__ . '/config.production.php';
} else {
    // Local development (XAMPP)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'studyhub_db');
    define('DB_USER', 'root');     // default XAMPP user
    define('DB_PASS', '');         // default XAMPP password (empty)
}
