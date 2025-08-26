<?php
// ajax/test_api.php - CRUD for tests
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

try {
    $action = $_REQUEST['action'] ?? 'list';

    if ($action === 'list') {
        // return reference_range as normal_range for older UI compatibility
        $stmt = $pdo->query("SELECT t.id, tc.name as category_name, t.name, t.description, t.price, t.reference_range as normal_range, t.unit FROM tests t LEFT JOIN categories tc ON t.category_id = tc.id ORDER BY t.id DESC");
        $rows = $stmt->fetchAll();
        json_response(['success'=>true,'data'=>$rows]);
    }

    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT * FROM tests WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        json_response(['success'=>true,'data'=>$row]);
    }

    if ($action === 'save') {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $id = $_POST['id'] ?? '';
        $category_id = $_POST['category_id'] ?? null;
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = $_POST['price'] ?? 0;
        $unit = trim($_POST['unit'] ?? '');
        $specimen = trim($_POST['specimen'] ?? '');
        $default_result = trim($_POST['default_result'] ?? '');
        $reference_range = trim($_POST['reference_range'] ?? '');
        $min = $_POST['min'] ?? null;
        $max = $_POST['max'] ?? null;
        $sub_heading = $_POST['sub_heading'] ?? 0;
        $test_code = trim($_POST['test_code'] ?? '');
        $method = trim($_POST['method'] ?? '');
        $print_new_page = $_POST['print_new_page'] ?? 0;
        $shortcut = trim($_POST['shortcut'] ?? '');

        if ($id) {
            $stmt = $pdo->prepare('UPDATE tests SET category_id=?, name=?, description=?, price=?, unit=?, specimen=?, default_result=?, reference_range=?, min=?, max=?, sub_heading=?, test_code=?, method=?, print_new_page=?, shortcut=? WHERE id=?');
            $stmt->execute([$category_id, $name, $description, $price, $unit, $specimen, $default_result, $reference_range, $min, $max, $sub_heading, $test_code, $method, $print_new_page, $shortcut, $id]);
            json_response(['success'=>true,'message'=>'Test updated']);
        } else {
            $added_by = $_SESSION['user_id'] ?? null;
            $stmt = $pdo->prepare('INSERT INTO tests (category_id, name, description, price, unit, specimen, default_result, reference_range, min, max, sub_heading, test_code, method, print_new_page, shortcut, added_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $stmt->execute([$category_id, $name, $description, $price, $unit, $specimen, $default_result, $reference_range, $min, $max, $sub_heading, $test_code, $method, $print_new_page, $shortcut, $added_by]);
            json_response(['success'=>true,'message'=>'Test created']);
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $stmt = $pdo->prepare('DELETE FROM tests WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success'=>true,'message'=>'Test deleted']);
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);
} catch (Throwable $e) {
    json_response(['success'=>false,'message'=>$e->getMessage()],500);
}
