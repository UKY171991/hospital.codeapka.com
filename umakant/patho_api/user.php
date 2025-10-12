<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Centralized error handler
function handle_error($errno, $errstr, $errfile, $errline) {
    $error_data = [
        'success' => false,
        'message' => 'An unexpected error occurred.',
        'error' => [
            'type' => 'PHP Error',
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline
        ]
    ];
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($error_data);
    exit;
}
set_error_handler('handle_error');

// Centralized exception handler
function handle_exception($exception) {
    $error_data = [
        'success' => false,
        'message' => 'An unexpected exception occurred.',
        'error' => [
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]
    ];
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($error_data);
    exit;
}
set_exception_handler('handle_exception');

require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
require_once __DIR__ . '/../inc/api_config.php';
require_once __DIR__ . '/../inc/simple_auth.php';

// ... (rest of the user.php code) ...

// Entity Configuration for Users
$entity_config = [
    'table_name' => 'users',
    'id_field' => 'id',
    'required_fields' => ['username', 'full_name'],
    'allowed_fields' => [
        'username', 'full_name', 'email', 'password', 'role', 'user_type',
        'is_active', 'expire_date', 'added_by', 'api_token'
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

switch($action) {
    case 'list':
        handleList($pdo, $entity_config);
        break;
    case 'get':
        handleGet($pdo, $entity_config);
        break;
    case 'save':
        handleSave($pdo, $entity_config);
        break;
    case 'delete':
        handleDelete($pdo, $entity_config);
        break;
    default:
        json_response(['success' => false, 'message' => 'Invalid action specified'], 400);
}

function handleList($pdo, $config) {
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        json_response([
            'success' => false, 
            'message' => 'Authentication required',
            'debug_info' => getAuthDebugInfo()
        ], 401);
    }
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list users'], 403);
    }

    $scopeIds = getScopedUserIds($pdo, $user_data);
    $params = [];
    $where = '';
    if (is_array($scopeIds)) {
        $placeholders = implode(',', array_fill(0, count($scopeIds), '?'));
        $where = " WHERE id IN ($placeholders) OR added_by IN ($placeholders)";
        $params = array_merge($scopeIds, $scopeIds);
    }

    $sql = "SELECT id, username, full_name, email, password, role, user_type, is_active,
                   created_at, updated_at, last_login, expire_date, added_by, api_token
            FROM {$config['table_name']}" . $where . " ORDER BY username";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    json_response(['success' => true, 'data' => $users, 'total' => count($users)]);
}

// ... (rest of the handleGet, handleSave, handleDelete functions) ...

function handleGet($pdo, $config) {
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        json_response([
            'success' => false, 
            'message' => 'Authentication required',
            'debug_info' => getAuthDebugInfo()
        ], 401);
    }

    $id = $_GET['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'User ID is required'], 400);
    }

    $sql = "SELECT id, username, full_name, email, password, role, user_type, is_active,
                   created_at, updated_at, last_login, expire_date, added_by, api_token
            FROM {$config['table_name']} WHERE {$config['id_field']} = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        json_response(['success' => false, 'message' => 'User not found'], 404);
    }

    $scopeIds = getScopedUserIds($pdo, $user_data);
    if (is_array($scopeIds)) {
        if (!in_array((int)$user['id'], $scopeIds, true) && !in_array((int)($user['added_by'] ?? 0), $scopeIds, true)) {
            json_response(['success' => false, 'message' => 'Permission denied to view this user'], 403);
        }
    }

    json_response(['success' => true, 'data' => $user]);
}

