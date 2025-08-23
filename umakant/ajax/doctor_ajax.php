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
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $specialization = mysqli_real_escape_string($conn, $_POST['specialization']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    $sql = "INSERT INTO doctors (name, specialization, phone, email, address) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $specialization, $phone, $email, $address);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Doctor added successfully', 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error adding doctor: ' . $conn->error]);
    }
    
    $stmt->close();
}

// Edit doctor function
function editDoctor($conn) {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $specialization = mysqli_real_escape_string($conn, $_POST['specialization']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    $sql = "UPDATE doctors SET name=?, specialization=?, phone=?, email=?, address=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $name, $specialization, $phone, $email, $address, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Doctor updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating doctor: ' . $conn->error]);
    }
    
    $stmt->close();
}

// Delete doctor function
function deleteDoctor($conn) {
    $id = intval($_POST['id']);
    
    $sql = "DELETE FROM doctors WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Doctor deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting doctor: ' . $conn->error]);
    }
    
    $stmt->close();
}

// Get doctor function
function getDoctor($conn) {
    $id = intval($_POST['id']);
    
    $sql = "SELECT * FROM doctors WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $doctor = $result->fetch_assoc();
        echo json_encode(['status' => 'success', 'data' => $doctor]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Doctor not found']);
    }
    
    $stmt->close();
}

$conn->close();
?>