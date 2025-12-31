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
        'owner_id', 'server_id', 'patient_id', 'doctor_id', 'entry_date', 'status', 
        'priority', 'referral_source', 'subtotal', 'discount_amount', 'total_price',
        'payment_status', 'notes', 'added_by', 'created_at', 'updated_at',
        'date_slot', 'service_location', 'collection_address'
    ]
];

$user_data = simpleAuthenticate($pdo);
if (!$user_data) {
    json_response(['success' => false, 'message' => 'Authentication required', 'debug_info' => getAuthDebugInfo()], 401);
}

$requestMethod = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? null;

// Default actions based on request method if no explicit action provided
if (!$action) {
    if ($requestMethod === 'GET') {
        $action = isset($_GET['id']) ? 'get' : 'list';
    } elseif ($requestMethod === 'POST' || $requestMethod === 'PUT') {
        $action = 'save';
    } elseif ($requestMethod === 'DELETE') {
        $action = 'delete';
    } else {
        $action = strtolower($requestMethod);
    }
}

try {
    switch($action) {
        case 'list': handleList($pdo, $entity_config, $user_data); break;
        case 'get': handleGet($pdo, $entity_config, $user_data); break;
        case 'save': handleSave($pdo, $entity_config, $user_data); break;
        case 'update': handleSave($pdo, $entity_config, $user_data); break; // Alias for save
        case 'delete': handleDelete($pdo, $entity_config, $user_data); break;
        case 'stats': handleStats($pdo, $user_data); break;
        case 'add_test': handleAddTest($pdo, $user_data); break;
        case 'remove_test': handleRemoveTest($pdo, $user_data); break;
        case 'get_tests': handleGetTests($pdo, $user_data); break;
        case 'update_test_result': handleUpdateTestResult($pdo, $user_data); break;
        case 'report_list': handleReportList($pdo, $user_data); break;
        case 'cleanup_duplicates': handleCleanupDuplicates($pdo, $user_data); break;
        case 'debug_get_entry': handleDebugGetEntry($pdo, $user_data); break;
        case 'debug_entry_17': handleDebugEntry17($pdo, $user_data); break;
        case 'refresh_aggregates': handleRefreshAggregates($pdo, $user_data); break;
        case 'debug_all_entries': handleDebugAllEntries($pdo, $user_data); break;
        case 'test_aggregation_sql': handleTestAggregationSQL($pdo, $user_data); break;
        case 'refresh_all_aggregates': handleRefreshAllAggregates($pdo, $user_data); break;
        case 'debug': handleDebug($pdo, $entity_config, $user_data); break;
        case 'fix_schema': handleFixSchema($pdo, $user_data); break;
        case 'debug_schema': handleDebugSchema($pdo, $user_data); break;
        case 'diagnose': handleDiagnose($pdo, $user_data); break;
        default: json_response(['success' => false, 'message' => 'Invalid action specified'], 400);
    }
} catch (Exception $e) {
    error_log("Entry API Uncaught Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An internal server error occurred.'], 500);
}

function handleDebug($pdo, $config, $user_data) {
    // Get total entries count
    $totalStmt = $pdo->query("SELECT COUNT(*) as total FROM entries");
    $totalCount = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get entries with tests count
    $withTestsStmt = $pdo->query("
        SELECT COUNT(DISTINCT e.id) as count 
        FROM entries e 
        INNER JOIN entry_tests et ON e.id = et.entry_id
    ");
    $withTestsCount = $withTestsStmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get user's entries count
    $scopeIds = getScopedUserIds($pdo, $user_data);
    $userEntriesCount = 0;
    if (is_array($scopeIds)) {
        $placeholders = implode(',', array_fill(0, count($scopeIds), '?'));
        $userStmt = $pdo->prepare("SELECT COUNT(*) as count FROM entries WHERE added_by IN ($placeholders)");
        $userStmt->execute($scopeIds);
        $userEntriesCount = $userStmt->fetch(PDO::FETCH_ASSOC)['count'];
    }
    
    // Get sample entries
    $sampleStmt = $pdo->query("
        SELECT e.id, e.patient_id, e.added_by, e.created_at,
               (SELECT COUNT(*) FROM entry_tests WHERE entry_id = e.id) as tests_count
        FROM entries e 
        ORDER BY e.id DESC 
        LIMIT 5
    ");
    $sampleEntries = $sampleStmt->fetchAll(PDO::FETCH_ASSOC);
    
    json_response([
        'success' => true,
        'debug_info' => [
            'user_data' => $user_data,
            'scope_ids' => $scopeIds,
            'total_entries' => (int)$totalCount,
            'entries_with_tests' => (int)$withTestsCount,
            'user_entries_count' => (int)$userEntriesCount,
            'sample_entries' => $sampleEntries,
            'database_connected' => true
        ]
    ]);
}

function handleList($pdo, $config, $user_data) {
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list entries'], 403);
    }

    $scopeIds = getScopedUserIds($pdo, $user_data);
    $where = [];
    $params = [];
    
    if (is_array($scopeIds)) {
        $placeholders = implode(',', array_fill(0, count($scopeIds), '?'));
        $where[] = ' e.added_by IN (' . $placeholders . ')';
        $params = $scopeIds;
    }

    // Additional filters
    if (!empty($_GET['status'])) {
        $where[] = "e.status = ?";
        $params[] = $_GET['status'];
    }
    if (!empty($_GET['patient_id'])) {
        $where[] = "e.patient_id = ?";
        $params[] = $_GET['patient_id'];
    }
    if (!empty($_GET['doctor_id'])) {
        $where[] = "e.doctor_id = ?";
        $params[] = $_GET['doctor_id'];
    }
    if (!empty($_GET['date_from'])) {
        $where[] = "e.entry_date >= ?";
        $params[] = $_GET['date_from'];
    }
    if (!empty($_GET['date_to'])) {
        $where[] = "e.entry_date <= ?";
        $params[] = $_GET['date_to'];
    }
    if (!empty($_GET['search']['value'])) {
        $search = "%{$_GET['search']['value']}%";
        $where[] = "(p.name LIKE ? OR e.id LIKE ? OR e.notes LIKE ?)";
        $params[] = $search;
        $params[] = $search;
        $params[] = $search;
    }

    $whereSql = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';

    // Count total records
    $totalStmt = $pdo->query("SELECT COUNT(*) FROM {$config['table_name']}");
    $recordsTotal = $totalStmt->fetchColumn();

    // Count filtered records
    $filteredStmt = $pdo->prepare("SELECT COUNT(*) FROM {$config['table_name']} e LEFT JOIN patients p ON e.patient_id = p.id {$whereSql}");
    $filteredStmt->execute($params);
    $recordsFiltered = $filteredStmt->fetchColumn();

    // Pagination
    $limit = "";
    if (isset($_GET['start']) && $_GET['length'] != -1) {
        $limit = " LIMIT " . (int)$_GET['start'] . ", " . (int)$_GET['length'];
    }

    // Enhanced query with all entry fields and test information
    $sql = "SELECT 
                e.*,
                p.name as patient_name,
                p.uhid as patient_uhid,
                p.age as patient_age,
                p.sex as patient_gender,
                p.contact as patient_contact,
                d.name as doctor_name,
                d.specialization as doctor_specialization,
                d.hospital as doctor_hospital,
                u.username as added_by_username,
                u.full_name as added_by_full_name
            FROM {$config['table_name']} e
            LEFT JOIN patients p ON e.patient_id = p.id
            LEFT JOIN doctors d ON e.doctor_id = d.id
            LEFT JOIN users u ON e.added_by = u.id
            {$whereSql} 
            ORDER BY e.id DESC
            {$limit}";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch test information for each entry with enhanced details
    foreach ($entries as &$entry) {
        $testStmt = $pdo->prepare("
            SELECT 
                GROUP_CONCAT(DISTINCT t.name ORDER BY t.name SEPARATOR ', ') as test_names,
                COUNT(DISTINCT et.id) as tests_count,
                GROUP_CONCAT(DISTINCT et.test_id ORDER BY et.test_id SEPARATOR ',') as test_ids
            FROM entry_tests et
            LEFT JOIN tests t ON et.test_id = t.id
            WHERE et.entry_id = ?
        ");
        $testStmt->execute([$entry['id']]);
        $testData = $testStmt->fetch(PDO::FETCH_ASSOC);
        
        $entry['test_names'] = $testData['test_names'] ?? 'No tests';
        $entry['tests_count'] = (int)($testData['tests_count'] ?? 0);
        $entry['test_ids'] = $testData['test_ids'] ?? '';
    }

    json_response([
        'success' => true, 
        'draw' => isset($_GET['draw']) ? (int)$_GET['draw'] : 0,
        'recordsTotal' => (int)$recordsTotal,
        'recordsFiltered' => (int)$recordsFiltered,
        'data' => $entries, 
        'message' => 'Entries retrieved successfully'
    ]);
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
    
    // Handle optional fields with defaults
    $data['status'] = $data['status'] ?? 'pending';
    $data['priority'] = $data['priority'] ?? 'normal';
    $data['referral_source'] = $data['referral_source'] ?? null;
    $data['notes'] = $data['notes'] ?? null;
    $data['subtotal'] = (float)($data['subtotal'] ?? 0);
    $data['discount_amount'] = (float)($data['discount_amount'] ?? 0);
    $data['total_price'] = (float)($data['total_price'] ?? 0);
    $data['payment_status'] = $data['payment_status'] ?? 'pending';
    $data['date_slot'] = $data['date_slot'] ?? null;
    $data['service_location'] = $data['service_location'] ?? null;
    $data['collection_address'] = $data['collection_address'] ?? null;

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
            
            // Enhanced duplicate prevention with validation
            $insertedTestIds = [];
            $duplicatesSkipped = [];
            $testsProcessed = 0;
            
            // Pre-validate tests for duplicates
            $testIdCounts = array_count_values(array_column($tests, 'test_id'));
            $hasDuplicates = array_filter($testIdCounts, function($count) { return $count > 1; });
            
            if (!empty($hasDuplicates)) {
                error_log("Duplicate test IDs detected in submission: " . json_encode($hasDuplicates));
            }
            
            // Insert new tests with enhanced duplicate checking
            foreach ($tests as $index => $test) {
                if (!empty($test['test_id'])) {
                    $testId = (int)$test['test_id'];
                    
                    // Check for duplicate test ID in this entry (client-side duplicates)
                    if (in_array($testId, $insertedTestIds)) {
                        error_log("Skipping client-side duplicate test ID $testId for entry $entry_id");
                        $duplicatesSkipped[] = $testId;
                        continue;
                    }
                    
                    // Check for existing test in database (for updates)
                    if ($id) {
                        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM entry_tests WHERE entry_id = ? AND test_id = ?");
                        $checkStmt->execute([$entry_id, $testId]);
                        $existingCount = $checkStmt->fetchColumn();
                        
                        if ($existingCount > 0) {
                            error_log("Test ID $testId already exists in database for entry $entry_id");
                        }
                    }
                    
                    $insertedTestIds[] = $testId;
                    
                    // Fetch test details from database to get category_id and main_category_id
                    $testInfoStmt = $pdo->prepare("SELECT category_id, main_category_id FROM tests WHERE id = ?");
                    $testInfoStmt->execute([$testId]);
                    $testInfo = $testInfoStmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Use provided category_id or fetch from test table
                    $categoryId = $test['category_id'] ?? ($testInfo['category_id'] ?? 0);
                    $mainCategoryId = $test['main_category_id'] ?? ($testInfo['main_category_id'] ?? 0);
                    
                    $testData = [
                        'entry_id' => $entry_id,
                        'test_id' => $testId,
                        'category_id' => (int)$categoryId,
                        'main_category_id' => (int)$mainCategoryId,
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
                        $testsProcessed++;
                        error_log("Successfully inserted test with ID: $insertedTestId for entry: $entry_id (test_id: $testId, category: $categoryId, main_category: $mainCategoryId)");
                    } else {
                        $errorInfo = $testStmt->errorInfo();
                        error_log("Failed to insert test $index (test_id: $testId): " . json_encode($errorInfo));
                        throw new Exception("Failed to insert test: " . $errorInfo[2]);
                    }
                }
            }
            
            // Log duplicate prevention results
            if (!empty($duplicatesSkipped)) {
                error_log("Duplicate prevention: Skipped " . count($duplicatesSkipped) . " duplicate tests for entry $entry_id: " . implode(', ', $duplicatesSkipped));
            }
            
            error_log("Test insertion summary for entry $entry_id: $testsProcessed tests inserted, " . count($duplicatesSkipped) . " duplicates skipped");
            
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

        // Prepare response with duplicate information
        $responseMessage = "Entry {$action_status} successfully";
        $responseData = [
            'success' => true,
            'message' => $responseMessage,
            'data' => $saved_entry,
            'id' => $entry_id
        ];
        
        // Add duplicate information if any were skipped
        if (isset($duplicatesSkipped) && !empty($duplicatesSkipped)) {
            $responseData['duplicates_skipped'] = count($duplicatesSkipped);
            $responseData['duplicate_test_ids'] = $duplicatesSkipped;
            $responseMessage .= " (" . count($duplicatesSkipped) . " duplicate tests were automatically removed)";
            $responseData['message'] = $responseMessage;
        }
        
        if (isset($testsProcessed)) {
            $responseData['tests_processed'] = $testsProcessed;
        }
        
        json_response($responseData);

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

/**
 * Handle cleanup of duplicate entries
 */
function handleCleanupDuplicates($pdo, $user_data) {
    if (!simpleCheckPermission($user_data, 'delete')) {
        json_response(['success' => false, 'message' => 'Permission denied to cleanup duplicates'], 403);
    }

    try {
        error_log("Enhanced cleanup of duplicate test entries");
        
        // Find duplicate test entries (same entry_id and test_id)
        $sql = "SELECT entry_id, test_id, COUNT(*) as count, GROUP_CONCAT(id ORDER BY id) as ids,
                       GROUP_CONCAT(created_at ORDER BY id) as created_dates
                FROM entry_tests 
                GROUP BY entry_id, test_id 
                HAVING COUNT(*) > 1
                ORDER BY entry_id, test_id";
        
        $stmt = $pdo->query($sql);
        $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $cleaned = 0;
        $affectedEntries = [];
        $cleanupDetails = [];
        
        $pdo->beginTransaction();
        
        foreach ($duplicates as $duplicate) {
            $entryId = $duplicate['entry_id'];
            $testId = $duplicate['test_id'];
            $ids = explode(',', $duplicate['ids']);
            $createdDates = explode(',', $duplicate['created_dates']);
            
            // Keep the first one (oldest), delete the rest
            $keepId = array_shift($ids);
            $keepDate = array_shift($createdDates);
            
            if (!empty($ids)) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $deleteStmt = $pdo->prepare("DELETE FROM entry_tests WHERE id IN ($placeholders)");
                $deleteStmt->execute($ids);
                
                $deletedCount = count($ids);
                $cleaned += $deletedCount;
                $affectedEntries[] = $entryId;
                
                $cleanupDetails[] = [
                    'entry_id' => $entryId,
                    'test_id' => $testId,
                    'kept_id' => $keepId,
                    'kept_date' => $keepDate,
                    'deleted_ids' => $ids,
                    'deleted_count' => $deletedCount
                ];
                
                error_log("Cleaned duplicates for entry $entryId, test $testId: kept ID $keepId, deleted $deletedCount duplicates (" . implode(',', $ids) . ")");
            }
        }
        
        $pdo->commit();
        
        // Refresh aggregates for affected entries
        $affectedEntries = array_unique($affectedEntries);
        $aggregatesRefreshed = 0;
        
        foreach ($affectedEntries as $entryId) {
            try {
                refreshEntryAggregates($pdo, $entryId);
                $aggregatesRefreshed++;
            } catch (Exception $e) {
                error_log("Failed to refresh aggregates for entry $entryId: " . $e->getMessage());
            }
        }
        
        error_log("Duplicate cleanup completed: $cleaned duplicates removed from " . count($affectedEntries) . " entries, $aggregatesRefreshed aggregates refreshed");
        
        json_response([
            'success' => true,
            'message' => "Cleaned up $cleaned duplicate test entries from " . count($affectedEntries) . " entries",
            'duplicates_found' => count($duplicates),
            'entries_cleaned' => $cleaned,
            'affected_entries' => count($affectedEntries),
            'aggregates_refreshed' => $aggregatesRefreshed,
            'cleanup_details' => $cleanupDetails
        ]);
        
    } catch (Exception $e) {
        error_log('Error cleaning duplicates: ' . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to cleanup duplicates', 'error' => $e->getMessage()], 500);
    }
}

/**
 * Debug function to get entry details
 */
function handleDebugGetEntry($pdo, $user_data) {
    $entryId = (int)($_GET['entry_id'] ?? $_GET['id'] ?? 0);
    if (!$entryId) {
        json_response(['success' => false, 'message' => 'Entry ID required']);
    }
    
    error_log("Debug: Checking get action for entry $entryId");
    
    try {
        // Get entry with all related data
        $sql = "SELECT e.*, 
                       p.name AS patient_name, p.uhid, p.age, p.sex AS gender, 
                       p.contact AS patient_contact, p.address AS patient_address,
                       d.name AS doctor_name, d.specialization AS doctor_specialization,
                       u.username AS added_by_username, u.full_name AS added_by_full_name
                FROM entries e 
                LEFT JOIN patients p ON e.patient_id = p.id 
                LEFT JOIN doctors d ON e.doctor_id = d.id 
                LEFT JOIN users u ON e.added_by = u.id
                WHERE e.id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$entryId]);
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$entry) {
            json_response(['success' => false, 'message' => 'Entry not found']);
        }
        
        // Get associated tests
        $testSql = "SELECT et.*, t.name as test_name, t.category_id, c.name as category_name 
                    FROM entry_tests et 
                    LEFT JOIN tests t ON et.test_id = t.id 
                    LEFT JOIN categories c ON t.category_id = c.id 
                    WHERE et.entry_id = ? 
                    ORDER BY et.id";
        
        $testStmt = $pdo->prepare($testSql);
        $testStmt->execute([$entryId]);
        $tests = $testStmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'success' => true,
            'entry_id' => $entryId,
            'entry_data' => $entry,
            'tests_data' => $tests,
            'tests_count' => count($tests),
            'debug_info' => [
                'sql_used' => $sql,
                'test_sql_used' => $testSql
            ]
        ]);
        
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Debug failed', 'error' => $e->getMessage()], 500);
    }
}

