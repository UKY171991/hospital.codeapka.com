<?php
// ajax/test_category_ajax.php
require_once '../inc/auth.php';
require_once '../inc/connection.php';

$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    $stmt = $pdo->query('SELECT c.*, u.username AS added_by_username FROM test_categories c LEFT JOIN users u ON c.added_by = u.id ORDER BY c.id DESC');
    while ($row = $stmt->fetch()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['description']) . '</td>';
        echo '<td>' . htmlspecialchars($row['added_by_username'] ?? '') . '</td>';
        echo '<td>';
        echo '<button class="btn btn-sm btn-info edit-btn" data-id="' . $row['id'] . '"><i class="fas fa-edit"></i> Edit</button> ';
        echo '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $row['id'] . '"><i class="fas fa-trash"></i> Delete</button>';
        echo '</td>';
        echo '</tr>';
    }
    exit;
}

if ($action === 'get' && isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM test_categories WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $cat = $stmt->fetch();
    header('Content-Type: application/json');
    echo json_encode($cat);
    exit;
}

if ($action === 'save') {
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $added_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    if ($id) {
        $stmt = $pdo->prepare('UPDATE test_categories SET name=?, description=? WHERE id=?');
        $stmt->execute([$name, $description, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO test_categories (name, description, added_by) VALUES (?, ?, ?)');
        $stmt->execute([$name, $description, $added_by]);
    }
    exit('success');
}

if ($action === 'delete' && isset($_POST['id'])) {
    $stmt = $pdo->prepare('DELETE FROM test_categories WHERE id = ?');
    $stmt->execute([$_POST['id']]);
    exit('success');
}
