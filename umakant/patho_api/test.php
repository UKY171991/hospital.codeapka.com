<?php
/**
 * Test API - Enhanced CRUD operations for tests with main category and test category relationships
 * Supports: CREATE, READ, UPDATE, DELETE operations with proper category hierarchy
 * Authentication: Multiple methods supported
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key, X-Api-Key, X-API-Secret');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
require_once __DIR__ . '/../inc/api_config.php';
require_once __DIR__ . '/../inc/simple_auth.php';

// Entity Configuration for Tests
$entity_config = [
    'table_name' => 'tests',
    'id_field' => 'id',
    'required_fields' => ['name', 'main_category_id'],
    'allowed_fields' => [
        'name', 'category_id', 'main_category_id', 'method', 'price', 'description',
        'min_male', 'max_male', 'min_female', 'max_female', 'min_child', 'max_child',
        'min', 'max', 'unit', 'default_result', 'reference_range',
        'test_code', 'shortcut', 'sub_heading', 'print_new_page', 'specimen', 'added_by'
    ],
    'list_fields' => 't.id, t.name, t.category_id, t.main_category_id, t.price, t.unit, t.specimen, t.default_result, t.reference_range, t.min, t.max, t.description, t.min_male, t.max_male, t.min_female, t.max_female, t.min_child, t.max_child, t.sub_heading, t.test_code, t.method, t.print_new_page, t.shortcut, t.added_by, t.created_at, t.updated_at, u.username as added_by_username, c.name as category_name, mc.name as main_category_name',
    'get_fields' => 't.*, u.username as added_by_username, c.name as category_name, c.main_category_id, mc.name as main_category_name'
];

// Authenticate user at the beginning of the script
$user_data = simpleAuthenticate($pdo);
if (!$user_data) {
    json_response(['success' => false, 'message' => 'Authentication required', 'debug_info' => getAuthDebugInfo()], 401);
}

// Determine action - Fixed to properly handle explicit action parameters
$requestMethod = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? null;

// If no explicit action is provided, determine from HTTP method and parameters
if (!$action) {
    if ($requestMethod === 'GET' && isset($_GET['id'])) {
        $action = 'get';
    } elseif ($requestMethod === 'GET' && !isset($_GET['id'])) {
        $action = 'list';
    } elseif ($requestMethod === 'POST' || $requestMethod === 'PUT') {
        $action = 'save';
    } elseif ($requestMethod === 'DELETE') {
        $action = 'delete';
    } else {
        $action = 'list'; // default fallback
    }
}

// Debug: Log the determined action
error_log("Test API: Processing action '$action' for method '$requestMethod'");
$GLOBALS['current_action'] = $action;

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
        case 'main_categories':
            handleMainCategories($pdo, $user_data);
            break;
        case 'categories':
            handleCategories($pdo, $user_data);
            break;
        case 'categories_by_main':
            handleCategoriesByMain($pdo, $user_data);
            break;
        case 'stats':
            handleStats($pdo, $user_data);
            break;
        case 'debug':
            handleDebug($pdo, $user_data);
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
        $stmt = $pdo->query("SELECT t.id, t.name, t.category_id, t.main_category_id, t.price, t.unit, t.specimen, t.default_result, t.reference_range, t.min, t.max, t.description, t.min_male, t.max_male, t.min_female, t.max_female, t.min_child, t.max_child, t.sub_heading, t.test_code, t.method, t.print_new_page, t.shortcut, t.added_by, t.created_at, t.updated_at, c.name as category_name, mc.name as main_category_name 
                            FROM {$config['table_name']} t 
                            LEFT JOIN categories c ON t.category_id = c.id 
                            LEFT JOIN main_test_categories mc ON t.main_category_id = mc.id 
                            ORDER BY mc.name, c.name, t.name");
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $tests]);
        return;
    }

    $scopeIds = getScopedUserIds($pdo, $user_data);
    $where = '';
    $paramsScope = [];
    $paramsFiltered = [];

    // Build WHERE clause for user scoping
    if (is_array($scopeIds)) {
        $placeholders = implode(',', array_fill(0, count($scopeIds), '?'));
        $where = ' WHERE t.added_by IN (' . $placeholders . ')';
        $paramsScope = $scopeIds;
        $paramsFiltered = $scopeIds;
    }

    $draw = (int)($_REQUEST['draw'] ?? 1);
    $start = (int)($_REQUEST['start'] ?? 0);
    $length = (int)($_REQUEST['length'] ?? 25);
    $search = $_REQUEST['search']['value'] ?? '';

    // Enhanced base query with main categories
    $baseQuery = "{$config['table_name']} t 
                  LEFT JOIN categories c ON t.category_id = c.id 
                  LEFT JOIN main_test_categories mc ON t.main_category_id = mc.id 
                  LEFT JOIN users u ON t.added_by = u.id";
    $whereClause = $where;

    // Add search conditions
    if (!empty($search)) {
        $searchWhere = (empty($where) ? ' WHERE ' : ' AND ') . "(t.name LIKE ? OR c.name LIKE ? OR mc.name LIKE ? OR t.test_code LIKE ?)";
        $whereClause .= $searchWhere;
        $searchTerm = "%$search%";
        array_push($paramsFiltered, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    }

    $totalStmt = $pdo->prepare("SELECT COUNT(*) FROM {$config['table_name']} t $where");
    $totalStmt->execute($paramsScope);
    $totalRecords = $totalStmt->fetchColumn();

    $filteredStmt = $pdo->prepare("SELECT COUNT(*) FROM $baseQuery $whereClause");
    $filteredStmt->execute($paramsFiltered);
    $filteredRecords = $filteredStmt->fetchColumn();

    $query = "SELECT {$config['list_fields']} FROM $baseQuery $whereClause ORDER BY t.id DESC LIMIT $start, $length";
    $stmt = $pdo->prepare($query);
    $stmt->execute($paramsFiltered);
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

    $stmt = $pdo->prepare("SELECT {$config['get_fields']} 
                          FROM {$config['table_name']} t 
                          LEFT JOIN categories c ON t.category_id = c.id 
                          LEFT JOIN main_test_categories mc ON c.main_category_id = mc.id 
                          LEFT JOIN users u ON t.added_by = u.id 
                          WHERE t.id = ?");
    $stmt->execute([$id]);
    $test = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$test) {
        json_response(['success' => false, 'message' => 'Test not found'], 404);
    }

    $scopeIds = getScopedUserIds($pdo, $user_data);
    if (is_array($scopeIds) && !in_array((int)$test['added_by'], $scopeIds, true)) {
        json_response(['success' => false, 'message' => 'Permission denied to view this test'], 403);
    }

    json_response(['success' => true, 'data' => $test]);
}

function handleSave($pdo, $config, $user_data) {
    if (!simpleCheckPermission($user_data, 'save')) {
        json_response(['success' => false, 'message' => 'Permission denied to save tests'], 403);
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
        $scopeIds = getScopedUserIds($pdo, $user_data);
        if (is_array($scopeIds) && !in_array((int)$existing['added_by'], $scopeIds, true)) {
            json_response(['success' => false, 'message' => 'Permission denied to update this test'], 403);
        }
    }

    foreach ($config['required_fields'] as $field) {
        if (empty($input[$field])) {
            json_response(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'], 400);
        }
    }

    // Validate main category exists (required)
    if (!empty($input['main_category_id'])) {
        $stmt = $pdo->prepare("SELECT id FROM main_test_categories WHERE id = ?");
        $stmt->execute([$input['main_category_id']]);
        if (!$stmt->fetch()) {
            json_response(['success' => false, 'message' => 'Invalid main category selected'], 400);
        }
    }

    // Validate category exists
    if (!empty($input['category_id'])) {
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
        $stmt->execute([$input['category_id']]);
        if (!$stmt->fetch()) {
            json_response(['success' => false, 'message' => 'Invalid category selected'], 400);
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

    $stmt = $pdo->prepare("SELECT {$config['get_fields']} 
                          FROM {$config['table_name']} t 
                          LEFT JOIN categories c ON t.category_id = c.id 
                          LEFT JOIN main_test_categories mc ON c.main_category_id = mc.id 
                          LEFT JOIN users u ON t.added_by = u.id 
                          WHERE t.id = ?");
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
    if (!simpleCheckPermission($user_data, 'delete')) {
        json_response(['success' => false, 'message' => 'Permission denied to delete tests'], 403);
    }

    $id = $_REQUEST['id'] ?? $_GET['id'] ?? $_POST['id'] ?? null;
    if (!$id || !is_numeric($id)) {
        json_response(['success' => false, 'message' => 'Valid test ID is required'], 400);
    }

    $id = (int)$id;

    $stmt = $pdo->prepare("SELECT id, name, added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
    $stmt->execute([$id]);
    $test = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$test) {
        json_response(['success' => false, 'message' => 'Test not found'], 404);
    }

    $scopeIds = getScopedUserIds($pdo, $user_data);
    if (is_array($scopeIds) && !in_array((int)$test['added_by'], $scopeIds, true)) {
        json_response(['success' => false, 'message' => 'Permission denied to delete this test'], 403);
    }

    // Check for foreign key constraints
    $constraintCheck = checkTestConstraints($pdo, $id);
    if (!$constraintCheck['can_delete']) {
        json_response([
            'success' => false,
            'message' => 'Cannot delete test: ' . $constraintCheck['reason'],
            'debug' => [
                'constraints' => $constraintCheck['constraints'],
                'test_name' => $test['name']
            ]
        ], 409);
    }

    $stmt = $pdo->prepare("DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
    $result = $stmt->execute([$id]);
    $rowsAffected = $stmt->rowCount();

    if ($result && $rowsAffected > 0) {
        json_response([
            'success' => true,
            'message' => 'Test deleted successfully',
            'data' => [
                'deleted_id' => $id,
                'deleted_name' => $test['name'],
                'rows_affected' => $rowsAffected
            ]
        ]);
    } else {
        json_response(['success' => false, 'message' => 'Failed to delete test'], 500);
    }
}

function handleStats($pdo, $user_data) {
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to view stats'], 403);
    }

    $stats = [];
    $stmt = $pdo->query('SELECT COUNT(*) FROM tests');
    $stats['total'] = (int) $stmt->fetchColumn();

    $stmt = $pdo->query('SELECT COUNT(DISTINCT category_id) FROM tests WHERE category_id IS NOT NULL');
    $stats['categories'] = (int) $stmt->fetchColumn();

    $stmt = $pdo->query('SELECT COUNT(*) FROM main_test_categories');
    $stats['main_categories'] = (int) $stmt->fetchColumn();

    $stmt = $pdo->query('SELECT COUNT(*) FROM categories');
    $stats['test_categories'] = (int) $stmt->fetchColumn();

    json_response(['success' => true, 'data' => $stats]);
}

/**
 * Get list of main test categories
 */
