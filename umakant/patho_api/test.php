<?php
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

$user_data = simpleAuthenticate($pdo);
if (!$user_data) {
    json_response(['success' => false, 'message' => 'Authentication required', 'debug_info' => getAuthDebugInfo()], 401);
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
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list tests'], 403);
    }

    if ($isSimpleList) {
        $stmt = $pdo->query("SELECT t.id, t.name, t.price, c.name as category_name FROM {$config['table_name']} t LEFT JOIN categories c ON t.category_id = c.id ORDER BY t.name");
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $tests]);
        return;
    }

    $scopeIds = getScopedUserIds($pdo, $user_data);
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
}

function handleGet($pdo, $config, $user_data) {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Test ID is required'], 400);
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