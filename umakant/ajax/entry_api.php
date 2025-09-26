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
session_start();

header('Content-Type: application/json; charset=utf-8');

$action = $_REQUEST['action'] ?? 'list';

try {
    if ($action === 'stats') {
        // Get statistics for dashboard
        $stats = [];
        $viewerRole = $_SESSION['role'] ?? 'user';
        $viewerId = (int)($_SESSION['user_id'] ?? 0);
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
        // Updated to match new schema with comprehensive data
        $viewerRole = $_SESSION['role'] ?? 'user';
        $viewerId = (int)($_SESSION['user_id'] ?? 0);
        $scopeWhere = '';
        $params = [];
        if ($viewerRole !== 'master') {
            $scopeWhere = ' WHERE e.added_by = ?';
            $params = [$viewerId];
        }

        $sql = "SELECT e.*, 
                   p.name AS patient_name, p.uhid, p.age, p.sex AS gender,
                   d.name AS doctor_name,
                   u.username AS added_by_username,
                   COUNT(et.id) as tests_count,
                   GROUP_CONCAT(DISTINCT t.name ORDER BY t.name SEPARATOR ', ') as test_names,
                   GROUP_CONCAT(DISTINCT et.test_id ORDER BY et.test_id) as test_ids,
                   SUM(et.price) as total_price,
                   SUM(et.discount_amount) as total_discount
            FROM entries e 
            LEFT JOIN patients p ON e.patient_id = p.id 
            LEFT JOIN doctors d ON e.doctor_id = d.id 
            LEFT JOIN users u ON e.added_by = u.id
            LEFT JOIN entry_tests et ON e.id = et.entry_id
            LEFT JOIN tests t ON et.test_id = t.id" .
            $scopeWhere .
            " GROUP BY e.id
              ORDER BY COALESCE(e.entry_date, e.created_at) DESC, e.id DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format data for frontend compatibility
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
            $row['total_price'] = (float)($row['total_price'] ?? 0);
            $row['total_discount'] = (float)($row['total_discount'] ?? 0);
            $row['final_amount'] = $row['total_price'] - $row['total_discount'];
            
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
        // Return comprehensive entry data
        $viewerRole = $_SESSION['role'] ?? 'user';
        $viewerId = (int)($_SESSION['user_id'] ?? 0);
        $scopeWhere = '';
        $params = [$_GET['id']];
        if ($viewerRole !== 'master') {
            $scopeWhere = ' AND e.added_by = ?';
            $params[] = $viewerId;
        }

        $sql = "SELECT e.*, 
                   p.name AS patient_name, p.uhid, p.age, p.sex AS gender,
                   d.name AS doctor_name,
                   u.username AS added_by_username,
                   COUNT(et.id) as tests_count,
                   GROUP_CONCAT(DISTINCT t.name ORDER BY t.name SEPARATOR ', ') as test_names,
                   GROUP_CONCAT(DISTINCT et.test_id ORDER BY et.test_id) as test_ids,
                   SUM(et.price) as total_price,
                   SUM(et.discount_amount) as total_discount
            FROM entries e 
            LEFT JOIN patients p ON e.patient_id = p.id 
            LEFT JOIN doctors d ON e.doctor_id = d.id 
            LEFT JOIN users u ON e.added_by = u.id
            LEFT JOIN entry_tests et ON e.id = et.entry_id
            LEFT JOIN tests t ON et.test_id = t.id
            WHERE e.id = ?" . $scopeWhere . "
            GROUP BY e.id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            http_response_code(404);
            json_response(['success' => false, 'message' => 'Entry not found']);
            return;
        }
        
        // Format data for frontend compatibility
        if (empty($row['entry_date'])) {
            $row['entry_date'] = $row['created_at'];
        }
        
        // Format test information
        $row['tests_count'] = (int)($row['tests_count'] ?? 0);
        $row['test_names'] = $row['test_names'] ?? '';
        $row['test_ids'] = $row['test_ids'] ?? '';
        
        // Format pricing
        $row['total_price'] = (float)($row['total_price'] ?? 0);
        $row['total_discount'] = (float)($row['total_discount'] ?? 0);
        $row['final_amount'] = $row['total_price'] - $row['total_discount'];
        
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
        
        json_response(['success' => true, 'data' => $row]);
    }

    if ($action === 'save') {
        // Handle multiple tests per entry
        $input = [];
        $content_type = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($content_type, 'application/json') !== false) {
            $input = json_decode(file_get_contents('php://input'), true) ?: [];
        } else {
            $input = $_POST;
        }

        // Add user/session info if needed
        if (!isset($input['added_by']) && isset($_SESSION['user_id'])) {
            $input['added_by'] = $_SESSION['user_id'];
        }

        // Check if this is a multiple tests entry
        if (isset($input['tests']) && !empty($input['tests'])) {
            $tests = json_decode($input['tests'], true);
            
            if (is_array($tests) && count($tests) > 0) {
                try {
                    $pdo->beginTransaction();
                    
                    // Create the main entry
                    $entryData = [
                        'patient_id' => $input['patient_id'],
                        'doctor_id' => $input['doctor_id'] ?? null,
                        'entry_date' => $input['entry_date'] ?? date('Y-m-d'),
                        'status' => $input['status'] ?? 'pending',
                        'added_by' => $input['added_by'],
                        'remarks' => $input['notes'] ?? null,
                        'grouped' => 1, // This is a grouped entry
                        'tests_count' => count($tests)
                    ];
                    
                    // Calculate total price
                    $totalPrice = 0;
                    $totalDiscount = 0;
                    foreach ($tests as $test) {
                        $price = floatval($test['price'] ?? 0);
                        $discount = floatval($test['discount_amount'] ?? 0);
                        $totalPrice += $price;
                        $totalDiscount += $discount;
                    }
                    
                    $entryData['price'] = $totalPrice;
                    $entryData['discount_amount'] = $totalDiscount;
                    $entryData['total_price'] = $totalPrice - $totalDiscount;
                    
                    // Set primary test to first test for backward compatibility
                    if (!empty($tests)) {
                        $entryData['test_id'] = $tests[0]['test_id']; // Use first test as primary
                    }
                    
                    // Insert entry
                    $entryFields = implode(', ', array_keys($entryData));
                    $entryPlaceholders = ':' . implode(', :', array_keys($entryData));
                    $sql = "INSERT INTO entries ($entryFields) VALUES ($entryPlaceholders)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($entryData);
                    $entryId = $pdo->lastInsertId();
                    
                    // Insert individual tests
                    $testIds = [];
                    $testNames = [];
                    foreach ($tests as $test) {
                        $testData = [
                            'entry_id' => $entryId,
                            'test_id' => $test['test_id'],
                            'result_value' => $test['result_value'] ?? null,
                            'unit' => $test['unit'] ?? null,
                            'remarks' => $test['remarks'] ?? null,
                            'status' => 'pending',
                            'price' => $test['price'] ?? 0,
                            'discount_amount' => $test['discount_amount'] ?? 0,
                            'total_price' => ($test['price'] ?? 0) - ($test['discount_amount'] ?? 0)
                        ];
                        
                        $testFields = implode(', ', array_keys($testData));
                        $testPlaceholders = ':' . implode(', :', array_keys($testData));
                        $testSql = "INSERT INTO entry_tests ($testFields) VALUES ($testPlaceholders)";
                        $testStmt = $pdo->prepare($testSql);
                        $testStmt->execute($testData);
                        
                        $testIds[] = $test['test_id'];
                        $testNames[] = $test['test_name'];
                    }
                    
                    // Update entry with aggregated test data
                    $updateSql = "UPDATE entries SET 
                        test_ids = :test_ids,
                        test_names = :test_names,
                        updated_at = NOW()
                        WHERE id = :entry_id";
                    $updateStmt = $pdo->prepare($updateSql);
                    $updateStmt->execute([
                        'test_ids' => implode(',', $testIds),
                        'test_names' => implode(', ', $testNames),
                        'entry_id' => $entryId
                    ]);
                    
                    $pdo->commit();
                    
                    json_response([
                        'success' => true,
                        'message' => 'Entry with multiple tests created successfully',
                        'data' => ['id' => $entryId, 'tests_count' => count($tests)]
                    ]);
                    
                } catch (Exception $e) {
                    $pdo->rollBack();
                    error_log("Multiple tests entry save error: " . $e->getMessage());
                    json_response(['success' => false, 'message' => 'Failed to save entry with multiple tests'], 500);
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
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($input));
            // Forward session cookie if needed
            if (isset($_COOKIE[session_name()])) {
                curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . $_COOKIE[session_name()]);
            }
            // Forward content-type
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

            $apiResponse = curl_exec($ch);
            $curlErr = curl_error($ch);
            curl_close($ch);

            if ($apiResponse === false) {
                json_response(['success' => false, 'message' => 'API request failed', 'error' => $curlErr], 500);
            }

            // Output the API response directly
            header('Content-Type: application/json');
            echo $apiResponse;
            exit;
        }
    } else if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) {
            json_response(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        $stmt = $pdo->prepare('DELETE FROM entries WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success' => true, 'message' => 'Entry deleted']);
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
