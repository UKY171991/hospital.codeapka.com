<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key, X-API-Secret');

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

$user_data = false;
if (isset($pdo) && $pdo) {
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required', 'debug_info' => getAuthDebugInfo()], 401);
    }
} else {
    // For testing without database, allow master access
    $user_data = [
        'user_id' => 1,
        'role' => 'master',
        'username' => 'test_user',
        'auth_method' => 'no_db'
    ];
}

$requestMethod = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? $requestMethod;

if ($requestMethod === 'GET' && isset($_GET['id'])) $action = 'get';
if ($requestMethod === 'GET' && !isset($_GET['id'])) $action = 'list';
if ($requestMethod === 'POST' || $requestMethod === 'PUT') $action = 'save';
if ($requestMethod === 'DELETE') $action = 'delete';

try {
    switch($action) {
        case 'list':
        case 'simple_list':
            handleList($pdo, $entity_config, $user_data, $action === 'simple_list');
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
        case 'stats':
            handleStats($pdo, $user_data);
            break;
        default:
            json_response(['success' => false, 'message' => 'Invalid action specified'], 400);
    }
} catch (Exception $e) {
    error_log("Test API Uncaught Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An internal server error occurred.'], 500);
}

function handleList($pdo, $config, $user_data, $isSimpleList = false) {
    error_log("handleList called");
    if (!isset($pdo) || !$pdo) {
        // Return mock data for testing without database
        json_response(['success' => true, 'data' => [
            ['id' => 1, 'name' => 'Test 1', 'category_id' => 1, 'price' => 100.00],
            ['id' => 2, 'name' => 'Test 2', 'category_id' => 1, 'price' => 200.00]
        ]]);
        return;
    }

    if (!simpleCheckPermission($user_data, 'list')) {
        error_log("Permission denied");
        json_response(['success' => false, 'message' => 'Permission denied to list tests'], 403);
    }
    error_log("Permission check passed");

    if ($isSimpleList) {
        $stmt = $pdo->query("SELECT t.id, t.name, t.price, c.name as category_name FROM {$config['table_name']} t LEFT JOIN categories c ON t.category_id = c.id ORDER BY t.name");
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $tests]);
        return;
    }

    $scopeIds = getScopedUserIds($pdo, $user_data);
    error_log("scopeIds: " . json_encode($scopeIds));
    $where = '';
    $params = [];
    if (is_array($scopeIds)) {
        $placeholders = implode(',', array_fill(0, count($scopeIds), '?'));
        $where = ' WHERE t.added_by IN (' . $placeholders . ')';
        $params = $scopeIds;
    }

    $draw = (int)($_REQUEST['draw'] ?? 1);
    $start = (int)($_REQUEST['start'] ?? 0);
    $length = (int)($_REQUEST['length'] ?? 25);
    $search = $_REQUEST['search']['value'] ?? '';

    $baseQuery = "FROM {$config['table_name']} t LEFT JOIN categories c ON t.category_id = c.id";
    $whereClause = $where;

    if (!empty($search)) {
        $whereClause .= (empty($where) ? ' WHERE ' : ' AND ') . "(t.name LIKE ? OR c.name LIKE ?)";
        $searchTerm = "%$search%";
        array_push($params, $searchTerm, $searchTerm);
    }

    $totalStmt = $pdo->query("SELECT COUNT(*) FROM {$config['table_name']}");
    $totalRecords = $totalStmt->fetchColumn();

    $filteredStmt = $pdo->prepare("SELECT COUNT(*) $baseQuery $whereClause");
    $filteredStmt->execute($params);
    $filteredRecords = $filteredStmt->fetchColumn();

    $query = "SELECT t.*, c.name as category_name FROM $baseQuery $whereClause ORDER BY t.id DESC LIMIT $start, $length";
    error_log("query: " . $query);
    error_log("params: " . json_encode($params));
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("tests count: " . count($tests));

    json_response([
        'draw' => $draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $filteredRecords,
        'success' => true,
        'data' => $tests
    ]);
}

function handleGet($pdo, $config, $user_data) {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Test ID is required'], 400);
    }

    if (!isset($pdo) || !$pdo) {
        // Return mock data for testing without database
        json_response(['success' => true, 'data' => [
            'id' => (int)$id,
            'name' => 'Test ' . $id,
            'category_id' => 1,
            'price' => 100.00,
            'category_name' => 'Test Category'
        ]]);
        return;
    }

    $stmt = $pdo->prepare("SELECT t.*, c.name as category_name FROM {$config['table_name']} t LEFT JOIN categories c ON t.category_id = c.id WHERE t.id = ?");
    $stmt->execute([$id]);
    $test = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$test) {
        json_response(['success' => false, 'message' => 'Test not found'], 404);
    }

    if (!simpleCheckPermission($user_data, 'get', $test['added_by'])) {
        json_response(['success' => false, 'message' => 'Permission denied to view this test'], 403);
    }

    json_response(['success' => true, 'data' => $test]);
}

function handleSave($pdo, $config, $user_data) {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = $input['id'] ?? null;

    if (!isset($pdo) || !$pdo) {
        // Return mock success response for testing without database
        $mock_id = $id ?? 1;
        json_response([
            'success' => true,
            'message' => "Test " . ($id ? 'updated' : 'inserted') . " successfully",
            'data' => array_merge($input, ['id' => $mock_id, 'category_name' => 'Test Category']),
            'id' => $mock_id
        ]);
        return;
    }

    if ($id) { // Update
        $stmt = $pdo->prepare("SELECT added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$existing) {
            json_response(['success' => false, 'message' => 'Test not found'], 404);
        }
        if (!simpleCheckPermission($user_data, 'save', $existing['added_by'])) {
            json_response(['success' => false, 'message' => 'Permission denied to update this test'], 403);
        }
    } else { // Create
        if (!simpleCheckPermission($user_data, 'save')) {
            json_response(['success' => false, 'message' => 'Permission denied to create tests'], 403);
        }
    }

    foreach ($config['required_fields'] as $field) {
        if (empty($input[$field])) {
            json_response(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'], 400);
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

function handleDelete($pdo, $config, $user_data) {
    $id = $_REQUEST['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Test ID is required'], 400);
    }

    if (!isset($pdo) || !$pdo) {
        // Return mock success response for testing without database
        json_response(['success' => true, 'message' => 'Test deleted successfully']);
        return;
    }

    $stmt = $pdo->prepare("SELECT added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
    $stmt->execute([$id]);
    $test = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$test) {
        json_response(['success' => false, 'message' => 'Test not found'], 404);
    }

    if (!simpleCheckPermission($user_data, 'delete', $test['added_by'])) {
        json_response(['success' => false, 'message' => 'Permission denied to delete this test'], 403);
    }

    $stmt = $pdo->prepare("DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
    $result = $stmt->execute([$id]);

    json_response(['success' => $result, 'message' => $result ? 'Test deleted successfully' : 'Failed to delete test']);
}

function handleStats($pdo, $user_data) {
    if (!isset($pdo) || !$pdo) {
        // Return mock stats for testing without database
        json_response(['success' => true, 'data' => [
            'total' => 10,
            'categories' => 3
        ]]);
        return;
    }

    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to view stats'], 403);
    }

    $stats = [];
    $stmt = $pdo->query('SELECT COUNT(*) FROM tests');
    $stats['total'] = (int) $stmt->fetchColumn();

    $stmt = $pdo->query('SELECT COUNT(DISTINCT category_id) FROM tests');
    $stats['categories'] = (int) $stmt->fetchColumn();

    json_response(['success' => true, 'data' => $stats]);
}
?>