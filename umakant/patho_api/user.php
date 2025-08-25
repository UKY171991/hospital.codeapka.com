<?php
// patho_api/user.php - public API for users (JSON)
// Mirrors ajax/user_api.php behavior but served from /patho_api/
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';

$method = $_SERVER['REQUEST_METHOD'];
action:
$action = $_REQUEST['action'] ?? 'list';

// Simple authentication: allow listing if authenticated. For public endpoints adjust as needed.
$viewerRole = $_SESSION['role'] ?? 'user';
$viewerId = $_SESSION['user_id'] ?? null;

try {
    if ($action === 'list') {
        // Public API: only return users with role = 'user'
    // Select all columns visible in the users table structure
    $stmt = $pdo->prepare("SELECT id, username, password, full_name, email, role, added_by, is_active, created_at, last_login, expire_date, updated_at FROM users WHERE role = 'user' ORDER BY id DESC");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        json_response(['success'=>true,'data'=>$rows]);
    }

    if ($action === 'get' && isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT id, username, password, full_name, email, role, added_by, is_active, created_at, last_login, expire_date, updated_at FROM users WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        if (!$row) json_response(['success'=>false,'message'=>'User not found'],404);
        if ($viewerRole === 'master' || $row['added_by'] == $viewerId || $row['id'] == $viewerId) {
            json_response(['success'=>true,'data'=>$row]);
        } else {
            json_response(['success'=>false,'message'=>'Unauthorized'],403);
        }
    }

    if ($action === 'save') {
        if (!isset($_SESSION['user_id'])) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $id = $_POST['id'] ?? '';
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'user';
        $is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 0;
        $creatorId = $_SESSION['user_id'];

        if ($id) {
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE users SET username=?, password=?, full_name=?, email=?, role=?, is_active=? WHERE id=?');
                $stmt->execute([$username, $hash, $full_name, $email, $role, $is_active, $id]);
            } else {
                $stmt = $pdo->prepare('UPDATE users SET username=?, full_name=?, email=?, role=?, is_active=? WHERE id=?');
                $stmt->execute([$username, $full_name, $email, $role, $is_active, $id]);
            }
            json_response(['success'=>true,'message'=>'User updated']);
        } else {
            $hash = password_hash($password ?: 'password', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, password, full_name, email, role, added_by, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
            $stmt->execute([$username, $hash, $full_name, $email, $role, $creatorId, $is_active]);
            json_response(['success'=>true,'message'=>'User created']);
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'master') json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success'=>true,'message'=>'User deleted']);
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);
} catch (Exception $e) {
    json_response(['success'=>false,'message'=>'Server error: '.$e->getMessage()],500);
}
