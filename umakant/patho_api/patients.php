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
        // Get specific patient
        $stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
        $stmt->execute([$params['id']]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($patient) {
            echo json_encode(['status' => 'success', 'data' => $patient]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Patient not found']);
        }
    } else {
        // Get all patients
        $stmt = $conn->prepare("SELECT * FROM patients");
        $stmt->execute();
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['status' => 'success', 'data' => $patients]);
    }
}

function handlePost($conn, $input) {
    // Validate required fields
    if (!isset($input['name']) || !isset($input['mobile'])) {
        echo json_encode(['status' => 'error', 'message' => 'Name and mobile are required']);
        return;
    }
    
    // Insert new patient
    $stmt = $conn->prepare("INSERT INTO patients (name, mobile, father_husband, address, sex, age, age_unit, uhid, added_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([
            $input['name'],
            $input['mobile'],
            isset($input['father_husband']) ? $input['father_husband'] : null,
            isset($input['address']) ? $input['address'] : null,
            isset($input['sex']) ? $input['sex'] : null,
            isset($input['age']) ? $input['age'] : null,
            isset($input['age_unit']) ? $input['age_unit'] : null,
            isset($input['uhid']) ? $input['uhid'] : null,
            isset($input['added_by']) ? $input['added_by'] : null
        ]);
        
        $id = $conn->lastInsertId();
        echo json_encode(['status' => 'success', 'message' => 'Patient created successfully', 'id' => $id]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error creating patient: ' . $e->getMessage()]);
    }
}

function handlePut($conn, $input) {
    // Validate required fields
    if (!isset($input['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID is required']);
        return;
    }
    
    // Update patient
    $stmt = $conn->prepare("UPDATE patients SET name = ?, mobile = ?, father_husband = ?, address = ?, sex = ?, age = ?, age_unit = ?, uhid = ?, added_by = ? WHERE id = ?");
    
    try {
        $stmt->execute([
            isset($input['name']) ? $input['name'] : null,
            isset($input['mobile']) ? $input['mobile'] : null,
            isset($input['father_husband']) ? $input['father_husband'] : null,
            isset($input['address']) ? $input['address'] : null,
            isset($input['sex']) ? $input['sex'] : null,
            isset($input['age']) ? $input['age'] : null,
            isset($input['age_unit']) ? $input['age_unit'] : null,
            isset($input['uhid']) ? $input['uhid'] : null,
            isset($input['added_by']) ? $input['added_by'] : null,
            $input['id']
        ]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Patient updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Patient not found or no changes made']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error updating patient: ' . $e->getMessage()]);
    }
}

function handleDelete($conn, $params) {
    if (!isset($params['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID is required']);
        return;
    }
    
    // Delete patient
    $stmt = $conn->prepare("DELETE FROM patients WHERE id = ?");
    
    try {
        $stmt->execute([$params['id']]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Patient deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Patient not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting patient: ' . $e->getMessage()]);
    }
}
?>