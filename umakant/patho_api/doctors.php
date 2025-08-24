<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'db_connection.php';

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Get request data
$input = json_decode(file_get_contents('php://input'), true);

switch($method) {
    case 'GET':
        handleGet($conn, $_GET);
        break;
    case 'POST':
        handlePost($conn, $input);
        break;
    case 'PUT':
        handlePut($conn, $input);
        break;
    case 'DELETE':
        handleDelete($conn, $_GET);
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
        break;
}

function handleGet($conn, $params) {
    if (isset($params['id'])) {
        // Get specific doctor
        $stmt = $conn->prepare("SELECT * FROM doctors WHERE id = ?");
        $stmt->execute([$params['id']]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($doctor) {
            echo json_encode(['status' => 'success', 'data' => $doctor]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Doctor not found']);
        }
    } else {
        // Get all doctors
        $stmt = $conn->prepare("SELECT * FROM doctors");
        $stmt->execute();
        $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['status' => 'success', 'data' => $doctors]);
    }
}

function handlePost($conn, $input) {
    // Validate required fields
    if (!isset($input['name'])) {
        echo json_encode(['status' => 'error', 'message' => 'Name is required']);
        return;
    }
    
    // Insert new doctor
    $stmt = $conn->prepare("INSERT INTO doctors (name, qualification, specialization, hospital, contact_no, phone, email, address, registration_no, percent, added_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([
            $input['name'],
            isset($input['qualification']) ? $input['qualification'] : null,
            isset($input['specialization']) ? $input['specialization'] : null,
            isset($input['hospital']) ? $input['hospital'] : null,
            isset($input['contact_no']) ? $input['contact_no'] : null,
            isset($input['phone']) ? $input['phone'] : null,
            isset($input['email']) ? $input['email'] : null,
            isset($input['address']) ? $input['address'] : null,
            isset($input['registration_no']) ? $input['registration_no'] : null,
            isset($input['percent']) ? $input['percent'] : 0,
            isset($input['added_by']) ? $input['added_by'] : null
        ]);
        
        $id = $conn->lastInsertId();
        echo json_encode(['status' => 'success', 'message' => 'Doctor created successfully', 'id' => $id]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error creating doctor: ' . $e->getMessage()]);
    }
}

function handlePut($conn, $input) {
    // Validate required fields
    if (!isset($input['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID is required']);
        return;
    }
    
    // Update doctor
    $stmt = $conn->prepare("UPDATE doctors SET name = ?, qualification = ?, specialization = ?, hospital = ?, contact_no = ?, phone = ?, email = ?, address = ?, registration_no = ?, percent = ?, added_by = ? WHERE id = ?");
    
    try {
        $stmt->execute([
            isset($input['name']) ? $input['name'] : null,
            isset($input['qualification']) ? $input['qualification'] : null,
            isset($input['specialization']) ? $input['specialization'] : null,
            isset($input['hospital']) ? $input['hospital'] : null,
            isset($input['contact_no']) ? $input['contact_no'] : null,
            isset($input['phone']) ? $input['phone'] : null,
            isset($input['email']) ? $input['email'] : null,
            isset($input['address']) ? $input['address'] : null,
            isset($input['registration_no']) ? $input['registration_no'] : null,
            isset($input['percent']) ? $input['percent'] : null,
            isset($input['added_by']) ? $input['added_by'] : null,
            $input['id']
        ]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Doctor updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Doctor not found or no changes made']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error updating doctor: ' . $e->getMessage()]);
    }
}

function handleDelete($conn, $params) {
    if (!isset($params['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID is required']);
        return;
    }
    
    // Delete doctor
    $stmt = $conn->prepare("DELETE FROM doctors WHERE id = ?");
    
    try {
        $stmt->execute([$params['id']]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Doctor deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Doctor not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting doctor: ' . $e->getMessage()]);
    }
}
?>