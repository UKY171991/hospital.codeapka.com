<?php
// patho_api/login.php
header('Content-Type: application/json');
require_once '../inc/connection.php';
session_start();

$data = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

if (!$username || !$password) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Username and password required.']);
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['created_at'] = $user['created_at'];
    $_SESSION['updated_at'] = $user['updated_at'];
    $_SESSION['expire'] = $user['expire'];
    $_SESSION['added_by'] = $user['added_by'];
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'created_at' => $user['created_at'],
            'updated_at' => $user['updated_at'],
            'expire' => $user['expire'],
            'added_by' => $user['added_by']
        ]
    ]);
} else {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
}