function handleMainCategories($pdo, $user_data) {
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list main categories'], 403);
    }

    $stmt = $pdo->query("SELECT id, name, description FROM main_test_categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    json_response(['success' => true, 'data' => $categories]);
}

/**
 * Get list of test categories (optionally filtered by main category)
 */
function handleCategories($pdo, $user_data) {
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list categories'], 403);
    }

    $mainCategoryId = $_GET['main_category_id'] ?? null;
    
    if ($mainCategoryId) {
        $stmt = $pdo->prepare("SELECT c.id, c.name, c.description, c.main_category_id, mc.name as main_category_name 
                              FROM categories c 
                              LEFT JOIN main_test_categories mc ON c.main_category_id = mc.id 
                              WHERE c.main_category_id = ? 
                              ORDER BY c.name");
        $stmt->execute([$mainCategoryId]);
    } else {
        $stmt = $pdo->query("SELECT c.id, c.name, c.description, c.main_category_id, mc.name as main_category_name 
                            FROM categories c 
                            LEFT JOIN main_test_categories mc ON c.main_category_id = mc.id 
                            ORDER BY mc.name, c.name");
    }
    
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    json_response(['success' => true, 'data' => $categories]);
}

/**
 * Get test categories by main category ID
 */
function handleCategoriesByMain($pdo, $user_data) {
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list categories'], 403);
    }

    $mainCategoryId = $_GET['main_category_id'] ?? $_POST['main_category_id'] ?? null;
    if (!$mainCategoryId) {
        json_response(['success' => false, 'message' => 'Main category ID is required'], 400);
    }

    $stmt = $pdo->prepare("SELECT id, name, description FROM categories WHERE main_category_id = ? ORDER BY name");
    $stmt->execute([$mainCategoryId]);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    json_response(['success' => true, 'data' => $categories]);
}

