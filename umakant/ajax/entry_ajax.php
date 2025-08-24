<?php
require_once '../inc/connection.php';

// Check if request is AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    header('HTTP/1.0 403 Forbidden');
    exit('Access denied');
}

// Handle different actions
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    
    switch ($action) {
        case 'add':
            addEntry($conn);
            break;
        case 'edit':
            editEntry($conn);
            break;
        case 'delete':
            deleteEntry($conn);
            break;
        case 'get':
            getEntry($conn);
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No action specified']);
}

// Add entry function
function addEntry($conn) {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $test_id = $_POST['test_id'];
    $entry_date = $_POST['entry_date'];
    $result_value = $_POST['result_value'];
    $unit = $_POST['unit'];
    $remarks = $_POST['remarks'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("INSERT INTO entries (patient_id, doctor_id, test_id, entry_date, result_value, unit, remarks, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([$patient_id, $doctor_id, $test_id, $entry_date, $result_value, $unit, $remarks, $status]);
        echo json_encode(['status' => 'success', 'message' => 'Entry added successfully', 'id' => $conn->lastInsertId()]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error adding entry: ' . $e->getMessage()]);
    }
}

// Edit entry function
function editEntry($conn) {
    $id = intval($_POST['id']);
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $test_id = $_POST['test_id'];
    $entry_date = $_POST['entry_date'];
    $result_value = $_POST['result_value'];
    $unit = $_POST['unit'];
    $remarks = $_POST['remarks'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE entries SET patient_id=?, doctor_id=?, test_id=?, entry_date=?, result_value=?, unit=?, remarks=?, status=? WHERE id=?");
    
    try {
        $stmt->execute([$patient_id, $doctor_id, $test_id, $entry_date, $result_value, $unit, $remarks, $status, $id]);
        echo json_encode(['status' => 'success', 'message' => 'Entry updated successfully']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error updating entry: ' . $e->getMessage()]);
    }
}

// Delete entry function
function deleteEntry($conn) {
    $id = intval($_POST['id']);
    
    $stmt = $conn->prepare("DELETE FROM entries WHERE id=?");
    
    try {
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Entry deleted successfully']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting entry: ' . $e->getMessage()]);
    }
}

// Get entry function
function getEntry($conn) {
    $id = intval($_POST['id']);
    
    $stmt = $conn->prepare("SELECT * FROM entries WHERE id=?");
    $stmt->execute([$id]);
    $entry = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($entry) {
        echo json_encode(['status' => 'success', 'data' => $entry]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Entry not found']);
    }
}
?>