<?php
// ajax/test_category_api.php
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

$action = $_REQUEST['action'] ?? 'list';

if ($action === 'list') {
    $stmt = $pdo->query('SELECT id, name, description, created_at FROM categories ORDER BY id DESC');
    $rows = $stmt->fetchAll();
    json_response(['success' => true, 'data' => $rows]);
}

if ($action === 'get' && isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
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
        $stmt = $pdo->prepare('UPDATE categories SET name=?, description=? WHERE id=?');
        $stmt->execute([$name, $description, $id]);
        json_response(['success' => true, 'message' => 'Category updated']);
    } else {
        $stmt = $pdo->prepare('INSERT INTO categories (name, description, created_at) VALUES (?, ?, NOW())');
        $stmt->execute([$name, $description]);
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