/**
 * Check if a test can be safely deleted (no foreign key constraints)
 */
function checkTestConstraints($pdo, $testId) {
    $constraints = [];
    $canDelete = true;
    $reason = '';

    try {
        // Check common tables that might reference tests
        $tablesToCheck = [
            'test_results' => 'test_id',
            'patient_tests' => 'test_id',
            'lab_reports' => 'test_id',
            'entries' => 'test_id'
        ];

        foreach ($tablesToCheck as $table => $column) {
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE $column = ?");
                $stmt->execute([$testId]);
                $count = $stmt->fetchColumn();
                
                if ($count > 0) {
                    $constraints[] = [
                        'table' => $table,
                        'column' => $column,
                        'count' => $count
                    ];
                    $canDelete = false;
                    $reason .= "$count records in $table; ";
                }
            } catch (PDOException $e) {
                // Table might not exist, which is fine
                continue;
            }
        }

        if (!$canDelete) {
            $reason = "Referenced by: " . rtrim($reason, '; ');
        }

    } catch (Exception $e) {
        // If we can't check constraints, allow deletion but log the issue
        error_log("Warning: Could not check test constraints: " . $e->getMessage());
    }

    return [
        'can_delete' => $canDelete,
        'reason' => $reason,
        'constraints' => $constraints
    ];
}

