<?php
// ajax/doctor_ajax.php
require_once '../inc/auth.php';
require_once '../inc/connection.php';

$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    $stmt = $pdo->query('SELECT d.*, u.username AS added_by_username FROM doctors d LEFT JOIN users u ON d.added_by = u.id ORDER BY d.id DESC');
    while ($row = $stmt->fetch()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['specialization']) . '</td>';
        echo '<td>' . htmlspecialchars($row['email']) . '</td>';
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
    $stmt = $pdo->prepare('SELECT * FROM doctors WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $doctor = $stmt->fetch();
    header('Content-Type: application/json');
    echo json_encode($doctor);
    exit;
}

if ($action === 'save') {
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $qualification = trim($_POST['qualification'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $registration_no = trim($_POST['registration_no'] ?? '');
    if ($id) {
        $stmt = $pdo->prepare('UPDATE doctors SET name=?, qualification=?, specialization=?, phone=?, email=?, address=?, registration_no=? WHERE id=?');
        $stmt->execute([$name, $qualification, $specialization, $phone, $email, $address, $registration_no, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO doctors (name, qualification, specialization, phone, email, address, registration_no) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $qualification, $specialization, $phone, $email, $address, $registration_no]);
    }
    exit('success');
}

if ($action === 'delete' && isset($_POST['id'])) {
    $stmt = $pdo->prepare('DELETE FROM doctors WHERE id = ?');
    $stmt->execute([$_POST['id']]);
    exit('success');
}
