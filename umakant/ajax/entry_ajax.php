<?php
require_once '../inc/auth.php';
require_once '../inc/connection.php';
$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    $stmt = $pdo->query('SELECT e.*, p.client_name AS patient_name, t.test_name, d.name AS doctor_name, u.username AS added_by_username FROM entries e
        LEFT JOIN patients p ON e.patient_id = p.id
        LEFT JOIN tests t ON e.test_id = t.id
        LEFT JOIN doctors d ON e.doctor_id = d.id
        LEFT JOIN users u ON e.added_by = u.id
        ORDER BY e.id DESC');
    while ($row = $stmt->fetch()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['patient_name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['test_name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['entry_date']) . '</td>';
        echo '<td>' . htmlspecialchars($row['status']) . '</td>';
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
    $stmt = $pdo->prepare('SELECT * FROM entries WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $entry = $stmt->fetch();
    header('Content-Type: application/json');
    echo json_encode($entry);
    exit;
}

if ($action === 'save') {
    $id = $_POST['id'] ?? '';
    $patient_id = trim($_POST['patient_id'] ?? '');
    $doctor_id = trim($_POST['doctor_id'] ?? '');
    $test_id = trim($_POST['test_id'] ?? '');
    $entry_date = trim($_POST['entry_date'] ?? '');
    $result_value = trim($_POST['result_value'] ?? '');
    $unit = trim($_POST['unit'] ?? '');
    $remarks = trim($_POST['remarks'] ?? '');
    $status = trim($_POST['status'] ?? 'pending');
    $added_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    if ($id) {
        $stmt = $pdo->prepare('UPDATE entries SET patient_id=?, doctor_id=?, test_id=?, entry_date=?, result_value=?, unit=?, remarks=?, status=? WHERE id=?');
        $stmt->execute([$patient_id, $doctor_id, $test_id, $entry_date, $result_value, $unit, $remarks, $status, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO entries (patient_id, doctor_id, test_id, entry_date, result_value, unit, remarks, status, added_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$patient_id, $doctor_id, $test_id, $entry_date, $result_value, $unit, $remarks, $status, $added_by]);
    }
    exit('success');
}

if ($action === 'delete' && isset($_POST['id'])) {
    $stmt = $pdo->prepare('DELETE FROM entries WHERE id = ?');
    $stmt->execute([$_POST['id']]);
    exit('success');
}
