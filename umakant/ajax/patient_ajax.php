<?php
// ajax/patient_ajax.php
require_once '../inc/auth.php';
require_once '../inc/connection.php';

$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    $stmt = $pdo->query('SELECT id, client_name, mobile_number, father_or_husband, gender, age, age_unit, uhid, added_by, created_at FROM patients ORDER BY id DESC');
    while ($row = $stmt->fetch()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['client_name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['mobile_number']) . '</td>';
        echo '<td>' . htmlspecialchars($row['father_or_husband'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($row['gender'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($row['age'] ?? 'N/A') . ' ' . htmlspecialchars($row['age_unit'] ?? '') . '</td>';
        echo '<td>' . htmlspecialchars($row['uhid'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($row['added_by']) . '</td>';
        echo '<td>' . date('d M Y', strtotime($row['created_at'])) . '</td>';
        echo '<td>';
        echo '<button class="btn btn-sm btn-info" onclick="viewPatient(' . $row['id'] . ')"><i class="fas fa-eye"></i> View</button> ';
        echo '<button class="btn btn-sm btn-warning" onclick="editPatient(' . $row['id'] . ')"><i class="fas fa-edit"></i> Edit</button> ';
        echo '<button class="btn btn-sm btn-danger" onclick="deletePatient(' . $row['id'] . ')"><i class="fas fa-trash"></i> Delete</button>';
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
    
    if ($id) {
        // Edit
        $stmt = $pdo->prepare('UPDATE patients SET client_name=?, mobile_number=?, father_or_husband=?, address=?, gender=?, age=?, age_unit=?, uhid=? WHERE id=?');
        $stmt->execute([$client_name, $mobile_number, $father_or_husband, $address, $gender, $age, $age_unit, $uhid, $id]);
        $message = 'Patient updated successfully!';
    } else {
        // Add
        $stmt = $pdo->prepare('INSERT INTO patients (client_name, mobile_number, father_or_husband, address, gender, age, age_unit, uhid, added_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$client_name, $mobile_number, $father_or_husband, $address, $gender, $age, $age_unit, $uhid, $_SESSION['user_id']]);
        $message = 'Patient added successfully!';
    }
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => $message]);
    exit;
}

if ($action === 'delete' && isset($_POST['id'])) {
    try {
        $stmt = $pdo->prepare('DELETE FROM patients WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Patient deleted successfully!']);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Error deleting patient: ' . $e->getMessage()]);
    }
    exit;
}
