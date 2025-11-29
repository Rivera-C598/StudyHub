<?php
// includes/security.php

/**
 * Hash a plain text password using a recommended algorithm.
 * Prefers Argon2id if available, otherwise falls back to PASSWORD_DEFAULT (usually bcrypt).
 */
function hashPassword(string $plainPassword): string {
    if ($plainPassword === '') {
        throw new InvalidArgumentException('Password cannot be empty.');
    }

    if (defined('PASSWORD_ARGON2ID')) {
        return password_hash($plainPassword, PASSWORD_ARGON2ID);
    }

    // Fallback (typically bcrypt)
    return password_hash($plainPassword, PASSWORD_DEFAULT);
}

/**
 * Verify a plain text password against a stored hash.
 */
function verifyPassword(string $plainPassword, string $hash): bool {
    if ($plainPassword === '' || $hash === '') {
        return false;
    }
    return password_verify($plainPassword, $hash);
}

/**
 * Generate a CSRF token and store it in the session.
 */
function generateCsrfToken(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token from POST request.
 */
function validateCsrfToken(): bool {
    $token = $_POST['csrf_token'] ?? '';
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Validate resource type enum.
 */
function isValidResourceType(string $type): bool {
    return in_array($type, ['note', 'link', 'task'], true);
}

/**
 * Validate status enum.
 */
function isValidStatus(string $status): bool {
    return in_array($status, ['todo', 'in_progress', 'done'], true);
}

/**
 * Rate limiting for login attempts.
 */
function checkRateLimit(string $identifier, int $maxAttempts = 5, int $timeWindow = 300): bool {
    if (!isset($_SESSION['rate_limit'])) {
        $_SESSION['rate_limit'] = [];
    }

    $now = time();
    $key = 'login_' . md5($identifier);

    // Clean old entries
    if (isset($_SESSION['rate_limit'][$key])) {
        $_SESSION['rate_limit'][$key] = array_filter(
            $_SESSION['rate_limit'][$key],
            fn($timestamp) => ($now - $timestamp) < $timeWindow
        );
    } else {
        $_SESSION['rate_limit'][$key] = [];
    }

    // Check if limit exceeded
    if (count($_SESSION['rate_limit'][$key]) >= $maxAttempts) {
        return false;
    }

    // Record attempt
    $_SESSION['rate_limit'][$key][] = $now;
    return true;
}
