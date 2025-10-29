<?php
/**
 * Entry API - Comprehensive CRUD operations for test entries
 * Supports: CREATE, READ, UPDATE, DELETE operations with statistics
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
    'table_name' => 'entries',
    'id_field' => 'id',
    'required_fields' => ['patient_id'],
    'allowed_fields' => [
        'server_id', 'patient_id', 'doctor_id', 'test_id', 'entry_date', 'result_value',
        'unit', 'remarks', 'status', 'added_by', 'price', 'discount_amount', 'total_price',
        'reported_date', 'result_status', 'grouped', 'tests_count', 'test_ids', 'test_names', 'test_results'
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
        case 'list': handleList($pdo, $entity_config, $user_data); break;
        case 'get': handleGet($pdo, $entity_config, $user_data); break;
        case 'save': handleSave($pdo, $entity_config, $user_data); break;
        case 'delete': handleDelete($pdo, $entity_config, $user_data); break;
        case 'stats': handleStats($pdo, $user_data); break;
        case 'add_test': handleAddTest($pdo, $user_data); break;
        case 'remove_test': handleRemoveTest($pdo, $user_data); break;
        case 'get_tests': handleGetTests($pdo, $user_data); break;
        case 'update_test_result': handleUpdateTestResult($pdo, $user_data); break;
        case 'report_list': handleReportList($pdo, $user_data); break;
        default: json_response(['success' => false, 'message' => 'Invalid action specified'], 400);
    }
} catch (Exception $e) {
    error_log("Entry API Uncaught Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An internal server error occurred.'], 500);
}

function handleList($pdo, $config, $user_data) {
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list entries'], 403);
    }

    $scopeIds = getScopedUserIds($pdo, $user_data);
    $where = '';
    $params = [];
    if (is_array($scopeIds)) {
        $placeholders = implode(',', array_fill(0, count($scopeIds), '?'));
        $where = ' WHERE e.added_by IN (' . $placeholders . ')';
        $params = $scopeIds;
    }

    $sql = "SELECT e.*, p.name as patient_name, p.uhid as patient_uhid, d.name as doctor_name, u.username as added_by_username
            FROM {$config['table_name']} e
            LEFT JOIN patients p ON e.patient_id = p.id
            LEFT JOIN doctors d ON e.doctor_id = d.id
            LEFT JOIN users u ON e.added_by = u.id{$where} ORDER BY e.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    json_response(['success' => true, 'data' => $entries, 'total' => count($entries)]);
}

function handleGet($pdo, $config, $user_data) {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Entry ID is required'], 400);
    }

    $sql = "SELECT e.*, 
                   p.name as patient_name, p.uhid as patient_uhid, p.age, p.sex as gender, 
                   p.contact as patient_contact, p.address as patient_address,
                   d.name as doctor_name, d.specialization as doctor_specialization,
                   u.username as added_by_username, u.full_name as added_by_full_name
            FROM {$config['table_name']} e
            LEFT JOIN patients p ON e.patient_id = p.id
            LEFT JOIN doctors d ON e.doctor_id = d.id
            LEFT JOIN users u ON e.added_by = u.id
            WHERE e.{$config['id_field']} = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $entry = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$entry) {
        json_response(['success' => false, 'message' => 'Entry not found'], 404);
    }

    $scopeIds = getScopedUserIds($pdo, $user_data);
    if (is_array($scopeIds) && !in_array((int)$entry['added_by'], $scopeIds, true)) {
        json_response(['success' => false, 'message' => 'Permission denied to view this entry'], 403);
    }

    // Get associated tests with detailed information
    $testsStmt = $pdo->prepare("
        SELECT et.id as entry_test_id,
               et.entry_id,
               et.test_id,
               et.result_value,
               et.unit as et_unit,
               et.remarks,
               et.status,
               et.price,
               et.discount_amount,
               et.total_price,
               et.created_at as et_created_at,
               t.name AS test_name, 
               t.category_id,
               t.unit as test_unit, 
               t.min, 
               t.max,
               t.min_male,
               t.max_male,
               t.min_female,
               t.max_female,
               t.reference_range,
               t.price as test_default_price,
               c.name AS category_name
        FROM entry_tests et
        LEFT JOIN tests t ON et.test_id = t.id
        LEFT JOIN categories c ON t.category_id = c.id
        WHERE et.entry_id = ?
        ORDER BY et.id, t.name
    ");
    $testsStmt->execute([$id]);
    $tests = $testsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format test data
    foreach ($tests as &$test) {
        // Use entry_tests price if available, otherwise use test default price
        $test['price'] = (float)($test['price'] ?? $test['test_default_price'] ?? 0);
        $test['discount_amount'] = (float)($test['discount_amount'] ?? 0);
        $test['total_price'] = $test['price'] - $test['discount_amount'];
        $test['result_value'] = $test['result_value'] ?? '';
        $test['status'] = $test['status'] ?? 'pending';
        
        // Use entry_tests unit if available, otherwise use test unit
        $test['unit'] = $test['et_unit'] ?? $test['test_unit'] ?? '';
        
        // Clean up duplicate fields
        unset($test['et_unit'], $test['test_unit'], $test['test_default_price']);
    }
    
    $entry['tests'] = $tests;
    $entry['tests_count'] = count($tests);

    json_response(['success' => true, 'data' => $entry]);
}

function handleSave($pdo, $config, $user_data) {
    if (!simpleCheckPermission($user_data, 'save')) {
        json_response(['success' => false, 'message' => 'Permission denied to save entries'], 403);
    }

    // Handle both JSON and form data
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = $input['id'] ?? null;
    
    error_log("Entry API Save: Input data: " . json_encode($input));

    if ($id) { // Update
        $stmt = $pdo->prepare("SELECT added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$existing) {
            json_response(['success' => false, 'message' => 'Entry not found'], 404);
        }
        $scopeIds = getScopedUserIds($pdo, $user_data);
        if (is_array($scopeIds) && !in_array((int)$existing['added_by'], $scopeIds, true)) {
            json_response(['success' => false, 'message' => 'Permission denied to update this entry'], 403);
        }
    }

    // Validate required fields
    foreach ($config['required_fields'] as $field) {
        if (empty($input[$field])) {
            json_response(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'], 400);
        }
    }

    // Prepare entry data
    $data = array_intersect_key($input, array_flip($config['allowed_fields']));
    $data['added_by'] = $data['added_by'] ?? $user_data['user_id'];
    $data['entry_date'] = empty($data['entry_date']) ? date('Y-m-d') : date('Y-m-d', strtotime($data['entry_date']));
    
    // Handle optional fields
    $data['status'] = $data['status'] ?? 'pending';
    $data['priority'] = $input['priority'] ?? 'normal';
    $data['referral_source'] = $input['referral_source'] ?? null;
    $data['notes'] = $input['notes'] ?? null;
    $data['subtotal'] = (float)($input['subtotal'] ?? 0);
    $data['discount_amount'] = (float)($input['discount_amount'] ?? 0);
    $data['total_price'] = (float)($input['total_price'] ?? 0);

    try {
        $pdo->beginTransaction();

        if ($id) {
            // Update existing entry
            $updateFields = [];
            $updateParams = [];
            
            foreach ($data as $field => $value) {
                if ($field !== 'added_by') { // Don't change the original creator
                    $updateFields[] = "`$field` = ?";
                    $updateParams[] = $value;
                }
            }
            
            $sql = "UPDATE {$config['table_name']} SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE {$config['id_field']} = ?";
            $updateParams[] = $id;
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($updateParams);
            $entry_id = $id;
            $action_status = 'updated';
        } else {
            // Create new entry
            $cols = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $sql = "INSERT INTO {$config['table_name']} ($cols) VALUES ($placeholders)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_values($data));
            $entry_id = $pdo->lastInsertId();
            $action_status = 'created';
        }

        // Handle tests if provided
        $tests = null;
        if (isset($input['tests'])) {
            if (is_array($input['tests'])) {
                $tests = $input['tests'];
            } else if (is_string($input['tests'])) {
                $tests = json_decode($input['tests'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    error_log('Error decoding tests JSON: ' . json_last_error_msg());
                    $tests = null;
                }
            }
        }

        if ($tests && is_array($tests)) {
            error_log('Processing ' . count($tests) . ' tests for entry ID: ' . $entry_id);
            
            // Delete existing tests for this entry (if updating)
            if ($id) {
                $stmt = $pdo->prepare("DELETE FROM entry_tests WHERE entry_id = ?");
                $stmt->execute([$entry_id]);
                error_log("Deleted existing tests for entry ID: $entry_id");
            }
            
            // Track inserted test IDs to prevent duplicates
            $insertedTestIds = [];
            
            // Insert new tests
            foreach ($tests as $index => $test) {
                if (!empty($test['test_id'])) {
                    $testId = (int)$test['test_id'];
                    
                    // Check for duplicate test ID in this entry
                    if (in_array($testId, $insertedTestIds)) {
                        error_log("Skipping duplicate test ID $testId for entry $entry_id");
                        continue;
                    }
                    
                    $insertedTestIds[] = $testId;
                    
                    $testData = [
                        'entry_id' => $entry_id,
                        'test_id' => $testId,
                        'category_id' => $test['category_id'] ?? null,
                        'main_category_id' => $test['main_category_id'] ?? null,
                        'result_value' => $test['result_value'] ?? null,
                        'unit' => $test['unit'] ?? null,
                        'remarks' => $test['remarks'] ?? null,
                        'status' => $test['status'] ?? 'pending',
                        'price' => (float)($test['price'] ?? 0),
                        'discount_amount' => (float)($test['discount_amount'] ?? 0),
                        'total_price' => (float)($test['price'] ?? 0) - (float)($test['discount_amount'] ?? 0)
                    ];
                    
                    $testFields = array_keys($testData);
                    $testPlaceholders = ':' . implode(', :', $testFields);
                    
                    $testSql = "INSERT INTO entry_tests (`" . implode('`, `', $testFields) . "`) VALUES ($testPlaceholders)";
                    
                    $testStmt = $pdo->prepare($testSql);
                    $result = $testStmt->execute($testData);
                    
                    if ($result) {
                        $insertedTestId = $pdo->lastInsertId();
                        error_log("Successfully inserted test with ID: $insertedTestId for entry: $entry_id");
                    } else {
                        error_log("Failed to insert test $index: " . json_encode($testStmt->errorInfo()));
                    }
                }
            }
            
            // Refresh entry aggregates
            refreshEntryAggregates($pdo, $entry_id);
        }

        $pdo->commit();

        // Get the saved entry with related data
        $stmt = $pdo->prepare("
            SELECT e.*, p.name as patient_name, p.uhid as patient_uhid, d.name as doctor_name 
            FROM {$config['table_name']} e
            LEFT JOIN patients p ON e.patient_id = p.id
            LEFT JOIN doctors d ON e.doctor_id = d.id
            WHERE e.id = ?
        ");
        $stmt->execute([$entry_id]);
        $saved_entry = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response([
            'success' => true,
            'message' => "Entry {$action_status} successfully",
            'data' => $saved_entry,
            'id' => $entry_id
        ]);

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log('Error saving entry: ' . $e->getMessage());
        
        // Handle specific error codes
        $errorMessage = 'Failed to save entry';
        if (strpos($e->getMessage(), '1062') !== false) {
            $errorMessage = 'Duplicate entry detected. Please check for duplicate tests.';
        } else {
            $errorMessage = $e->getMessage();
        }
        
        json_response(['success' => false, 'message' => $errorMessage, 'error' => $e->getMessage()], 500);
    }
}

function handleDelete($pdo, $config, $user_data) {
    if (!simpleCheckPermission($user_data, 'delete')) {
        json_response(['success' => false, 'message' => 'Permission denied to delete entries'], 403);
    }

    $id = $_REQUEST['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Entry ID is required'], 400);
    }

    $stmt = $pdo->prepare("SELECT added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
    $stmt->execute([$id]);
    $entry = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$entry) {
        json_response(['success' => false, 'message' => 'Entry not found'], 404);
    }

    $scopeIds = getScopedUserIds($pdo, $user_data);
    if (is_array($scopeIds) && !in_array((int)$entry['added_by'], $scopeIds, true)) {
        json_response(['success' => false, 'message' => 'Permission denied to delete this entry'], 403);
    }

    $pdo->prepare("DELETE FROM entry_tests WHERE entry_id = ?")->execute([$id]);
    $result = $pdo->prepare("DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?")->execute([$id]);

    json_response(['success' => $result, 'message' => $result ? 'Entry deleted successfully' : 'Failed to delete entry']);
}

function handleStats($pdo, $user_data) {
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to view stats'], 403);
    }

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
}

function handleAddTest($pdo, $user_data) {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $entry_id = $input['entry_id'] ?? null;
    $test_id = $input['test_id'] ?? null;

    if (!$entry_id || !$test_id) {
        json_response(['success' => false, 'message' => 'Entry ID and Test ID are required'], 400);
    }

    $stmt = $pdo->prepare("INSERT INTO entry_tests (entry_id, test_id) VALUES (?, ?)");
    $result = $stmt->execute([$entry_id, $test_id]);

    json_response(['success' => $result, 'message' => $result ? 'Test added successfully' : 'Failed to add test']);
}

function handleRemoveTest($pdo, $user_data) {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $entry_test_id = $input['entry_test_id'] ?? null;

    if (!$entry_test_id) {
        json_response(['success' => false, 'message' => 'Entry Test ID is required'], 400);
    }

    $stmt = $pdo->prepare("DELETE FROM entry_tests WHERE id = ?");
    $result = $stmt->execute([$entry_test_id]);

    json_response(['success' => $result, 'message' => $result ? 'Test removed successfully' : 'Failed to remove test']);
}

function handleGetTests($pdo, $user_data) {
    $entry_id = $_GET['entry_id'] ?? null;
    if (!$entry_id) {
        json_response(['success' => false, 'message' => 'Entry ID is required'], 400);
    }

    $stmt = $pdo->prepare("SELECT et.*, t.name as test_name FROM entry_tests et LEFT JOIN tests t ON et.test_id = t.id WHERE et.entry_id = ?");
    $stmt->execute([$entry_id]);
    $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    json_response(['success' => true, 'data' => $tests]);
}

function handleUpdateTestResult($pdo, $user_data) {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $entry_test_id = $input['entry_test_id'] ?? null;

    if (!$entry_test_id) {
        json_response(['success' => false, 'message' => 'Entry Test ID is required'], 400);
    }

    $data = array_intersect_key($input, array_flip(['result_value', 'unit', 'remarks', 'status', 'price', 'discount_amount']));
    if (empty($data)) {
        json_response(['success' => false, 'message' => 'No fields to update'], 400);
    }

    $set_clause = implode(', ', array_map(fn($field) => "`$field` = ?", array_keys($data)));
    $sql = "UPDATE entry_tests SET $set_clause, updated_at = NOW() WHERE id = ?";
    $values = array_merge(array_values($data), [$entry_test_id]);

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($values);

    json_response(['success' => $result, 'message' => $result ? 'Test result updated successfully' : 'Failed to update test result']);
}

function handleReportList($pdo, $user_data) {
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list reports'], 403);
    }

    $params = [];
    $where = [];

    $scopeIds = getScopedUserIds($pdo, $user_data);
    if (is_array($scopeIds)) {
        $placeholders = implode(',', array_fill(0, count($scopeIds), '?'));
        $where[] = 'e.added_by IN (' . $placeholders . ')';
        $params = $scopeIds;
    }

    if (!empty($_GET['test_id'])) {
        $where[] = 'et.test_id = ?';
        $params[] = $_GET['test_id'];
    }
    if (!empty($_GET['doctor_id'])) {
        $where[] = 'e.doctor_id = ?';
        $params[] = $_GET['doctor_id'];
    }
    if (!empty($_GET['status'])) {
        $where[] = 'et.status = ?';
        $params[] = $_GET['status'];
    }
    if (!empty($_GET['date_from'])) {
        $where[] = 'DATE(e.entry_date) >= ?';
        $params[] = $_GET['date_from'];
    }
    if (!empty($_GET['date_to'])) {
        $where[] = 'DATE(e.entry_date) <= ?';
        $params[] = $_GET['date_to'];
    }

    $sql = "SELECT e.id as entry_id, e.entry_date, p.name as patient_name, d.name as doctor_name, t.name as test_name, et.result_value, et.status
            FROM entries e
            LEFT JOIN patients p ON e.patient_id = p.id
            LEFT JOIN doctors d ON e.doctor_id = d.id
            LEFT JOIN entry_tests et ON e.id = et.entry_id
            LEFT JOIN tests t ON et.test_id = t.id";

    if (!empty($where)) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

    json_response(['success' => true, 'data' => $reports, 'total' => count($reports)]);
}

/**
 * Refresh entry aggregates (tests count, names, etc.)
 */
