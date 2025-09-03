<?php
// patho_api/login.php - simple login API for patho_api consumers
// Accepts POST { username, password }
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';

try {
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method !== 'POST') {
        json_response(['success' => false, 'message' => 'Method not allowed'], 405);
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        json_response(['success' => false, 'message' => 'Username and password are required'], 400);
    }

    // Fetch user by username (include api_token)
    $stmt = $pdo->prepare('SELECT id, username, password, full_name, role, is_active, api_token FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        json_response(['success' => false, 'message' => 'Invalid credentials'], 401);
    }

    if (!$user['is_active']) {
        json_response(['success' => false, 'message' => 'User account is inactive'], 403);
    }

    // Password must be present in DB
    if (!isset($user['password']) || $user['password'] === null || $user['password'] === '') {
        // No valid password stored for this user
        json_response(['success' => false, 'message' => 'Invalid credentials'], 401);
    }

    // Verify password robustly:
    // 1) password_hash() / password_verify() (bcrypt/argon)
    // 2) legacy MD5 (32 chars) or SHA1 (40 chars)
    // 3) raw plaintext fallback (not recommended)
    $stored = $user['password'];
    $passOk = false;

    // bcrypt/argon style hashes usually start with $2y$ or $2a$ or $argon$
    if (is_string($stored) && (strpos($stored, '$2y$') === 0 || strpos($stored, '$2a$') === 0 || strpos($stored, '$argon') === 0 || password_needs_rehash($stored, PASSWORD_DEFAULT) || password_verify($password, $stored))) {
        // Use password_verify when possible; password_needs_rehash check above is just to ensure format is compatible
        if (password_verify($password, $stored)) {
            $passOk = true;
        }
    }

    // Common legacy hash formats
    if (!$passOk && is_string($stored)) {
        $len = strlen($stored);
        if ($len === 32) { // likely MD5
            if (hash_equals($stored, md5($password))) $passOk = true;
        } elseif ($len === 40) { // likely SHA1
            if (hash_equals($stored, sha1($password))) $passOk = true;
        }
    }

    // Last resort: direct comparison (only if stored value equals provided password)
    if (!$passOk) {
        if (hash_equals((string)$stored, (string)$password)) {
            $passOk = true;
        }
    }

    if (!$passOk) json_response(['success' => false, 'message' => 'Invalid credentials'], 401);

    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    // update last_login
    $update = $pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = ?');
    $update->execute([$user['id']]);

    // Return minimal user info
    $safeUser = [
        'id' => $user['id'],
        'username' => $user['username'],
        'full_name' => $user['full_name'] ?? null,
        'role' => $user['role']
    ];

    // Ensure an API token exists for this user so scripts can use token-based auth.
    // We return api_token in the response. If the DB row already contains one, keep it.
    if (empty($user['api_token'])) {
        try {
            $newToken = bin2hex(random_bytes(32));
            $upd = $pdo->prepare('UPDATE users SET api_token = ? WHERE id = ?');
            $upd->execute([$newToken, $user['id']]);
            $safeUser['api_token'] = $newToken;
        } catch (Exception $e) {
            // If token generation or update fails, ignore and don't return a token (login still succeeds)
        }
    } else {
        $safeUser['api_token'] = $user['api_token'];
    }

    json_response(['success' => true, 'message' => 'Login successful', 'user' => $safeUser]);

} catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Server error: '.$e->getMessage()], 500);
}