/**
 * Debug function for entry 17 (specific debugging)
 */
function handleDebugEntry17($pdo, $user_data) {
    error_log("Debug: Checking entry 17 data");
    
    try {
        // Check entry_tests table directly
        $stmt = $pdo->prepare("SELECT * FROM entry_tests WHERE entry_id = 17 ORDER BY id");
        $stmt->execute();
        $directTests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Check with JOIN to tests table
        $stmt = $pdo->prepare("SELECT et.*, t.name as test_name, t.category_id, c.name as category_name 
                               FROM entry_tests et 
                               LEFT JOIN tests t ON et.test_id = t.id 
                               LEFT JOIN categories c ON t.category_id = c.id 
                               WHERE et.entry_id = 17 
                               ORDER BY et.id");
        $stmt->execute();
        $joinedTests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'success' => true,
            'direct_tests' => $directTests,
            'joined_tests' => $joinedTests,
            'direct_count' => count($directTests),
            'joined_count' => count($joinedTests)
        ]);
        
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Debug failed', 'error' => $e->getMessage()], 500);
    }
}

/**
 * Manually refresh aggregates for a specific entry
 */
function handleRefreshAggregates($pdo, $user_data) {
    $entryId = (int)($_GET['entry_id'] ?? $_POST['entry_id'] ?? 0);
    if (!$entryId) {
        json_response(['success' => false, 'message' => 'Entry ID required']);
    }
    
    error_log("Manual refresh aggregates for entry ID: $entryId");
    
    try {
        // Get entry_tests data before refresh
        $stmt = $pdo->prepare("SELECT et.*, t.name as test_name FROM entry_tests et LEFT JOIN tests t ON et.test_id = t.id WHERE et.entry_id = ?");
        $stmt->execute([$entryId]);
        $beforeTests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Refresh aggregates
        refreshEntryAggregates($pdo, $entryId);
        
        // Get updated entry data
        $stmt = $pdo->prepare("SELECT id, tests_count, test_names, test_ids FROM entries WHERE id = ?");
        $stmt->execute([$entryId]);
        $entryData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        json_response([
            'success' => true,
            'message' => 'Aggregates refreshed',
            'entry_data' => $entryData,
            'entry_tests_data' => $beforeTests,
            'entry_tests_count' => count($beforeTests)
        ]);
        
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Failed to refresh aggregates', 'error' => $e->getMessage()], 500);
    }
}