function refreshEntryAggregates($pdo, $entryId) {
    try {
        error_log("Refreshing aggregates for entry ID: $entryId");
        
        // Get all tests for this entry
        $stmt = $pdo->prepare("
            SELECT et.test_id, et.price, t.name as test_name 
            FROM entry_tests et 
            LEFT JOIN tests t ON et.test_id = t.id 
            WHERE et.entry_id = ? 
            ORDER BY t.name
        ");
        $stmt->execute([$entryId]);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $testsCount = count($tests);
        $testIds = array_column($tests, 'test_id');
        $testNames = array_column($tests, 'test_name');
        $totalPrice = array_sum(array_column($tests, 'price'));
        
        // Update entry with aggregated data
        $updateData = [
            'tests_count' => $testsCount,
            'test_ids' => implode(',', $testIds),
            'test_names' => implode(', ', array_filter($testNames)),
            'total_price' => $totalPrice
        ];
        
        // Check which columns exist before updating
        $updateFields = [];
        $updateParams = ['id' => $entryId];
        
        // Check if columns exist in the entries table
        $columnsStmt = $pdo->prepare("SHOW COLUMNS FROM entries");
        $columnsStmt->execute();
        $columns = $columnsStmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($updateData as $field => $value) {
            if (in_array($field, $columns)) {
                $updateFields[] = "`$field` = :$field";
                $updateParams[$field] = $value;
            }
        }
        
        if (!empty($updateFields)) {
            $sql = "UPDATE entries SET " . implode(', ', $updateFields) . " WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($updateParams);
            
            error_log("Updated entry $entryId aggregates: " . json_encode($updateData));
        } else {
            error_log("No aggregate fields to update for entry $entryId");
        }
        
    } catch (Exception $e) {
        error_log("Error refreshing aggregates for entry $entryId: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Check if a database table exists
 */
function tableExists($pdo, $table) {
    try {
        $stmt = $pdo->prepare("SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
        $stmt->execute([$table]);
        return $stmt->fetchColumn() !== false;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Check if a database column exists
 */
function columnExists($pdo, $table, $column) {
    try {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM `{$table}` LIKE ?");
        $stmt->execute([$column]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    } catch (Exception $e) {
        return false;
    }
}
?>