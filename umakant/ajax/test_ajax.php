<?php
// ajax/test_ajax.php
require_once '../inc/auth.php';
require_once '../inc/connection.php';

$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    $stmt = $pdo->query('SELECT id, test_name, category, price, unit, reference_range, min_value, max_value, method, added_by, created_at FROM tests ORDER BY id DESC');
    while ($row = $stmt->fetch()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['test_name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['category'] ?? 'N/A') . '</td>';
        echo '<td>₹' . htmlspecialchars($row['price']) . '</td>';
        echo '<td>' . htmlspecialchars($row['unit'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($row['reference_range'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($row['min_value'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($row['max_value'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($row['method'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($row['added_by']) . '</td>';
        echo '<td>' . date('d M Y', strtotime($row['created_at'])) . '</td>';
        echo '<td>';
        echo '<button class="btn btn-sm btn-info" onclick="viewTest(' . $row['id'] . ')"><i class="fas fa-eye"></i> View</button> ';
        echo '<button class="btn btn-sm btn-warning" onclick="editTest(' . $row['id'] . ')"><i class="fas fa-edit"></i> Edit</button> ';
        echo '<button class="btn btn-sm btn-danger" onclick="deleteTest(' . $row['id'] . ')"><i class="fas fa-trash"></i> Delete</button>';
        echo '</td>';
        echo '</tr>';
    }
    exit;
}

if ($action === 'get' && isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM tests WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $test = $stmt->fetch();
    header('Content-Type: application/json');
    echo json_encode($test);
    exit;
}

if ($action === 'save') {
    $id = $_POST['id'] ?? '';
    $test_name = trim($_POST['test_name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $unit = trim($_POST['unit'] ?? '');
    $reference_range = trim($_POST['reference_range'] ?? '');
    $min_value = trim($_POST['min_value'] ?? '');
    $max_value = trim($_POST['max_value'] ?? '');
    $method = trim($_POST['method'] ?? '');
    
    if ($id) {
        // Edit
        $stmt = $pdo->prepare('UPDATE tests SET test_name=?, category=?, description=?, price=?, unit=?, reference_range=?, min_value=?, max_value=?, method=? WHERE id=?');
        $stmt->execute([$test_name, $category, $description, $price, $unit, $reference_range, $min_value, $max_value, $method, $id]);
        $message = 'Test updated successfully!';
    } else {
        // Add
        $stmt = $pdo->prepare('INSERT INTO tests (test_name, category, description, price, unit, reference_range, min_value, max_value, method, added_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$test_name, $category, $description, $price, $unit, $reference_range, $min_value, $max_value, $method, $_SESSION['user_id']]);
        $message = 'Test added successfully!';
    }
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => $message]);
    exit;
}

if ($action === 'delete' && isset($_POST['id'])) {
    try {
        $stmt = $pdo->prepare('DELETE FROM tests WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Test deleted successfully!']);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Error deleting test: ' . $e->getMessage()]);
    }
    exit;
}
