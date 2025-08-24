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
            addUser($conn);
            break;
        case 'edit':
            editUser($conn);
            break;
        case 'delete':
            deleteUser($conn);
            break;
        case 'get':
            getUser($conn);
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No action specified']);
}

// Add user function
function addUser($conn) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $is_active = $_POST['is_active'];
    
    $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, email, role, is_active) VALUES (?, ?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([$username, $password, $full_name, $email, $role, $is_active]);
        echo json_encode(['status' => 'success', 'message' => 'User added successfully', 'id' => $conn->lastInsertId()]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error adding user: ' . $e->getMessage()]);
    }
}

// Edit user function
function editUser($conn) {
    $id = intval($_POST['id']);
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $is_active = $_POST['is_active'];
    
    // Check if password is provided
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username=?, password=?, full_name=?, email=?, role=?, is_active=? WHERE id=?");
        $params = [$username, $password, $full_name, $email, $role, $is_active, $id];
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, full_name=?, email=?, role=?, is_active=? WHERE id=?");
        $params = [$username, $full_name, $email, $role, $is_active, $id];
    }
    
    try {
        $stmt->execute($params);
        echo json_encode(['status' => 'success', 'message' => 'User updated successfully']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error updating user: ' . $e->getMessage()]);
    }
}

// Delete user function
function deleteUser($conn) {
    $id = intval($_POST['id']);
    
    // Prevent deleting the admin user
    $stmt = $conn->prepare("SELECT username FROM users WHERE id=?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && $user['username'] === 'admin') {
        echo json_encode(['status' => 'error', 'message' => 'Cannot delete admin user']);
        return;
    }
    
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    
    try {
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'User deleted successfully']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting user: ' . $e->getMessage()]);
    }
}

// Get user function
function getUser($conn) {
    $id = intval($_POST['id']);
    
    $stmt = $conn->prepare("SELECT id, username, full_name, email, role, is_active FROM users WHERE id=?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo json_encode(['status' => 'success', 'data' => $user]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
    }
}
?>