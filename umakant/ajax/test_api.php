<?php
// ajax/test_api.php - CRUD for tests
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

try {
    $action = $_REQUEST['action'] ?? 'list';

if ($action === 'list') {
        // Check which categories table exists - Default to 'categories' based on schema
        $categories_table = 'categories';
        try{
            $stmt = $pdo->query("SHOW TABLES LIKE 'categories'");
            if($stmt->fetch()){
                $categories_table = 'categories';
            } else {
                // Fallback to test_categories if categories doesn't exist
                $stmt2 = $pdo->query("SHOW TABLES LIKE 'test_categories'");
                if($stmt2->fetch()) {
                    $categories_table = 'test_categories';
                }
            }
        }catch(Throwable $e){
            $categories_table = 'categories';
        }
        
        // Support DataTables server-side processing - validate and sanitize parameters
        $draw = (int)($_REQUEST['draw'] ?? 1);
        $start = max(0, (int)($_REQUEST['start'] ?? 0));
        $length = max(1, min(100, (int)($_REQUEST['length'] ?? 25))); // Limit between 1-100 records
        $search = trim($_REQUEST['search']['value'] ?? '');
        
        // Base query for counting
        $countBaseQuery = "FROM tests t LEFT JOIN {$categories_table} tc ON t.category_id = tc.id LEFT JOIN main_test_categories mc ON t.main_category_id = mc.id LEFT JOIN users u ON t.added_by = u.id";
        
        // Add search conditions - search across all relevant text fields
        $whereClause = "";
        $params = [];
        if (!empty($search)) {
            $whereClause = " WHERE (t.name LIKE ? OR tc.name LIKE ? OR mc.name LIKE ? OR t.description LIKE ? OR t.test_code LIKE ? OR t.method LIKE ? OR t.specimen LIKE ? OR t.unit LIKE ? OR u.username LIKE ?)";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm];
        }
        
        // Get total records with error handling
        try {
            // Use simple count without JOIN for better performance when no filters
            if (empty($whereClause)) {
                $totalStmt = $pdo->query("SELECT COUNT(*) FROM tests");
            } else {
                $totalStmt = $pdo->prepare("SELECT COUNT(*) " . $countBaseQuery);
            }
            $totalRecords = $totalStmt->fetchColumn();
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error counting total records: ' . $e->getMessage()], 500);
        }

        // Get filtered records with error handling
        try {
            if (empty($whereClause)) {
                $filteredRecords = $totalRecords; // No filtering, so filtered count = total count
            } else {
                $filteredStmt = $pdo->prepare("SELECT COUNT(*) " . $countBaseQuery . $whereClause);
                $filteredStmt->execute($params);
                $filteredRecords = $filteredStmt->fetchColumn();
            }
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error counting filtered records: ' . $e->getMessage()], 500);
        }
        
        // Get filtered records
        $orderBy = " ORDER BY t.id DESC";
        $limit = " LIMIT $start, $length";
        
        // Build the complete query with ALL tests table fields
        $dataQuery = "SELECT 
            t.*,
            COALESCE(mc.name, '') AS main_category_name,
            COALESCE(tc.name, '') AS category_name,
            COALESCE(u.username, '') AS added_by_username
            FROM tests t 
            LEFT JOIN {$categories_table} tc ON t.category_id = tc.id 
            LEFT JOIN main_test_categories mc ON t.main_category_id = mc.id
            LEFT JOIN users u ON t.added_by = u.id" . $whereClause . $orderBy . $limit;
        
        // Get data records with error handling
        try {
            if (empty($params)) {
                $dataStmt = $pdo->query($dataQuery);
                $data = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $dataStmt = $pdo->prepare($dataQuery);
                $dataStmt->execute($params);
                $data = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error fetching data records: ' . $e->getMessage(), 'query' => $dataQuery], 500);
        }
        
        // Add debug info when no data or data issues detected
        $debug_info = [];
        if (empty($data)) {
            $debug_info['note'] = 'No data returned from query';
            $debug_info['query'] = $dataQuery;
            $debug_info['table'] = $categories_table;
        } else {
            // Check first record for field completeness
            $first = $data[0] ?? null;
            if ($first) {
                $debug_info['field_count'] = count($first);
                $debug_info['fields_returned'] = array_keys($first);
                $debug_info['sample_data'] = $first;
                
                // Check if all expected fields are present
                $expected_fields = ['id', 'name', 'category_id', 'main_category_id', 'price', 'unit', 'specimen', 
                                  'default_result', 'reference_range', 'min', 'max', 'description', 'min_male', 
                                  'max_male', 'min_female', 'max_female', 'min_child', 'max_child', 
                                  'sub_heading', 'test_code', 'method', 'print_new_page', 'shortcut', 'added_by', 
                                  'created_at', 'updated_at', 'category_name', 'main_category_name', 'added_by_username'];
                $missing_fields = array_diff($expected_fields, array_keys($first));
                if (!empty($missing_fields)) {
                    $debug_info['missing_fields'] = $missing_fields;
                }
            }
        }
        
        // Return DataTables format
        $response = [
            'draw' => intval($draw),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($filteredRecords),
            'success' => true,
            'data' => $data,
            'categories_table_used' => $categories_table
        ];
        
        // Add debug info if present
        if (!empty($debug_info)) {
            $response['debug'] = $debug_info;
        }
        
        json_response($response);
    }

    if ($action === 'get' && isset($_GET['id'])) {
        // Check which categories table exists - Default to 'categories' based on schema
        $categories_table = 'categories';
        try{
            $stmt = $pdo->query("SHOW TABLES LIKE 'categories'");
            if($stmt->fetch()){
                $categories_table = 'categories';
            } else {
                // Fallback to test_categories if categories doesn't exist
                $stmt2 = $pdo->query("SHOW TABLES LIKE 'test_categories'");
                if($stmt2->fetch()) {
                    $categories_table = 'test_categories';
                }
            }
        }catch(Throwable $e){
            $categories_table = 'categories';
        }
        
        // return full record for edit/view with ALL fields and joined names
        $stmt = $pdo->prepare("SELECT t.id, t.name, t.category_id, t.main_category_id, t.price, t.unit, t.specimen, 
                              t.default_result, t.reference_range, t.min, t.max, t.description, t.min_male, t.max_male, 
                              t.min_female, t.max_female, t.min_child, t.max_child, t.sub_heading, 
                              t.test_code, t.method, t.print_new_page, t.shortcut, t.added_by, t.created_at, t.updated_at,
                              tc.name as category_name, mc.name as main_category_name, u.username as added_by_username
            FROM tests t
            LEFT JOIN {$categories_table} tc ON t.category_id = tc.id
            LEFT JOIN main_test_categories mc ON t.main_category_id = mc.id
            LEFT JOIN users u ON t.added_by = u.id
            WHERE t.id = ?");
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        json_response(['success'=>true,'data'=>$row]);
    }

    if ($action === 'save') {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $id = $_POST['id'] ?? '';
        $category_id = $_POST['category_id'] ?? null;
        // Determine which categories table exists so we can validate category_id - Default to 'categories'
        $categories_table = 'categories';
        try{
            $stmt = $pdo->query("SHOW TABLES LIKE 'categories'");
            if($stmt->fetch()){
                $categories_table = 'categories';
            } else {
                // Fallback to test_categories if categories doesn't exist
                $stmt2 = $pdo->query("SHOW TABLES LIKE 'test_categories'");
                if($stmt2->fetch()) {
                    $categories_table = 'test_categories';
                }
            }
        }catch(Throwable $e){
            $categories_table = 'categories';
        }
        $name = trim($_POST['name'] ?? '');
        $main_category_id = $_POST['main_category_id'] ?? null;
        $description = trim($_POST['description'] ?? '');
        
        // Debug: Log received POST data
        error_log('Test API Save - Received POST data: ' . json_encode([
            'name' => $name,
            'main_category_id' => $main_category_id,
            'category_id' => $category_id,
            'raw_main_category_id' => $_POST['main_category_id'] ?? 'NOT_SET'
        ]));
        $price = $_POST['price'] ?? 0;
        $unit = trim($_POST['unit'] ?? '');
        $specimen = trim($_POST['specimen'] ?? '');
        $default_result = trim($_POST['default_result'] ?? '');
        $reference_range = trim($_POST['reference_range'] ?? '');
        $min = $_POST['min'] ?? null;
        $max = $_POST['max'] ?? null;
        $min_male = $_POST['min_male'] ?? null;
        $max_male = $_POST['max_male'] ?? null;
        $min_female = $_POST['min_female'] ?? null;
        $max_female = $_POST['max_female'] ?? null;
        $min_child = $_POST['min_child'] ?? null;
        $max_child = $_POST['max_child'] ?? null;
        // child_unit removed - using main unit field for all ranges
        $sub_heading = $_POST['sub_heading'] ?? 0;
        $test_code = trim($_POST['test_code'] ?? '');
        $method = trim($_POST['method'] ?? '');
        $print_new_page = $_POST['print_new_page'] ?? 0;
        $shortcut = trim($_POST['shortcut'] ?? '');

        // Server-side validation: ensure max >= min for each range when values provided
        $ranges = [
            ['min'=>$min, 'max'=>$max, 'label'=>'General'],
            ['min'=>$min_male, 'max'=>$max_male, 'label'=>'Male'],
            ['min'=>$min_female, 'max'=>$max_female, 'label'=>'Female'],
            ['min'=>$min_child, 'max'=>$max_child, 'label'=>'Child']
        ];
        foreach($ranges as $r){
            if($r['min'] !== null && $r['max'] !== null && $r['min'] !== '' && $r['max'] !== ''){
                if(!is_numeric($r['min']) || !is_numeric($r['max'])) json_response(['success'=>false,'message'=>$r['label'].' range must be numeric'],400);
                if(floatval($r['max']) < floatval($r['min'])) json_response(['success'=>false,'message'=>'Max Value ('.$r['label'].') cannot be less than Min Value ('.$r['label'].')'],400);
            }
        }

        // Validate main_category_id: it's required (NOT NULL in database)
        if ($main_category_id === '' || $main_category_id === null || !is_numeric($main_category_id)) {
            json_response(['success' => false, 'message' => 'Main category is required'], 400);
        }
        
        $main_category_id = intval($main_category_id);
        if ($main_category_id <= 0) {
            json_response(['success' => false, 'message' => 'Invalid main category selected'], 400);
        }
        
        // Verify main category exists
        try {
            $chk = $pdo->prepare("SELECT id FROM main_test_categories WHERE id = ?");
            $chk->execute([$main_category_id]);
            if (!$chk->fetch()) {
                json_response(['success' => false, 'message' => 'Selected main category does not exist'], 400);
            }
        } catch (Throwable $e) {
            json_response(['success' => false, 'message' => 'Error validating main category: ' . $e->getMessage()], 500);
        }

        // Normalize/validate category_id: convert empty or invalid to NULL and ensure it exists
        if ($category_id === '' || $category_id === null) {
            $category_id = null;
        } else {
            // allow numeric ids only
            if (!is_numeric($category_id)) {
                $category_id = null;
            } else {
                $category_id = intval($category_id);
                if ($category_id <= 0) {
                    $category_id = null;
                } else {
                    // verify existence in categories table; if not found, treat as null
                    try{
                        $chk = $pdo->prepare("SELECT id FROM {$categories_table} WHERE id = ?");
                        $chk->execute([$category_id]);
                        if(!$chk->fetch()){
                            // category doesn't exist — normalize to NULL to avoid FK errors
                            $category_id = null;
                        }
                    }catch(Throwable $e){
                        // if check fails, default to null
                        $category_id = null;
                    }
                }
            }
        }

        if ($id) {
            try {
                $stmt = $pdo->prepare('UPDATE tests SET category_id=?, main_category_id=?, name=?, description=?, price=?, unit=?, specimen=?, default_result=?, reference_range=?, min=?, max=?, min_male=?, max_male=?, min_female=?, max_female=?, min_child=?, max_child=?, sub_heading=?, test_code=?, method=?, print_new_page=?, shortcut=?, updated_at=NOW() WHERE id=?');
                // Bind explicitly to ensure NULLs are preserved
                $b = 1;
                if ($category_id === null) {
                    $stmt->bindValue($b++, null, PDO::PARAM_NULL);
                } else {
                    $stmt->bindValue($b++, $category_id, PDO::PARAM_INT);
                }
                if ($main_category_id === null) {
                    $stmt->bindValue($b++, null, PDO::PARAM_NULL);
                } else {
                    $stmt->bindValue($b++, $main_category_id, PDO::PARAM_INT);
                }
                $stmt->bindValue($b++, $name, PDO::PARAM_STR);
                $stmt->bindValue($b++, $description, PDO::PARAM_STR);
                $stmt->bindValue($b++, $price, PDO::PARAM_STR);
                $stmt->bindValue($b++, $unit, PDO::PARAM_STR);
                $stmt->bindValue($b++, $specimen, PDO::PARAM_STR);
                $stmt->bindValue($b++, $default_result, PDO::PARAM_STR);
                $stmt->bindValue($b++, $reference_range, PDO::PARAM_STR);
                $stmt->bindValue($b++, $min, PDO::PARAM_STR);
                $stmt->bindValue($b++, $max, PDO::PARAM_STR);
                $stmt->bindValue($b++, $min_male, PDO::PARAM_STR);
                $stmt->bindValue($b++, $max_male, PDO::PARAM_STR);
                $stmt->bindValue($b++, $min_female, PDO::PARAM_STR);
                $stmt->bindValue($b++, $max_female, PDO::PARAM_STR);
                $stmt->bindValue($b++, $min_child, PDO::PARAM_STR);
                $stmt->bindValue($b++, $max_child, PDO::PARAM_STR);
                // child_unit removed
                $stmt->bindValue($b++, $sub_heading, PDO::PARAM_INT);
                $stmt->bindValue($b++, $test_code, PDO::PARAM_STR);
                $stmt->bindValue($b++, $method, PDO::PARAM_STR);
                $stmt->bindValue($b++, $print_new_page, PDO::PARAM_INT);
                $stmt->bindValue($b++, $shortcut, PDO::PARAM_STR);
                $stmt->bindValue($b++, $id, PDO::PARAM_INT);
                $stmt->execute();
                json_response(['success'=>true,'message'=>'Test updated']);
            } catch (Throwable $e) {
                json_response(['success'=>false,'message'=>'Failed to update test: '.$e->getMessage()],500);
            }
        } else {
            $added_by = $_SESSION['user_id'] ?? null;
            try {
                $stmt = $pdo->prepare('INSERT INTO tests (category_id, main_category_id, name, description, price, unit, specimen, default_result, reference_range, min, max, min_male, max_male, min_female, max_female, min_child, max_child, sub_heading, test_code, method, print_new_page, shortcut, added_by, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())');
                // Bind parameters explicitly to ensure NULL values are sent as NULL (not empty string)
                $bindIndex = 1;
                if ($category_id === null) {
                    $stmt->bindValue($bindIndex++, null, PDO::PARAM_NULL);
                } else {
                    $stmt->bindValue($bindIndex++, $category_id, PDO::PARAM_INT);
                }
                if ($main_category_id === null) {
                    $stmt->bindValue($bindIndex++, null, PDO::PARAM_NULL);
                } else {
                    $stmt->bindValue($bindIndex++, $main_category_id, PDO::PARAM_INT);
                }
                $stmt->bindValue($bindIndex++, $name, PDO::PARAM_STR);
                $stmt->bindValue($bindIndex++, $description, PDO::PARAM_STR);
                $stmt->bindValue($bindIndex++, $price, PDO::PARAM_STR);
                $stmt->bindValue($bindIndex++, $unit, PDO::PARAM_STR);
                $stmt->bindValue($bindIndex++, $specimen, PDO::PARAM_STR);
                $stmt->bindValue($bindIndex++, $default_result, PDO::PARAM_STR);
                $stmt->bindValue($bindIndex++, $reference_range, PDO::PARAM_STR);
                $stmt->bindValue($bindIndex++, $min, PDO::PARAM_STR);
                $stmt->bindValue($bindIndex++, $max, PDO::PARAM_STR);
                $stmt->bindValue($bindIndex++, $min_male, PDO::PARAM_STR);
                $stmt->bindValue($bindIndex++, $max_male, PDO::PARAM_STR);
                $stmt->bindValue($bindIndex++, $min_female, PDO::PARAM_STR);
                $stmt->bindValue($bindIndex++, $max_female, PDO::PARAM_STR);
                $stmt->bindValue($bindIndex++, $min_child, PDO::PARAM_STR);
                $stmt->bindValue($bindIndex++, $max_child, PDO::PARAM_STR);
                // child_unit removed
                $stmt->bindValue($bindIndex++, $sub_heading, PDO::PARAM_INT);
                $stmt->bindValue($bindIndex++, $test_code, PDO::PARAM_STR);
                $stmt->bindValue($bindIndex++, $method, PDO::PARAM_STR);
                $stmt->bindValue($bindIndex++, $print_new_page, PDO::PARAM_INT);
                $stmt->bindValue($bindIndex++, $shortcut, PDO::PARAM_STR);
                if ($added_by === null) {
                    $stmt->bindValue($bindIndex++, null, PDO::PARAM_NULL);
                } else {
                    $stmt->bindValue($bindIndex++, $added_by, PDO::PARAM_INT);
                }
                $stmt->execute();

                // Get the newly inserted record with joined data
                $newId = $pdo->lastInsertId();
            } catch (Throwable $e) {
                // Return clearer validation message if FK fails or other DB issue
                json_response(['success'=>false,'message'=>'Failed to save test: '.$e->getMessage()],500);
            }
            
            // Check which categories table exists
            $categories_table = 'categories';
            try{
                $stmt = $pdo->query("SHOW TABLES LIKE 'categories'");
                if(!$stmt->fetch()){
                    $stmt2 = $pdo->query("SHOW TABLES LIKE 'test_categories'");
                    if($stmt2->fetch()) $categories_table = 'test_categories';
                }
            }catch(Throwable $e){
                $categories_table = 'categories';
            }
            
            $stmt = $pdo->prepare("SELECT t.id, t.name, t.category_id, t.main_category_id, t.price, t.unit, t.specimen, 
                              t.default_result, t.reference_range, t.min, t.max, t.description, t.min_male, t.max_male, 
                              t.min_female, t.max_female, t.min_child, t.max_child, t.sub_heading, 
                              t.test_code, t.method, t.print_new_page, t.shortcut, t.added_by, t.created_at, t.updated_at,
                              tc.name as category_name, mc.name as main_category_name, u.username as added_by_username
                FROM tests t
                LEFT JOIN {$categories_table} tc ON t.category_id = tc.id
                LEFT JOIN main_test_categories mc ON tc.main_category_id = mc.id
                LEFT JOIN users u ON t.added_by = u.id
                WHERE t.id = ?");
            $stmt->execute([$newId]);
            $newRecord = $stmt->fetch(PDO::FETCH_ASSOC);
            
            json_response(['success'=>true,'message'=>'Test created', 'data'=>$newRecord]);
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $stmt = $pdo->prepare('DELETE FROM tests WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success'=>true,'message'=>'Test deleted']);
    }

if ($action === 'stats') {
        // Get test statistics
        try {
            $totalStmt = $pdo->query('SELECT COUNT(*) FROM tests');
            $total = $totalStmt->fetchColumn();
        } catch (Exception $e) {
            $total = 0;
        }
        
        try {
            $activeStmt = $pdo->query('SELECT COUNT(*) FROM tests WHERE status = "active" OR status IS NULL');
            $active = $activeStmt->fetchColumn();
        } catch (Exception $e) {
            $active = 0;
        }
        
        try {
            $categoriesStmt = $pdo->query('SELECT COUNT(DISTINCT category_id) FROM tests WHERE category_id IS NOT NULL');
            $categories = $categoriesStmt->fetchColumn();
        } catch (Exception $e) {
            $categories = 0;
        }
        
        // Test entries count - check if entries table exists
        try {
            $entriesStmt = $pdo->query('SELECT COUNT(*) FROM entries');
            $entries = $entriesStmt->fetchColumn();
        } catch (Exception $e) {
            // Table might not exist, try alternative table names
            try {
                $entriesStmt = $pdo->query('SELECT COUNT(*) FROM test_entries');
                $entries = $entriesStmt->fetchColumn();
            } catch (Exception $e2) {
                $entries = 0;
            }
        }
        
        json_response([
            'success' => true,
            'data' => [
                'total' => $total,
                'active' => $active,
                'categories' => $categories,
                'entries' => $entries
            ]
        ]);
    } else if ($action === 'simple_list') {
        // Simple list action for dropdowns
        try {
            $stmt = $pdo->query("SELECT t.id, t.name, t.category_id, t.main_category_id, t.price, t.unit, t.specimen, 
                                       t.default_result, t.reference_range, t.min, t.max, t.description, t.min_male, t.max_male, 
                                       t.min_female, t.max_female, t.min_child, t.max_child, t.sub_heading, 
                                       t.test_code, t.method, t.print_new_page, t.shortcut, t.added_by, t.created_at, t.updated_at,
                                       tc.name as category_name, mc.name as main_category_name, u.username as added_by_username
                                FROM tests t 
                                LEFT JOIN categories tc ON t.category_id = tc.id 
                                LEFT JOIN main_test_categories mc ON t.main_category_id = mc.id
                                LEFT JOIN users u ON t.added_by = u.id
                                ORDER BY t.name ASC");
            $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            json_response([
                'success' => true,
                'data' => $tests
            ]);
        } catch (Exception $e) {
            json_response(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);
} catch (Throwable $e) {
    json_response(['success'=>false,'message'=>$e->getMessage()],500);
}
