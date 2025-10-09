<?php
// ajax/entry_api.php - CRUD for entries
try {
    require_once __DIR__ . '/../inc/connection.php';
} catch (Exception $e) {
    // If database connection fails, provide fallback response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Database connection error. Please ensure MySQL is running.',
        'error' => $e->getMessage()
    ]);
    exit;
}

require_once __DIR__ . '/../inc/ajax_helpers.php';
require_once __DIR__ . '/../inc/simple_auth.php';

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

$action = $_REQUEST['action'] ?? 'list';

// Authenticate user
$user_data = simpleAuthenticate($pdo);
if (!$user_data) {
    json_response([
        'success' => false, 
        'message' => 'Authentication required',
        'debug_info' => getAuthDebugInfo()
    ], 401);
}

function db_table_exists($pdo, $table) {
    try {
        $stmt = $pdo->prepare("SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
        $stmt->execute([$table]);
        return $stmt->fetchColumn() !== false;
    } catch (Exception $e) {
        return false;
    }
}

function db_column_exists($pdo, $table, $column) {
    try {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM `{$table}` LIKE ?");
        $stmt->execute([$column]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    } catch (Exception $e) {
        return false;
    }
}

function get_entries_schema_capabilities($pdo) {
    return [
        'has_grouped' => db_column_exists($pdo, 'entries', 'grouped'),
        'has_tests_count' => db_column_exists($pdo, 'entries', 'tests_count'),
        'has_test_ids' => db_column_exists($pdo, 'entries', 'test_ids'),
        'has_test_names' => db_column_exists($pdo, 'entries', 'test_names'),
        'has_test_id' => db_column_exists($pdo, 'entries', 'test_id'),
        'has_price' => db_column_exists($pdo, 'entries', 'price'),
        'has_discount_amount' => db_column_exists($pdo, 'entries', 'discount_amount'),
        'has_total_price' => db_column_exists($pdo, 'entries', 'total_price'),
        // optionally present column linking an entry to an owner/clinic
        'has_owner_id' => db_column_exists($pdo, 'entries', 'owner_id'),
        'has_subtotal' => db_column_exists($pdo, 'entries', 'subtotal'),
        'has_notes' => db_column_exists($pdo, 'entries', 'notes'),
        'has_remarks' => db_column_exists($pdo, 'entries', 'remarks'),
        'has_updated_at' => db_column_exists($pdo, 'entries', 'updated_at'),
        // Additional fields that may or may not exist
        'has_priority' => db_column_exists($pdo, 'entries', 'priority'),
        'has_referral_source' => db_column_exists($pdo, 'entries', 'referral_source'),
        'has_patient_contact' => db_column_exists($pdo, 'entries', 'patient_contact'),
        'has_patient_address' => db_column_exists($pdo, 'entries', 'patient_address'),
        'has_gender' => db_column_exists($pdo, 'entries', 'gender')
    ];
}

function get_entry_tests_schema_capabilities($pdo) {
    $exists = db_table_exists($pdo, 'entry_tests');
    if (!$exists) {
        return [
            'table_exists' => false,
            'has_price' => false,
            'has_discount_amount' => false,
            'has_total_price' => false
        ];
    }

    return [
        'table_exists' => true,
        'has_price' => db_column_exists($pdo, 'entry_tests', 'price'),
        'has_discount_amount' => db_column_exists($pdo, 'entry_tests', 'discount_amount'),
        'has_total_price' => db_column_exists($pdo, 'entry_tests', 'total_price')
    ];
}

function build_entry_tests_aggregation_sql($pdo) {
    $caps = get_entry_tests_schema_capabilities($pdo);
    if (!$caps['table_exists']) {
        return "SELECT NULL AS entry_id, 0 AS tests_count, '' AS test_names, '' AS test_ids, 0 AS total_price, 0 AS total_discount FROM dual WHERE 1 = 0";
    }
    $sumPrice = $caps['has_price'] ? 'SUM(et.price)' : 'SUM(0)';
    $sumDiscount = $caps['has_discount_amount'] ? 'SUM(et.discount_amount)' : 'SUM(0)';

    return "SELECT et.entry_id,\n               COUNT(*) as tests_count,\n               GROUP_CONCAT(DISTINCT t.name ORDER BY t.name SEPARATOR ', ') as test_names,\n               GROUP_CONCAT(DISTINCT et.test_id ORDER BY et.test_id) as test_ids,\n               {$sumPrice} as total_price,\n               {$sumDiscount} as total_discount\n        FROM entry_tests et\n        LEFT JOIN tests t ON et.test_id = t.id\n        GROUP BY et.entry_id";
}

function refresh_entry_aggregates($pdo, $entryId) {
    $entriesCaps = get_entries_schema_capabilities($pdo);
    $entryTestsCaps = get_entry_tests_schema_capabilities($pdo);
    if (!$entryTestsCaps['table_exists']) {
        return;
    }

    $aggSql = build_entry_tests_aggregation_sql($pdo);

    $stmt = $pdo->prepare("SELECT tests_count, test_ids, test_names, total_price, total_discount FROM (" . $aggSql . ") agg WHERE entry_id = ?");
    $stmt->execute([$entryId]);
    $aggRow = $stmt->fetch(PDO::FETCH_ASSOC);

    $testsCount = $aggRow ? (int)($aggRow['tests_count'] ?? 0) : 0;
    $testIds = $aggRow ? ($aggRow['test_ids'] ?? '') : '';
    $testNames = $aggRow ? ($aggRow['test_names'] ?? '') : '';
    $totalPrice = $aggRow ? (float)($aggRow['total_price'] ?? 0) : 0;
    $totalDiscount = $aggRow ? (float)($aggRow['total_discount'] ?? 0) : 0;
    $netAmount = max($totalPrice - $totalDiscount, 0);

    $fields = [];
    $params = ['entry_id' => $entryId];

    if ($entriesCaps['has_grouped']) {
        $fields[] = 'grouped = :grouped';
        $params['grouped'] = $testsCount > 1 ? 1 : 0;
    }
    if ($entriesCaps['has_tests_count']) {
        $fields[] = 'tests_count = :tests_count';
        $params['tests_count'] = $testsCount;
    }
    if ($entriesCaps['has_test_ids']) {
        $fields[] = 'test_ids = :test_ids';
        $params['test_ids'] = $testIds;
    }
    if ($entriesCaps['has_test_names']) {
        $fields[] = 'test_names = :test_names';
        $params['test_names'] = $testNames;
    }
    if ($entriesCaps['has_price']) {
        $fields[] = 'price = :price';
        $params['price'] = $totalPrice;
    }
    if ($entriesCaps['has_subtotal']) {
        $fields[] = 'subtotal = :subtotal';
        $params['subtotal'] = $totalPrice;
    }
    if ($entriesCaps['has_discount_amount']) {
        $fields[] = 'discount_amount = :discount_amount';
        $params['discount_amount'] = $totalDiscount;
    }
    if ($entriesCaps['has_total_price']) {
        $fields[] = 'total_price = :total_price';
        $params['total_price'] = $netAmount;
    }
    if ($entriesCaps['has_updated_at']) {
        $fields[] = 'updated_at = NOW()';
    }

    if (!$fields) {
        return;
    }

    $sql = "UPDATE entries SET " . implode(', ', $fields) . " WHERE id = :entry_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
}

try {
    if ($action === 'stats') {
        // Check permission
        if (!simpleCheckPermission($user_data, 'list')) {
            json_response(['success' => false, 'message' => 'Permission denied to view statistics'], 403);
        }
        
        // Get statistics for dashboard
        $stats = [];
        $viewerRole = $user_data['role'] ?? 'user';
        $viewerId = (int)($user_data['user_id'] ?? 0);
        $scopeWhere = '';
        $params = [];
        if ($viewerRole !== 'master') {
            $scopeWhere = ' WHERE added_by = ?';
            $params = [$viewerId];
        }

        try {
            // Total entries
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM entries" . $scopeWhere);
            $stmt->execute($params);
            $stats['total'] = (int) $stmt->fetchColumn();
            
            // Pending entries
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM entries" . ($scopeWhere ? $scopeWhere . " AND status = 'pending'" : " WHERE status = 'pending'"));
            $stmt->execute($params);
            $stats['pending'] = (int) $stmt->fetchColumn();
            
            // Completed entries  
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM entries" . ($scopeWhere ? $scopeWhere . " AND status = 'completed'" : " WHERE status = 'completed'"));
            $stmt->execute($params);
            $stats['completed'] = (int) $stmt->fetchColumn();
            
            // Today's entries - try both date fields
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM entries" . ($scopeWhere ? $scopeWhere . " AND DATE(COALESCE(entry_date, created_at)) = CURDATE()" : " WHERE DATE(COALESCE(entry_date, created_at)) = CURDATE()"));
            $stmt->execute($params);
            $stats['today'] = (int) $stmt->fetchColumn();
            
        } catch (Exception $e) {
            // Fallback for missing columns
            $stats = ['total' => 0, 'pending' => 0, 'completed' => 0, 'today' => 0];
        }
        
        json_response(['success' => true, 'status' => 'success', 'data' => $stats]);
    } else if ($action === 'list') {
        // Check permission
        if (!simpleCheckPermission($user_data, 'list')) {
            json_response(['success' => false, 'message' => 'Permission denied to list entries'], 403);
        }
        
        // Updated to match new schema with comprehensive data
        $viewerRole = $user_data['role'] ?? 'user';
        $viewerId = (int)($user_data['user_id'] ?? 0);
        $scopeWhere = '';
        $params = [];
        if ($viewerRole !== 'master') {
            $scopeWhere = ' WHERE e.added_by = ?';
            $params = [$viewerId];
        }

        $entryTestsCaps = get_entry_tests_schema_capabilities($pdo);
        if ($entryTestsCaps['table_exists']) {
            $aggSql = build_entry_tests_aggregation_sql($pdo);
            $aggSelect = "COALESCE(agg.tests_count, 0) AS agg_tests_count,\n                   COALESCE(agg.test_names, '') AS agg_test_names,\n                   COALESCE(agg.test_ids, '') AS agg_test_ids,\n                   COALESCE(agg.total_price, 0) AS agg_total_price,\n                   COALESCE(agg.total_discount, 0) AS agg_total_discount";
            $aggJoin = " LEFT JOIN (" . $aggSql . ") agg ON agg.entry_id = e.id";
        } else {
            $aggSelect = "0 AS agg_tests_count, '' AS agg_test_names, '' AS agg_test_ids, 0 AS agg_total_price, 0 AS agg_total_discount";
            $aggJoin = '';
        }

        // Build SELECT and JOINs conditionally based on schema capabilities
        $entriesCaps = get_entries_schema_capabilities($pdo);
        $ownerSelect = '';
        $ownerJoin = '';
        if (!empty($entriesCaps['has_owner_id'])) {
            $ownerSelect = "o.name AS owner_name,";
            $ownerJoin = " LEFT JOIN owners o ON e.owner_id = o.id";
        }

        $sql = "SELECT e.*, \n                   p.name AS patient_name, p.uhid, p.age, p.sex AS gender, p.contact AS patient_contact, p.address AS patient_address,\n                   d.name AS doctor_name,\n                   {$ownerSelect}\n                   du.username AS doctor_added_by_username,\n                   u.username AS added_by_username, u.full_name AS added_by_full_name,\n                   {$aggSelect}\n            FROM entries e \n            LEFT JOIN patients p ON e.patient_id = p.id \n            LEFT JOIN doctors d ON e.doctor_id = d.id " .
            $ownerJoin .
            " LEFT JOIN users du ON d.added_by = du.id\n            LEFT JOIN users u ON e.added_by = u.id" .
            $aggJoin .
            $scopeWhere .
            " ORDER BY COALESCE(e.entry_date, e.created_at) DESC, e.id DESC";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            json_response(['success' => false, 'message' => 'Database error while listing entries', 'error' => $e->getMessage()], 500);
        }
        
        // Format data for frontend compatibility
        $entriesCaps = get_entries_schema_capabilities($pdo);
        foreach ($rows as &$row) {
            // Ensure entry_date is available for frontend
            if (empty($row['entry_date'])) {
                $row['entry_date'] = $row['created_at'];
            }
            
            // Format test information
            $row['tests_count'] = (int)($row['tests_count'] ?? 0);
            $row['test_names'] = $row['test_names'] ?? '';
            $row['test_ids'] = $row['test_ids'] ?? '';
            
            // Format pricing
            $aggTotalPrice = (float)($row['total_price'] ?? 0);
            $aggDiscount = (float)($row['total_discount'] ?? 0);
            $row['aggregated_total_price'] = $aggTotalPrice;
            $row['aggregated_total_discount'] = $aggDiscount;

            if ($entriesCaps['has_total_price'] && isset($row['total_price'])) {
                $finalAmount = (float)$row['total_price'];
            } else if ($entriesCaps['has_subtotal'] && isset($row['subtotal'])) {
                $finalAmount = (float)$row['subtotal'] - (float)($row['discount_amount'] ?? 0);
            } else {
                $finalAmount = $aggTotalPrice - $aggDiscount;
            }

            $row['total_price'] = $entriesCaps['has_total_price'] && isset($row['total_price'])
                ? (float)$row['total_price']
                : $aggTotalPrice;
            $row['total_discount'] = $entriesCaps['has_discount_amount'] && isset($row['discount_amount'])
                ? (float)$row['discount_amount']
                : $aggDiscount;
            $row['final_amount'] = $finalAmount;
            
            // Set grouped flag based on test count
            $row['grouped'] = $row['tests_count'] > 1 ? 1 : 0;
            
            // For backward compatibility, set test_name to first test or all tests
            if ($row['tests_count'] == 1) {
                $row['test_name'] = $row['test_names'];
            } else if ($row['tests_count'] > 1) {
                $row['test_name'] = $row['tests_count'] . ' tests: ' . $row['test_names'];
            } else {
                $row['test_name'] = 'No tests';
            }
        }
        
        json_response(['success' => true, 'data' => $rows, 'total' => count($rows)]);
    }

    if ($action === 'get' && isset($_GET['id'])) {
        // Check permission
        if (!simpleCheckPermission($user_data, 'get', $_GET['id'])) {
            json_response(['success' => false, 'message' => 'Permission denied to view entry'], 403);
        }
        
        // Return comprehensive entry data
        $viewerRole = $user_data['role'] ?? 'user';
        $viewerId = (int)($user_data['user_id'] ?? 0);
        $scopeWhere = '';
        $params = [$_GET['id']];
        if ($viewerRole !== 'master') {
            $scopeWhere = ' AND e.added_by = ?';
            $params[] = $viewerId;
        }

        $entryTestsCaps = get_entry_tests_schema_capabilities($pdo);
        if ($entryTestsCaps['table_exists']) {
            $aggSql = build_entry_tests_aggregation_sql($pdo);
            $aggSelect = "COALESCE(agg.tests_count, 0) AS agg_tests_count,\n                   COALESCE(agg.test_names, '') AS agg_test_names,\n                   COALESCE(agg.test_ids, '') AS agg_test_ids,\n                   COALESCE(agg.total_price, 0) AS agg_total_price,\n                   COALESCE(agg.total_discount, 0) AS agg_total_discount";
            $aggJoin = " LEFT JOIN (" . $aggSql . ") agg ON agg.entry_id = e.id";
        } else {
            $aggSelect = "0 AS agg_tests_count, '' AS agg_test_names, '' AS agg_test_ids, 0 AS agg_total_price, 0 AS agg_total_discount";
            $aggJoin = '';
        }

        $sql = "SELECT e.*, \n                   p.name AS patient_name, p.uhid, p.age, p.sex AS gender, p.contact AS patient_contact, p.address AS patient_address,\n                   d.name AS doctor_name,\n                   o.name AS owner_name,\n                   du.username AS doctor_added_by_username,\n                   u.username AS added_by_username, u.full_name AS added_by_full_name,\n                   {$aggSelect}\n            FROM entries e \n            LEFT JOIN patients p ON e.patient_id = p.id \n            LEFT JOIN doctors d ON e.doctor_id = d.id \n            LEFT JOIN owners o ON e.owner_id = o.id\n            LEFT JOIN users du ON d.added_by = du.id\n            LEFT JOIN users u ON e.added_by = u.id" .
            $aggJoin .
            " WHERE e.id = ?" . $scopeWhere;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            http_response_code(404);
            json_response(['success' => false, 'message' => 'Entry not found']);
            return;
        }
        
        // Format data for frontend compatibility
        $entriesCaps = get_entries_schema_capabilities($pdo);
        if (empty($row['entry_date'])) {
            $row['entry_date'] = $row['created_at'];
        }
        
        // Format test information
        $testsCount = isset($row['tests_count']) ? (int)$row['tests_count'] : (int)($row['agg_tests_count'] ?? 0);
        $testNames = $row['test_names'] ?? ($row['agg_test_names'] ?? '');
        $testIds = $row['test_ids'] ?? ($row['agg_test_ids'] ?? '');

        $row['tests_count'] = $testsCount;
        $row['test_names'] = $testNames;
        $row['test_ids'] = $testIds;
        
        // Format pricing
        $aggTotalPrice = isset($row['agg_total_price']) ? (float)$row['agg_total_price'] : (float)($row['total_price'] ?? 0);
        $aggDiscount = isset($row['agg_total_discount']) ? (float)$row['agg_total_discount'] : (float)($row['total_discount'] ?? 0);
        $row['aggregated_total_price'] = $aggTotalPrice;
        $row['aggregated_total_discount'] = $aggDiscount;

        if ($entriesCaps['has_total_price'] && isset($row['total_price'])) {
            $finalAmount = (float)$row['total_price'];
        } else if ($entriesCaps['has_subtotal'] && isset($row['subtotal'])) {
            $finalAmount = (float)$row['subtotal'] - (float)($row['discount_amount'] ?? 0);
        } else {
            $finalAmount = $aggTotalPrice - $aggDiscount;
        }

        $row['total_price'] = $entriesCaps['has_total_price'] && isset($row['total_price'])
            ? (float)$row['total_price']
            : $aggTotalPrice;
        $row['total_discount'] = $entriesCaps['has_discount_amount'] && isset($row['discount_amount'])
            ? (float)$row['discount_amount']
            : $aggDiscount;
        $row['final_amount'] = $finalAmount;
        
        // Set grouped flag based on test count
        $row['grouped'] = $row['tests_count'] > 1 ? 1 : 0;
        
        // For backward compatibility, set test_name to first test or all tests
        if ($row['tests_count'] == 1) {
            $row['test_name'] = $row['test_names'];
        } else if ($row['tests_count'] > 1) {
            $row['test_name'] = $row['tests_count'] . ' tests: ' . $row['test_names'];
        } else {
            $row['test_name'] = 'No tests';
        }
        
        // Also fetch individual tests for the entry
        $entryTestsCaps = get_entry_tests_schema_capabilities($pdo);
        if ($entryTestsCaps['table_exists']) {
            $testsSql = "SELECT et.*, t.name as test_name, t.unit, t.reference_range, t.min, t.max, t.min_male, t.max_male, t.min_female, t.max_female, tc.name as category_name \n                         FROM entry_tests et \n                         LEFT JOIN tests t ON et.test_id = t.id\n                         LEFT JOIN categories tc ON t.category_id = tc.id\n                         WHERE et.entry_id = ?";
            $testsStmt = $pdo->prepare($testsSql);
            $testsStmt->execute([$_GET['id']]);
            $row['tests'] = $testsStmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $row['tests'] = [];
        }

        json_response(['success' => true, 'data' => $row]);
    }

    if ($action === 'save') {
        // Check permission
        if (!simpleCheckPermission($user_data, 'save')) {
            json_response(['success' => false, 'message' => 'Permission denied to save entry'], 403);
        }
        
        // Handle multiple tests per entry
        $input = [];
        $content_type = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($content_type, 'application/json') !== false) {
            $input = json_decode(file_get_contents('php://input'), true) ?: [];
        } else {
            $input = $_POST;
        }

        // Add user/session info if needed
        if (!isset($input['added_by']) && isset($user_data['user_id'])) {
            $input['added_by'] = $user_data['user_id'];
        }

        // Check if this is a multiple tests entry
        if (isset($input['tests']) && !empty($input['tests'])) {
            $tests = json_decode($input['tests'], true);
            
            if (is_array($tests) && count($tests) > 0) {
                $entryTestCaps = get_entry_tests_schema_capabilities($pdo);
                if (!$entryTestCaps['table_exists']) {
                    json_response(['success' => false, 'message' => 'Multiple tests are not supported on this installation (missing entry_tests table).'], 400);
                }

                try {
                    // Debug logging
                    error_log('Entry API save: received ' . count($tests) . ' tests');
                    error_log('Entry API save payload keys: ' . implode(',', array_keys($input ?? [])));
                    error_log('Entry API pricing data received: subtotal=' . ($input['subtotal'] ?? 'NOT SET') . 
                              ', discount=' . ($input['discount_amount'] ?? 'NOT SET') . 
                              ', total=' . ($input['total_price'] ?? 'NOT SET'));
                    
                    // Also write a lightweight debug file for easier inspection on the server
                    $debugDir = __DIR__ . '/../tmp';
                    if (!is_dir($debugDir)) {
                        @mkdir($debugDir, 0755, true);
                    }
                    $debugFile = $debugDir . '/entry_api_debug.log';
                    $debugLine = date('[Y-m-d H:i:s]') . " SAVE_RECEIVED tests=" . count($tests) . 
                                 " subtotal=" . ($input['subtotal'] ?? 'NONE') . 
                                 " discount=" . ($input['discount_amount'] ?? 'NONE') . 
                                 " total=" . ($input['total_price'] ?? 'NONE') . "\n";
                    @file_put_contents($debugFile, $debugLine, FILE_APPEND | LOCK_EX);
                    $pdo->beginTransaction();
                    
                    $entryCaps = get_entries_schema_capabilities($pdo);
                    
                    // Check if this is an update (has id) or create (no id)
                    $isUpdate = !empty($input['id']);
                    $entryId = $isUpdate ? (int)$input['id'] : null;

                    // Create the main entry with schema-awareness
                    // Validate and clean patient_id (required)
                    if (empty($input['patient_id'])) {
                        throw new Exception('Patient ID is required');
                    }
                    $patientId = (int)$input['patient_id'];
                    
                    // Clean up doctor_id: convert empty string to NULL
                    $doctorId = isset($input['doctor_id']) && $input['doctor_id'] !== '' && $input['doctor_id'] !== '0' 
                        ? (int)$input['doctor_id'] 
                        : null;
                    
                    // Clean up owner_id: convert empty string to NULL
                    $ownerId = isset($input['owner_id']) && $input['owner_id'] !== '' && $input['owner_id'] !== '0'
                        ? (int)$input['owner_id']
                        : null;
                    
                    // Clean up added_by (required)
                    if (empty($input['added_by'])) {
                        throw new Exception('Added by user ID is required');
                    }
                    $addedBy = (int)$input['added_by'];
                    
                    $entryData = [
                        'patient_id' => $patientId,
                        'doctor_id' => $doctorId,
                        'owner_id' => $ownerId,
                        'entry_date' => $input['entry_date'] ?? date('Y-m-d'),
                        'status' => $input['status'] ?? 'pending',
                        'added_by' => $addedBy
                    ];
                    
                    // Log the cleaned IDs
                    error_log("Entry data IDs: patient_id=$patientId, doctor_id=" . ($doctorId ?? 'NULL') . ", owner_id=" . ($ownerId ?? 'NULL') . ", added_by=$addedBy");

                    // Add additional optional fields if they exist in the input AND in the database
                    if (isset($input['priority']) && $entryCaps['has_priority']) {
                        $entryData['priority'] = $input['priority'];
                    }
                    if (isset($input['referral_source']) && $entryCaps['has_referral_source']) {
                        $entryData['referral_source'] = $input['referral_source'];
                    }
                    if (isset($input['patient_contact']) && $entryCaps['has_patient_contact']) {
                        $entryData['patient_contact'] = $input['patient_contact'];
                    }
                    if (isset($input['patient_address']) && $entryCaps['has_patient_address']) {
                        $entryData['patient_address'] = $input['patient_address'];
                    }
                    if (isset($input['gender']) && $entryCaps['has_gender']) {
                        $entryData['gender'] = $input['gender'];
                    }

                    if ($entryCaps['has_remarks']) {
                        $entryData['remarks'] = $input['notes'] ?? null;
                    } elseif ($entryCaps['has_notes']) {
                        $entryData['notes'] = $input['notes'] ?? null;
                    }
                    if ($entryCaps['has_grouped']) {
                        $entryData['grouped'] = 1;
                    }
                    if ($entryCaps['has_tests_count']) {
                        $entryData['tests_count'] = count($tests);
                    }

                    // Calculate totals based on test data and form input
                    $calculatedSubtotal = 0;
                    
                    // Calculate subtotal from test prices
                    foreach ($tests as $test) {
                        $testPrice = floatval($test['price'] ?? 0);
                        $calculatedSubtotal += $testPrice;
                        error_log("  Test ID {$test['test_id']} price: $testPrice");
                    }
                    
                    error_log("Calculated subtotal from tests: $calculatedSubtotal");
                    
                    // PRIORITY: Use form values if provided (including 0.00)
                    // Accept string values like "0.00" or numeric 0
                    $formSubtotal = isset($input['subtotal']) && $input['subtotal'] !== '' ? floatval($input['subtotal']) : null;
                    $formDiscount = isset($input['discount_amount']) && $input['discount_amount'] !== '' ? floatval($input['discount_amount']) : null;
                    $formTotal = isset($input['total_price']) && $input['total_price'] !== '' ? floatval($input['total_price']) : null;
                    
                    error_log("Form pricing values: subtotal={$formSubtotal}, discount={$formDiscount}, total={$formTotal}");
                    
                    // Use form values if they're set (including 0.00), otherwise use calculated
                    $finalSubtotal = ($formSubtotal !== null) 
                        ? $formSubtotal 
                        : $calculatedSubtotal;
                    
                    // Discount can be 0, so just check if it's set
                    $finalDiscount = ($formDiscount !== null) 
                        ? $formDiscount 
                        : 0;
                    
                    // Total: use form value if set, otherwise calculate
                    $finalTotal = ($formTotal !== null) 
                        ? $formTotal 
                        : max($finalSubtotal - $finalDiscount, 0);
                    
                    // Log final values that will be saved
                    error_log("FINAL pricing to save: subtotal=$finalSubtotal, discount=$finalDiscount, total=$finalTotal");

                    // Store pricing in entry record
                    if ($entryCaps['has_price']) {
                        $entryData['price'] = $finalSubtotal;
                    }
                    //if ($entryCaps['has_subtotal']) {
                        $entryData['subtotal'] = $finalSubtotal;
                    //}
                    //if ($entryCaps['has_discount_amount']) {
                        $entryData['discount_amount'] = $finalDiscount;
                    //}
                    //if ($entryCaps['has_total_price']) {
                        $entryData['total_price'] = $finalTotal;
                    //}

                    // Set primary test to first test for backward compatibility
                    if (!empty($tests) && $entryCaps['has_test_id']) {
                        $entryData['test_id'] = $tests[0]['test_id'];
                    }
                    
                    // Log what we're about to save
                    error_log("Entry data being saved: " . json_encode($entryData));
                    if (isset($entryData['subtotal'])) {
                        error_log("✓ Pricing fields included in save: subtotal={$entryData['subtotal']}, discount={$entryData['discount_amount']}, total={$entryData['total_price']}");
                    } else {
                        error_log("✗ WARNING: Pricing fields NOT in entryData!");
                    }
                    
                    // Insert or Update entry
                    if ($isUpdate) {
                        // UPDATE existing entry
                        $updateFields = [];
                        $updateParams = [];
                        foreach ($entryData as $key => $value) {
                            $updateFields[] = "$key = :$key";
                            $updateParams[$key] = $value;
                        }
                        if ($entryCaps['has_updated_at']) {
                            $updateFields[] = 'updated_at = NOW()';
                        }
                        $updateParams['entry_id'] = $entryId;

                        //print_r($updateParams); die;
                        
                        error_log("UPDATE SQL fields: " . implode(', ', $updateFields));
                        
                        $sql = "UPDATE entries SET " . implode(', ', $updateFields) . " WHERE id = :entry_id";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($updateParams);
                        
                        // Delete existing tests for this entry
                        $deleteStmt = $pdo->prepare("DELETE FROM entry_tests WHERE entry_id = ?");
                        $deleteStmt->execute([$entryId]);
                        
                        error_log('Entry API save: updated entry id ' . $entryId);
                        @file_put_contents($debugFile, date('[Y-m-d H:i:s]') . " UPDATED_ENTRY id=" . $entryId . "\n", FILE_APPEND | LOCK_EX);
                    } else {
                        // INSERT new entry
                        $entryFields = implode(', ', array_keys($entryData));
                        $entryPlaceholders = ':' . implode(', :', array_keys($entryData));
                        
                        error_log("INSERT SQL fields: $entryFields");
                        error_log("INSERT SQL: INSERT INTO entries ($entryFields) VALUES ($entryPlaceholders)");
                        
                        $sql = "INSERT INTO entries ($entryFields) VALUES ($entryPlaceholders)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($entryData);
                        $entryId = $pdo->lastInsertId();
                        error_log('Entry API save: created entry id ' . $entryId);
                        @file_put_contents($debugFile, date('[Y-m-d H:i:s]') . " CREATED_ENTRY id=" . $entryId . "\n", FILE_APPEND | LOCK_EX);
                    }
                    
                    // Insert individual tests
                    $testIds = [];
                    $testNames = [];
                    foreach ($tests as $test) {
                        // Ensure test price is set (from test data)
                        $testPrice = floatval($test['price'] ?? 0);
                        $testDiscount = floatval($test['discount_amount'] ?? 0);
                        
                        $testData = [
                            'entry_id' => $entryId,
                            'test_id' => $test['test_id'],
                            'result_value' => $test['result_value'] ?? null,
                            'unit' => $test['unit'] ?? null,
                            'remarks' => $test['remarks'] ?? null,
                            'status' => 'pending'
                        ];
                        
                        // Add pricing fields if columns exist
                        if ($entryTestCaps['has_price']) {
                            $testData['price'] = $testPrice;
                        }
                        if ($entryTestCaps['has_discount_amount']) {
                            $testData['discount_amount'] = $testDiscount;
                        }
                        //if ($entryTestCaps['has_total_price']) {
                            $testData['total_price'] = max($testPrice - $testDiscount, 0);
                        //}
                        
                        $testFields = implode(', ', array_keys($testData));
                        $testPlaceholders = ':' . implode(', :', array_keys($testData));
                        $testSql = "INSERT INTO entry_tests ($testFields) VALUES ($testPlaceholders)";
                        $testStmt = $pdo->prepare($testSql);
                        $testStmt->execute($testData);
                        
                        error_log("Entry API: saved test $test[test_id] with price=$testPrice, discount=$testDiscount");
                        @file_put_contents($debugFile, date('[Y-m-d H:i:s]') . " INSERT_TEST entry=$entryId test_id={$test['test_id']} price=$testPrice\n", FILE_APPEND | LOCK_EX);
                        
                        $testIds[] = $test['test_id'];
                        $testNames[] = $test['test_name'] ?? '';
                    }
                    
                    // Update entry with aggregated test data
                    $updateFields = [];
                    $updateParams = ['entry_id' => $entryId];
                    if ($entryCaps['has_test_ids']) {
                        $updateFields[] = 'test_ids = :test_ids';
                        $updateParams['test_ids'] = implode(',', $testIds);
                    }
                    if ($entryCaps['has_test_names']) {
                        $updateFields[] = 'test_names = :test_names';
                        $updateParams['test_names'] = implode(', ', $testNames);
                    }
                    if ($entryCaps['has_updated_at']) {
                        $updateFields[] = 'updated_at = NOW()';
                    }
                    if ($updateFields) {
                        $updateSql = "UPDATE entries SET " . implode(', ', $updateFields) . " WHERE id = :entry_id";
                        $updateStmt = $pdo->prepare($updateSql);
                        $updateStmt->execute($updateParams);
                    }

                    // Refresh aggregated totals based on actual test rows
                    refresh_entry_aggregates($pdo, $entryId);
                    
                    $pdo->commit();
                    error_log('Entry API save: commit successful for entry ' . $entryId);
                    @file_put_contents($debugFile, date('[Y-m-d H:i:s]') . " COMMIT entry=" . $entryId . "\n", FILE_APPEND | LOCK_EX);
                    
                    // VERIFY: Read back the saved entry to confirm pricing was saved
                    $verifyStmt = $pdo->prepare("SELECT subtotal, discount_amount, total_price FROM entries WHERE id = ?");
                    $verifyStmt->execute([$entryId]);
                    $savedEntry = $verifyStmt->fetch(PDO::FETCH_ASSOC);
                    error_log("VERIFICATION: Entry $entryId saved pricing - subtotal={$savedEntry['subtotal']}, discount={$savedEntry['discount_amount']}, total={$savedEntry['total_price']}");
                    
                    if ($savedEntry['subtotal'] == 0 && $savedEntry['total_price'] == 0) {
                        error_log("⚠️ WARNING: Entry saved but pricing is ZERO in database!");
                    }
                    
                    $successMessage = $isUpdate 
                        ? 'Entry with multiple tests updated successfully' 
                        : 'Entry with multiple tests created successfully';
                    
                    json_response(
                        [
                            'success' => true,
                            'message' => $successMessage,
                            'data' => [
                                'id' => $entryId, 
                                'tests_count' => count($tests), 
                                'action' => $isUpdate ? 'updated' : 'created',
                                'saved_pricing' => $savedEntry // Include saved pricing in response for debugging
                            ]
                        ]
                    );
                    
                } catch (Exception $e) {
                    // Rollback and provide a detailed debug entry for troubleshooting
                    try {
                        $pdo->rollBack();
                    } catch (Exception $__) {
                        // ignore rollback errors
                    }
                    json_response(
                        [
                            'success' => false,
                            'message' => 'Failed to save entry with multiple tests',
                            'error' => [
                                'message' => $e->getMessage(),
                                'file' => $e->getFile(),
                                'line' => $e->getLine(),
                                'trace' => $e->getTraceAsString()
                            ]
                        ],
                        500
                    );
                }
            } else {
                json_response(['success' => false, 'message' => 'No valid tests provided'], 400);
            }
        } else {
            // Handle single test entry (existing logic)
            // Proxy the save request to the main API to enforce duplicate prevention and update-on-change logic
            $apiUrl = __DIR__ . '/../patho_api/entry.php';
            $apiEndpoint = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/../patho_api/entry.php';

            // Use cURL to POST data to the API
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            // Ensure gender (if present) is forwarded
            $forwardPayload = $input;
            if (isset($input['gender'])) {
                $forwardPayload['gender'] = $input['gender'];
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($forwardPayload));
            // Forward session cookie if needed
            if (isset($_COOKIE[session_name()])) {
                curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . $_COOKIE[session_name()]);
            }
            // Forward content-type
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

            $apiResponse = curl_exec($ch);
            $curlErr = curl_error($ch);
            curl_close($ch);

            error_log('Entry API proxy response: ' . substr($apiResponse ?? '', 0, 1000));
            @file_put_contents(__DIR__ . '/../tmp/entry_api_debug.log', date('[Y-m-d H:i:s]') . " PROXY_RESPONSE " . substr($apiResponse ?? '', 0, 1000) . "\n", FILE_APPEND | LOCK_EX);
            if ($curlErr) {
                error_log('Entry API proxy curl error: ' . $curlErr);
                @file_put_contents(__DIR__ . '/../tmp/entry_api_debug.log', date('[Y-m-d H:i:s]') . " PROXY_CURL_ERR " . $curlErr . "\n", FILE_APPEND | LOCK_EX);
            }

            if ($apiResponse === false) {
                json_response(['success' => false, 'message' => 'API request failed', 'error' => $curlErr], 500);
            }

            // Output the API response directly
            header('Content-Type: application/json');
            echo $apiResponse;
            exit;
        }
    } else if ($action === 'delete' && isset($_POST['id'])) {
        // Check permission
        if (!simpleCheckPermission($user_data, 'delete', $_POST['id'])) {
            json_response(['success' => false, 'message' => 'Permission denied to delete entry'], 403);
        }
        
        try {
            $pdo->beginTransaction();
            
            // Delete related entry_tests first (if table exists)
            $entryTestsCaps = get_entry_tests_schema_capabilities($pdo);
            if ($entryTestsCaps['table_exists']) {
                $stmtTests = $pdo->prepare('DELETE FROM entry_tests WHERE entry_id = ?');
                $stmtTests->execute([$_POST['id']]);
            }
            
            // Delete the entry
            $stmt = $pdo->prepare('DELETE FROM entries WHERE id = ?');
            $stmt->execute([$_POST['id']]);
            
            $pdo->commit();
            json_response(['success' => true, 'message' => 'Entry deleted successfully']);
        } catch (Exception $e) {
            try {
                $pdo->rollBack();
            } catch (Exception $__) {
                // ignore rollback errors
            }
            json_response(['success' => false, 'message' => 'Failed to delete entry', 'error' => $e->getMessage()], 500);
        }
    }

    if ($action === 'export') {
        // Check permission
        if (!simpleCheckPermission($user_data, 'list')) {
            json_response(['success' => false, 'message' => 'Permission denied to export entries'], 403);
        }
        
        // Get export parameters
        $format = $_GET['format'] ?? 'csv';
        $status = $_GET['status'] ?? '';
        $dateFilter = $_GET['date'] ?? '';
        
        // Build query with filters
        $viewerRole = $user_data['role'] ?? 'user';
        $viewerId = (int)($user_data['user_id'] ?? 0);
        $scopeWhere = '';
        $params = [];
        
        if ($viewerRole !== 'master') {
            $scopeWhere = ' WHERE e.added_by = ?';
            $params[] = $viewerId;
        }
        
        // Add status filter
        if ($status) {
            if ($scopeWhere) {
                $scopeWhere .= ' AND e.status = ?';
            } else {
                $scopeWhere = ' WHERE e.status = ?';
            }
            $params[] = $status;
        }
        
        // Add date filter
        if ($dateFilter) {
            $dateCondition = '';
            switch ($dateFilter) {
                case 'today':
                    $dateCondition = 'DATE(COALESCE(e.entry_date, e.created_at)) = CURDATE()';
                    break;
                case 'yesterday':
                    $dateCondition = 'DATE(COALESCE(e.entry_date, e.created_at)) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
                    break;
                case 'this_week':
                    $dateCondition = 'DATE(COALESCE(e.entry_date, e.created_at)) >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)';
                    break;
                case 'this_month':
                    $dateCondition = 'MONTH(COALESCE(e.entry_date, e.created_at)) = MONTH(CURDATE()) AND YEAR(COALESCE(e.entry_date, e.created_at)) = YEAR(CURDATE())';
                    break;
            }
            
            if ($dateCondition) {
                if ($scopeWhere) {
                    $scopeWhere .= ' AND ' . $dateCondition;
                } else {
                    $scopeWhere = ' WHERE ' . $dateCondition;
                }
            }
        }
        
        // Get entry tests aggregation
        $entryTestsCaps = get_entry_tests_schema_capabilities($pdo);
        if ($entryTestsCaps['table_exists']) {
            $aggSql = build_entry_tests_aggregation_sql($pdo);
            $aggSelect = "COALESCE(agg.tests_count, 0) AS tests_count,\n                   COALESCE(agg.test_names, '') AS test_names,\n                   COALESCE(agg.total_price, 0) AS total_price,\n                   COALESCE(agg.total_discount, 0) AS total_discount";
            $aggJoin = " LEFT JOIN (" . $aggSql . ") agg ON agg.entry_id = e.id";
        } else {
            $aggSelect = "0 AS tests_count, '' AS test_names, 0 AS total_price, 0 AS total_discount";
            $aggJoin = '';
        }
        
        // Build dynamic SELECT based on available columns
        $entriesCaps = get_entries_schema_capabilities($pdo);
        $selectFields = "e.id, e.entry_date, e.status, e.created_at";
        
        if ($entriesCaps['has_priority']) {
            $selectFields .= ", e.priority";
        }
        if ($entriesCaps['has_referral_source']) {
            $selectFields .= ", e.referral_source";
        }
        if ($entriesCaps['has_patient_contact']) {
            $selectFields .= ", e.patient_contact";
        }
        if ($entriesCaps['has_patient_address']) {
            $selectFields .= ", e.patient_address";
        }
        if ($entriesCaps['has_gender']) {
            $selectFields .= ", e.gender";
        }
        
        $sql = "SELECT {$selectFields},\n                       p.name AS patient_name, p.uhid, p.age, p.sex, p.contact AS patient_contact, p.address AS patient_address,\n                       d.name AS doctor_name,\n                       o.name AS owner_name,\n                       u.username AS added_by_username, u.full_name AS added_by_full_name,\n                       {$aggSelect}\n                FROM entries e \n                LEFT JOIN patients p ON e.patient_id = p.id \n                LEFT JOIN doctors d ON e.doctor_id = d.id \n                LEFT JOIN owners o ON e.owner_id = o.id\n                LEFT JOIN users u ON e.added_by = u.id" .
                $aggJoin .
                $scopeWhere .
                " ORDER BY COALESCE(e.entry_date, e.created_at) DESC, e.id DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format data for export
        $exportData = [];
        foreach ($rows as $row) {
            $finalAmount = max((float)($row['total_price'] ?? 0) - (float)($row['total_discount'] ?? 0), 0);
            $exportData[] = [
                'ID' => $row['id'],
                'Date' => $row['entry_date'] ? date('d/m/Y', strtotime($row['entry_date'])) : '',
                'Patient Name' => $row['patient_name'] ?? '',
                'UHID' => $row['uhid'] ?? '',
                'Age/Gender' => ($row['age'] ? $row['age'] : '') . ($row['sex'] ? ' ' . $row['sex'] : ''),
                'Patient Contact' => $row['patient_contact'] ?? '',
                'Patient Address' => $row['patient_address'] ?? '',
                'Doctor' => $row['doctor_name'] ?? '',
                'Owner/Lab' => $row['owner_name'] ?? '',
                'Tests' => $row['test_names'] ?? '',
                'Tests Count' => $row['tests_count'] ?? 0,
                'Status' => ucfirst($row['status'] ?? ''),
                'Priority' => ucfirst($row['priority'] ?? 'Normal'),
                'Referral Source' => ucfirst($row['referral_source'] ?? 'N/A'),
                'Amount' => number_format($finalAmount, 2),
                'Added By' => $row['added_by_full_name'] ?? $row['added_by_username'] ?? '',
                'Created' => date('d/m/Y H:i', strtotime($row['created_at']))
            ];
        }
        
        if ($format === 'csv') {
            // Set headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="entries_' . date('Y-m-d_H-i-s') . '.csv"');
            
            // Create CSV output
            $output = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers
            if (!empty($exportData)) {
                fputcsv($output, array_keys($exportData[0]));
                
                // Add data
                foreach ($exportData as $row) {
                    fputcsv($output, $row);
                }
            }
            
            fclose($output); 
            exit;
        } else {
            // Return JSON for other formats
            json_response(['success' => true, 'data' => $exportData, 'total' => count($exportData)]);
        }
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);

} catch (PDOException $e) {
    error_log('Entry API PDO error: ' . $e->getMessage());
    http_response_code(500);
    json_response(['success' => false, 'message' => 'Database error', 'error' => $e->getMessage()]);
} catch (Exception $e) {
    error_log('Entry API error: ' . $e->getMessage());
    http_response_code(500);
    json_response(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()]);
}