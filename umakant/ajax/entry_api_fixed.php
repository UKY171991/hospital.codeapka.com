<?php
// ajax/entry_api.php - CRUD for entries

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output
ini_set('log_errors', 1);

// Set content type early
header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../inc/connection.php';
} catch (Exception $e) {
    // If database connection fails, provide fallback response
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
    http_response_code(401);
    echo json_encode([
        'success' => false, 
        'message' => 'Authentication required',
        'debug_info' => getAuthDebugInfo()
    ]);
    exit;
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
        'has_gender' => db_column_exists($pdo, 'entries', 'gender'),
        'has_age' => db_column_exists($pdo, 'entries', 'age')
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
        error_log("Entry tests table does not exist, returning empty aggregation");
        return "SELECT NULL AS entry_id, 0 AS tests_count, '' AS test_names, '' AS test_ids, 0 AS total_price, 0 AS total_discount FROM dual WHERE 1 = 0";
    }
    
    // Use COALESCE to handle NULL values properly
    $sumPrice = $caps['has_price'] ? 'SUM(COALESCE(et.price, 0))' : 'SUM(0)';
    $sumDiscount = $caps['has_discount_amount'] ? 'SUM(COALESCE(et.discount_amount, 0))' : 'SUM(0)';

    $sql = "SELECT et.entry_id,
                   COUNT(DISTINCT et.test_id) as tests_count,
                   GROUP_CONCAT(DISTINCT COALESCE(t.name, CONCAT('Test_', et.test_id)) ORDER BY t.name SEPARATOR ', ') as test_names,
                   GROUP_CONCAT(DISTINCT et.test_id ORDER BY et.test_id) as test_ids,
                   {$sumPrice} as total_price,
                   {$sumDiscount} as total_discount
            FROM entry_tests et
            LEFT JOIN tests t ON et.test_id = t.id
            WHERE et.entry_id IS NOT NULL AND et.test_id IS NOT NULL
            GROUP BY et.entry_id";
    
    error_log("Built improved aggregation SQL: " . $sql);
    return $sql;
}

function refresh_entry_aggregates($pdo, $entryId) {
    error_log("Refreshing aggregates for entry ID: $entryId");
    $entriesCaps = get_entries_schema_capabilities($pdo);
    $entryTestsCaps = get_entry_tests_schema_capabilities($pdo);
    if (!$entryTestsCaps['table_exists']) {
        error_log("Entry tests table does not exist, skipping aggregate refresh");
        return;
    }

    // First, verify the entry exists
    $entryCheckStmt = $pdo->prepare("SELECT id FROM entries WHERE id = ?");
    $entryCheckStmt->execute([$entryId]);
    if (!$entryCheckStmt->fetch()) {
        error_log("Entry ID $entryId does not exist, skipping aggregate refresh");
        return;
    }

    $aggSql = build_entry_tests_aggregation_sql($pdo);
    $fullQuery = "SELECT tests_count, test_ids, test_names, total_price, total_discount FROM (" . $aggSql . ") agg WHERE entry_id = ?";
    error_log("Aggregate refresh query: " . $fullQuery);

    $stmt = $pdo->prepare($fullQuery);
    $stmt->execute([$entryId]);
    $aggRow = $stmt->fetch(PDO::FETCH_ASSOC);
    
    error_log("Aggregate query result for entry $entryId: " . json_encode($aggRow));

    // If no aggregation data found, check if there are any tests for this entry
    if (!$aggRow) {
        $testCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM entry_tests WHERE entry_id = ?");
        $testCheckStmt->execute([$entryId]);
        $testCount = $testCheckStmt->fetchColumn();
        
        if ($testCount > 0) {
            error_log("Warning: Entry $entryId has $testCount tests but no aggregation data returned");
        }
        
        // Set default values for entries with no tests
        $testsCount = 0;
        $testIds = '';
        $testNames = '';
        $totalPrice = 0;
        $totalDiscount = 0;
    } else {
        $testsCount = (int)($aggRow['tests_count'] ?? 0);
        $testIds = $aggRow['test_ids'] ?? '';
        $testNames = $aggRow['test_names'] ?? '';
        $totalPrice = (float)($aggRow['total_price'] ?? 0);
        $totalDiscount = (float)($aggRow['total_discount'] ?? 0);
    }
    
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
        error_log("No fields to update for entry ID: $entryId (no matching columns in entries table)");
        return;
    }

    $sql = "UPDATE entries SET " . implode(', ', $fields) . " WHERE id = :entry_id";
    error_log("Updating entry aggregates with SQL: " . $sql);
    error_log("Update parameters: " . json_encode($params));
    
    try {
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($params);
        
        if ($result) {
            $rowsAffected = $stmt->rowCount();
            error_log("Successfully updated aggregates for entry ID: $entryId (rows affected: $rowsAffected)");
        } else {
            error_log("Failed to update aggregates for entry ID: $entryId - " . json_encode($stmt->errorInfo()));
        }
    } catch (Exception $e) {
        error_log("Exception updating aggregates for entry ID: $entryId - " . $e->getMessage());
    }
}

