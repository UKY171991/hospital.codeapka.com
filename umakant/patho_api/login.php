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

    // Fetch user by username
    $stmt = $pdo->prepare('SELECT id, username, password, full_name, role, is_active FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        json_response(['success' => false, 'message' => 'Invalid credentials'], 401);
    }

    if (!$user['is_active']) {
        json_response(['success' => false, 'message' => 'User account is inactive'], 403);
    }

    // Verify password (supports both plain (legacy) and password_hash)
    $passOk = false;
    if (password_verify($password, $user['password'])) {
        $passOk = true;
    } else {
        // legacy: compare raw (not recommended) — only if hashes don't match
        if ($password === $user['password']) $passOk = true;
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

    json_response(['success' => true, 'message' => 'Login successful', 'user' => $safeUser]);

} catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Server error: '.$e->getMessage()], 500);
}
