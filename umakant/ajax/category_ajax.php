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
            addCategory($conn);
            break;
        case 'edit':
            editCategory($conn);
            break;
        case 'delete':
            deleteCategory($conn);
            break;
        case 'get':
            getCategory($conn);
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No action specified']);
}

// Add category function
function addCategory($conn) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    
    $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
    
    try {
        $stmt->execute([$name, $description]);
        echo json_encode(['status' => 'success', 'message' => 'Category added successfully', 'id' => $conn->lastInsertId()]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error adding category: ' . $e->getMessage()]);
    }
}

// Edit category function
function editCategory($conn) {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $description = $_POST['description'];
    
    $stmt = $conn->prepare("UPDATE categories SET name=?, description=? WHERE id=?");
    
    try {
        $stmt->execute([$name, $description, $id]);
        echo json_encode(['status' => 'success', 'message' => 'Category updated successfully']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error updating category: ' . $e->getMessage()]);
    }
}

// Delete category function
function deleteCategory($conn) {
    $id = intval($_POST['id']);
    
    $stmt = $conn->prepare("DELETE FROM categories WHERE id=?");
    
    try {
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Category deleted successfully']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting category: ' . $e->getMessage()]);
    }
}

// Get category function
function getCategory($conn) {
    $id = intval($_POST['id']);
    
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id=?");
    $stmt->execute([$id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($category) {
        echo json_encode(['status' => 'success', 'data' => $category]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Category not found']);
    }
}
?>