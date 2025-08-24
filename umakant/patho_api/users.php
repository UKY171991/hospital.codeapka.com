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
        // Get specific user
        $stmt = $conn->prepare("SELECT id, username, full_name, email, role, is_active, created_at, last_login, expire_date FROM users WHERE id = ?");
        $stmt->execute([$params['id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo json_encode(['status' => 'success', 'data' => $user]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
        }
    } else {
        // Get all users
        $stmt = $conn->prepare("SELECT id, username, full_name, email, role, is_active, created_at, last_login, expire_date FROM users");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['status' => 'success', 'data' => $users]);
    }
}

function handlePost($conn, $input) {
    // Validate required fields
    if (!isset($input['username']) || !isset($input['password']) || !isset($input['full_name'])) {
        echo json_encode(['status' => 'error', 'message' => 'Username, password, and full name are required']);
        return;
    }
    
    // Hash the password
    $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, email, role, is_active, expire_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([
            $input['username'],
            $hashedPassword,
            $input['full_name'],
            isset($input['email']) ? $input['email'] : null,
            isset($input['role']) ? $input['role'] : 'user',
            isset($input['is_active']) ? $input['is_active'] : 1,
            isset($input['expire_date']) ? $input['expire_date'] : null
        ]);
        
        $id = $conn->lastInsertId();
        echo json_encode(['status' => 'success', 'message' => 'User created successfully', 'id' => $id]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error creating user: ' . $e->getMessage()]);
    }
}

function handlePut($conn, $input) {
    // Validate required fields
    if (!isset($input['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID is required']);
        return;
    }
    
    // Prepare update data
    $updateData = [
        isset($input['username']) ? $input['username'] : null,
        isset($input['full_name']) ? $input['full_name'] : null,
        isset($input['email']) ? $input['email'] : null,
        isset($input['role']) ? $input['role'] : null,
        isset($input['is_active']) ? $input['is_active'] : null,
        isset($input['expire_date']) ? $input['expire_date'] : null,
        $input['id']
    ];
    
    // If password is provided, hash it and include in update
    if (isset($input['password'])) {
        $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username = ?, full_name = ?, email = ?, role = ?, is_active = ?, expire_date = ?, password = ? WHERE id = ?");
        $updateData = [
            isset($input['username']) ? $input['username'] : null,
            isset($input['full_name']) ? $input['full_name'] : null,
            isset($input['email']) ? $input['email'] : null,
            isset($input['role']) ? $input['role'] : null,
            isset($input['is_active']) ? $input['is_active'] : null,
            isset($input['expire_date']) ? $input['expire_date'] : null,
            $hashedPassword,
            $input['id']
        ];
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, full_name = ?, email = ?, role = ?, is_active = ?, expire_date = ? WHERE id = ?");
    }
    
    try {
        $stmt->execute($updateData);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'User updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found or no changes made']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error updating user: ' . $e->getMessage()]);
    }
}

function handleDelete($conn, $params) {
    if (!isset($params['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID is required']);
        return;
    }
    
    // Delete user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    
    try {
        $stmt->execute([$params['id']]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'User deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting user: ' . $e->getMessage()]);
    }
}
?>