<?php
// ajax/test_category_ajax.php
require_once '../inc/auth.php';
require_once '../inc/connection.php';

$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    $stmt = $pdo->query('SELECT id, name, description, added_by, created_at, updated_at FROM test_categories ORDER BY id DESC');
    while ($row = $stmt->fetch()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['name']) . '</td>';
        echo '<td>' . htmlspecialchars(substr($row['description'] ?? 'N/A', 0, 50)) . (strlen($row['description'] ?? '') > 50 ? '...' : '') . '</td>';
        echo '<td>' . htmlspecialchars($row['added_by']) . '</td>';
        echo '<td>' . date('d M Y', strtotime($row['created_at'])) . '</td>';
        echo '<td>' . date('d M Y', strtotime($row['updated_at'])) . '</td>';
        echo '<td>';
        echo '<button class="btn btn-sm btn-info" onclick="viewCategory(' . $row['id'] . ')"><i class="fas fa-eye"></i> View</button> ';
        echo '<a href="../test-category.php?id=' . $row['id'] . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a> ';
        echo '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $row['id'] . '"><i class="fas fa-trash"></i> Delete</button>';
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
    } else {
        // Add
        $stmt = $pdo->prepare('INSERT INTO test_categories (name, description, added_by) VALUES (?, ?, ?)');
        $stmt->execute([$name, $description, $_SESSION['user_id']]);
    }
    exit('success');
}

if ($action === 'delete' && isset($_POST['id'])) {
    $stmt = $pdo->prepare('DELETE FROM test_categories WHERE id = ?');
    $stmt->execute([$_POST['id']]);
    exit('success');
}