/**
 * Debug all entries with multiple tests
 */
function handleDebugAllEntries($pdo, $user_data) {
    error_log("Debug: Checking all entries with multiple tests");
    
    try {
        // Get all entries with their test counts
        $sql = "SELECT e.id, e.patient_id, e.tests_count as stored_tests_count, e.test_names as stored_test_names,
                       COUNT(et.id) as actual_tests_count,
                       GROUP_CONCAT(t.name SEPARATOR ', ') as actual_test_names
                FROM entries e 
                LEFT JOIN entry_tests et ON e.id = et.entry_id
                LEFT JOIN tests t ON et.test_id = t.id
                GROUP BY e.id
                HAVING actual_tests_count > 1 OR stored_tests_count > 1
                ORDER BY e.id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'success' => true,
            'entries_with_multiple_tests' => $entries,
            'count' => count($entries)
        ]);
        
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Debug failed', 'error' => $e->getMessage()], 500);
    }
}

/**
 * Test aggregation SQL directly
 */
function handleTestAggregationSQL($pdo, $user_data) {
    error_log("Testing aggregation SQL directly");
    
    try {
        $aggSql = "SELECT 
                       et.entry_id,
                       COUNT(et.id) as tests_count,
                       GROUP_CONCAT(et.test_id ORDER BY t.name) as test_ids,
                       GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ', ') as test_names,
                       SUM(et.price) as total_price
                   FROM entry_tests et
                   LEFT JOIN tests t ON et.test_id = t.id
                   GROUP BY et.entry_id";
        
        error_log("Aggregation SQL: " . $aggSql);
        
        $stmt = $pdo->prepare($aggSql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'success' => true,
            'sql' => $aggSql,
            'results' => $results,
            'count' => count($results)
        ]);
        
    } catch (Exception $e) {
        json_response([
            'success' => false,
            'error' => $e->getMessage(),
            'sql' => $aggSql ?? 'Failed to build SQL'
        ], 500);
    }
}

