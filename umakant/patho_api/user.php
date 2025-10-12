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
require_once __DIR__ . '/../inc/simple_auth.php';

// Entity Configuration for Users
$entity_config = [
    'table_name' => 'users',
    'id_field' => 'id',
    'required_fields' => ['username', 'full_name'],
    'allowed_fields' => [
        'username', 'full_name', 'email', 'password', 'role', 'user_type',
        'is_active', 'expire_date', 'added_by', 'api_token'
    ],
    'list_fields' => 'id, username, full_name, email, password, role, user_type, is_active, created_at, updated_at, last_login, expire_date, added_by, api_token',
    'get_fields' => 'id, username, full_name, email, password, role, user_type, is_active, created_at, updated_at, last_login, expire_date, added_by, api_token'
];

// Authenticate user at the beginning of the script
$user_data = simpleAuthenticate($pdo);
if (!$user_data) {
    json_response(['success' => false, 'message' => 'Authentication required', 'debug_info' => getAuthDebugInfo()], 401);
}

// Determine action
$requestMethod = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? $requestMethod;

if ($requestMethod === 'GET' && isset($_GET['id'])) $action = 'get';
if ($requestMethod === 'GET' && !isset($_GET['id'])) $action = 'list';
if ($requestMethod === 'POST' || $requestMethod === 'PUT') $action = 'save';
if ($requestMethod === 'DELETE') $action = 'delete';


try {
    switch($action) {
        case 'list':
            handleList($pdo, $entity_config, $user_data);
            break;
        case 'get':
            handleGet($pdo, $entity_config, $user_data);
            break;
        case 'save':
            handleSave($pdo, $entity_config, $user_data);
            break;
        case 'delete':
            handleDelete($pdo, $entity_config, $user_data);
            break;
        default:
            json_response(['success' => false, 'message' => 'Invalid action specified'], 400);
    }
} catch (Exception $e) {
    error_log("User API Uncaught Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An internal server error occurred.'], 500);
}

function handleList($pdo, $config, $user_data) {
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list users'], 403);
    }

    $params = [];
    $where = '';
    $scopeIds = getScopedUserIds($pdo, $user_data);
    if (is_array($scopeIds)) {
        $placeholders = implode(',', array_fill(0, count($scopeIds), '?'));
        $where = " WHERE id IN ($placeholders) OR added_by IN ($placeholders)";
        $params = array_merge($scopeIds, $scopeIds);
    }

    // WARNING: Exposing password hashes is a security risk.
    // This is included as per specific requirements.
    $sql = "SELECT {$config['list_fields']} FROM {$config['table_name']}{$where} ORDER BY username";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    json_response(['success' => true, 'data' => $users, 'total' => count($users)]);
}

function handleGet($pdo, $config, $user_data) {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'User ID is required'], 400);
    }

    // WARNING: Exposing password hashes is a security risk.
    // This is included as per specific requirements.
    $sql = "SELECT {$config['get_fields']} FROM {$config['table_name']} WHERE {$config['id_field']} = ?";
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

function handleSave($pdo, $config, $user_data) {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = $input['id'] ?? null;

    if ($id) { // Update
        if (!simpleCheckPermission($user_data, 'update')) {
            json_response(['success' => false, 'message' => 'Permission denied to update users'], 403);
        }
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
        if (!simpleCheckPermission($user_data, 'create')) {
            json_response(['success' => false, 'message' => 'Permission denied to create users'], 403);
        }
    }

    foreach ($config['required_fields'] as $field) {
        if (empty($input[$field])) {
            json_response(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'], 400);
        }
    }

    check_duplicate($pdo, $config['table_name'], 'username', $input['username'] ?? null, $id);
    check_duplicate($pdo, $config['table_name'], 'email', $input['email'] ?? null, $id);

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
        $action_status = 'updated';
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
        $action_status = 'inserted';
    }

    $stmt = $pdo->prepare("SELECT {$config['get_fields']} FROM {$config['table_name']} WHERE id = ?");
    $stmt->execute([$user_id]);
    $saved_user = $stmt->fetch(PDO::FETCH_ASSOC);

    json_response([
        'success' => true,
        'message' => "User {$action_status} successfully",
        'data' => $saved_user,
        'id' => $user_id
    ]);
}

function handleDelete($pdo, $config, $user_data) {
    if (!simpleCheckPermission($user_data, 'delete')) {
        json_response(['success' => false, 'message' => 'Permission denied to delete users'], 403);
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

    if ($user['role'] === 'admin' || $user['role'] === 'master') {
        json_response(['success' => false, 'message' => 'Cannot delete admin or master users'], 400);
    }

    $stmt = $pdo->prepare("DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
    $result = $stmt->execute([$id]);

    if ($result) {
        json_response(['success' => true, 'message' => 'User deleted successfully']);
    } else {
        json_response(['success' => false, 'message' => 'Failed to delete user'], 500);
    }
}

function check_duplicate($pdo, $table, $field, $value, $id = null) {
    if (empty($value)) return;
    $sql = "SELECT id FROM {$table} WHERE {$field} = ?";
    $params = [$value];
    if ($id) {
        $sql .= " AND id != ?";
        $params[] = $id;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    if ($stmt->fetch()) {
        json_response(['success' => false, 'message' => ucfirst($field) . ' already exists'], 409);
    }
}
?>