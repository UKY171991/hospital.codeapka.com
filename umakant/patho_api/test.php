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
require_once __DIR__ . '/../inc/smart_upsert.php';
require_once __DIR__ . '/../inc/simple_auth.php';

// Entity Configuration for Tests
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
        case 'stats':
            handleStats($pdo);
            break;
    }
} catch (Exception $e) {
    error_log("Test API Uncaught Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An internal server error occurred.'], 500);
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
        json_response(['success' => false, 'message' => 'Permission denied to list tests'], 403);
    }

    // Check if this is a simple_list request (for dropdowns)
    $isSimpleList = isset($_GET['action']) && $_GET['action'] === 'simple_list';

    if ($isSimpleList) {
        try {
            // Check which categories table exists - Default to 'categories' based on schema
            $categories_table = 'categories';
            try{
                $stmt = $pdo->query("SHOW TABLES LIKE 'categories'");
                if(!$stmt->fetch()){
                    $stmt2 = $pdo->query("SHOW TABLES LIKE 'test_categories'");
                    if($stmt2->fetch()) {
                        $categories_table = 'test_categories';
                    }
                }
            }catch(Throwable $e){
                $categories_table = 'categories';
            }

            $stmt = $pdo->prepare("SELECT t.id, t.name, t.unit, t.reference_range, t.price,
                                           t.min, t.max, t.min_male, t.max_male, t.min_female, t.max_female,
                                           tc.name as category_name, t.category_id
                                    FROM tests t
                                    LEFT JOIN {$categories_table} tc ON t.category_id = tc.id
                                    ORDER BY t.name ASC");
            $stmt->execute();
            $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

            json_response([
                'success' => true,
                'data' => $tests
            ]);
        } catch (Exception $e) {
            json_response(['success' => false, 'message' => $e->getMessage()], 500);
        }
        return;
    }

    try {
        // Role-based scoping by added_by
        $scopeIds = getScopedUserIds($pdo, $user_data);
        $where = '';
        $params = [];
        if (is_array($scopeIds)) {
            $placeholders = implode(',', array_fill(0, count($scopeIds), '?'));
            $where = ' WHERE t.added_by IN (' . $placeholders . ')';
            $params = $scopeIds;
        }

        // Check which categories table exists - Default to 'categories' based on schema
        $categories_table = 'categories';
        try{
            $stmt = $pdo->query("SHOW TABLES LIKE 'categories'");
            if(!$stmt->fetch()){
                $stmt2 = $pdo->query("SHOW TABLES LIKE 'test_categories'");
                if($stmt2->fetch()) {
                    $categories_table = 'test_categories';
                }
            }
        }catch(Throwable $e){
            $categories_table = 'categories';
        }

        // Support DataTables server-side processing
        $draw = $_POST['draw'] ?? 1;
        $start = $_POST['start'] ?? 0;
        $length = $_POST['length'] ?? 25;
        $search = $_POST['search']['value'] ?? '';

        // Base query for counting
        $countBaseQuery = "FROM tests t LEFT JOIN {$categories_table} tc ON t.category_id = tc.id LEFT JOIN users u ON t.added_by = u.id";

        // Add search conditions
        if (!empty($search)) {
            $whereClause = (strpos($where, 'WHERE') === false) ? ' WHERE ' : ' AND ';
            $whereClause .= "(t.name LIKE ? OR tc.name LIKE ? OR t.description LIKE ?)";
            $searchTerm = "%$search%";
            $searchParams = [$searchTerm, $searchTerm, $searchTerm];
            $params = array_merge($params, $searchParams);
        }

        // Get total records
        $totalStmt = $pdo->prepare("SELECT COUNT(*) " . $countBaseQuery . $where);
        $totalStmt->execute($params);
        $totalRecords = $totalStmt->fetchColumn();

        // Get filtered records
        $orderBy = " ORDER BY t.id DESC";
        $limit = " LIMIT $start, $length";

        // Build the complete query with explicit table names
        $dataQuery = "SELECT
            t.id,
            COALESCE(t.name, '') AS name,
            COALESCE(tc.name, '') AS category_name,
            t.category_id,
            COALESCE(t.description, '') AS description,
            COALESCE(t.price, 0) AS price,
            COALESCE(t.unit, '') AS unit,
            COALESCE(t.reference_range, '') AS reference_range,
            t.min,
            t.max,
            t.min_male,
            t.max_male,
            t.min_female,
            t.max_female,
            COALESCE(t.sub_heading, 0) AS sub_heading,
            COALESCE(t.print_new_page, 0) AS print_new_page,
            COALESCE(u.username, '') AS added_by_username
            FROM tests t
            LEFT JOIN {$categories_table} tc ON t.category_id = tc.id
            LEFT JOIN users u ON t.added_by = u.id" . $where . $orderBy . $limit;

        $dataStmt = $pdo->prepare($dataQuery);
        $dataStmt->execute($params);
        $data = $dataStmt->fetchAll();

        // Add debug info when no data or data issues detected
        $debug_info = [];
        if (empty($data)) {
            $debug_info['note'] = 'No data returned from query';
            $debug_info['query'] = $dataQuery;
            $debug_info['table'] = $categories_table;
        } else {
            // Check first record for null names
            $first = $data[0] ?? null;
            if ($first && (empty($first['name']) || $first['name'] === null || $first['name'] === '')) {
                $debug_info['warning'] = 'Test names are null/empty';
                $debug_info['sample'] = $first;
                $debug_info['table'] = $categories_table;
                $debug_info['query'] = $dataQuery;
            }
        }

        // Return DataTables format
        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'success' => true,
            'data' => $data,
            'categories_table_used' => $categories_table
        ];

        // Add debug info if present
        if (!empty($debug_info)) {
            $response['debug'] = $debug_info;
        }

        json_response($response);
    } catch (Exception $e) {
        error_log("List tests error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch tests', 'error' => $e->getMessage()], 500);
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
    }

    $id = $_GET['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Test ID is required'], 400);
    }

    try {
        $sql = "SELECT t.*, c.name as category_name FROM {$config['table_name']} t LEFT JOIN categories c ON t.category_id = c.id WHERE t.{$config['id_field']} = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $test = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$test) {
            json_response(['success' => false, 'message' => 'Test not found'], 404);
        }

        if (!simpleCheckPermission($user_data, 'get', $test['added_by'] ?? null)) {
            json_response(['success' => false, 'message' => 'Permission denied to view this test'], 403);
        }

        json_response(['success' => true, 'data' => $test]);
    } catch (Exception $e) {
        error_log("Get test error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch test'], 500);
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

    try {
        $data = [];
        foreach ($config['allowed_fields'] as $field) {
            if (isset($input[$field])) {
                $data[$field] = $input[$field];
            }
        }
        $data['added_by'] = $data['added_by'] ?? $user_data['user_id'];

        if ($id) {
            $set_clause = implode(', ', array_map(fn($field) => "$field = ?", array_keys($data)));
            $sql = "UPDATE {$config['table_name']} SET $set_clause, updated_at = NOW() WHERE {$config['id_field']} = ?";
            $values = array_merge(array_values($data), [$id]);
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            $test_id = $id;
            $action = 'updated';
        } else {
            // Use smart upsert to prevent duplicates
            $uniqueWhere = getUniqueWhere('test', $data);
            
            if (empty($uniqueWhere)) {
                json_response(['success' => false, 'message' => 'Cannot determine unique criteria for duplicate prevention'], 400);
            }

            // Use smart upsert function
            $result = smartUpsert($pdo, $config['table_name'], $uniqueWhere, $data, [
                'compare_timestamps' => true,
                'force_update' => false
            ]);
            
            if ($result['action'] === 'error') {
                json_response(['success' => false, 'message' => $result['message']], 500);
            }
            
            $test_id = $result['id'];
            $action = $result['action'];
        }

        $stmt = $pdo->prepare("SELECT t.*, c.name as category_name FROM {$config['table_name']} t LEFT JOIN categories c ON t.category_id = c.id WHERE t.id = ?");
        $stmt->execute([$test_id]);
        $saved_test = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response([
            'success' => true,
            'message' => "Test {$action} successfully",
            'data' => $saved_test,
            'id' => $test_id
        ]);
    } catch (Exception $e) {
        error_log("Save test error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to save test'], 500);
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
        json_response(['success' => false, 'message' => 'Test ID is required'], 400);
    }

    try {
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

        if ($result) {
            json_response(['success' => true, 'message' => 'Test deleted successfully']);
        } else {
            json_response(['success' => false, 'message' => 'Failed to delete test'], 500);
        }
    } catch (Exception $e) {
        error_log("Delete test error: " . $e->getMessage());
function handleStats($pdo) {
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        json_response([
            'success' => false,
            'message' => 'Authentication required',
            'debug_info' => getAuthDebugInfo()
        ], 401);
    }
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to view stats'], 403);
    }

    try {
        $stats = [];

        // Total tests
        $stmt = $pdo->query('SELECT COUNT(*) FROM tests');
        $stats['total'] = (int) $stmt->fetchColumn();

        // Active tests
        $stmt = $pdo->query('SELECT COUNT(*) FROM tests WHERE status = "active" OR status IS NULL');
        $stats['active'] = (int) $stmt->fetchColumn();

        // Categories count
        $stmt = $pdo->query('SELECT COUNT(DISTINCT category_id) FROM tests WHERE category_id IS NOT NULL');
        $stats['categories'] = (int) $stmt->fetchColumn();

        // Test entries count - check if entries table exists
        try {
            $entriesStmt = $pdo->query('SELECT COUNT(*) FROM entries');
            $stats['entries'] = (int) $entriesStmt->fetchColumn();
        } catch (Exception $e) {
            // Table might not exist, try alternative table names
            try {
                $entriesStmt = $pdo->query('SELECT COUNT(*) FROM test_entries');
                $stats['entries'] = (int) $entriesStmt->fetchColumn();
            } catch (Exception $e2) {
                $stats['entries'] = 0;
            }
        }

        json_response(['success' => true, 'data' => $stats]);
    } catch (Exception $e) {
        error_log("Stats error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch statistics'], 500);
    }
}