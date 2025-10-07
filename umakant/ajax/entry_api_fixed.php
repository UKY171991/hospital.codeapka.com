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
session_start();

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
        'has_updated_at' => db_column_exists($pdo, 'entries', 'updated_at')
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
        // ... (existing code)
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

        $sql = "SELECT * FROM entries";
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
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
    } else if ($action === 'get' && isset($_GET['id'])) {
        // ... (existing code)
    } else if ($action === 'save') {
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
            $entryId = $input['id'] ?? null;

            if ($entryId) {
                // Update existing entry
                try {
                    $pdo->beginTransaction();

                    // 1. Update the main entry
                    $entryData = [
                        'patient_id' => $input['patient_id'],
                        'doctor_id' => $input['doctor_id'] ?? null,
                        'owner_id' => $input['owner_id'] ?? null,
                        'entry_date' => $input['entry_date'] ?? date('Y-m-d'),
                        'status' => $input['status'] ?? 'pending',
                        'notes' => $input['notes'] ?? null,
                        'id' => $entryId
                    ];
                    $sql = "UPDATE entries SET patient_id=:patient_id, doctor_id=:doctor_id, owner_id=:owner_id, entry_date=:entry_date, status=:status, notes=:notes, updated_at=NOW() WHERE id=:id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($entryData);

                    // 2. Delete existing tests
                    $stmt = $pdo->prepare('DELETE FROM entry_tests WHERE entry_id = ?');
                    $stmt->execute([$entryId]);

                    // 3. Insert new tests
                    $entryTestCaps = get_entry_tests_schema_capabilities($pdo);
                    foreach ($tests as $test) {
                        $testData = [
                            'entry_id' => $entryId,
                            'test_id' => $test['test_id'],
                            'result_value' => $test['result_value'] ?? null,
                            'unit' => $test['unit'] ?? null,
                            'remarks' => $test['remarks'] ?? null,
                            'status' => 'pending'
                        ];
                        if ($entryTestCaps['has_price']) {
                            $testData['price'] = $test['price'] ?? 0;
                        }
                        if ($entryTestCaps['has_discount_amount']) {
                            $testData['discount_amount'] = $test['discount_amount'] ?? 0;
                        }
                        if ($entryTestCaps['has_total_price']) {
                            $testData['total_price'] = max(($test['price'] ?? 0) - ($test['discount_amount'] ?? 0), 0);
                        }

                        $testFields = implode(', ', array_keys($testData));
                        $testPlaceholders = ':' . implode(', :', array_keys($testData));
                        $testSql = "INSERT INTO entry_tests ($testFields) VALUES ($testPlaceholders)";
                        $testStmt = $pdo->prepare($testSql);
                        $testStmt->execute($testData);
                    }

                    // 4. Refresh aggregated totals
                    refresh_entry_aggregates($pdo, $entryId);

                    $pdo->commit();
                    json_response(['success' => true, 'message' => 'Entry updated successfully']);
                } catch (Exception $e) {
                    $pdo->rollBack();
                    json_response(['success' => false, 'message' => 'Failed to update entry', 'error' => $e->getMessage()], 500);
                }
            } else {
                // Create new entry
                if (is_array($tests) && count($tests) > 0) {
                    $entryTestCaps = get_entry_tests_schema_capabilities($pdo);
                    if (!$entryTestCaps['table_exists']) {
                        json_response(['success' => false, 'message' => 'Multiple tests are not supported on this installation (missing entry_tests table).'], 400);
                    }

                    try {
                        $pdo->beginTransaction();

                        $entryCaps = get_entries_schema_capabilities($pdo);

                        // Create the main entry with schema-awareness
                        $entryData = [
                            'patient_id' => $input['patient_id'],
                            'doctor_id' => $input['doctor_id'] ?? null,
                            'owner_id' => $input['owner_id'] ?? null,
                            'entry_date' => $input['entry_date'] ?? date('Y-m-d'),
                            'status' => $input['status'] ?? 'pending',
                            'added_by' => $input['added_by']
                        ];

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

                        // Calculate totals based on test data and schema
                        $totalPrice = 0;
                        $totalDiscount = 0;
                        foreach ($tests as $test) {
                            $totalPrice += floatval($test['price'] ?? 0);
                            $totalDiscount += floatval($test['discount_amount'] ?? 0);
                        }

                        if ($entryCaps['has_price']) {
                            $entryData['price'] = $totalPrice;
                        }
                        if ($entryCaps['has_subtotal']) {
                            $entryData['subtotal'] = $totalPrice;
                        }
                        if ($entryCaps['has_discount_amount']) {
                            $entryData['discount_amount'] = $totalDiscount;
                        }
                        if ($entryCaps['has_total_price']) {
                            $entryData['total_price'] = max($totalPrice - $totalDiscount, 0);
                        }

                        // Set primary test to first test for backward compatibility
                        if (!empty($tests) && $entryCaps['has_test_id']) {
                            $entryData['test_id'] = $tests[0]['test_id'];
                        }

                        // Insert entry
                        $entryFields = implode(', ', array_keys($entryData));
                        $entryPlaceholders = ':' . implode(', :', array_keys($entryData));
                        $sql = "INSERT INTO entries ($entryFields) VALUES ($entryPlaceholders)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($entryData);
                        $entryId = $pdo->lastInsertId();

                        // Insert individual tests
                        foreach ($tests as $test) {
                            $testData = [
                                'entry_id' => $entryId,
                                'test_id' => $test['test_id'],
                                'result_value' => $test['result_value'] ?? null,
                                'unit' => $test['unit'] ?? null,
                                'remarks' => $test['remarks'] ?? null,
                                'status' => 'pending'
                            ];
                            if ($entryTestCaps['has_price']) {
                                $testData['price'] = $test['price'] ?? 0;
                            }
                            if ($entryTestCaps['has_discount_amount']) {
                                $testData['discount_amount'] = $test['discount_amount'] ?? 0;
                            }
                            if ($entryTestCaps['has_total_price']) {
                                $testData['total_price'] = max(($test['price'] ?? 0) - ($test['discount_amount'] ?? 0), 0);
                            }

                            $testFields = implode(', ', array_keys($testData));
                            $testPlaceholders = ':' . implode(', :', array_keys($testData));
                            $testSql = "INSERT INTO entry_tests ($testFields) VALUES ($testPlaceholders)";
                            $testStmt = $pdo->prepare($testSql);
                            $testStmt->execute($testData);
                        }

                        // Refresh aggregated totals
                        refresh_entry_aggregates($pdo, $entryId);

                        $pdo->commit();
                        json_response(['success' => true, 'message' => 'Entry with multiple tests created successfully', 'data' => ['id' => $entryId, 'tests_count' => count($tests)]]);
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        json_response(['success' => false, 'message' => 'Failed to save entry with multiple tests', 'error' => ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTraceAsString()]], 500);
                    }
                } else {
                    json_response(['success' => false, 'message' => 'No valid tests provided'], 400);
                }
            }
        } else {
            // Handle single test entry (existing logic)
            // ... (existing proxy logic)
        }
    } else if ($action === 'delete' && isset($_POST['id'])) {
        // ... (existing code)
    } else if ($action === 'export') {
        // ... (existing code)
    } else {
        json_response(['success'=>false,'message'=>'Invalid action'],400);
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