try {
    if ($action === 'stats') {
        // Check permission
        if (!simpleCheckPermission($user_data, 'list')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Permission denied to view statistics']);
            exit;
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
        
        echo json_encode(['success' => true, 'status' => 'success', 'data' => $stats]);
        exit;
    } else if ($action === 'list') {
        // Check permission
        if (!simpleCheckPermission($user_data, 'list')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Permission denied to list entries']);
            exit;
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

        $sql = "SELECT e.*, \n                   p.name AS patient_name, p.uhid, p.age AS patient_age, p.sex AS gender, p.contact AS patient_contact, p.address AS patient_address,\n                   d.name AS doctor_name,\n                   {$ownerSelect}\n                   du.username AS doctor_added_by_username,\n                   u.username AS added_by_username, u.full_name AS added_by_full_name,\n                   {$aggSelect}\n            FROM entries e \n            LEFT JOIN patients p ON e.patient_id = p.id \n            LEFT JOIN doctors d ON e.doctor_id = d.id " .
            $ownerJoin .
            " LEFT JOIN users du ON d.added_by = du.id\n            LEFT JOIN users u ON e.added_by = u.id" .
            $aggJoin .
            $scopeWhere .
            " ORDER BY COALESCE(e.entry_date, e.created_at) DESC, e.id DESC";
        
        try {
            error_log("Executing list query: " . $sql);
            error_log("With parameters: " . json_encode($params));
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Retrieved " . count($rows) . " entries from database");
            
            // Debug first row to see aggregation data
            if (count($rows) > 0) {
                $firstRow = $rows[0];
                error_log("First row aggregation data: agg_tests_count={$firstRow['agg_tests_count']}, agg_test_names='{$firstRow['agg_test_names']}'");
            }
        } catch (Exception $e) {
            error_log("Database error in list query: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error while listing entries', 'error' => $e->getMessage()]);
            exit;
        }
        
        // Format data for frontend compatibility
        $entriesCaps = get_entries_schema_capabilities($pdo);
        foreach ($rows as &$row) {
            // Ensure entry_date is available for frontend
            if (empty($row['entry_date'])) {
                $row['entry_date'] = $row['created_at'];
            }
            
            // Format test information - use aggregated data if available
            $row['tests_count'] = (int)($row['agg_tests_count'] ?? $row['tests_count'] ?? 0);
            $row['test_names'] = $row['agg_test_names'] ?? $row['test_names'] ?? '';
            $row['test_ids'] = $row['agg_test_ids'] ?? $row['test_ids'] ?? '';
            
            // Debug logging for test aggregation
            error_log("Entry ID {$row['id']}: tests_count={$row['tests_count']}, test_names='{$row['test_names']}'");
            if ($row['tests_count'] > 1) {
                error_log("Entry ID {$row['id']} has multiple tests: agg_tests_count={$row['agg_tests_count']}, agg_test_names='{$row['agg_test_names']}'");
            }
            
            // Format pricing - use aggregated data if available
            $aggTotalPrice = (float)($row['agg_total_price'] ?? $row['total_price'] ?? 0);
            $aggDiscount = (float)($row['agg_total_discount'] ?? $row['total_discount'] ?? 0);
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
        
        // Return the formatted data
        echo json_encode(['success' => true, 'data' => $rows]);
        exit;
        
    } else if ($action === 'report_list') {
        if (!simpleCheckPermission($user_data, 'list')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Permission denied to list reports']);
            exit;
        }

        try {
            $test_id = $_GET['test_id'] ?? null;
            $doctor_id = $_GET['doctor_id'] ?? null;
            $status = $_GET['status'] ?? '';
            $date_from = $_GET['date_from'] ?? null;
            $date_to = $_GET['date_to'] ?? null;

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
                      LEFT JOIN tests t ON et.test_id = t.id
                      WHERE 1=1";

            $params = [];

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

            // Add viewer scope if not admin - use the same logic as the list action
            $viewerRole = $user_data['role'] ?? 'user';
            $viewerId = (int)($user_data['user_id'] ?? 0);
            if ($viewerRole !== 'master') {
                $query .= " AND e.added_by = ?";
                $params[] = $viewerId;
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

            echo json_encode([
                'success' => true,
                'data' => $reports,
                'summary' => [
                    'total_records' => count($reports),
                    'total_amount' => $total_amount,
                    'total_amount_formatted' => number_format($total_amount, 2)
                ]
            ]);
            exit;

        } catch (Exception $e) {
            error_log('Error in report_list: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch reports',
                'error' => $e->getMessage()
            ]);
            exit;
        }
    } else if ($action === 'get') {
        // Debug logging
        error_log("Entry API: GET action called with ID: " . ($_GET['id'] ?? 'none'));
        error_log("Entry API: User data: " . print_r($user_data, true));
        
        // Check permission
        if (!simpleCheckPermission($user_data, 'get')) {
            error_log("Entry API: Permission denied for user: " . ($user_data['user_id'] ?? 'unknown'));
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Permission denied to view entry details']);
            exit;
        }
        
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            error_log("Entry API: No ID provided");
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Entry ID is required']);
            exit;
        }
        
        error_log("Entry API: Processing entry ID: $id");
        
        try {
            // Check if user has permission to view this entry
            $viewerRole = $user_data['role'] ?? 'user';
            $viewerId = (int)($user_data['user_id'] ?? 0);
            
            // Build the main entry query with all related data
            $entriesCaps = get_entries_schema_capabilities($pdo);
            $ownerSelect = '';
            $ownerJoin = '';
            if (!empty($entriesCaps['has_owner_id'])) {
                $ownerSelect = "o.name AS owner_name,";
                $ownerJoin = " LEFT JOIN owners o ON e.owner_id = o.id";
            }
            
            $sql = "SELECT e.*, 
                           p.name AS patient_name, p.uhid, p.age, p.sex AS gender, p.contact AS patient_contact, p.address AS patient_address,
                           d.name AS doctor_name, d.specialization AS doctor_specialization,
                           {$ownerSelect}
                           u.username AS added_by_username, u.full_name AS added_by_full_name
                    FROM entries e 
                    LEFT JOIN patients p ON e.patient_id = p.id 
                    LEFT JOIN doctors d ON e.doctor_id = d.id " .
                    $ownerJoin .
                    " LEFT JOIN users u ON e.added_by = u.id
                    WHERE e.id = ?";
            
            // Add scope restriction for non-master users
            if ($viewerRole !== 'master') {
                $sql .= " AND e.added_by = ?";
                $params = [$id, $viewerId];
            } else {
                $params = [$id];
            }
            
            error_log("Entry API: SQL Query: $sql");
            error_log("Entry API: SQL Params: " . print_r($params, true));
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $entry = $stmt->fetch(PDO::FETCH_ASSOC);

            error_log("Entry API: Query result: " . ($entry ? 'Found entry' : 'No entry found'));
            
            if (!$entry) {
                error_log("Entry API: Entry not found for ID $id");
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Entry not found or access denied']);
                exit;
            }
            
            // Get associated tests for this entry
            $entryTestsCaps = get_entry_tests_schema_capabilities($pdo);
            $tests = [];
            
            if ($entryTestsCaps['table_exists']) {
                // Enhanced test SQL with better error handling and logging
                $testSql = "SELECT et.id as entry_test_id,
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
                                   t.id as test_table_id,
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
                                   c.id as category_table_id,
                                   c.name AS category_name
                            FROM entry_tests et
                            LEFT JOIN tests t ON et.test_id = t.id
                            LEFT JOIN categories c ON t.category_id = c.id
                            WHERE et.entry_id = ?
                            ORDER BY et.id, t.name";
                
                error_log("Entry API: Executing test query: $testSql with entry_id: $id");
                
                $testStmt = $pdo->prepare($testSql);
                $testStmt->execute([$id]);
                $tests = $testStmt->fetchAll(PDO::FETCH_ASSOC);
                 
                error_log("Entry API: Raw test query results for entry $id: " . json_encode($tests));
                
                // Format test data with better field handling
                foreach ($tests as &$test) {
                    // Use entry_tests price if available, otherwise use test default price
                    $test['price'] = (float)($test['price'] ?? $test['test_default_price'] ?? 0);
                    $test['discount_amount'] = (float)($test['discount_amount'] ?? 0);
                    $test['total_price'] = $test['price'] - $test['discount_amount'];
                    $test['result_value'] = $test['result_value'] ?? '';
                    $test['status'] = $test['status'] ?? 'pending';
                    
                    // Use entry_tests unit if available, otherwise use test unit
                    
                    echo json_encode(['success' => false, 'message' => 'Entry Testing '.$test]); die;
                    
                    // Clean up duplicate fields
                    unset($test['et_unit'], $test['test_unit'], $test['test_default_price']);
                    unset($test['test_table_id'], $test['category_table_id']);
                    
                    error_log("Entry API: Formatted test data: " . json_encode($test));
                }
            }
            
            // Format entry data
            $entry['tests'] = $tests;
            $entry['tests_count'] = count($tests);
            
            // Debug logging for tests
            error_log("Entry API: Found " . count($tests) . " tests for entry ID: $id");
            if (count($tests) > 0) {
                error_log("Entry API: First test data: " . json_encode($tests[0]));
                error_log("Entry API: All test IDs: " . implode(', ', array_column($tests, 'test_id')));
                error_log("Entry API: All tests data: " . json_encode($tests));
            } else {
                error_log("Entry API: No tests found - checking entry_tests table directly");
                // Check if entry_tests records exist
                $checkStmt = $pdo->prepare("SELECT * FROM entry_tests WHERE entry_id = ?");
                $checkStmt->execute([$id]);
                $directTests = $checkStmt->fetchAll(PDO::FETCH_ASSOC);
                error_log("Entry API: Direct entry_tests query found " . count($directTests) . " records");
                if (count($directTests) > 0) {
                    error_log("Entry API: Direct entry_tests data: " . json_encode($directTests));
                }
            }
            
            // Calculate totals from tests
            $subtotal = 0;
            $totalDiscount = 0;
            foreach ($tests as $test) {
                $subtotal += (float)($test['price'] ?? 0);
                $totalDiscount += (float)($test['discount_amount'] ?? 0);
            }
            
            $entry['subtotal'] = $subtotal;
            $entry['total_discount'] = $totalDiscount;
            $entry['final_amount'] = $subtotal - $totalDiscount;
            
            // Format dates
            if ($entry['entry_date']) {
                $entry['entry_date_formatted'] = date('d M Y', strtotime($entry['entry_date']));
            }
            if ($entry['created_at']) {
                $entry['created_at_formatted'] = date('d M Y H:i', strtotime($entry['created_at']));
            }
            if ($entry['updated_at']) {
                $entry['updated_at_formatted'] = date('d M Y H:i', strtotime($entry['updated_at']));
            }
            
            // Format pricing fields
            $entry['price'] = (float)($entry['price'] ?? 0);
            $entry['discount_amount'] = (float)($entry['discount_amount'] ?? 0);
            $entry['total_price'] = (float)($entry['total_price'] ?? 0);
            
            // Ensure all expected fields exist
            $entry['priority'] = $entry['priority'] ?? 'normal';
            $entry['status'] = $entry['status'] ?? 'pending';
            $entry['notes'] = $entry['notes'] ?? '';
            $entry['referral_source'] = $entry['referral_source'] ?? '';
            
            error_log("Entry API: Returning successful response for entry ID: $id");
            $response = ['success' => true, 'data' => $entry];
            $json = json_encode($response);
            error_log("Entry API: JSON response length: " . strlen($json));
            echo $json;
            exit;
            
        } catch (Exception $e) {
            error_log('Error getting entry details: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to get entry details',
                'error' => $e->getMessage()
            ]);
            exit;
        }
    } else if ($action === 'save') {
        // Handle both create and update operations
        error_log("Entry API: SAVE action called");
        
        // Check permission
        $entryId = (int)($_POST['id'] ?? 0);
        $isUpdate = $entryId > 0;
        
        if ($isUpdate) {
            if (!simpleCheckPermission($user_data, 'update', $_POST['added_by'] ?? null)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Permission denied to update entry']);
                exit;
            }
        } else {
            if (!simpleCheckPermission($user_data, 'create')) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Permission denied to create entry']);
                exit;
            }
        }
        
        try {
            // Debug logging
            error_log("Entry API SAVE: POST data: " . json_encode($_POST));
            
            // Validate required fields
            $patientId = (int)($_POST['patient_id'] ?? 0);
            $entryDate = $_POST['entry_date'] ?? date('Y-m-d');
            $status = $_POST['status'] ?? 'pending';
            $priority = $_POST['priority'] ?? 'normal';
            
            if (!$patientId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Patient is required']);
                exit;
            }
            
            // Get schema capabilities to check which columns exist
            $entriesCaps = get_entries_schema_capabilities($pdo);
            
            // Prepare entry data - only include fields that exist in the database
            $entryData = [
                'patient_id' => $patientId,
                'doctor_id' => (int)($_POST['doctor_id'] ?? 0) ?: null,
                'entry_date' => $entryDate,
                'status' => $status,
                'added_by' => $user_data['user_id']
            ];
            
            // Add optional fields only if they exist in the database schema
            if ($entriesCaps['has_priority']) {
                $entryData['priority'] = $priority;
            }
            if ($entriesCaps['has_referral_source']) {
                $entryData['referral_source'] = $_POST['referral_source'] ?? null;
            }
            // Only add these fields if the columns actually exist in the database
            if ($entriesCaps['has_patient_contact'] && isset($_POST['patient_contact'])) {
                $entryData['patient_contact'] = $_POST['patient_contact'];
                error_log("Entry API: Adding patient_contact to entry data");
            } else if (isset($_POST['patient_contact'])) {
                error_log("Entry API: Skipping patient_contact - column does not exist in entries table");
            }
            
            if ($entriesCaps['has_patient_address'] && isset($_POST['patient_address'])) {
                $entryData['patient_address'] = $_POST['patient_address'];
                error_log("Entry API: Adding patient_address to entry data");
            } else if (isset($_POST['patient_address'])) {
                error_log("Entry API: Skipping patient_address - column does not exist in entries table");
            }
            
            if ($entriesCaps['has_gender'] && isset($_POST['gender'])) {
                $entryData['gender'] = $_POST['gender'];
                error_log("Entry API: Adding gender to entry data");
            } else if (isset($_POST['gender'])) {
                error_log("Entry API: Skipping gender - column does not exist in entries table");
            }
            
            if ($entriesCaps['has_age'] && isset($_POST['age'])) {
                $entryData['age'] = (int)($_POST['age'] ?? 0) ?: null;
                error_log("Entry API: Adding age to entry data");
            } else if (isset($_POST['age'])) {
                error_log("Entry API: Skipping age - column does not exist in entries table");
            }
            if ($entriesCaps['has_subtotal']) {
                $entryData['subtotal'] = (float)($_POST['subtotal'] ?? 0);
            }
            if ($entriesCaps['has_discount_amount']) {
                $entryData['discount_amount'] = (float)($_POST['discount_amount'] ?? 0);
            }
            if ($entriesCaps['has_total_price']) {
                $entryData['total_price'] = (float)($_POST['total_price'] ?? 0);
            }
            if ($entriesCaps['has_notes']) {
                $entryData['notes'] = $_POST['notes'] ?? null;
            }
            
            // Add patient_name if it's being sent from the form
            if (isset($_POST['patient_name']) && !empty($_POST['patient_name'])) {
                // Check if patient_name column exists (it might not be in the capabilities check)
                if (db_column_exists($pdo, 'entries', 'patient_name')) {
                    $entryData['patient_name'] = $_POST['patient_name'];
                    error_log("Entry API: Added patient_name to entry data: " . $_POST['patient_name']);
                } else {
                    error_log("Entry API: patient_name column does not exist in entries table");
                }
            }
            
            // Debug logging
            error_log("Entry API SAVE: Final entry data: " . json_encode($entryData));
            
            if ($isUpdate) {
                // Update existing entry
                $entryData['updated_at'] = date('Y-m-d H:i:s');
                
                $fields = [];
                $params = ['id' => $entryId];
                
                foreach ($entryData as $key => $value) {
                    if ($key !== 'added_by') { // Don't change the original creator
                        $fields[] = "`$key` = :$key";
                        $params[$key] = $value;
                    }
                }
                
                $sql = "UPDATE entries SET " . implode(', ', $fields) . " WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                
                $savedEntryId = $entryId;
            } else {
                // Create new entry
                $fields = array_keys($entryData);
                $placeholders = ':' . implode(', :', $fields);
                
                $sql = "INSERT INTO entries (`" . implode('`, `', $fields) . "`) VALUES ($placeholders)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($entryData);
                
                $savedEntryId = $pdo->lastInsertId();
            }
            
            // Handle tests if provided
            $tests = null;
            if (isset($_POST['tests'])) {
                if (is_array($_POST['tests'])) {
                    $tests = $_POST['tests'];
                } else if (is_string($_POST['tests'])) {
                    // Decode JSON string
                    $tests = json_decode($_POST['tests'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        error_log('Error decoding tests JSON: ' . json_last_error_msg());
                        $tests = null;
                    }
                }
            }
            
            if ($tests && is_array($tests)) {
                error_log('Processing ' . count($tests) . ' tests for entry ID: ' . $savedEntryId);
                error_log('Raw tests data: ' . json_encode($tests));
                
                // Delete existing tests for this entry
                if ($isUpdate) {
                    $stmt = $pdo->prepare("DELETE FROM entry_tests WHERE entry_id = ?");
                    $stmt->execute([$savedEntryId]);
                    error_log("Deleted existing tests for entry ID: $savedEntryId");
                }
                
                // Track inserted test IDs to prevent duplicates
                $insertedTestIds = [];
                
                // Insert new tests
                foreach ($tests as $index => $test) {
                    error_log("Processing test $index: " . json_encode($test));
                    if (!empty($test['test_id'])) {
                        $testId = (int)$test['test_id'];
                        
                        // Check for duplicate test ID in this entry
                        if (in_array($testId, $insertedTestIds)) {
                            error_log("Skipping duplicate test ID $testId for entry $savedEntryId");
                            continue;
                        }
                        
                        // Check if this test already exists for this entry in the database
                        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM entry_tests WHERE entry_id = ? AND test_id = ?");
                        $checkStmt->execute([$savedEntryId, $testId]);
                        $existingCount = $checkStmt->fetchColumn();
                        
                        if ($existingCount > 0) {
                            error_log("Test ID $testId already exists for entry $savedEntryId, skipping");
                            continue;
                        }
                        
                        $testData = [
                            'entry_id' => $savedEntryId,
                            'test_id' => $testId,
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
                        error_log("Executing SQL: $testSql");
                        error_log("With data: " . json_encode($testData));
                        
                        $testStmt = $pdo->prepare($testSql);
                        $result = $testStmt->execute($testData);
                        
                        if ($result) {
                            $insertedTestId = $pdo->lastInsertId();
                            $insertedTestIds[] = $testId; // Track this test ID
                            error_log("Successfully inserted test with ID: $insertedTestId for entry: $savedEntryId");
                        } else {
                            error_log("Failed to insert test $index: " . json_encode($testStmt->errorInfo()));
                        }
                    } else {
                        error_log("Skipping test $index - no test_id provided: " . json_encode($test));
                    }
                }
            } else {
                error_log('No tests provided or tests data is invalid');
            }
            
            // Refresh aggregated data
            refresh_entry_aggregates($pdo, $savedEntryId);
            
            // Also refresh aggregates for entry ID 17 for testing (remove this later)
            if ($savedEntryId != 17) {
                error_log("Also refreshing aggregates for test entry ID 17");
                refresh_entry_aggregates($pdo, 17);
            }
            
            echo json_encode([
                'success' => true,
                'message' => $isUpdate ? 'Entry updated successfully' : 'Entry created successfully',
                'data' => ['id' => $savedEntryId]
            ]);
            exit;
            
        } catch (Exception $e) {
            error_log('Error saving entry: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to save entry',
                'error' => $e->getMessage()
            ]);
            exit;
        }
        
    } else if ($action === 'delete') {
        // Handle entry deletion
        error_log("Entry API: DELETE action called");
        
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Entry ID is required']);
            exit;
        }
        
        try {
            // Check if entry exists and get owner info
            $stmt = $pdo->prepare("SELECT added_by FROM entries WHERE id = ?");
            $stmt->execute([$id]);
            $entry = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$entry) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Entry not found']);
                exit;
            }
            
            // Check permission
            if (!simpleCheckPermission($user_data, 'delete', $entry['added_by'])) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Permission denied to delete entry']);
                exit;
            }
            
            // Delete associated tests first
            $stmt = $pdo->prepare("DELETE FROM entry_tests WHERE entry_id = ?");
            $stmt->execute([$id]);
            
            // Delete the entry
            $stmt = $pdo->prepare("DELETE FROM entries WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Entry deleted successfully'
            ]);
            exit;
            
        } catch (Exception $e) {
            error_log('Error deleting entry: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete entry',
                'error' => $e->getMessage()
            ]);
            exit;
        }
        
    } else if ($action === 'cleanup_duplicates') {
        // Clean up duplicate test entries
        error_log("Cleaning up duplicate test entries");
        
        try {
            // Find duplicate test entries
            $stmt = $pdo->query("
                SELECT entry_id, test_id, COUNT(*) as count, GROUP_CONCAT(id ORDER BY id) as ids
                FROM entry_tests 
                GROUP BY entry_id, test_id 
                HAVING count > 1 
                ORDER BY entry_id, test_id
            ");
            
            $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $cleanedCount = 0;
            $affectedEntries = [];
            
            foreach ($duplicates as $duplicate) {
                $entryId = $duplicate['entry_id'];
                $testId = $duplicate['test_id'];
                $ids = explode(',', $duplicate['ids']);
                
                // Keep the first record, delete the rest
                $keepId = array_shift($ids);
                $deleteIds = $ids;
                
                if (!empty($deleteIds)) {
                    $placeholders = implode(',', array_fill(0, count($deleteIds), '?'));
                    $deleteStmt = $pdo->prepare("DELETE FROM entry_tests WHERE id IN ($placeholders)");
                    $deleteStmt->execute($deleteIds);
                    
                    $cleanedCount += count($deleteIds);
                    $affectedEntries[] = $entryId;
                    
                    error_log("Cleaned duplicates for entry $entryId, test $testId: kept ID $keepId, deleted " . count($deleteIds) . " duplicates");
                }
            }
            
            // Refresh aggregates for affected entries
            $affectedEntries = array_unique($affectedEntries);
            foreach ($affectedEntries as $entryId) {
                refresh_entry_aggregates($pdo, $entryId);
            }
            
            echo json_encode([
                'success' => true,
                'message' => "Cleaned up $cleanedCount duplicate test entries",
                'duplicates_found' => count($duplicates),
                'records_cleaned' => $cleanedCount,
                'affected_entries' => $affectedEntries
            ]);
            exit;
            
        } catch (Exception $e) {
            error_log('Error cleaning duplicates: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to clean duplicates',
                'error' => $e->getMessage()
            ]);
            exit;
        }
        
    } else if ($action === 'debug_get_entry') {
        // Debug action to check what the get action returns for any entry
        $entryId = (int)($_GET['entry_id'] ?? $_GET['id'] ?? 0);
        if (!$entryId) {
            echo json_encode(['success' => false, 'message' => 'Entry ID required']);
            exit;
        }
        
        error_log("Debug: Checking get action for entry $entryId");
        
        // Call the actual get action logic
        $_GET['id'] = $entryId;
        $_GET['action'] = 'get';
        
        // Capture the get action output
        ob_start();
        
        // Simulate the get action (copy the logic)
        $viewerRole = $user_data['role'] ?? 'user';
        $viewerId = (int)($user_data['user_id'] ?? 0);
        
        // Build the main entry query with all related data
        $entriesCaps = get_entries_schema_capabilities($pdo);
        $ownerSelect = '';
        $ownerJoin = '';
        if (!empty($entriesCaps['has_owner_id'])) {
            $ownerSelect = "o.name AS owner_name,";
            $ownerJoin = " LEFT JOIN owners o ON e.owner_id = o.id";
        }
        
        $sql = "SELECT e.*, 
                       p.name AS patient_name, p.uhid, p.age, p.sex AS gender, p.contact AS patient_contact, p.address AS patient_address,
                       d.name AS doctor_name, d.specialization AS doctor_specialization,
                       {$ownerSelect}
                       u.username AS added_by_username, u.full_name AS added_by_full_name
                FROM entries e 
                LEFT JOIN patients p ON e.patient_id = p.id 
                LEFT JOIN doctors d ON e.doctor_id = d.id " .
                $ownerJoin .
                " LEFT JOIN users u ON e.added_by = u.id
                WHERE e.id = ?";
        
        // Add scope restriction for non-master users
        if ($viewerRole !== 'master') {
            $sql .= " AND e.added_by = ?";
            $params = [$entryId, $viewerId];
        } else {
            $params = [$entryId];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$entry) {
            echo json_encode(['success' => false, 'message' => 'Entry not found or access denied']);
            exit;
        }
        
        // Get associated tests for this entry
        $entryTestsCaps = get_entry_tests_schema_capabilities($pdo);
        $tests = [];
        
        if ($entryTestsCaps['table_exists']) {
            $testSql = "SELECT et.id as entry_test_id,
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
                               t.id as test_table_id,
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
                               c.id as category_table_id,
                               c.name AS category_name
                        FROM entry_tests et
                        LEFT JOIN tests t ON et.test_id = t.id
                        LEFT JOIN categories c ON t.category_id = c.id
                        WHERE et.entry_id = ?
                        ORDER BY et.id, t.name";
            
            $testStmt = $pdo->prepare($testSql);
            $testStmt->execute([$entryId]);
            $tests = $testStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format test data with better field handling
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
                unset($test['test_table_id'], $test['category_table_id']);
            }
        }
        
        // Format entry data
        $entry['tests'] = $tests;
        $entry['tests_count'] = count($tests);
        
        ob_end_clean();
        
        echo json_encode([
            'success' => true,
            'entry_id' => $entryId,
            'entry_data' => $entry,
            'tests_data' => $tests,
            'tests_count' => count($tests),
            'debug_info' => [
                'sql_used' => $sql,
                'test_sql_used' => $testSql ?? 'N/A',
                'params_used' => $params
            ]
        ]);
        exit;
        
    } else if ($action === 'debug_entry_17') {
        // Debug action to check entry 17 data
        error_log("Debug: Checking entry 17 data");
        
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
        
        // Check aggregation
        $aggSql = build_entry_tests_aggregation_sql($pdo);
        $stmt = $pdo->prepare("SELECT * FROM (" . $aggSql . ") agg WHERE entry_id = 17");
        $stmt->execute();
        $aggData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Test the new get API logic
        $entryTestsCaps = get_entry_tests_schema_capabilities($pdo);
        $testSql = "SELECT et.id as entry_test_id,
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
                           t.id as test_table_id,
                           t.name AS test_name, 
                           t.category_id,
                           t.unit as test_unit, 
                           t.min, 
                           t.max,
                           c.id as category_table_id,
                           c.name AS category_name
                    FROM entry_tests et
                    LEFT JOIN tests t ON et.test_id = t.id
                    LEFT JOIN categories c ON t.category_id = c.id
                    WHERE et.entry_id = 17
                    ORDER BY et.id, t.name";
        
        $stmt = $pdo->prepare($testSql);
        $stmt->execute();
        $newApiTests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'direct_tests' => $directTests,
            'joined_tests' => $joinedTests,
            'new_api_tests' => $newApiTests,
            'aggregation_data' => $aggData,
            'direct_count' => count($directTests),
            'joined_count' => count($joinedTests),
            'new_api_count' => count($newApiTests)
        ]);
        exit;
        
    } else if ($action === 'refresh_aggregates') {
        // Debug action to manually refresh aggregates for an entry
        $entryId = (int)($_GET['entry_id'] ?? $_POST['entry_id'] ?? 0);
        if (!$entryId) {
            echo json_encode(['success' => false, 'message' => 'Entry ID required']);
            exit;
        }
        
        error_log("Manual refresh aggregates for entry ID: $entryId");
        
        // Get entry_tests data before refresh
        $stmt = $pdo->prepare("SELECT et.*, t.name as test_name FROM entry_tests et LEFT JOIN tests t ON et.test_id = t.id WHERE et.entry_id = ?");
        $stmt->execute([$entryId]);
        $beforeTests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Refresh aggregates
        refresh_entry_aggregates($pdo, $entryId);
        
        // Get updated entry data
        $stmt = $pdo->prepare("SELECT id, tests_count, test_names, test_ids FROM entries WHERE id = ?");
        $stmt->execute([$entryId]);
        $entryData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'message' => 'Aggregates refreshed',
            'entry_data' => $entryData,
            'entry_tests_data' => $beforeTests,
            'entry_tests_count' => count($beforeTests)
        ]);
        exit;
        
    } else if ($action === 'debug_all_entries') {
        // Debug action to check all entries with multiple tests
        error_log("Debug: Checking all entries with multiple tests");
        
        // Get all entries with their aggregated data
        $aggSql = build_entry_tests_aggregation_sql($pdo);
        $stmt = $pdo->prepare("SELECT e.id, e.patient_id, e.tests_count as stored_tests_count, e.test_names as stored_test_names, agg.tests_count as agg_tests_count, agg.test_names as agg_test_names FROM entries e LEFT JOIN (" . $aggSql . ") agg ON agg.entry_id = e.id WHERE agg.tests_count > 1 OR e.tests_count > 1 ORDER BY e.id");
        $stmt->execute();
        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'entries_with_multiple_tests' => $entries,
            'count' => count($entries)
        ]);
        exit;
        
    } else if ($action === 'test_aggregation_sql') {
        // Test the aggregation SQL directly
        error_log("Testing aggregation SQL directly");
        
        try {
            $aggSql = build_entry_tests_aggregation_sql($pdo);
            error_log("Aggregation SQL: " . $aggSql);
            
            $stmt = $pdo->prepare($aggSql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'sql' => $aggSql,
                'results' => $results,
                'count' => count($results)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'sql' => $aggSql ?? 'Failed to build SQL'
            ]);
        }
        exit;
        
    } else if ($action === 'refresh_all_aggregates') {
        // Refresh aggregates for all entries that have tests
        error_log("Refreshing aggregates for all entries with tests");
        
        try {
            // Get all entry IDs that have tests
            $stmt = $pdo->query("SELECT DISTINCT entry_id FROM entry_tests ORDER BY entry_id");
            $entryIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $refreshed = 0;
            foreach ($entryIds as $entryId) {
                refresh_entry_aggregates($pdo, $entryId);
                $refreshed++;
            }
            
            echo json_encode([
                'success' => true,
                'message' => "Refreshed aggregates for $refreshed entries",
                'refreshed_count' => $refreshed,
                'entry_ids' => $entryIds
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
        
    } else {
        error_log("Entry API: Invalid action received: $action");
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action: ' . $action]);
        exit;
    }

} catch (PDOException $e) {
    error_log('Entry API PDO error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error', 'error' => $e->getMessage()]);
} catch (Exception $e) {
    error_log('Entry API error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()]);
} catch (Throwable $e) {
    error_log('Entry API fatal error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Fatal error occurred', 'error' => $e->getMessage()]);
}

// Ensure we always output something
if (!headers_sent()) {
    // If we reach here without any output, something went wrong
    error_log('Entry API: No response generated for action: ' . ($_REQUEST['action'] ?? 'unknown'));
    echo json_encode(['success' => false, 'message' => 'No response generated']);
}





