<?php
// patho_api/test_category.php - public API for test categories (JSON)
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';

$action = $_REQUEST['action'] ?? 'list';

try {
    if ($action === 'list') {
        $stmt = $pdo->query('SELECT c.id, c.name, c.description, c.added_by, u.username as added_by_username FROM categories c LEFT JOIN users u ON c.added_by = u.id ORDER BY c.id DESC');
        $rows = $stmt->fetchAll();
        json_response(['success' => true, 'data' => $rows]);
    }

    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT c.*, u.username as added_by_username FROM categories c LEFT JOIN users u ON c.added_by = u.id WHERE c.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        if (!$row) json_response(['success'=>false,'message'=>'Category not found'],404);
        json_response(['success' => true, 'data' => $row]);
    }

    if ($action === 'save') {
        // allow master and admin to create/update categories
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);

        $id = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($name === '') json_response(['success'=>false,'message'=>'Name is required'],400);

        if ($id) {
            $stmt = $pdo->prepare('UPDATE categories SET name=?, description=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$name, $description, $id]);
            json_response(['success' => true, 'message' => 'Category updated']);
        } else {
            $added_by = $_SESSION['user_id'] ?? null;
            if (empty($added_by) && !empty($PATHO_API_DEFAULT_USER_ID)) {
                $added_by = (int)$PATHO_API_DEFAULT_USER_ID;
            }
            $data = ['name'=>$name, 'description'=>$description, 'added_by'=>$added_by];
            $unique = ['name'=>$name];
            $res = upsert_or_skip($pdo, 'categories', $unique, $data);
            json_response(['success' => true, 'message' => 'Category '.$res['action'],'id'=>$res['id']]);
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success' => true, 'message' => 'Category deleted']);
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);
} catch (Throwable $e) {
    json_response(['success'=>false,'message'=>'Server error: '.$e->getMessage()],500);
}
