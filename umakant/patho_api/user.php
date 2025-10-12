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
} catch (Exception $e) {
    error_log("User API Uncaught Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An internal server error occurred.'], 500);
}

function handleList($pdo, $config) {
    try {
        error_log("handleList called");
        $user_data = simpleAuthenticate($pdo);
        if (!$user_data) {
            error_log("Authentication failed");
            json_response([
                'success' => false, 
                'message' => 'Authentication required',
                'debug_info' => getAuthDebugInfo()
            ], 401);
        }
        error_log("Authentication successful: " . json_encode($user_data));
        if (!simpleCheckPermission($user_data, 'list')) {
            error_log("Permission denied");
            json_response(['success' => false, 'message' => 'Permission denied to list users'], 403);
        }
        error_log("Permission check passed");

        try {
            // Role-based scoping
            error_log("About to call getScopedUserIds with user_data: " . json_encode($user_data));
            $scopeIds = getScopedUserIds($pdo, $user_data); // null => no restriction (master)
            error_log("getScopedUserIds returned: " . json_encode($scopeIds));
            $params = [];
            $where = '';
            if (is_array($scopeIds)) {
                // admin: own + users they added; user: own only
                // Visible rows: id in scope OR added_by in scope
                $placeholders = implode(',', array_fill(0, count($scopeIds), '?'));
                $where = " WHERE id IN ($placeholders) OR added_by IN ($placeholders)";
                $params = array_merge($scopeIds, $scopeIds);
                error_log("WHERE clause: $where");
                error_log("Params: " . json_encode($params));
            } else {
                error_log("No WHERE clause needed - scopeIds is not an array");
            }

            // Check if users table exists and has correct structure
            try {
                $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
                $tableExists = $stmt->rowCount() > 0;

                if (!$tableExists) {
                    error_log("Users table does not exist");
                    json_response([
                        'success' => false,
                        'message' => 'Users table does not exist. Please create the users table in your database.',
                        'debug_info' => [
                            'issue' => 'missing_users_table',
                            'solution' => 'Run the following SQL to create the users table:',
                            'sql' => 'CREATE TABLE users (id int(11) NOT NULL AUTO_INCREMENT, username varchar(255) NOT NULL, full_name varchar(255) NOT NULL, email varchar(255) DEFAULT NULL, password varchar(255) NOT NULL, role varchar(50) NOT NULL DEFAULT "user", user_type varchar(50) DEFAULT NULL, is_active tinyint(1) NOT NULL DEFAULT 1, expire_date datetime DEFAULT NULL, added_by int(11) DEFAULT NULL, api_token varchar(255) DEFAULT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, last_login datetime DEFAULT NULL, PRIMARY KEY (id), UNIQUE KEY username (username)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;'
                        ]
                    ], 500);
                    return;
                }
            } catch (Exception $e) {
                error_log("Error checking users table: " . $e->getMessage());
                json_response([
                    'success' => false,
                    'message' => 'Database connection issue. Please check your database configuration.',
                    'debug_info' => ['error' => $e->getMessage()]
                ], 500);
                return;
            }

            $sql = "SELECT id, username, full_name, email, password, role, user_type, is_active,
                           created_at, updated_at, last_login, expire_date, added_by, api_token
                    FROM {$config['table_name']}" . $where . " ORDER BY username";
            error_log("About to execute query: " . $sql);
            error_log("Query params: " . json_encode($params));
            error_log("Table name: " . $config['table_name']);

            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                error_log("Query executed successfully, found " . count($users) . " users");
                json_response(['success' => true, 'data' => $users, 'total' => count($users)]);
            } catch (PDOException $e) {
                error_log("PDO Error in query execution: " . $e->getMessage());
                error_log("SQL: " . $sql);
                error_log("Params: " . json_encode($params));

                // Check if this is a table doesn't exist error
                if (strpos($e->getMessage(), "Table") !== false && strpos($e->getMessage(), "doesn't exist") !== false) {
                    json_response([
                        'success' => false,
                        'message' => 'Users table does not exist. Please create the users table in your database.',
                        'debug_info' => [
                            'issue' => 'missing_users_table',
                            'error' => $e->getMessage(),
                            'solution' => 'Run the following SQL to create the users table:',
                            'sql' => 'CREATE TABLE users (id int(11) NOT NULL AUTO_INCREMENT, username varchar(255) NOT NULL, full_name varchar(255) NOT NULL, email varchar(255) DEFAULT NULL, password varchar(255) NOT NULL, role varchar(50) NOT NULL DEFAULT "user", user_type varchar(50) DEFAULT NULL, is_active tinyint(1) NOT NULL DEFAULT 1, expire_date datetime DEFAULT NULL, added_by int(11) DEFAULT NULL, api_token varchar(255) DEFAULT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, last_login datetime DEFAULT NULL, PRIMARY KEY (id), UNIQUE KEY username (username)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;'
                        ]
                    ], 500);
                } else {
                    json_response([
                        'success' => false,
                        'message' => 'Database query failed.',
                        'debug_info' => [
                            'error' => $e->getMessage(),
                            'sql' => $sql,
                            'params' => $params
                        ]
                    ], 500);
                }
            } catch (Exception $e) {
                error_log("General error in query execution: " . $e->getMessage());
                json_response(['success' => false, 'message' => 'Failed to fetch users'], 500);
            }
        } catch (Exception $e) {
            error_log("Error in handleList: " . $e->getMessage());
            json_response(['success' => false, 'message' => 'Failed to fetch users'], 500);
        }
    } catch (Exception $e) {
        error_log("Fatal error in handleList: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Internal server error'], 500);
    }

function handleGet($pdo, $config) {
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        error_log("Authentication failed");
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

    try {
        $sql = "SELECT id, username, full_name, email, password, role, user_type, is_active,
                       created_at, updated_at, last_login, expire_date, added_by, api_token
                FROM {$config['table_name']} WHERE {$config['id_field']} = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            json_response(['success' => false, 'message' => 'User not found'], 404);
        }

        // Scoped visibility: master all; admin self + users they added; user self
        $scopeIds = getScopedUserIds($pdo, $user_data);
        if (is_array($scopeIds)) {
            if (!in_array((int)$user['id'], $scopeIds, true) && !in_array((int)($user['added_by'] ?? 0), $scopeIds, true)) {
                json_response(['success' => false, 'message' => 'Permission denied to view this user'], 403);
            }
        }

        json_response(['success' => true, 'data' => $user]);
    } catch (Exception $e) {
        error_log("Get user error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch user'], 500);
    }
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
        // Scoped visibility
        $scopeIds = getScopedUserIds($pdo, $user_data);
        if (is_array($scopeIds)) {
            if (!in_array((int)$existing['id'], $scopeIds, true) && !in_array((int)($existing['added_by'] ?? 0), $scopeIds, true)) {
                json_response(['success' => false, 'message' => 'Permission denied to update this user'], 403);
            }
        }
    } else { // Create
        // Only master or admin can create users; user cannot
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

    try {
        // Check for duplicate username
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
        
        // Check for duplicate email (if provided)
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
    } catch (Exception $e) {
        error_log("Save user error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to save user'], 500);
    }
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

    try {
        $stmt = $pdo->prepare("SELECT id, added_by, role FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            json_response(['success' => false, 'message' => 'User not found'], 404);
        }

        // Scoped visibility
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
    } catch (Exception $e) {
        error_log("Delete user error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to delete user'], 500);
    }
}
?>