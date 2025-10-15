<?php
// ajax/main_test_category_api.php
try {
    require_once __DIR__ . '/../inc/connection.php';
} catch (Exception $e) {
    // If database connection fails, provide fallback response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Database connection error. Please ensure MySQL is running.',
        'error' => $e->getMessage()
    ]);
    exit;
}

require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

$action = $_REQUEST['action'] ?? 'list';
$table_name = 'main_test_categories';

if ($action === 'list') {
    $stmt = $pdo->query("SELECT c.id, c.name, c.description, c.added_by, u.username as added_by_username, 
        (SELECT COUNT(*) FROM categories t WHERE t.main_category_id = c.id) as test_count
        FROM {$table_name} c 
        LEFT JOIN users u ON c.added_by = u.id 
        ORDER BY c.id DESC");
    $rows = $stmt->fetchAll();
    
    foreach ($rows as $index => &$row) {
        $row['sno'] = $index + 1;
    }
    
    json_response(['success' => true, 'data' => $rows]);
}

if ($action === 'get' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT c.*, u.username as added_by_username FROM {$table_name} c LEFT JOIN users u ON c.added_by = u.id WHERE c.id = ?");
    $stmt->execute([$_GET['id']]);
    $row = $stmt->fetch();
    json_response(['success' => true, 'data' => $row]);
}

if ($action === 'save') {
    if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);

    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === '') {
        json_response(['success' => false, 'message' => 'Name is required'], 400);
    }

    if ($id) {
        try {
            $stmt = $pdo->prepare("UPDATE {$table_name} SET name=?, description=?, updated_at=NOW() WHERE id=?");
            $stmt->execute([$name, $description, $id]);
            json_response(['success' => true, 'message' => 'Category updated']);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    } else {
        $added_by = $_SESSION['user_id'] ?? null;
        try {
            $stmt = $pdo->prepare("INSERT INTO {$table_name} (name, description, added_by, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$name, $description, $added_by]);
            json_response(['success' => true, 'message' => 'Category created']);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }
}

if ($action === 'delete' && isset($_POST['id'])) {
    if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
    try {
    $stmt = $pdo->prepare("DELETE FROM {$table_name} WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        json_response(['success' => true, 'message' => 'Category deleted']);
    } catch (PDOException $e) {
        json_response(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
}

json_response(['success'=>false,'message'=>'Invalid action'],400);