function handleSave($pdo, $config) {
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        json_response([
            'success' => false, 
            'message' => 'Authentication required',
            'debug_info' => getAuthDebugInfo()
        ], 401);
    }

    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = $input['id'] ?? null;

    if ($id) { // Update
        $stmt = $pdo->prepare("SELECT id, added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$existing) {
            json_response(['success' => false, 'message' => 'User not found'], 404);
        }
        $scopeIds = getScopedUserIds($pdo, $user_data);
        if (is_array($scopeIds)) {
            if (!in_array((int)$existing['id'], $scopeIds, true) && !in_array((int)($existing['added_by'] ?? 0), $scopeIds, true)) {
                json_response(['success' => false, 'message' => 'Permission denied to update this user'], 403);
            }
        }
    } else { // Create
        $role = $user_data['role'] ?? 'user';
        if (!in_array($role, ['master','admin'], true)) {
            json_response(['success' => false, 'message' => 'Permission denied to create users'], 403);
        }
    }

    foreach ($config['required_fields'] as $field) {
        if (empty($input[$field])) {
            json_response(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'], 400);
        }
    }

    if (!empty($input['username'])) {
        $check_sql = "SELECT id FROM {$config['table_name']} WHERE username = ?";
        if ($id) {
            $check_sql .= " AND id != ?";
            $stmt = $pdo->prepare($check_sql);
            $stmt->execute([$input['username'], $id]);
        } else {
            $stmt = $pdo->prepare($check_sql);
            $stmt->execute([$input['username']]);
        }
        
        if ($stmt->fetch()) {
            json_response(['success' => false, 'message' => 'Username already exists'], 409);
        }
    }
    
    if (!empty($input['email'])) {
        $check_sql = "SELECT id FROM {$config['table_name']} WHERE email = ?";
        if ($id) {
            $check_sql .= " AND id != ?";
            $stmt = $pdo->prepare($check_sql);
            $stmt->execute([$input['email'], $id]);
        } else {
            $stmt = $pdo->prepare($check_sql);
            $stmt->execute([$input['email']]);
        }
        
        if ($stmt->fetch()) {
            json_response(['success' => false, 'message' => 'Email already exists'], 409);
        }
    }
    
    $data = [];
    foreach ($config['allowed_fields'] as $field) {
        if (isset($input[$field])) {
            if ($field === 'password' && !empty($input[$field])) {
                $data[$field] = password_hash($input[$field], PASSWORD_DEFAULT);
            } else if ($field !== 'password') {
                $data[$field] = $input[$field];
            }
        }
    }
    $data['added_by'] = $data['added_by'] ?? $user_data['user_id'];

    if ($id) {
        $set_clause = implode(', ', array_map(fn($field) => "$field = ?", array_keys($data)));
        $sql = "UPDATE {$config['table_name']} SET $set_clause, updated_at = NOW() WHERE {$config['id_field']} = ?";
        $values = array_merge(array_values($data), [$id]);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        $user_id = $id;
        $action = 'updated';
    } else {
        if (empty($input['password'])) {
            json_response(['success' => false, 'message' => 'Password is required for new users'], 400);
        }
        $cols = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$config['table_name']} ($cols) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));
        $user_id = $pdo->lastInsertId();
        $action = 'inserted';
    }

    $stmt = $pdo->prepare("SELECT id, username, full_name, email, password, role, user_type, is_active,
                                  created_at, updated_at, last_login, expire_date, added_by, api_token
                           FROM {$config['table_name']} WHERE id = ?");
    $stmt->execute([$user_id]);
    $saved_user = $stmt->fetch(PDO::FETCH_ASSOC);

    json_response([
        'success' => true,
        'message' => "User {$action} successfully",
        'data' => $saved_user,
        'id' => $user_id
    ]);
}

function handleDelete($pdo, $config) {
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        json_response([
            'success' => false, 
            'message' => 'Authentication required',
            'debug_info' => getAuthDebugInfo()
        ], 401);
    }

    $id = $_REQUEST['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'User ID is required'], 400);
    }

    $stmt = $pdo->prepare("SELECT id, added_by, role FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        json_response(['success' => false, 'message' => 'User not found'], 404);
    }

    $scopeIds = getScopedUserIds($pdo, $user_data);
    if (is_array($scopeIds)) {
        if (!in_array((int)$user['id'], $scopeIds, true) && !in_array((int)($user['added_by'] ?? 0), $scopeIds, true)) {
            json_response(['success' => false, 'message' => 'Permission denied to delete this user'], 403);
        }
    }

    if ($user['role'] === 'admin') {
        json_response(['success' => false, 'message' => 'Cannot delete admin users'], 400);
    }

    $stmt = $pdo->prepare("DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
    $result = $stmt->execute([$id]);

    if ($result) {
        json_response(['success' => true, 'message' => 'User deleted successfully']);
    } else {
        json_response(['success' => false, 'message' => 'Failed to delete user'], 500);
    }
}
?>