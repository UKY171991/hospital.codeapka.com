<?php
// ajax/doctor_ajax.php
require_once '../inc/auth.php';
require_once '../inc/connection.php';

$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    $stmt = $pdo->query('SELECT id, name, qualification, specialization, phone, email, registration_no, added_by, created_at FROM doctors ORDER BY id DESC');
    while ($row = $stmt->fetch()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['qualification'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($row['specialization'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($row['phone'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($row['email'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($row['registration_no'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($row['added_by']) . '</td>';
        echo '<td>' . date('d M Y', strtotime($row['created_at'])) . '</td>';
        echo '<td>';
        echo '<button class="btn btn-sm btn-info" onclick="viewDoctor(' . $row['id'] . ')"><i class="fas fa-eye"></i> View</button> ';
        echo '<a href="../doctor.php?id=' . $row['id'] . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a> ';
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
        // Edit
        $stmt = $pdo->prepare('UPDATE doctors SET name=?, qualification=?, specialization=?, phone=?, email=?, address=?, registration_no=? WHERE id=?');
        $stmt->execute([$name, $qualification, $specialization, $phone, $email, $address, $registration_no, $id]);
    } else {
        // Add
        $stmt = $pdo->prepare('INSERT INTO doctors (name, qualification, specialization, phone, email, address, registration_no, added_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $qualification, $specialization, $phone, $email, $address, $registration_no, $_SESSION['user_id']]);
    }
    exit('success');
}

if ($action === 'delete' && isset($_POST['id'])) {
    $stmt = $pdo->prepare('DELETE FROM doctors WHERE id = ?');
    $stmt->execute([$_POST['id']]);
    exit('success');
}
