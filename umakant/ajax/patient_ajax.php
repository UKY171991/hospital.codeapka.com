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
            addPatient($conn);
            break;
        case 'edit':
            editPatient($conn);
            break;
        case 'delete':
            deletePatient($conn);
            break;
        case 'get':
            getPatient($conn);
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No action specified']);
}

// Add patient function
function addPatient($conn) {
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $father_husband = $_POST['father_husband'];
    $address = $_POST['address'];
    $sex = $_POST['sex'];
    $age = $_POST['age'];
    $age_unit = $_POST['age_unit'];
    $uhid = $_POST['uhid'];
    
    $stmt = $conn->prepare("INSERT INTO patients (name, mobile, father_husband, address, sex, age, age_unit, uhid) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([$name, $mobile, $father_husband, $address, $sex, $age, $age_unit, $uhid]);
        echo json_encode(['status' => 'success', 'message' => 'Patient added successfully', 'id' => $conn->lastInsertId()]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error adding patient: ' . $e->getMessage()]);
    }
}

// Edit patient function
function editPatient($conn) {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $father_husband = $_POST['father_husband'];
    $address = $_POST['address'];
    $sex = $_POST['sex'];
    $age = $_POST['age'];
    $age_unit = $_POST['age_unit'];
    $uhid = $_POST['uhid'];
    
    $stmt = $conn->prepare("UPDATE patients SET name=?, mobile=?, father_husband=?, address=?, sex=?, age=?, age_unit=?, uhid=? WHERE id=?");
    
    try {
        $stmt->execute([$name, $mobile, $father_husband, $address, $sex, $age, $age_unit, $uhid, $id]);
        echo json_encode(['status' => 'success', 'message' => 'Patient updated successfully']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error updating patient: ' . $e->getMessage()]);
    }
}

// Delete patient function
function deletePatient($conn) {
    $id = intval($_POST['id']);
    
    $stmt = $conn->prepare("DELETE FROM patients WHERE id=?");
    
    try {
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Patient deleted successfully']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting patient: ' . $e->getMessage()]);
    }
}

// Get patient function
function getPatient($conn) {
    $id = intval($_POST['id']);
    
    $stmt = $conn->prepare("SELECT * FROM patients WHERE id=?");
    $stmt->execute([$id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($patient) {
        echo json_encode(['status' => 'success', 'data' => $patient]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Patient not found']);
    }
}
?>