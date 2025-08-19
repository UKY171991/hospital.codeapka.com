<?php
require_once '../inc/auth.php';
require_once '../inc/connection.php';
$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    $stmt = $pdo->query('SELECT * FROM patients ORDER BY id DESC');
    while ($row = $stmt->fetch()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['client_name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['age']) . ' ' . htmlspecialchars($row['age_unit']) . '</td>';
        echo '<td>' . htmlspecialchars($row['gender']) . '</td>';
        echo '<td>' . htmlspecialchars($row['mobile_number']) . '</td>';
        echo '<td>' . htmlspecialchars($row['uhid']) . '</td>';
        echo '<td>';
        echo '<button class="btn btn-sm btn-info edit-btn" data-id="' . $row['id'] . '"><i class="fas fa-edit"></i> Edit</button> ';
        echo '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $row['id'] . '"><i class="fas fa-trash"></i> Delete</button>';
        echo '</td>';
        echo '</tr>';
    }
    exit;
}

if ($action === 'get' && isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM patients WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $patient = $stmt->fetch();
    header('Content-Type: application/json');
    echo json_encode($patient);
    exit;
}

if ($action === 'save') {
    $id = $_POST['id'] ?? '';
    $client_name = trim($_POST['client_name'] ?? '');
    $mobile_number = trim($_POST['mobile_number'] ?? '');
    $father_or_husband = trim($_POST['father_or_husband'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $age = trim($_POST['age'] ?? '');
    $age_unit = trim($_POST['age_unit'] ?? '');
    $uhid = trim($_POST['uhid'] ?? '');
    $added_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    if ($id) {
        $stmt = $pdo->prepare('UPDATE patients SET client_name=?, mobile_number=?, father_or_husband=?, address=?, gender=?, age=?, age_unit=?, uhid=? WHERE id=?');
        $stmt->execute([$client_name, $mobile_number, $father_or_husband, $address, $gender, $age, $age_unit, $uhid, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO patients (client_name, mobile_number, father_or_husband, address, gender, age, age_unit, uhid, added_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$client_name, $mobile_number, $father_or_husband, $address, $gender, $age, $age_unit, $uhid, $added_by]);
    }
    exit('success');
}

if ($action === 'delete' && isset($_POST['id'])) {
    $stmt = $pdo->prepare('DELETE FROM patients WHERE id = ?');
    $stmt->execute([$_POST['id']]);
    exit('success');
}
