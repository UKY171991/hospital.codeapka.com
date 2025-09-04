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
        // Support DataTables server-side processing
        $draw = $_POST['draw'] ?? 1;
        $start = $_POST['start'] ?? 0;
        $length = $_POST['length'] ?? 25;
        $search = $_POST['search']['value'] ?? '';
        
        // Base query
        $baseQuery = "FROM tests t LEFT JOIN categories tc ON t.category_id = tc.id LEFT JOIN users u ON t.added_by = u.id";
        $whereClause = "";
        $params = [];
        
        // Add search conditions
        if (!empty($search)) {
            $whereClause = " WHERE (t.name LIKE ? OR tc.name LIKE ? OR t.description LIKE ?)";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm];
        }
        
        // Get total records
        $totalStmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery . $whereClause);
        $totalStmt->execute($params);
        $totalRecords = $totalStmt->fetchColumn();
        
        // Get filtered records
        $orderBy = " ORDER BY t.id DESC";
        $limit = " LIMIT $start, $length";
        
        $dataQuery = "SELECT t.id, t.name as test_name, tc.name as category, t.price,
                      CASE 
                        WHEN t.min_male IS NOT NULL AND t.max_male IS NOT NULL AND t.min_female IS NOT NULL AND t.max_female IS NOT NULL 
                        THEN 'Both' 
                        WHEN t.min_male IS NOT NULL AND t.max_male IS NOT NULL 
                        THEN 'Male' 
                        WHEN t.min_female IS NOT NULL AND t.max_female IS NOT NULL 
                        THEN 'Female' 
                        ELSE 'General' 
                      END as gender,
                      CONCAT(
                        CASE 
                          WHEN t.min_male IS NOT NULL AND t.max_male IS NOT NULL 
                          THEN CONCAT('M: ', t.min_male, '-', t.max_male) 
                          ELSE '' 
                        END,
                        CASE 
                          WHEN t.min_female IS NOT NULL AND t.max_female IS NOT NULL AND t.min_male IS NOT NULL 
                          THEN CONCAT(' | F: ', t.min_female, '-', t.max_female)
                          WHEN t.min_female IS NOT NULL AND t.max_female IS NOT NULL 
                          THEN CONCAT('F: ', t.min_female, '-', t.max_female)
                          ELSE 'N/A' 
                        END
                      ) as range,
                      t.unit, u.username as added_by " . 
                      $baseQuery . $whereClause . $orderBy . $limit;
        
        $dataStmt = $pdo->prepare($dataQuery);
        $dataStmt->execute($params);
        $data = $dataStmt->fetchAll();
        
        // Return DataTables format
        json_response([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'success' => true,
            'data' => $data
        ]);
    }

    if ($action === 'get' && isset($_GET['id'])) {
        // return full record for edit/view with joined names
        $stmt = $pdo->prepare("SELECT t.*, tc.name as category_name, u.username as added_by_username
            FROM tests t
            LEFT JOIN categories tc ON t.category_id = tc.id
            LEFT JOIN users u ON t.added_by = u.id
            WHERE t.id = ?");
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        json_response(['success'=>true,'data'=>$row]);
    }

    if ($action === 'save') {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $id = $_POST['id'] ?? '';
        $category_id = $_POST['category_id'] ?? null;
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = $_POST['price'] ?? 0;
    $unit = trim($_POST['unit'] ?? '');
        $default_result = trim($_POST['default_result'] ?? '');
        $reference_range = trim($_POST['reference_range'] ?? '');
    $min = $_POST['min'] ?? null;
    $max = $_POST['max'] ?? null;
    $min_male = $_POST['min_male'] ?? null;
    $max_male = $_POST['max_male'] ?? null;
    $min_female = $_POST['min_female'] ?? null;
    $max_female = $_POST['max_female'] ?? null;
        $sub_heading = $_POST['sub_heading'] ?? 0;
        $test_code = trim($_POST['test_code'] ?? '');
        $method = trim($_POST['method'] ?? '');
        $print_new_page = $_POST['print_new_page'] ?? 0;
        $shortcut = trim($_POST['shortcut'] ?? '');

        // Server-side validation: ensure max >= min for each range when values provided
        $ranges = [
            ['min'=>$min, 'max'=>$max, 'label'=>'General'],
            ['min'=>$min_male, 'max'=>$max_male, 'label'=>'Male'],
            ['min'=>$min_female, 'max'=>$max_female, 'label'=>'Female']
        ];
        foreach($ranges as $r){
            if($r['min'] !== null && $r['max'] !== null && $r['min'] !== '' && $r['max'] !== ''){
                if(!is_numeric($r['min']) || !is_numeric($r['max'])) json_response(['success'=>false,'message'=>$r['label'].' range must be numeric'],400);
                if(floatval($r['max']) < floatval($r['min'])) json_response(['success'=>false,'message'=>'Max Value ('.$r['label'].') cannot be less than Min Value ('.$r['label'].')'],400);
            }
        }

        if ($id) {
            $stmt = $pdo->prepare('UPDATE tests SET category_id=?, name=?, description=?, price=?, unit=?, default_result=?, reference_range=?, min=?, max=?, min_male=?, max_male=?, min_female=?, max_female=?, sub_heading=?, test_code=?, method=?, print_new_page=?, shortcut=? WHERE id=?');
            $stmt->execute([$category_id, $name, $description, $price, $unit, $default_result, $reference_range, $min, $max, $min_male, $max_male, $min_female, $max_female, $sub_heading, $test_code, $method, $print_new_page, $shortcut, $id]);
            json_response(['success'=>true,'message'=>'Test updated']);
        } else {
            $added_by = $_SESSION['user_id'] ?? null;
            $stmt = $pdo->prepare('INSERT INTO tests (category_id, name, description, price, unit, default_result, reference_range, min, max, min_male, max_male, min_female, max_female, sub_heading, test_code, method, print_new_page, shortcut, added_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $stmt->execute([$category_id, $name, $description, $price, $unit, $default_result, $reference_range, $min, $max, $min_male, $max_male, $min_female, $max_female, $sub_heading, $test_code, $method, $print_new_page, $shortcut, $added_by]);
            
            // Get the newly inserted record with joined data
            $newId = $pdo->lastInsertId();
            $stmt = $pdo->prepare("SELECT t.id,
                tc.name as category_name,
                t.category_id,
                t.name,
                t.description,
                t.price,
                t.unit,
                t.min,
                t.max,
                t.min_male,
                t.max_male,
                t.min_female,
                t.max_female,
                t.sub_heading,
                t.print_new_page,
                t.added_by,
                u.username as added_by_username
                FROM tests t
                LEFT JOIN categories tc ON t.category_id = tc.id
                LEFT JOIN users u ON t.added_by = u.id
                WHERE t.id = ?");
            $stmt->execute([$newId]);
            $newRecord = $stmt->fetch();
            
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
        $totalStmt = $pdo->query('SELECT COUNT(*) FROM tests');
        $total = $totalStmt->fetchColumn();
        
        $activeStmt = $pdo->query('SELECT COUNT(*) FROM tests WHERE status = "active" OR status IS NULL');
        $active = $activeStmt->fetchColumn();
        
        $categoriesStmt = $pdo->query('SELECT COUNT(DISTINCT category_id) FROM tests WHERE category_id IS NOT NULL');
        $categories = $categoriesStmt->fetchColumn();
        
        // Test entries count
        $entriesStmt = $pdo->query('SELECT COUNT(*) FROM entries');
        $entries = $entriesStmt->fetchColumn();
        
        json_response([
            'success' => true,
            'data' => [
                'total' => $total,
                'active' => $active,
                'categories' => $categories,
                'entries' => $entries
            ]
        ]);
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);
} catch (Throwable $e) {
    json_response(['success'=>false,'message'=>$e->getMessage()],500);
}
