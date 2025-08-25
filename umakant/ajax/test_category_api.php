<?php
// ajax/test_category_api.php
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

$action = $_REQUEST['action'] ?? 'list';

if ($action === 'list') {
    // include added_by and added_by_username from users table when available
    $stmt = $pdo->query('SELECT c.id, c.name, c.description, c.added_by, u.username as added_by_username FROM categories c LEFT JOIN users u ON c.added_by = u.id ORDER BY c.id DESC');
    $rows = $stmt->fetchAll();
    json_response(['success' => true, 'data' => $rows]);
}

if ($action === 'get' && isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT c.*, u.username as added_by_username FROM categories c LEFT JOIN users u ON c.added_by = u.id WHERE c.id = ?');
    $stmt->execute([$_GET['id']]);
    $row = $stmt->fetch();
    json_response(['success' => true, 'data' => $row]);
}

if ($action === 'save') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') json_response(['success'=>false,'message'=>'Unauthorized'],401);

    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($id) {
        $stmt = $pdo->prepare('UPDATE categories SET name=?, description=?, updated_at=NOW() WHERE id=?');
        $stmt->execute([$name, $description, $id]);
        json_response(['success' => true, 'message' => 'Category updated']);
    } else {
        // set added_by from session user id when creating
        $added_by = $_SESSION['user_id'] ?? null;
        $stmt = $pdo->prepare('INSERT INTO categories (name, description, added_by, created_at) VALUES (?, ?, ?, NOW())');
        $stmt->execute([$name, $description, $added_by]);
        json_response(['success' => true, 'message' => 'Category created']);
    }
}

if ($action === 'delete' && isset($_POST['id'])) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') json_response(['success'=>false,'message'=>'Unauthorized'],401);
    $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
    $stmt->execute([$_POST['id']]);
    json_response(['success' => true, 'message' => 'Category deleted']);
}

json_response(['success'=>false,'message'=>'Invalid action'],400);
