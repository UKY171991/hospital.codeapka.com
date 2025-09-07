<?php
// ajax/test_category_api.php
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
// Determine which categories table exists - Default to 'categories' based on schema
$categories_table = 'categories';
try{
    $stmt = $pdo->query("SHOW TABLES LIKE 'test_categories'");
    if($stmt->fetch()){
        $categories_table = 'test_categories';
    } else {
        // Verify categories table exists
        $stmt2 = $pdo->query("SHOW TABLES LIKE 'categories'");
        if(!$stmt2->fetch()) {
            $categories_table = 'test_categories'; // fallback
        }
    }
}catch(Throwable $e){
    $categories_table = 'categories';
}

if ($action === 'list') {
    // Support DataTables format with sequential numbering
    $stmt = $pdo->query("SELECT c.id, c.name, c.description, c.added_by, u.username as added_by_username, 
        (SELECT COUNT(*) FROM tests t WHERE t.category_id = c.id) as test_count
        FROM {$categories_table} c 
        LEFT JOIN users u ON c.added_by = u.id 
        ORDER BY c.id DESC");
    $rows = $stmt->fetchAll();
    
    // Add sequential numbering
    foreach ($rows as $index => &$row) {
        $row['sno'] = $index + 1;
    }
    
    json_response(['success' => true, 'data' => $rows]);
}

if ($action === 'get' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT c.*, u.username as added_by_username FROM {$categories_table} c LEFT JOIN users u ON c.added_by = u.id WHERE c.id = ?");
    $stmt->execute([$_GET['id']]);
    $row = $stmt->fetch();
    json_response(['success' => true, 'data' => $row]);
}

if ($action === 'save') {
    // allow master and admin to create/update categories
    if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);

    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // basic validation
    if ($name === '') {
        json_response(['success' => false, 'message' => 'Name is required'], 400);
    }

    if ($id) {
        try {
            $stmt = $pdo->prepare("UPDATE {$categories_table} SET name=?, description=?, updated_at=NOW() WHERE id=?");
            $stmt->execute([$name, $description, $id]);
            json_response(['success' => true, 'message' => 'Category updated']);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    } else {
        // set added_by from session user id when creating
        $added_by = $_SESSION['user_id'] ?? null;
        try {
            $stmt = $pdo->prepare("INSERT INTO {$categories_table} (name, description, added_by, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$name, $description, $added_by]);
            json_response(['success' => true, 'message' => 'Category created']);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }
}

if ($action === 'delete' && isset($_POST['id'])) {
    // allow master and admin to delete categories
    if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
    try {
    $stmt = $pdo->prepare("DELETE FROM {$categories_table} WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        json_response(['success' => true, 'message' => 'Category deleted']);
    } catch (PDOException $e) {
        json_response(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
    }
}

json_response(['success'=>false,'message'=>'Invalid action'],400);
