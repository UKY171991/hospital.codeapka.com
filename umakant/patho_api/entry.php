<?php
/**
 * Entry API - Comprehensive CRUD operations for test entries
 * Supports: CREATE, READ, UPDATE, DELETE operations with statistics
 * Authentication: Multiple methods supported
 * Database Schema: Complete 16-column support with enriched data
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

// Robust: always return JSON for unhandled errors
set_exception_handler(function($ex){
    json_response(['success' => false, 'message' => 'Server error', 'error' => $ex->getMessage()], 500);
});
set_error_handler(function($severity, $message, $file, $line){
    throw new ErrorException($message, 0, $severity, $file, $line);
});
register_shutdown_function(function(){
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'Fatal error', 'error' => $e['message']]);
    }
});

// Database connection with error handling
try {
    require_once __DIR__ . '/../inc/connection.php';
    $db_available = true;
} catch (Exception $e) {
    $db_available = false;
    json_response(['success' => false, 'message' => 'Database connection failed', 'error' => $e->getMessage()], 500);
}

require_once __DIR__ . '/../inc/ajax_helpers.php';
require_once __DIR__ . '/../inc/api_config.php';
require_once __DIR__ . '/../inc/smart_upsert.php';
require_once __DIR__ . '/../inc/simple_auth.php';

// Entity Configuration for Entries
$entity_config = [
    'table_name' => 'entries',
    'id_field' => 'id',
    'required_fields' => ['patient_id'], // test_id is now optional for multiple tests
    'allowed_fields' => [
        // Exact columns per DB schema
        'server_id',
        'patient_id',
        'doctor_id',
        'test_id', // Primary test for backward compatibility
        'entry_date',
        'result_value',
        'unit',
        'remarks',
        'status',
        'added_by',
        'price',
        'discount_amount',
        'total_price',
        'created_at',
        'updated_at',
        'reported_date',
        'result_status',
        'grouped',
        'tests_count',
        'test_ids',
        'test_names',
        'test_results'
    ]
];

// Get action from request
$action = $_REQUEST['action'] ?? $_SERVER['REQUEST_METHOD'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Map HTTP methods to actions
switch($requestMethod) {
    case 'GET':
        $action = isset($_GET['id']) ? 'get' : ($_GET['action'] ?? 'list');
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
        case 'stats':
            handleStats($pdo);
            break;
        case 'add_test':
            handleAddTest($pdo);
            break;
        case 'remove_test':
            handleRemoveTest($pdo);
            break;
        case 'get_tests':
            handleGetTests($pdo);
            break;
        case 'update_test_result':
            handleUpdateTestResult($pdo);
            break;
        case 'report_list':
            handleReportList($pdo);
            break;
        default:
            json_response(['success' => false, 'message' => 'Invalid action specified'], 400);
    }
} catch (Exception $e) {
    error_log("Entry API Uncaught Error: " . $e->getMessage());
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
        json_response(['success' => false, 'message' => 'Permission denied to list entries'], 403);
    }

    try {
        // Role-based scoping by added_by
        $scopeIds = getScopedUserIds($pdo, $user_data); // null => no restriction
        $where = '';
        $params = [];
        if (is_array($scopeIds)) {
            $placeholders = implode(',', array_fill(0, count($scopeIds), '?'));
            $where = ' WHERE e.added_by IN (' . $placeholders . ')';
            $params = $scopeIds;
        }

        // Primary (rich) query
        // Join tests table only if column test_id exists (older schema may not have it)
        $testJoin = '';
        try {
            $chk = $pdo->query("SHOW COLUMNS FROM {$config['table_name']} LIKE 'test_id'")->fetch();
            if ($chk) { $testJoin = " LEFT JOIN tests t ON e.test_id = t.id "; }
        } catch (Throwable $ignore) { $testJoin = ''; }

        $sqlRich = "SELECT e.*, p.name as patient_name, p.uhid as patient_uhid, d.name as doctor_name, ".
                ($testJoin ? "t.name as test_name, " : "NULL as test_name, ") .
                "u.username as added_by_username
                FROM {$config['table_name']} e 
                LEFT JOIN patients p ON e.patient_id = p.id 
                $testJoin
                LEFT JOIN doctors d ON e.doctor_id = d.id 
                LEFT JOIN users u ON e.added_by = u.id" .
                $where .
                " ORDER BY COALESCE(e.entry_date, e.created_at) DESC, e.id DESC";

        try {
            $stmt = $pdo->prepare($sqlRich);
            $stmt->execute($params);
            $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $inner) {
            // Fallback query with minimal dependencies
            $sqlFallback = "SELECT e.* FROM {$config['table_name']} e" . $where . " ORDER BY e.id DESC";
            $stmt = $pdo->prepare($sqlFallback);
            $stmt->execute($params);
            $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Enrich each entry with its tests from entry_tests table
        foreach ($entries as &$entry) {
            try {
                $testsStmt = $pdo->prepare("
                    SELECT et.*, t.name as test_name, t.rate as test_rate, t.unit as test_unit
                    FROM entry_tests et
                    LEFT JOIN tests t ON et.test_id = t.id
                    WHERE et.entry_id = ?
                    ORDER BY et.id ASC
                ");
                $testsStmt->execute([$entry['id']]);
                $tests = $testsStmt->fetchAll(PDO::FETCH_ASSOC);
                $entry['tests'] = $tests;
                $entry['tests_count'] = count($tests);
                
            } catch (Throwable $e) {
                error_log("Entry {$entry['id']}: Error fetching tests: " . $e->getMessage());
                $entry['tests'] = [];
                $entry['tests_count'] = 0;
            }
        }
        unset($entry); // Break reference

        json_response(['success' => true, 'data' => $entries, 'total' => count($entries)]);
    } catch (Exception $e) {
        error_log("List entries error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch entries', 'error' => $e->getMessage()], 500);
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
        json_response(['success' => false, 'message' => 'Entry ID is required'], 400);
    }

    try {
        $sql = "SELECT e.*, p.name as patient_name, p.uhid as patient_uhid, d.name as doctor_name, t.name as test_name, u.username as added_by_username
                FROM {$config['table_name']} e 
                LEFT JOIN patients p ON e.patient_id = p.id 
                LEFT JOIN tests t ON e.test_id = t.id 
                LEFT JOIN doctors d ON e.doctor_id = d.id 
                LEFT JOIN users u ON e.added_by = u.id
                WHERE e.{$config['id_field']} = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$entry) {
            json_response(['success' => false, 'message' => 'Entry not found'], 404);
        }

        // Scoped visibility
        $scopeIds = getScopedUserIds($pdo, $user_data);
        if (is_array($scopeIds)) {
            if (!in_array((int)$entry['added_by'], $scopeIds, true)) {
                json_response(['success' => false, 'message' => 'Permission denied to view this entry'], 403);
            }
        }

        // Include tests from entry_tests table
        try {
            $testsStmt = $pdo->prepare("
                SELECT et.*, t.name as test_name, t.rate as test_rate, t.unit as test_unit
                FROM entry_tests et
                LEFT JOIN tests t ON et.test_id = t.id
                WHERE et.entry_id = ?
                ORDER BY et.id ASC
            ");
            $testsStmt->execute([$id]);
            $tests = $testsStmt->fetchAll(PDO::FETCH_ASSOC);
            $entry['tests'] = $tests;
            $entry['tests_count'] = count($tests);
        } catch (Throwable $ignore) {
            $entry['tests'] = [];
            $entry['tests_count'] = 0;
        }

        json_response(['success' => true, 'data' => $entry]);
    } catch (Exception $e) {
        error_log("Get entry error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch entry'], 500);
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
            json_response(['success' => false, 'message' => 'Entry not found'], 404);
        }
        // Scoped visibility
        $scopeIds = getScopedUserIds($pdo, $user_data);
        if (is_array($scopeIds)) {
            if (!in_array((int)$existing['added_by'], $scopeIds, true)) {
                json_response(['success' => false, 'message' => 'Permission denied to update this entry'], 403);
            }
        }
    } else { // Create
        // Any authenticated user can create; added_by will be current user
    }

    foreach ($config['required_fields'] as $field) {
        if (empty($input[$field])) {
            json_response(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'], 400);
        }
    }

    try {
        $data = [];
        foreach ($config['allowed_fields'] as $field) {
            if (array_key_exists($field, $input)) {
                $data[$field] = $input[$field];
            }
        }

        // Defaults and normalization
        $data['added_by'] = $data['added_by'] ?? $user_data['user_id'];
        if (isset($data['status']) && !in_array($data['status'], ['pending','completed','cancelled'], true)) {
            $data['status'] = 'pending';
        }
        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }
        // Normalize entry_date to Y-m-d (accepts date or datetime)
        if (isset($data['entry_date']) && $data['entry_date'] !== '') {
            $ts = strtotime($data['entry_date']);
            if ($ts !== false) {
                $data['entry_date'] = date('Y-m-d', $ts);
            }
        } else {
            // Set default entry_date to current date if not provided
            $data['entry_date'] = date('Y-m-d');
        }
        // Normalize decimals
        foreach (['price','discount_amount','total_price'] as $numField) {
            if (isset($data[$numField]) && $data[$numField] !== '') {
                $data[$numField] = number_format((float)$data[$numField], 2, '.', '');
            }
        }
        // Compute total_price if not provided but price/discount present
        if ((!isset($data['total_price']) || $data['total_price'] === '') && isset($data['price'])) {
            $price = (float)($data['price'] ?? 0);
            $disc = (float)($data['discount_amount'] ?? 0);
            $data['total_price'] = number_format($price - $disc, 2, '.', '');
        }

        if ($id) {
            $set_clause = implode(', ', array_map(fn($field) => "$field = ?", array_keys($data)));
            // Do not override DB-managed updated_at if client provided none
            if (!array_key_exists('updated_at', $data)) {
                $sql = "UPDATE {$config['table_name']} SET $set_clause, updated_at = NOW() WHERE {$config['id_field']} = ?";
            } else {
                $sql = "UPDATE {$config['table_name']} SET $set_clause WHERE {$config['id_field']} = ?";
            }
            $values = array_merge(array_values($data), [$id]);
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            $entry_id = $id;
            $action = 'updated';
        } else {
            $cols = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $sql = "INSERT INTO {$config['table_name']} ($cols) VALUES ($placeholders)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_values($data));
            $entry_id = $pdo->lastInsertId();
            $action = 'inserted';
        }

        $stmt = $pdo->prepare("SELECT e.*, p.name as patient_name, d.name as doctor_name, t.name as test_name FROM {$config['table_name']} e LEFT JOIN patients p ON e.patient_id = p.id LEFT JOIN doctors d ON e.doctor_id = d.id LEFT JOIN tests t ON e.test_id = t.id WHERE e.id = ?");
        $stmt->execute([$entry_id]);
        $saved_entry = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response([
            'success' => true,
            'message' => "Entry {$action} successfully",
            'data' => $saved_entry,
            'id' => $entry_id
        ]);
    } catch (Exception $e) {
        error_log("Save entry error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to save entry'], 500);
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
        json_response(['success' => false, 'message' => 'Entry ID is required'], 400);
    }

    try {
        $stmt = $pdo->prepare("SELECT added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$entry) {
            json_response(['success' => false, 'message' => 'Entry not found'], 404);
        }

        // Scoped visibility
        $scopeIds = getScopedUserIds($pdo, $user_data);
        if (is_array($scopeIds)) {
            if (!in_array((int)$entry['added_by'], $scopeIds, true)) {
                json_response(['success' => false, 'message' => 'Permission denied to delete this entry'], 403);
            }
        }

        $stmt = $pdo->prepare("DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $result = $stmt->execute([$id]);

        if ($result) {
            json_response(['success' => true, 'message' => 'Entry deleted successfully']);
        } else {
            json_response(['success' => false, 'message' => 'Failed to delete entry'], 500);
        }
    } catch (Exception $e) {
        error_log("Delete entry error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to delete entry'], 500);
    }
}

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
        $stmt = $pdo->query("SELECT COUNT(*) FROM entries");
        $stats['total'] = (int) $stmt->fetchColumn();
        $stmt = $pdo->query("SELECT COUNT(*) FROM entries WHERE status = 'pending'");
        $stats['pending'] = (int) $stmt->fetchColumn();
        $stmt = $pdo->query("SELECT COUNT(*) FROM entries WHERE status = 'completed'");
        $stats['completed'] = (int) $stmt->fetchColumn();
        $stmt = $pdo->query("SELECT COUNT(*) FROM entries WHERE DATE(COALESCE(entry_date, created_at)) = CURDATE()");
        $stats['today'] = (int) $stmt->fetchColumn();
        
        json_response(['success' => true, 'data' => $stats]);
    } catch (Exception $e) {
        error_log("Stats error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch statistics'], 500);
    }
}

// Handle adding a test to an entry
function handleAddTest($pdo) {
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        json_response([
            'success' => false, 
            'message' => 'Authentication required',
            'debug_info' => getAuthDebugInfo()
        ], 401);
    }
    
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $entry_id = $input['entry_id'] ?? null;
    $test_id = $input['test_id'] ?? null;
    
    if (!$entry_id || !$test_id) {
        json_response(['success' => false, 'message' => 'Entry ID and Test ID are required'], 400);
    }
    
    try {
        // Check if entry exists
        $stmt = $pdo->prepare("SELECT id FROM entries WHERE id = ?");
        $stmt->execute([$entry_id]);
        if (!$stmt->fetch()) {
            json_response(['success' => false, 'message' => 'Entry not found'], 404);
        }
        
        // Check if test exists
        $stmt = $pdo->prepare("SELECT id, name, price, unit FROM tests WHERE id = ?");
        $stmt->execute([$test_id]);
        $test = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$test) {
            json_response(['success' => false, 'message' => 'Test not found'], 404);
        }
        
        // Insert into entry_tests table
        $stmt = $pdo->prepare("INSERT INTO entry_tests (entry_id, test_id, result_value, unit, status, price, total_price) VALUES (?, ?, ?, ?, 'pending', ?, ?)");
        $result = $stmt->execute([
            $entry_id, 
            $test_id, 
            $input['result_value'] ?? null,
            $input['unit'] ?? $test['unit'],
            $input['price'] ?? $test['price'],
            $input['price'] ?? $test['price']
        ]);
        
        if ($result) {
            // Update entry's test count and aggregated data
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM entry_tests WHERE entry_id = ?");
            $stmt->execute([$entry_id]);
            $tests_count = $stmt->fetchColumn();
            
            $stmt = $pdo->prepare("SELECT GROUP_CONCAT(test_id ORDER BY test_id) FROM entry_tests WHERE entry_id = ?");
            $stmt->execute([$entry_id]);
            $test_ids = $stmt->fetchColumn();
            
            $stmt = $pdo->prepare("SELECT GROUP_CONCAT(t.name ORDER BY et.test_id SEPARATOR ', ') FROM entry_tests et JOIN tests t ON et.test_id = t.id WHERE et.entry_id = ?");
            $stmt->execute([$entry_id]);
            $test_names = $stmt->fetchColumn();
            
            $stmt = $pdo->prepare("UPDATE entries SET grouped = ?, tests_count = ?, test_ids = ?, test_names = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([
                $tests_count > 1 ? 1 : 0,
                $tests_count,
                $test_ids,
                $test_names,
                $entry_id
            ]);
            
            json_response([
                'success' => true, 
                'message' => 'Test added to entry successfully',
                'data' => [
                    'entry_id' => $entry_id,
                    'test_id' => $test_id,
                    'test_name' => $test['name'],
                    'tests_count' => $tests_count
                ]
            ]);
        } else {
            json_response(['success' => false, 'message' => 'Failed to add test to entry'], 500);
        }
    } catch (Exception $e) {
        error_log("Add test error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to add test to entry'], 500);
    }
}

// Handle removing a test from an entry
function handleRemoveTest($pdo) {
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        json_response([
            'success' => false, 
            'message' => 'Authentication required',
            'debug_info' => getAuthDebugInfo()
        ], 401);
    }
    
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $entry_id = $input['entry_id'] ?? null;
    $test_id = $input['test_id'] ?? null;
    
    if (!$entry_id || !$test_id) {
        json_response(['success' => false, 'message' => 'Entry ID and Test ID are required'], 400);
    }
    
    try {
        // Remove from entry_tests table
        $stmt = $pdo->prepare("DELETE FROM entry_tests WHERE entry_id = ? AND test_id = ?");
        $result = $stmt->execute([$entry_id, $test_id]);
        
        if ($result) {
            // Check remaining tests count
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM entry_tests WHERE entry_id = ?");
            $stmt->execute([$entry_id]);
            $tests_count = $stmt->fetchColumn();
            
            if ($tests_count == 0) {
                // No tests left, delete the entry
                $stmt = $pdo->prepare("DELETE FROM entries WHERE id = ?");
                $stmt->execute([$entry_id]);
                json_response(['success' => true, 'message' => 'Test removed and entry deleted (no tests remaining)']);
            } else {
                // Update entry's test count and aggregated data
                $stmt = $pdo->prepare("SELECT GROUP_CONCAT(test_id ORDER BY test_id) FROM entry_tests WHERE entry_id = ?");
                $stmt->execute([$entry_id]);
                $test_ids = $stmt->fetchColumn();
                
                $stmt = $pdo->prepare("SELECT GROUP_CONCAT(t.name ORDER BY et.test_id SEPARATOR ', ') FROM entry_tests et JOIN tests t ON et.test_id = t.id WHERE et.entry_id = ?");
                $stmt->execute([$entry_id]);
                $test_names = $stmt->fetchColumn();
                
                $stmt = $pdo->prepare("UPDATE entries SET grouped = ?, tests_count = ?, test_ids = ?, test_names = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([
                    $tests_count > 1 ? 1 : 0,
                    $tests_count,
                    $test_ids,
                    $test_names,
                    $entry_id
                ]);
                
                json_response([
                    'success' => true, 
                    'message' => 'Test removed from entry successfully',
                    'data' => [
                        'entry_id' => $entry_id,
                        'test_id' => $test_id,
                        'tests_count' => $tests_count
                    ]
                ]);
            }
        } else {
            json_response(['success' => false, 'message' => 'Failed to remove test from entry'], 500);
        }
    } catch (Exception $e) {
        error_log("Remove test error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to remove test from entry'], 500);
    }
}

// Handle getting all tests for an entry
function handleGetTests($pdo) {
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        json_response([
            'success' => false, 
            'message' => 'Authentication required',
            'debug_info' => getAuthDebugInfo()
        ], 401);
    }
    
    $entry_id = $_GET['entry_id'] ?? null;
    if (!$entry_id) {
        json_response(['success' => false, 'message' => 'Entry ID is required'], 400);
    }
    
    try {
        $sql = "SELECT et.*, t.name as test_name, t.category_id, c.name as category_name, 
                       t.normal_value_male, t.normal_value_female, 
                       t.min_range_male, t.max_range_male, t.min_range_female, t.max_range_female, t.unit as test_unit
                FROM entry_tests et
                LEFT JOIN tests t ON et.test_id = t.id
                LEFT JOIN categories c ON t.category_id = c.id
                WHERE et.entry_id = ?
                ORDER BY et.test_id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$entry_id]);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response(['success' => true, 'data' => $tests]);
    } catch (Exception $e) {
        error_log("Get tests error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch tests'], 500);
    }
}

// Handle updating test result
function handleUpdateTestResult($pdo) {
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        json_response([
            'success' => false, 
            'message' => 'Authentication required',
            'debug_info' => getAuthDebugInfo()
        ], 401);
    }
    
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $entry_test_id = $input['entry_test_id'] ?? null;
    
    if (!$entry_test_id) {
        json_response(['success' => false, 'message' => 'Entry Test ID is required'], 400);
    }
    
    try {
        $update_fields = [];
        $update_values = [];
        
        if (isset($input['result_value'])) {
            $update_fields[] = 'result_value = ?';
            $update_values[] = $input['result_value'];
        }
        if (isset($input['unit'])) {
            $update_fields[] = 'unit = ?';
            $update_values[] = $input['unit'];
        }
        if (isset($input['remarks'])) {
            $update_fields[] = 'remarks = ?';
            $update_values[] = $input['remarks'];
        }
        if (isset($input['status'])) {
            $update_fields[] = 'status = ?';
            $update_values[] = $input['status'];
        }
        if (isset($input['price'])) {
            $update_fields[] = 'price = ?';
            $update_values[] = $input['price'];
        }
        if (isset($input['discount_amount'])) {
            $update_fields[] = 'discount_amount = ?';
            $update_values[] = $input['discount_amount'];
        }
        
        if (empty($update_fields)) {
            json_response(['success' => false, 'message' => 'No fields to update'], 400);
        }
        
        $update_fields[] = 'updated_at = NOW()';
        $update_values[] = $entry_test_id;
        
        $sql = "UPDATE entry_tests SET " . implode(', ', $update_fields) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($update_values);
        
        if ($result) {
            json_response(['success' => true, 'message' => 'Test result updated successfully']);
        } else {
            json_response(['success' => false, 'message' => 'Failed to update test result'], 500);
        }
    } catch (Exception $e) {
        error_log("Update test result error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to update test result'], 500);
    }
}

// Handle report list with filtering
function handleReportList($pdo) {
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        json_response([
            'success' => false,
            'message' => 'Authentication required',
            'debug_info' => getAuthDebugInfo()
        ], 401);
    }
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list reports'], 403);
    }

    try {
        $test_id = $_GET['test_id'] ?? null;
        $doctor_id = $_GET['doctor_id'] ?? null;
        $status = $_GET['status'] ?? '';
        $date_from = $_GET['date_from'] ?? null;
        $date_to = $_GET['date_to'] ?? null;

        // Role-based scoping by added_by
        $scopeIds = getScopedUserIds($pdo, $user_data);
        $where = '';
        $params = [];
        if (is_array($scopeIds)) {
            $placeholders = implode(',', array_fill(0, count($scopeIds), '?'));
            $where = ' WHERE e.added_by IN (' . $placeholders . ')';
            $params = $scopeIds;
        }

        // Base query with all necessary joins
        $query = "SELECT e.*,
                         p.name as patient_name,
                         d.name as doctor_name,
                         t.name as test_name,
                         COALESCE(et.result_value, '') as result,
                         COALESCE(et.status, 'pending') as entry_status,
                         COALESCE(et.price, 0) as test_price,
                         COALESCE(et.discount_amount, 0) as test_discount,
                         (COALESCE(et.price, 0) - COALESCE(et.discount_amount, 0)) as test_total
                  FROM entries e
                  LEFT JOIN patients p ON e.patient_id = p.id
                  LEFT JOIN doctors d ON e.doctor_id = d.id
                  LEFT JOIN entry_tests et ON e.id = et.entry_id
                  LEFT JOIN tests t ON et.test_id = t.id" . $where;

        // Add filters
        if ($test_id) {
            $query .= " AND et.test_id = ?";
            $params[] = $test_id;
        }

        if ($doctor_id) {
            $query .= " AND e.doctor_id = ?";
            $params[] = $doctor_id;
        }

        if ($status) {
            $query .= " AND COALESCE(et.status, 'pending') = ?";
            $params[] = $status;
        }

        if ($date_from) {
            $query .= " AND DATE(e.entry_date) >= ?";
            $params[] = $date_from;
        }

        if ($date_to) {
            $query .= " AND DATE(e.entry_date) <= ?";
            $params[] = $date_to;
        }

        $query .= " ORDER BY e.entry_date DESC, e.id DESC";

        $stmt = $pdo->prepare($query);

        // Execute with parameters if any
        if (!empty($params)) {
            $stmt->execute($params);
        } else {
            $stmt->execute();
        }

        $reports = [];
        $total_amount = 0;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $test_total = isset($row['test_total']) ? (float)$row['test_total'] : 0;

            $reports[] = [
                'entry_id' => $row['id'],
                'entry_date' => $row['entry_date'] ?? $row['created_at'],
                'entry_date_formatted' => date('d M Y', strtotime($row['entry_date'] ?? $row['created_at'])),
                'patient_name' => $row['patient_name'] ?? 'N/A',
                'doctor_name' => $row['doctor_name'] ?? 'N/A',
                'test_name' => $row['test_name'] ?? 'N/A',
                'result' => $row['result'] ?? '',
                'result_display' => !empty($row['result']) ? $row['result'] : 'Pending',
                'entry_status' => $row['entry_status'] ?? 'pending',
                'amount' => $test_total,
                'test_price' => (float)($row['test_price'] ?? 0),
                'test_discount' => (float)($row['test_discount'] ?? 0)
            ];
            $total_amount += $test_total;
        }

        json_response([
            'success' => true,
            'data' => $reports,
            'summary' => [
                'total_records' => count($reports),
                'total_amount' => $total_amount,
                'total_amount_formatted' => number_format($total_amount, 2)
            ]
        ]);

    } catch (Exception $e) {
        error_log('Error in report_list: ' . $e->getMessage());
        json_response([
            'success' => false,
            'message' => 'Failed to fetch reports',
            'error' => $e->getMessage()
        ], 500);
    }
}