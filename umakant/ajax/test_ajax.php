<?php
// ajax/test_ajax.php
require_once '../inc/auth.php';
require_once '../inc/connection.php';

$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    $stmt = $pdo->query('SELECT * FROM tests ORDER BY id DESC');
    while ($row = $stmt->fetch()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['category']) . '</td>';
        echo '<td>' . htmlspecialchars($row['price']) . '</td>';
        echo '<td>';
        echo '<button class="btn btn-sm btn-info edit-btn" data-id="' . $row['id'] . '"><i class="fas fa-edit"></i> Edit</button> ';
        echo '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $row['id'] . '"><i class="fas fa-trash"></i> Delete</button>';
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
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $sample_type = trim($_POST['sample_type'] ?? '');
    $normal_range = trim($_POST['normal_range'] ?? '');
    $unit = trim($_POST['unit'] ?? '');
    if ($id) {
        $stmt = $pdo->prepare('UPDATE tests SET name=?, category=?, description=?, price=?, sample_type=?, normal_range=?, unit=? WHERE id=?');
        $stmt->execute([$name, $category, $description, $price, $sample_type, $normal_range, $unit, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO tests (name, category, description, price, sample_type, normal_range, unit) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $category, $description, $price, $sample_type, $normal_range, $unit]);
    }
    exit('success');
}

if ($action === 'delete' && isset($_POST['id'])) {
    $stmt = $pdo->prepare('DELETE FROM tests WHERE id = ?');
    $stmt->execute([$_POST['id']]);
    exit('success');
}