/**
 * Debug endpoint to help troubleshoot API issues
 */
function handleDebug($pdo, $user_data) {
    $debugInfo = [
        'success' => true,
        'message' => 'Debug information',
        'authentication' => [
            'authenticated' => !empty($user_data),
            'user_data' => $user_data,
            'auth_method' => $user_data['auth_method'] ?? 'none'
        ],
        'request_info' => [
            'method' => $_SERVER['REQUEST_METHOD'],
            'determined_action' => $GLOBALS['current_action'] ?? 'not set',
            'explicit_action_param' => $_REQUEST['action'] ?? 'not provided',
            'query_params' => $_GET,
            'post_params' => $_POST,
            'request_params' => $_REQUEST,
            'headers' => getallheaders() ?: 'not available'
        ],
        'database_info' => [
            'connected' => !empty($pdo),
            'tests_table_exists' => false,
            'categories_table_exists' => false,
            'main_categories_table_exists' => false,
            'sample_tests' => []
        ],
        'permissions' => [
            'can_list' => simpleCheckPermission($user_data, 'list'),
            'can_save' => simpleCheckPermission($user_data, 'save'),
            'can_delete' => simpleCheckPermission($user_data, 'delete')
        ]
    ];

    // Check database info
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'tests'");
        $debugInfo['database_info']['tests_table_exists'] = $stmt->rowCount() > 0;
        
        $stmt = $pdo->query("SHOW TABLES LIKE 'categories'");
        $debugInfo['database_info']['categories_table_exists'] = $stmt->rowCount() > 0;
        
        $stmt = $pdo->query("SHOW TABLES LIKE 'main_test_categories'");
        $debugInfo['database_info']['main_categories_table_exists'] = $stmt->rowCount() > 0;
        
        if ($debugInfo['database_info']['tests_table_exists']) {
            $stmt = $pdo->query("SELECT t.id, t.name, t.main_category_id, c.name as category_name, mc.name as main_category_name 
                                FROM tests t 
                                LEFT JOIN categories c ON t.category_id = c.id 
                                LEFT JOIN main_test_categories mc ON t.main_category_id = mc.id 
                                LIMIT 3");
            $debugInfo['database_info']['sample_tests'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stmt = $pdo->query("SELECT COUNT(*) FROM tests");
            $debugInfo['database_info']['total_tests'] = $stmt->fetchColumn();
        }
    } catch (Exception $e) {
        $debugInfo['database_info']['error'] = $e->getMessage();
    }

    json_response($debugInfo);
}
?>