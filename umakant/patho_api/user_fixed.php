<?php
/**
 * User API - Comprehensive CRUD operations for users
 * Supports: CREATE, READ, UPDATE, DELETE operations
 * Authentication: Multiple methods supported
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key, X-API-Secret');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
require_once __DIR__ . '/../inc/api_config.php';

// Entity Configuration for Users
$entity_config = [
    'table_name' => 'users',
    'id_field' => 'id',
    'required_fields' => ['username', 'email'],
    'allowed_fields' => [
        'username', 'email', 'password', 'full_name', 'role', 'status', 
        'phone', 'department', 'last_login'
    ],
    'permission_map' => [
        'list' => 'read',
        'get' => 'read', 
        'save' => 'write',
        'delete' => 'delete'
    ]
];

// Get action from request
$action = $_REQUEST['action'] ?? $_SERVER['REQUEST_METHOD'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Map HTTP methods to actions
switch($requestMethod) {
    case 'GET':
        $action = isset($_GET['id']) ? 'get' : 'list';
        break;
    case 'POST':
        $action = $_REQUEST['action'] ?? 'save';
        break;
    case 'PUT':
        $action = 'save';
        break;
    case 'DELETE':
        $action = 'delete';
        break;
}

try {
    // Authenticate user
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Authentication required']);
        exit;
    }

    // Check permissions
    $required_permission = $entity_config['permission_map'][$action] ?? 'read';
    if (!checkPermission($user_data, $required_permission)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Insufficient permissions']);
        exit;
    }

    switch($action) {
        case 'list':
            handleList($pdo, $entity_config);
            break;
            
        case 'get':
            handleGet($pdo, $entity_config);
            break;
            
        case 'save':
            handleSave($pdo, $entity_config, $user_data);
            break;
            
        case 'delete':
            handleDelete($pdo, $entity_config);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }

} catch (Exception $e) {
    error_log("User API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
}

function handleList($pdo, $config) {
    try {
        // Don't return passwords in list
        $fields = array_filter($config['allowed_fields'], fn($field) => $field !== 'password');
        $fields_sql = implode(', ', $fields);
        
        $sql = "SELECT {$fields_sql} FROM {$config['table_name']} ORDER BY username";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => $users,
            'total' => count($users)
        ]);
    } catch (Exception $e) {
        error_log("List users error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch users']);
    }
}

function handleGet($pdo, $config) {
    try {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'User ID is required']);
            return;
        }

        // Don't return password in get
        $fields = array_filter($config['allowed_fields'], fn($field) => $field !== 'password');
        $fields_sql = implode(', ', $fields);
        
        $sql = "SELECT {$fields_sql} FROM {$config['table_name']} WHERE {$config['id_field']} = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'User not found']);
            return;
        }

        echo json_encode(['success' => true, 'data' => $user]);
    } catch (Exception $e) {
        error_log("Get user error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch user']);
    }
}

function handleSave($pdo, $config, $user_data) {
    try {
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate required fields
        foreach ($config['required_fields'] as $field) {
            if (empty($input[$field])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
                return;
            }
        }

        // Validate email format
        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid email format']);
            return;
        }

        $id = $input['id'] ?? null;
        $is_update = !empty($id);

        // Check for duplicate username
        $check_sql = "SELECT id FROM {$config['table_name']} WHERE username = ?";
        if ($is_update) {
            $check_sql .= " AND id != ?";
            $stmt = $pdo->prepare($check_sql);
            $stmt->execute([$input['username'], $id]);
        } else {
            $stmt = $pdo->prepare($check_sql);
            $stmt->execute([$input['username']]);
        }
        
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Username already exists']);
            return;
        }

        // Check for duplicate email
        $check_sql = "SELECT id FROM {$config['table_name']} WHERE email = ?";
        if ($is_update) {
            $check_sql .= " AND id != ?";
            $stmt = $pdo->prepare($check_sql);
            $stmt->execute([$input['email'], $id]);
        } else {
            $stmt = $pdo->prepare($check_sql);
            $stmt->execute([$input['email']]);
        }
        
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Email already exists']);
            return;
        }

        // Prepare data for saving
        $data = [];
        foreach ($config['allowed_fields'] as $field) {
            if (isset($input[$field])) {
                if ($field === 'password' && !empty($input[$field])) {
                    // Hash password if provided
                    $data[$field] = password_hash($input[$field], PASSWORD_DEFAULT);
                } else if ($field !== 'password') {
                    $data[$field] = $input[$field];
                }
            }
        }

        // Set default values
        if (!isset($data['role'])) {
            $data['role'] = 'user';
        }
        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }

        if ($is_update) {
            // Update existing user
            $set_clause = implode(', ', array_map(fn($field) => "$field = ?", array_keys($data)));
            $sql = "UPDATE {$config['table_name']} SET $set_clause WHERE {$config['id_field']} = ?";
            $values = array_merge(array_values($data), [$id]);
        } else {
            // Create new user - password is required for new users
            if (empty($input['password'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Password is required for new users']);
                return;
            }
            
            $fields = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $sql = "INSERT INTO {$config['table_name']} ($fields) VALUES ($placeholders)";
            $values = array_values($data);
        }

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($values);

        if ($result) {
            $user_id = $is_update ? $id : $pdo->lastInsertId();
            
            // Fetch the saved user (without password)
            $fields = array_filter($config['allowed_fields'], fn($field) => $field !== 'password');
            $fields_sql = implode(', ', $fields);
            
            $stmt = $pdo->prepare("SELECT {$fields_sql} FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
            $stmt->execute([$user_id]);
            $saved_user = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'message' => $is_update ? 'User updated successfully' : 'User created successfully',
                'data' => $saved_user
            ]);
        } else {
            throw new Exception('Failed to save user');
        }

    } catch (Exception $e) {
        error_log("Save user error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save user']);
    }
}

function handleDelete($pdo, $config) {
    try {
        $id = $_REQUEST['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'User ID is required']);
            return;
        }

        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'User not found']);
            return;
        }

        // Prevent deletion of admin users
        $stmt = $pdo->prepare("SELECT role FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user['role'] === 'admin') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Cannot delete admin users']);
            return;
        }

        // Delete the user
        $stmt = $pdo->prepare("DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $result = $stmt->execute([$id]);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
        } else {
            throw new Exception('Failed to delete user');
        }

    } catch (Exception $e) {
        error_log("Delete user error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
    }
}
?>
