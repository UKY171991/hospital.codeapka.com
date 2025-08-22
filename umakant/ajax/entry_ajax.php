<?php
// ajax/entry_ajax.php
require_once '../inc/auth.php';
require_once '../inc/connection.php';

$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    $stmt = $pdo->query('
        SELECT e.id, p.client_name as patient_name, d.name as doctor_name, t.test_name, e.amount, e.status, e.created_at 
        FROM entries e 
        LEFT JOIN patients p ON e.patient_id = p.id 
        LEFT JOIN doctors d ON e.doctor_id = d.id 
        LEFT JOIN tests t ON e.test_id = t.id 
        ORDER BY e.id DESC
    ');
    while ($row = $stmt->fetch()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['patient_name'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($row['doctor_name'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($row['test_name'] ?? 'N/A') . '</td>';
        echo '<td>₹' . htmlspecialchars($row['amount'] ?? '0') . '</td>';
        echo '<td><span class="badge badge-' . ($row['status'] === 'completed' ? 'success' : 'warning') . '">' . htmlspecialchars($row['status'] ?? 'pending') . '</span></td>';
        echo '<td>' . date('d M Y', strtotime($row['created_at'])) . '</td>';
        echo '<td>';
        echo '<button class="btn btn-sm btn-info" onclick="viewEntry(' . $row['id'] . ')"><i class="fas fa-eye"></i> View</button> ';
        echo '<button class="btn btn-sm btn-warning" onclick="editEntry(' . $row['id'] . ')"><i class="fas fa-edit"></i> Edit</button> ';
        echo '<button class="btn btn-sm btn-danger" onclick="deleteEntry(' . $row['id'] . ')"><i class="fas fa-trash"></i> Delete</button>';
        echo '</td>';
        echo '</tr>';
    }
    exit;
}

if ($action === 'get' && isset($_GET['id'])) {
    $stmt = $pdo->prepare('
        SELECT e.*, p.client_name as patient_name, d.name as doctor_name, t.test_name 
        FROM entries e 
        LEFT JOIN patients p ON e.patient_id = p.id 
        LEFT JOIN doctors d ON e.doctor_id = d.id 
        LEFT JOIN tests t ON e.test_id = t.id 
        WHERE e.id = ?
    ');
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
    $amount = trim($_POST['amount'] ?? '0');
    
    if ($id) {
        // Edit
        $stmt = $pdo->prepare('UPDATE entries SET patient_id=?, doctor_id=?, test_id=?, entry_date=?, result_value=?, unit=?, remarks=?, status=?, amount=? WHERE id=?');
        $stmt->execute([$patient_id, $doctor_id, $test_id, $entry_date, $result_value, $unit, $remarks, $status, $amount, $id]);
        $message = 'Entry updated successfully!';
    } else {
        // Add
        $stmt = $pdo->prepare('INSERT INTO entries (patient_id, doctor_id, test_id, entry_date, result_value, unit, remarks, status, amount, added_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$patient_id, $doctor_id, $test_id, $entry_date, $result_value, $unit, $remarks, $status, $amount, $_SESSION['user_id']]);
        $message = 'Entry added successfully!';
    }
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => $message]);
    exit;
}

if ($action === 'delete' && isset($_POST['id'])) {
    try {
        $stmt = $pdo->prepare('DELETE FROM entries WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Entry deleted successfully!']);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Error deleting entry: ' . $e->getMessage()]);
    }
    exit;
}

// Get patients for dropdown
if ($action === 'get_patients') {
    $stmt = $pdo->query('SELECT id, client_name FROM patients ORDER BY client_name');
    $patients = $stmt->fetchAll();
    header('Content-Type: application/json');
    echo json_encode($patients);
    exit;
}

// Get doctors for dropdown
if ($action === 'get_doctors') {
    $stmt = $pdo->query('SELECT id, name FROM doctors ORDER BY name');
    $doctors = $stmt->fetchAll();
    header('Content-Type: application/json');
    echo json_encode($doctors);
    exit;
}

// Get tests for dropdown
if ($action === 'get_tests') {
    $stmt = $pdo->query('SELECT id, test_name, price FROM tests ORDER BY test_name');
    $tests = $stmt->fetchAll();
    header('Content-Type: application/json');
    echo json_encode($tests);
    exit;
}
