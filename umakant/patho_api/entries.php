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
        // Get specific entry
        $stmt = $conn->prepare("SELECT * FROM entries WHERE id = ?");
        $stmt->execute([$params['id']]);
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($entry) {
            echo json_encode(['status' => 'success', 'data' => $entry]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Entry not found']);
        }
    } else {
        // Get all entries
        $stmt = $conn->prepare("SELECT * FROM entries");
        $stmt->execute();
        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['status' => 'success', 'data' => $entries]);
    }
}

function handlePost($conn, $input) {
    // Validate required fields
    if (!isset($input['patient_id']) || !isset($input['doctor_id']) || !isset($input['test_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Patient ID, Doctor ID, and Test ID are required']);
        return;
    }
    
    // Insert new entry
    $stmt = $conn->prepare("INSERT INTO entries (patient_id, doctor_id, test_id, entry_date, result_value, unit, remarks, status, added_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([
            $input['patient_id'],
            $input['doctor_id'],
            $input['test_id'],
            isset($input['entry_date']) ? $input['entry_date'] : null,
            isset($input['result_value']) ? $input['result_value'] : null,
            isset($input['unit']) ? $input['unit'] : null,
            isset($input['remarks']) ? $input['remarks'] : null,
            isset($input['status']) ? $input['status'] : 'pending',
            isset($input['added_by']) ? $input['added_by'] : null
        ]);
        
        $id = $conn->lastInsertId();
        echo json_encode(['status' => 'success', 'message' => 'Entry created successfully', 'id' => $id]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error creating entry: ' . $e->getMessage()]);
    }
}

function handlePut($conn, $input) {
    // Validate required fields
    if (!isset($input['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID is required']);
        return;
    }
    
    // Update entry
    $stmt = $conn->prepare("UPDATE entries SET patient_id = ?, doctor_id = ?, test_id = ?, entry_date = ?, result_value = ?, unit = ?, remarks = ?, status = ?, added_by = ? WHERE id = ?");
    
    try {
        $stmt->execute([
            isset($input['patient_id']) ? $input['patient_id'] : null,
            isset($input['doctor_id']) ? $input['doctor_id'] : null,
            isset($input['test_id']) ? $input['test_id'] : null,
            isset($input['entry_date']) ? $input['entry_date'] : null,
            isset($input['result_value']) ? $input['result_value'] : null,
            isset($input['unit']) ? $input['unit'] : null,
            isset($input['remarks']) ? $input['remarks'] : null,
            isset($input['status']) ? $input['status'] : null,
            isset($input['added_by']) ? $input['added_by'] : null,
            $input['id']
        ]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Entry updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Entry not found or no changes made']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error updating entry: ' . $e->getMessage()]);
    }
}

function handleDelete($conn, $params) {
    if (!isset($params['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID is required']);
        return;
    }
    
    // Delete entry
    $stmt = $conn->prepare("DELETE FROM entries WHERE id = ?");
    
    try {
        $stmt->execute([$params['id']]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Entry deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Entry not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting entry: ' . $e->getMessage()]);
    }
}
?>