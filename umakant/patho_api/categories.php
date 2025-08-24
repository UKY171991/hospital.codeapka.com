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
        // Get specific category
        $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$params['id']]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($category) {
            echo json_encode(['status' => 'success', 'data' => $category]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Category not found']);
        }
    } else {
        // Get all categories
        $stmt = $conn->prepare("SELECT * FROM categories");
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['status' => 'success', 'data' => $categories]);
    }
}

function handlePost($conn, $input) {
    // Validate required fields
    if (!isset($input['name'])) {
        echo json_encode(['status' => 'error', 'message' => 'Name is required']);
        return;
    }
    
    // Insert new category
    $stmt = $conn->prepare("INSERT INTO categories (name, description, added_by) VALUES (?, ?, ?)");
    
    try {
        $stmt->execute([
            $input['name'],
            isset($input['description']) ? $input['description'] : null,
            isset($input['added_by']) ? $input['added_by'] : null
        ]);
        
        $id = $conn->lastInsertId();
        echo json_encode(['status' => 'success', 'message' => 'Category created successfully', 'id' => $id]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error creating category: ' . $e->getMessage()]);
    }
}

function handlePut($conn, $input) {
    // Validate required fields
    if (!isset($input['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID is required']);
        return;
    }
    
    // Update category
    $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ?, added_by = ? WHERE id = ?");
    
    try {
        $stmt->execute([
            isset($input['name']) ? $input['name'] : null,
            isset($input['description']) ? $input['description'] : null,
            isset($input['added_by']) ? $input['added_by'] : null,
            $input['id']
        ]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Category updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Category not found or no changes made']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error updating category: ' . $e->getMessage()]);
    }
}

function handleDelete($conn, $params) {
    if (!isset($params['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID is required']);
        return;
    }
    
    // Delete category
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    
    try {
        $stmt->execute([$params['id']]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Category deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Category not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting category: ' . $e->getMessage()]);
    }
}
?>