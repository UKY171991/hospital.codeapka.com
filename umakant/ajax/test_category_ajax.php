<?php
// ajax/test_category_ajax.php
require_once '../inc/auth.php';
require_once '../inc/connection.php';

$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    $stmt = $pdo->query('SELECT id, name, description, added_by, created_at FROM test_categories ORDER BY id DESC');
    while ($row = $stmt->fetch()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['name']) . '</td>';
        echo '<td>' . htmlspecialchars(substr($row['description'] ?? '', 0, 50)) . (strlen($row['description'] ?? '') > 50 ? '...' : '') . '</td>';
        echo '<td>' . htmlspecialchars($row['added_by']) . '</td>';
        echo '<td>' . date('d M Y', strtotime($row['created_at'])) . '</td>';
        echo '<td>';
        echo '<button class="btn btn-sm btn-info" onclick="viewCategory(' . $row['id'] . ')"><i class="fas fa-eye"></i> View</button> ';
        echo '<button class="btn btn-sm btn-warning" onclick="editCategory(' . $row['id'] . ')"><i class="fas fa-edit"></i> Edit</button> ';
        echo '<button class="btn btn-sm btn-danger" onclick="deleteCategory(' . $row['id'] . ')"><i class="fas fa-trash"></i> Delete</button>';
        echo '</td>';
        echo '</tr>';
    }
    exit;
}

if ($action === 'get' && isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM test_categories WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $category = $stmt->fetch();
    header('Content-Type: application/json');
    echo json_encode($category);
    exit;
}

if ($action === 'save') {
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    if ($id) {
        // Edit
        $stmt = $pdo->prepare('UPDATE test_categories SET name=?, description=? WHERE id=?');
        $stmt->execute([$name, $description, $id]);
        $message = 'Test category updated successfully!';
    } else {
        // Add
        $stmt = $pdo->prepare('INSERT INTO test_categories (name, description, added_by) VALUES (?, ?, ?)');
        $stmt->execute([$name, $description, $_SESSION['user_id']]);
        $message = 'Test category added successfully!';
    }
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => $message]);
    exit;
}

if ($action === 'delete' && isset($_POST['id'])) {
    try {
        $stmt = $pdo->prepare('DELETE FROM test_categories WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Test category deleted successfully!']);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Error deleting test category: ' . $e->getMessage()]);
    }
    exit;
}