/**
 * Refresh aggregates for all entries
 */
function handleRefreshAllAggregates($pdo, $user_data) {
    if (!simpleCheckPermission($user_data, 'save')) {
        json_response(['success' => false, 'message' => 'Permission denied to refresh aggregates'], 403);
    }
    
    error_log("Refreshing aggregates for all entries with tests");
    
    try {
        // Get all entry IDs that have tests
        $stmt = $pdo->query("SELECT DISTINCT entry_id FROM entry_tests ORDER BY entry_id");
        $entryIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $refreshed = 0;
        foreach ($entryIds as $entryId) {
            refreshEntryAggregates($pdo, $entryId);
            $refreshed++;
        }
        
        json_response([
            'success' => true,
            'message' => "Refreshed aggregates for $refreshed entries",
            'refreshed_count' => $refreshed,
            'entry_ids' => $entryIds
        ]);
        
    } catch (Exception $e) {
        json_response([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}
/**
 * Handle schema fix
 */
function handleFixSchema($pdo, $user_data) {
    if (!simpleCheckPermission($user_data, 'save')) {
        json_response(['success' => false, 'message' => 'Permission denied to fix schema'], 403);
    }

    $updates = [];
    $errors = [];

    // Fix entries table
    $entriesColumns = [
        'tests_count' => "INT DEFAULT 0",
        'test_ids' => "TEXT",
        'test_names' => "TEXT",
        'tests_subtotal' => "DECIMAL(10,2) DEFAULT 0.00",
        'tests_discount' => "DECIMAL(10,2) DEFAULT 0.00",
        'tests_total' => "DECIMAL(10,2) DEFAULT 0.00"
    ];

    foreach ($entriesColumns as $col => $def) {
        if (!columnExists($pdo, 'entries', $col)) {
            try {
                $pdo->exec("ALTER TABLE entries ADD COLUMN $col $def");
                $updates[] = "Added column '$col' to 'entries' table";
            } catch (Exception $e) {
                $errors[] = "Failed to add column '$col' to 'entries': " . $e->getMessage();
            }
        }
    }

    // Fix entry_tests table
    $entryTestsColumns = [
        'main_category_id' => "INT DEFAULT 0",
        'category_id' => "INT DEFAULT 0",
        'unit' => "VARCHAR(50)",
        'price' => "DECIMAL(10,2) DEFAULT 0.00",
        'discount_amount' => "DECIMAL(10,2) DEFAULT 0.00",
        'total_price' => "DECIMAL(10,2) DEFAULT 0.00",
        'status' => "ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending'",
        'remarks' => "TEXT"
    ];

    foreach ($entryTestsColumns as $col => $def) {
        if (!columnExists($pdo, 'entry_tests', $col)) {
            try {
                $pdo->exec("ALTER TABLE entry_tests ADD COLUMN $col $def");
                $updates[] = "Added column '$col' to 'entry_tests' table";
            } catch (Exception $e) {
                $errors[] = "Failed to add column '$col' to 'entry_tests': " . $e->getMessage();
            }
        }
    }

    json_response([
        'success' => empty($errors),
        'message' => empty($updates) ? 'Schema is already up to date' : 'Schema updated successfully',
        'updates' => $updates,
        'errors' => $errors
    ]);
}

/**
 * Handle diagnosis
 */
function handleDiagnose($pdo, $user_data) {
    // Reuse handleDebug logic but maybe expand it
    handleDebug($pdo, [], $user_data);
}

function handleDebugSchema($pdo, $user_data) {
    try {
        $tables = ['entries', 'entry_tests', 'tests', 'patients', 'doctors'];
        $schema = [];
        foreach($tables as $table) {
            $schema[$table] = $pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
        }
        json_response([
            'success' => true,
            'tables' => $schema
        ]);
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
?>