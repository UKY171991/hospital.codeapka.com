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
        
        // Return the formatted data
        json_response(['success' => true, 'data' => $rows]);
        
    } else if ($action === 'report_list') {
        if (!simpleCheckPermission($user_data, 'list')) {
            json_response(['success' => false, 'message' => 'Permission denied to list reports'], 403);
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

            // Add viewer scope if not admin
            if ($user_data['role'] !== 'master') {
                $query .= " AND e.added_by = ?";
                $params[] = $user_data['user_id'];
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

} catch (PDOException $e) {
    error_log('Entry API PDO error: ' . $e->getMessage());
    http_response_code(500);
    json_response(['success' => false, 'message' => 'Database error', 'error' => $e->getMessage()]);
} catch (Exception $e) {
    error_log('Entry API error: ' . $e->getMessage());
    http_response_code(500);
    json_response(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()]);
}





