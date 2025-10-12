<?php
// patho_api/login.php - simple login API for patho_api consumers
// Accepts POST { username, password }
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_response(['success' => false, 'message' => 'Method not allowed'], 405);
    }

    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    
    $username = trim($input['username'] ?? '');
    $password = $input['password'] ?? '';

    if (empty($username) || empty($password)) {
        json_response(['success' => false, 'message' => 'Username and password are required'], 400);
    }

    $stmt = $pdo->prepare('SELECT id, username, password, full_name, role, is_active, api_token FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        json_response(['success' => false, 'message' => 'Invalid credentials'], 401);
    }

    if (!$user['is_active']) {
        json_response(['success' => false, 'message' => 'User account is inactive'], 403);
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    $pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = ?')->execute([$user['id']]);

    $safeUser = [
        'id' => $user['id'],
        'username' => $user['username'],
        'full_name' => $user['full_name'] ?? null,
        'role' => $user['role']
    ];

    if (empty($user['api_token'])) {
        $newToken = bin2hex(random_bytes(32));
        $pdo->prepare('UPDATE users SET api_token = ? WHERE id = ?')->execute([$newToken, $user['id']]);
        $safeUser['api_token'] = $newToken;
    } else {
        $safeUser['api_token'] = $user['api_token'];
    }

    json_response(['success' => true, 'message' => 'Login successful', 'user' => $safeUser]);

} catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Server error: '.$e->getMessage()], 500);
}