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
        // Get specific report
        $stmt = $conn->prepare("SELECT * FROM reports WHERE id = ?");
        $stmt->execute([$params['id']]);
        $report = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($report) {
            echo json_encode(['status' => 'success', 'data' => $report]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Report not found']);
        }
    } else {
        // Get all reports
        $stmt = $conn->prepare("SELECT * FROM reports");
        $stmt->execute();
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['status' => 'success', 'data' => $reports]);
    }
}

function handlePost($conn, $input) {
    // Validate required fields
    if (!isset($input['data'])) {
        echo json_encode(['status' => 'error', 'message' => 'Data is required']);
        return;
    }
    
    // Insert new report
    $stmt = $conn->prepare("INSERT INTO reports (data, added_by) VALUES (?, ?)");
    
    try {
        $stmt->execute([
            $input['data'],
            isset($input['added_by']) ? $input['added_by'] : null
        ]);
        
        $id = $conn->lastInsertId();
        echo json_encode(['status' => 'success', 'message' => 'Report created successfully', 'id' => $id]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error creating report: ' . $e->getMessage()]);
    }
}

function handlePut($conn, $input) {
    // Validate required fields
    if (!isset($input['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID is required']);
        return;
    }
    
    // Update report
    $stmt = $conn->prepare("UPDATE reports SET data = ?, added_by = ? WHERE id = ?");
    
    try {
        $stmt->execute([
            isset($input['data']) ? $input['data'] : null,
            isset($input['added_by']) ? $input['added_by'] : null,
            $input['id']
        ]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Report updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Report not found or no changes made']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error updating report: ' . $e->getMessage()]);
    }
}

function handleDelete($conn, $params) {
    if (!isset($params['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID is required']);
        return;
    }
    
    // Delete report
    $stmt = $conn->prepare("DELETE FROM reports WHERE id = ?");
    
    try {
        $stmt->execute([$params['id']]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Report deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Report not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting report: ' . $e->getMessage()]);
    }
}
?>