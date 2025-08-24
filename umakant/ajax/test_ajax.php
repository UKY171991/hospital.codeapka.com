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
            addTest($conn);
            break;
        case 'edit':
            editTest($conn);
            break;
        case 'delete':
            deleteTest($conn);
            break;
        case 'get':
            getTest($conn);
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No action specified']);
}

// Add test function
function addTest($conn) {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $unit = $_POST['unit'];
    $specimen = $_POST['specimen'];
    $default_result = $_POST['default_result'];
    $reference_range = $_POST['reference_range'];
    $min = $_POST['min'];
    $max = $_POST['max'];
    $sub_heading = $_POST['sub_heading'];
    $test_code = $_POST['test_code'];
    $method = $_POST['method'];
    $print_new_page = $_POST['print_new_page'];
    $shortcut = $_POST['shortcut'];
    
    $stmt = $conn->prepare("INSERT INTO tests (name, category_id, price, unit, specimen, default_result, reference_range, min, max, sub_heading, test_code, method, print_new_page, shortcut) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([$name, $category_id, $price, $unit, $specimen, $default_result, $reference_range, $min, $max, $sub_heading, $test_code, $method, $print_new_page, $shortcut]);
        echo json_encode(['status' => 'success', 'message' => 'Test added successfully', 'id' => $conn->lastInsertId()]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error adding test: ' . $e->getMessage()]);
    }
}

// Edit test function
function editTest($conn) {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $unit = $_POST['unit'];
    $specimen = $_POST['specimen'];
    $default_result = $_POST['default_result'];
    $reference_range = $_POST['reference_range'];
    $min = $_POST['min'];
    $max = $_POST['max'];
    $sub_heading = $_POST['sub_heading'];
    $test_code = $_POST['test_code'];
    $method = $_POST['method'];
    $print_new_page = $_POST['print_new_page'];
    $shortcut = $_POST['shortcut'];
    
    $stmt = $conn->prepare("UPDATE tests SET name=?, category_id=?, price=?, unit=?, specimen=?, default_result=?, reference_range=?, min=?, max=?, sub_heading=?, test_code=?, method=?, print_new_page=?, shortcut=? WHERE id=?");
    
    try {
        $stmt->execute([$name, $category_id, $price, $unit, $specimen, $default_result, $reference_range, $min, $max, $sub_heading, $test_code, $method, $print_new_page, $shortcut, $id]);
        echo json_encode(['status' => 'success', 'message' => 'Test updated successfully']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error updating test: ' . $e->getMessage()]);
    }
}

// Delete test function
function deleteTest($conn) {
    $id = intval($_POST['id']);
    
    $stmt = $conn->prepare("DELETE FROM tests WHERE id=?");
    
    try {
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Test deleted successfully']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting test: ' . $e->getMessage()]);
    }
}

// Get test function
function getTest($conn) {
    $id = intval($_POST['id']);
    
    $stmt = $conn->prepare("SELECT * FROM tests WHERE id=?");
    $stmt->execute([$id]);
    $test = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($test) {
        echo json_encode(['status' => 'success', 'data' => $test]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Test not found']);
    }
}
?>