# Security Documentation

This document outlines the security measures implemented in StudyHub.

## Overview

StudyHub implements multiple layers of security to protect user data and prevent common web vulnerabilities.

---

## Authentication & Authorization

### Password Security

**Hashing Algorithm:**
- Primary: Argon2id (if available)
- Fallback: bcrypt (PASSWORD_DEFAULT)
- Minimum password length: 8 characters

**Implementation:**
```php
// Password hashing
$hash = hashPassword($plainPassword);

// Password verification
$isValid = verifyPassword($plainPassword, $hash);
```

### Session Management

**Security Features:**
- Custom session name: `STUDYHUBSESSID`
- Session regeneration on login (prevents session fixation)
- Secure session destruction on logout
- HttpOnly cookies (when configured)

**Session Data:**
- `user_id`: User's database ID
- `username`: User's username
- `csrf_token`: CSRF protection token
- `rate_limit`: Login attempt tracking

---

## CSRF Protection

All forms include CSRF tokens to prevent Cross-Site Request Forgery attacks.

**Implementation:**
```php
// Generate token (in form)
<input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

// Validate token (on submission)
if (!validateCsrfToken()) {
    $errors[] = "Invalid security token.";
}
```

**Token Properties:**
- 64 characters (32 bytes hex-encoded)
- Stored in session
- Validated using timing-safe comparison

---

## SQL Injection Prevention

All database queries use prepared statements with parameter binding.

**Example:**
```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u");
$stmt->execute([':u' => $username]);
```

**Never used:**
- String concatenation in queries
- Direct variable interpolation
- Unescaped user input

---

## XSS Protection

All user-generated content is escaped before output.

**Implementation:**
```php
<?= htmlspecialchars($userInput) ?>
```

**Protected contexts:**
- HTML content
- HTML attributes
- JavaScript strings (when applicable)

---

## Rate Limiting

Login attempts are rate-limited to prevent brute force attacks.

**Configuration:**
- Maximum attempts: 5
- Time window: 300 seconds (5 minutes)
- Tracked per username

**Implementation:**
```php
if (!checkRateLimit($username)) {
    $errors[] = "Too many login attempts. Please try again in 5 minutes.";
}
```

---

## Input Validation

### Server-Side Validation

All user input is validated on the server:

**User Registration:**
- Username: Required, trimmed
- Email: Required, valid email format
- Password: Required, minimum 8 characters
- Password confirmation: Must match

**Resource Management:**
- Title: Required, trimmed
- Subject: Required, trimmed
- Type: Enum validation (note/link/task)
- URL: Optional, valid URL format
- Status: Enum validation (todo/in_progress/done)

**Validation Functions:**
```php
isValidResourceType($type);  // Validates resource type
isValidStatus($status);       // Validates status
filter_var($email, FILTER_VALIDATE_EMAIL);  // Email validation
```

---

## Database Security

### Connection Security

**Configuration:**
```php
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
```

### Foreign Key Constraints

Resources are linked to users with CASCADE delete:
```sql
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
```

### Indexes

Indexes on frequently queried columns improve performance and prevent timing attacks:
- `users.username`
- `users.email`
- `resources.user_id`
- `resources.status`

---

## Error Handling

### Production Best Practices

**Implemented:**
- Generic error messages to users
- Detailed errors logged server-side
- No database details exposed

**Example:**
```php
try {
    // Database operation
} catch (PDOException $e) {
    $errors[] = "An error occurred. Please try again later.";
    error_log("Error: " . $e->getMessage());
}
```

### Error Logging

Enable error logging in production:
```php
// php.ini or .htaccess
log_errors = On
error_log = /path/to/error.log
display_errors = Off
```

---

## File Upload Security

**Current Status:** File uploads are not implemented.

**If implementing in the future:**
- Validate file types (whitelist)
- Check file size limits
- Rename uploaded files
- Store outside web root
- Scan for malware

---

## HTTPS Recommendations

For production deployment:

1. **Obtain SSL Certificate:**
   - Let's Encrypt (free)
   - Commercial CA

2. **Configure Web Server:**
   ```apache
   # Apache .htaccess
   RewriteEngine On
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

3. **Update Session Settings:**
   ```php
   ini_set('session.cookie_secure', '1');
   ini_set('session.cookie_httponly', '1');
   ini_set('session.cookie_samesite', 'Strict');
   ```

---

## Security Headers

Recommended headers for production (configure in web server):

```apache
# Apache
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
Header set Referrer-Policy "strict-origin-when-cross-origin"
Header set Content-Security-Policy "default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline';"
```

---

## Vulnerability Checklist

- [x] SQL Injection - Protected (prepared statements)
- [x] XSS - Protected (output escaping)
- [x] CSRF - Protected (tokens)
- [x] Session Fixation - Protected (regeneration)
- [x] Brute Force - Protected (rate limiting)
- [x] Password Storage - Secure (Argon2id/bcrypt)
- [x] Authorization - Implemented (ownership checks)
- [ ] HTTPS - Recommended for production
- [ ] Security Headers - Recommended for production
- [ ] File Upload - Not implemented

---

## Reporting Security Issues

If you discover a security vulnerability:

1. Do not open a public issue
2. Contact the development team privately
3. Provide detailed information about the vulnerability
4. Allow time for a fix before public disclosure

---

## Regular Security Maintenance

**Recommended practices:**
- Keep PHP updated
- Update dependencies (Bootstrap, etc.)
- Review error logs regularly
- Audit user permissions
- Test authentication flows
- Monitor for suspicious activity
- Backup database regularly
