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

/**
 * Test API - Comprehensive CRUD operations for tests
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

$entity_config = [
    'table_name' => 'tests',
    'id_field' => 'id',
    'required_fields' => ['name', 'category_id'],
    'allowed_fields' => [
        'name', 'category_id', 'method', 'price', 'description',
        'min_male', 'max_male', 'min_female', 'max_female',
        'min', 'max', 'unit', 'default_result', 'reference_range',
        'test_code', 'shortcut', 'sub_heading', 'print_new_page', 'specimen', 'added_by'
    ]
];

$requestMethod = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? $requestMethod;

if ($requestMethod === 'GET' && isset($_GET['id'])) $action = 'get';
if ($requestMethod === 'GET' && !isset($_GET['id'])) $action = 'list';
if ($requestMethod === 'POST' || $requestMethod === 'PUT') $action = 'save';
if ($requestMethod === 'DELETE') $action = 'delete';

// Debug: Log the action being processed
error_log("Test API Debug: Action = " . $action . ", Method = " . $requestMethod);

try {
    switch($action) {
        case 'list':
        case 'simple_list':
            handleList($pdo, $entity_config, $action === 'simple_list');
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
        case 'stats':
            handleStats($pdo);
            break;
        default:
            json_response(['success' => false, 'message' => 'Invalid action specified'], 400);
    }
} catch (Exception $e) {
    error_log("Test API Uncaught Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An internal server error occurred.'], 500);
}

function handleList($pdo, $config, $isSimpleList = false) {
    error_log("Test API Debug: handleList called with isSimpleList = " . ($isSimpleList ? 'true' : 'false'));
    
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        error_log("Test API Debug: Authentication failed");
        json_response([
            'success' => false,
            'message' => 'Authentication required',
            'debug_info' => getAuthDebugInfo()
        ], 401);
        return;
    }
    error_log("Test API Debug: Authentication successful for user " . $user_data['user_id']);
    
    if (!simpleCheckPermission($user_data, 'list')) {
        error_log("Test API Debug: Permission denied for list action");
        json_response(['success' => false, 'message' => 'Permission denied to list tests'], 403);
        return;
    }
    error_log("Test API Debug: Permission check passed");

    if ($isSimpleList) {
        try {
            $stmt = $pdo->query("SELECT t.id, t.name, t.price, c.name as category_name FROM {$config['table_name']} t LEFT JOIN categories c ON t.category_id = c.id ORDER BY t.name");
            $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
            json_response(['success' => true, 'data' => $tests]);
            return;
        } catch (Exception $e) {
            json_response(['success' => false, 'message' => 'Database query error: ' . $e->getMessage()], 500);
            return;
        }
    }

    $scopeIds = getScopedUserIds($pdo, $user_data);
    $where = '';
    $params = [];

    // Build WHERE clause for user scoping
    if ($scopeIds !== null) { // null means no restriction (master user)
        if (is_array($scopeIds) && !empty($scopeIds)) {
            $placeholders = implode(',', array_fill(0, count($scopeIds), '?'));
            $where = ' WHERE t.added_by IN (' . $placeholders . ')';
            $params = array_merge($params, $scopeIds);
        }
    }

    $draw = (int)($_REQUEST['draw'] ?? 1);
    $start = (int)($_REQUEST['start'] ?? 0);
    $length = (int)($_REQUEST['length'] ?? 25);
    $search = $_REQUEST['search']['value'] ?? '';

    $baseQuery = "FROM {$config['table_name']} t LEFT JOIN categories c ON t.category_id = c.id";
    $whereClause = $where;

    // Add search conditions
    if (!empty($search)) {
        $searchWhere = (empty($where) ? ' WHERE ' : ' AND ') . "(t.name LIKE ? OR c.name LIKE ?)";
        $whereClause .= $searchWhere;
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    // Build queries with proper WHERE clauses
    $totalWhere = $where;
    $filteredWhere = $whereClause;

    $totalStmt = $pdo->prepare("SELECT COUNT(*) FROM {$config['table_name']} t $totalWhere");
    $totalStmt->execute($params);
    $totalRecords = $totalStmt->fetchColumn();

    $filteredStmt = $pdo->prepare("SELECT COUNT(*) $baseQuery $filteredWhere");
    $filteredStmt->execute($params);
    $filteredRecords = $filteredStmt->fetchColumn();

    try {
        $query = "SELECT t.*, c.name as category_name FROM $baseQuery $filteredWhere ORDER BY t.id DESC LIMIT $start, $length";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        json_response([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'success' => true,
            'data' => $tests
        ]);
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Database query error: ' . $e->getMessage()], 500);
    }
}

function handleGet($pdo, $config) {
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        json_response([
            'success' => false,
            'message' => 'Authentication required',
            'debug_info' => getAuthDebugInfo()
        ], 401);
        return;
    }

    $id = $_GET['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Test ID is required'], 400);
        return;
    }

    $stmt = $pdo->prepare("SELECT t.*, c.name as category_name FROM {$config['table_name']} t LEFT JOIN categories c ON t.category_id = c.id WHERE t.id = ?");
    $stmt->execute([$id]);
    $test = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$test) {
        json_response(['success' => false, 'message' => 'Test not found'], 404);
        return;
    }

    if (!simpleCheckPermission($user_data, 'get', $test['added_by'])) {
        json_response(['success' => false, 'message' => 'Permission denied to view this test'], 403);
        return;
    }

    json_response(['success' => true, 'data' => $test]);
}

function handleSave($pdo, $config) {
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        json_response([
            'success' => false,
            'message' => 'Authentication required',
            'debug_info' => getAuthDebugInfo()
        ], 401);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = $input['id'] ?? null;

    if ($id) { // Update
        $stmt = $pdo->prepare("SELECT added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$existing) {
            json_response(['success' => false, 'message' => 'Test not found'], 404);
            return;
        }
        if (!simpleCheckPermission($user_data, 'save', $existing['added_by'])) {
            json_response(['success' => false, 'message' => 'Permission denied to update this test'], 403);
            return;
        }
    } else { // Create
        if (!simpleCheckPermission($user_data, 'save')) {
            json_response(['success' => false, 'message' => 'Permission denied to create tests'], 403);
            return;
        }
    }

    foreach ($config['required_fields'] as $field) {
        if (empty($input[$field])) {
            json_response(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'], 400);
            return;
        }
    }

    $data = array_intersect_key($input, array_flip($config['allowed_fields']));
    $data['added_by'] = $data['added_by'] ?? $user_data['user_id'];

    if ($id) {
        $set_clause = implode(', ', array_map(fn($field) => "`$field` = ?", array_keys($data)));
        $sql = "UPDATE {$config['table_name']} SET $set_clause, updated_at = NOW() WHERE {$config['id_field']} = ?";
        $values = array_merge(array_values($data), [$id]);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        $test_id = $id;
        $action_status = 'updated';
    } else {
        $cols = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$config['table_name']} ($cols) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));
        $test_id = $pdo->lastInsertId();
        $action_status = 'inserted';
    }

    $stmt = $pdo->prepare("SELECT t.*, c.name as category_name FROM {$config['table_name']} t LEFT JOIN categories c ON t.category_id = c.id WHERE t.id = ?");
    $stmt->execute([$test_id]);
    $saved_test = $stmt->fetch(PDO::FETCH_ASSOC);

    json_response([
        'success' => true,
        'message' => "Test {$action_status} successfully",
        'data' => $saved_test,
        'id' => $test_id
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
        return;
    }

    $id = $_REQUEST['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Test ID is required'], 400);
        return;
    }

    $stmt = $pdo->prepare("SELECT added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
    $stmt->execute([$id]);
    $test = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$test) {
        json_response(['success' => false, 'message' => 'Test not found'], 404);
        return;
    }

    if (!simpleCheckPermission($user_data, 'delete', $test['added_by'])) {
        json_response(['success' => false, 'message' => 'Permission denied to delete this test'], 403);
        return;
    }

    $stmt = $pdo->prepare("DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
    $result = $stmt->execute([$id]);

    json_response(['success' => $result, 'message' => $result ? 'Test deleted successfully' : 'Failed to delete test']);
}

function handleStats($pdo) {
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        json_response([
            'success' => false,
            'message' => 'Authentication required',
            'debug_info' => getAuthDebugInfo()
        ], 401);
        return;
    }

    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to view stats'], 403);
        return;
    }

    $stats = [];
    $stmt = $pdo->query('SELECT COUNT(*) FROM tests');
    $stats['total'] = (int) $stmt->fetchColumn();

    $stmt = $pdo->query('SELECT COUNT(DISTINCT category_id) FROM tests');
    $stats['categories'] = (int) $stmt->fetchColumn();

    json_response(['success' => true, 'data' => $stats]);
}
?>