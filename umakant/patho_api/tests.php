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
        // Get specific test
        $stmt = $conn->prepare("SELECT * FROM tests WHERE id = ?");
        $stmt->execute([$params['id']]);
        $test = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($test) {
            echo json_encode(['status' => 'success', 'data' => $test]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Test not found']);
        }
    } else {
        // Get all tests
        $stmt = $conn->prepare("SELECT * FROM tests");
        $stmt->execute();
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['status' => 'success', 'data' => $tests]);
    }
}

function handlePost($conn, $input) {
    // Validate required fields
    if (!isset($input['name'])) {
        echo json_encode(['status' => 'error', 'message' => 'Name is required']);
        return;
    }
    
    // Insert new test
    $stmt = $conn->prepare("INSERT INTO tests (name, category_id, price, unit, specimen, default_result, reference_range, min, max, sub_heading, test_code, method, print_new_page, shortcut, added_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([
            $input['name'],
            isset($input['category_id']) ? $input['category_id'] : null,
            isset($input['price']) ? $input['price'] : 0,
            isset($input['unit']) ? $input['unit'] : null,
            isset($input['specimen']) ? $input['specimen'] : null,
            isset($input['default_result']) ? $input['default_result'] : null,
            isset($input['reference_range']) ? $input['reference_range'] : null,
            isset($input['min']) ? $input['min'] : null,
            isset($input['max']) ? $input['max'] : null,
            isset($input['sub_heading']) ? $input['sub_heading'] : 0,
            isset($input['test_code']) ? $input['test_code'] : null,
            isset($input['method']) ? $input['method'] : null,
            isset($input['print_new_page']) ? $input['print_new_page'] : 0,
            isset($input['shortcut']) ? $input['shortcut'] : null,
            isset($input['added_by']) ? $input['added_by'] : null
        ]);
        
        $id = $conn->lastInsertId();
        echo json_encode(['status' => 'success', 'message' => 'Test created successfully', 'id' => $id]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error creating test: ' . $e->getMessage()]);
    }
}

function handlePut($conn, $input) {
    // Validate required fields
    if (!isset($input['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID is required']);
        return;
    }
    
    // Update test
    $stmt = $conn->prepare("UPDATE tests SET name = ?, category_id = ?, price = ?, unit = ?, specimen = ?, default_result = ?, reference_range = ?, min = ?, max = ?, sub_heading = ?, test_code = ?, method = ?, print_new_page = ?, shortcut = ?, added_by = ? WHERE id = ?");
    
    try {
        $stmt->execute([
            isset($input['name']) ? $input['name'] : null,
            isset($input['category_id']) ? $input['category_id'] : null,
            isset($input['price']) ? $input['price'] : null,
            isset($input['unit']) ? $input['unit'] : null,
            isset($input['specimen']) ? $input['specimen'] : null,
            isset($input['default_result']) ? $input['default_result'] : null,
            isset($input['reference_range']) ? $input['reference_range'] : null,
            isset($input['min']) ? $input['min'] : null,
            isset($input['max']) ? $input['max'] : null,
            isset($input['sub_heading']) ? $input['sub_heading'] : null,
            isset($input['test_code']) ? $input['test_code'] : null,
            isset($input['method']) ? $input['method'] : null,
            isset($input['print_new_page']) ? $input['print_new_page'] : null,
            isset($input['shortcut']) ? $input['shortcut'] : null,
            isset($input['added_by']) ? $input['added_by'] : null,
            $input['id']
        ]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Test updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Test not found or no changes made']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error updating test: ' . $e->getMessage()]);
    }
}

function handleDelete($conn, $params) {
    if (!isset($params['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID is required']);
        return;
    }
    
    // Delete test
    $stmt = $conn->prepare("DELETE FROM tests WHERE id = ?");
    
    try {
        $stmt->execute([$params['id']]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Test deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Test not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting test: ' . $e->getMessage()]);
    }
}
?>