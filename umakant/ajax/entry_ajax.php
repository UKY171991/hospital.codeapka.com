<?php
// ajax/entry_ajax.php
require_once '../inc/auth.php';
require_once '../inc/connection.php';

$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    $stmt = $pdo->query('SELECT e.id, p.client_name as patient_name, d.name as doctor_name, t.test_name, e.amount, e.status, e.created_at FROM entries e LEFT JOIN patients p ON e.patient_id = p.id LEFT JOIN doctors d ON e.doctor_id = d.id LEFT JOIN tests t ON e.test_id = t.id ORDER BY e.id DESC');
    while ($row = $stmt->fetch()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['patient_name'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($row['doctor_name'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($row['test_name'] ?? 'N/A') . '</td>';
        echo '<td>₹' . htmlspecialchars($row['amount'] ?? 'N/A') . '</td>';
        echo '<td><span class="badge badge-' . ($row['status'] === 'completed' ? 'success' : 'warning') . '">' . htmlspecialchars($row['status'] ?? 'pending') . '</span></td>';
        echo '<td>' . date('d M Y', strtotime($row['created_at'])) . '</td>';
        echo '<td>';
        echo '<button class="btn btn-sm btn-info" onclick="viewEntry(' . $row['id'] . ')"><i class="fas fa-eye"></i> View</button> ';
        echo '<a href="../entry.php?id=' . $row['id'] . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a> ';
        echo '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $row['id'] . '"><i class="fas fa-trash"></i> Delete</button>';
        echo '</td>';
        echo '</tr>';
    }
    exit;
}

if ($action === 'get' && isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT e.*, p.client_name as patient_name, d.name as doctor_name, t.test_name FROM entries e LEFT JOIN patients p ON e.patient_id = p.id LEFT JOIN doctors d ON e.doctor_id = d.id LEFT JOIN tests t ON e.test_id = t.id WHERE e.id = ?');
    $stmt->execute([$_GET['id']]);
    $entry = $stmt->fetch();
    header('Content-Type: application/json');
    echo json_encode($entry);
    exit;
}

if ($action === 'save') {
    $id = $_POST['id'] ?? '';
    $patient_id = $_POST['patient_id'] ?? '';
    $doctor_id = $_POST['doctor_id'] ?? '';
    $test_id = $_POST['test_id'] ?? '';
    $entry_date = $_POST['entry_date'] ?? '';
    $result_value = $_POST['result_value'] ?? '';
    $unit = $_POST['unit'] ?? '';
    $remarks = $_POST['remarks'] ?? '';
    $status = $_POST['status'] ?? 'pending';
    
    if ($id) {
        // Edit
        $stmt = $pdo->prepare('UPDATE entries SET patient_id=?, doctor_id=?, test_id=?, entry_date=?, result_value=?, unit=?, remarks=?, status=? WHERE id=?');
        $stmt->execute([$patient_id, $doctor_id, $test_id, $entry_date, $result_value, $unit, $remarks, $status, $id]);
    } else {
        // Add
        $stmt = $pdo->prepare('INSERT INTO entries (patient_id, doctor_id, test_id, entry_date, result_value, unit, remarks, status, added_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$patient_id, $doctor_id, $test_id, $entry_date, $result_value, $unit, $remarks, $status, $_SESSION['user_id']]);
    }
    exit('success');
}

if ($action === 'delete' && isset($_POST['id'])) {
    $stmt = $pdo->prepare('DELETE FROM entries WHERE id = ?');
    $stmt->execute([$_POST['id']]);
    exit('success');
}
