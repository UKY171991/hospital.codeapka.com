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
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
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
                $testSql = "SELECT et.*, 
                                   t.name AS test_name, 
                                   t.unit, 
                                   t.min, 
                                   t.max,
                                   t.min_male,
                                   t.max_male,
                                   t.min_female,
                                   t.max_female,
                                   t.reference_range,
                                   c.name AS category_name
                            FROM entry_tests et
                            LEFT JOIN tests t ON et.test_id = t.id
                            LEFT JOIN categories c ON t.category_id = c.id
                            WHERE et.entry_id = ?
                            ORDER BY t.name";
                
                $testStmt = $pdo->prepare($testSql);
                $testStmt->execute([$id]);
                $tests = $testStmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Format test data
                foreach ($tests as &$test) {
                    $test['price'] = (float)($test['price'] ?? 0);
                    $test['discount_amount'] = (float)($test['discount_amount'] ?? 0);
                    $test['total_price'] = $test['price'] - $test['discount_amount'];
                    $test['result_value'] = $test['result_value'] ?? '';
                    $test['status'] = $test['status'] ?? 'pending';
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
            
            // Prepare entry data
            $entryData = [
                'patient_id' => $patientId,
                'doctor_id' => (int)($_POST['doctor_id'] ?? 0) ?: null,
                'entry_date' => $entryDate,
                'status' => $status,
                'priority' => $priority,
                'referral_source' => $_POST['referral_source'] ?? null,
                'patient_contact' => $_POST['patient_contact'] ?? null,
                'patient_address' => $_POST['patient_address'] ?? null,
                'gender' => $_POST['gender'] ?? null,
                'age' => (int)($_POST['age'] ?? 0) ?: null,
                'subtotal' => (float)($_POST['subtotal'] ?? 0),
                'discount_amount' => (float)($_POST['discount_amount'] ?? 0),
                'total_price' => (float)($_POST['total_price'] ?? 0),
                'notes' => $_POST['notes'] ?? null,
                'added_by' => $user_data['user_id']
            ];
            
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
            if (isset($_POST['tests']) && is_array($_POST['tests'])) {
                // Delete existing tests for this entry
                if ($isUpdate) {
                    $stmt = $pdo->prepare("DELETE FROM entry_tests WHERE entry_id = ?");
                    $stmt->execute([$savedEntryId]);
                }
                
                // Insert new tests
                foreach ($_POST['tests'] as $test) {
                    if (!empty($test['test_id'])) {
                        $testData = [
                            'entry_id' => $savedEntryId,
                            'test_id' => (int)$test['test_id'],
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
                        $testStmt->execute($testData);
                    }
                }
            }
            
            // Refresh aggregated data
            refresh_entry_aggregates($pdo, $savedEntryId);
            
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





