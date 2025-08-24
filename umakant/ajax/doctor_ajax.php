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
            addDoctor($conn);
            break;
        case 'edit':
            editDoctor($conn);
            break;
        case 'delete':
            deleteDoctor($conn);
            break;
        case 'get':
            getDoctor($conn);
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No action specified']);
}

// Add doctor function
function addDoctor($conn) {
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    
    $stmt = $conn->prepare("INSERT INTO doctors (name, specialization, phone, email, address) VALUES (?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([$name, $specialization, $phone, $email, $address]);
        echo json_encode(['status' => 'success', 'message' => 'Doctor added successfully', 'id' => $conn->lastInsertId()]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error adding doctor: ' . $e->getMessage()]);
    }
}

// Edit doctor function
function editDoctor($conn) {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    
    $stmt = $conn->prepare("UPDATE doctors SET name=?, specialization=?, phone=?, email=?, address=? WHERE id=?");
    
    try {
        $stmt->execute([$name, $specialization, $phone, $email, $address, $id]);
        echo json_encode(['status' => 'success', 'message' => 'Doctor updated successfully']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error updating doctor: ' . $e->getMessage()]);
    }
}

// Delete doctor function
function deleteDoctor($conn) {
    $id = intval($_POST['id']);
    
    $stmt = $conn->prepare("DELETE FROM doctors WHERE id=?");
    
    try {
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Doctor deleted successfully']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting doctor: ' . $e->getMessage()]);
    }
}

// Get doctor function
function getDoctor($conn) {
    $id = intval($_POST['id']);
    
    $stmt = $conn->prepare("SELECT * FROM doctors WHERE id=?");
    $stmt->execute([$id]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($doctor) {
        echo json_encode(['status' => 'success', 'data' => $doctor]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Doctor not found']);
    }
}
?>