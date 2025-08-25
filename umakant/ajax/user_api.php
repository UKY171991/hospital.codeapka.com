<?php
// ajax/user_api.php - CRUD for users via AJAX
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

$action = $_REQUEST['action'] ?? 'list';

if ($action === 'list') {
    $stmt = $pdo->query('SELECT id, username, email, full_name, role, is_active, last_login FROM users ORDER BY id DESC');
    $rows = $stmt->fetchAll();
    json_response(['success' => true, 'data' => $rows]);
}

if ($action === 'get' && isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT id, username, email, full_name, role, is_active, last_login FROM users WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $row = $stmt->fetch();
    json_response(['success' => true, 'data' => $row]);
}

if ($action === 'save') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') json_response(['success'=>false,'message'=>'Unauthorized'],401);

    $id = $_POST['id'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'user';
    $is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 0;

    if ($id) {
        // update (only change password if provided)
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET username=?, password=?, full_name=?, email=?, role=?, is_active=? WHERE id=?');
            $stmt->execute([$username, $hash, $full_name, $email, $role, $is_active, $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET username=?, full_name=?, email=?, role=?, is_active=? WHERE id=?');
            $stmt->execute([$username, $full_name, $email, $role, $is_active, $id]);
        }
        json_response(['success' => true, 'message' => 'User updated']);
    } else {
        // create
        $hash = password_hash($password ?: 'password', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, password, full_name, email, role, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$username, $hash, $full_name, $email, $role, $is_active]);
        json_response(['success' => true, 'message' => 'User created']);
    }
}

if ($action === 'delete' && isset($_POST['id'])) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') json_response(['success'=>false,'message'=>'Unauthorized'],401);
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$_POST['id']]);
    json_response(['success' => true, 'message' => 'User deleted']);
}

json_response(['success' => false, 'message' => 'Invalid action'], 400);
