<?php
/**
 * Test API - Comprehensive CRUD operations for tests
 * Supports: CREATE, READ, UPDATE, DELETE operations
 * Authentication: Session-based or API token
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
require_once __DIR__ . '/../inc/api_config.php';

// Get action from request
$action = $_REQUEST['action'] ?? $_SERVER['REQUEST_METHOD'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Map HTTP methods to actions
switch($requestMethod) {
    case 'GET':
        $action = isset($_GET['id']) ? 'get' : 'list';
        break;
    case 'POST':
        $action = $_REQUEST['action'] ?? 'create';
        break;
    case 'PUT':
        $action = 'update';
        break;
    case 'DELETE':
        $action = 'delete';
        break;
}

try {
    // Authenticate user
    function authenticateUser($pdo) {
        global $_SESSION;
        
        // Check session first
        if (isset($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        }
        
        // Check Authorization header
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $stmt = $pdo->prepare('SELECT id FROM users WHERE api_token = ? AND is_active = 1');
            $stmt->execute([$token]);
            $user = $stmt->fetch();
            if ($user) return $user['id'];
        }
        
        return null;
    }

    // Validate test data
    function validateTestData($data, $isUpdate = false) {
        $errors = [];
        
        if (!$isUpdate || isset($data['name'])) {
            if (empty(trim($data['name'] ?? ''))) {
                $errors[] = 'Test name is required';
            }
        }
        
        if (!$isUpdate || isset($data['description'])) {
            if (empty(trim($data['description'] ?? ''))) {
                $errors[] = 'Test description is required';
            }
        }
        
        if (isset($data['price'])) {
            $price = floatval($data['price']);
            if ($price < 0) {
                $errors[] = 'Price cannot be negative';
            }
        }
        
        if (isset($data['min']) && isset($data['max'])) {
            $min = floatval($data['min']);
            $max = floatval($data['max']);
            if ($min > $max) {
                $errors[] = 'Minimum value cannot be greater than maximum value';
            }
        }
        
        if (isset($data['min_male']) && isset($data['max_male'])) {
            $minMale = floatval($data['min_male']);
            $maxMale = floatval($data['max_male']);
            if ($minMale > $maxMale) {
                $errors[] = 'Minimum male value cannot be greater than maximum male value';
            }
        }
        
        if (isset($data['min_female']) && isset($data['max_female'])) {
            $minFemale = floatval($data['min_female']);
            $maxFemale = floatval($data['max_female']);
            if ($minFemale > $maxFemale) {
                $errors[] = 'Minimum female value cannot be greater than maximum female value';
            }
        }
        
        return $errors;
    }

    if ($action === 'list') {
        $userId = $_GET['user_id'] ?? null;
        $authenticatedUserId = authenticateUser($pdo);
        
        if (!$userId && $authenticatedUserId) {
            $userId = $authenticatedUserId;
        }
        
        // Search functionality
        $search = $_GET['search'] ?? '';
        $categoryId = $_GET['category_id'] ?? null;
        $limit = intval($_GET['limit'] ?? 50);
        $offset = intval($_GET['offset'] ?? 0);
        
        // Check if user wants to see all (master only)
        $viewerRole = $_SESSION['role'] ?? 'user';
        
        $whereClause = '';
        $params = [];
        
        if (isset($_GET['all']) && $_GET['all'] == '1' && $viewerRole === 'master') {
            // Master can see all tests
            $whereConditions = [];
            
            if (!empty($search)) {
                $whereConditions[] = '(t.name LIKE ? OR t.description LIKE ? OR t.test_code LIKE ?)';
                $searchParam = '%' . $search . '%';
                $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
            }
            
            if ($categoryId) {
                $whereConditions[] = 't.category_id = ?';
                $params[] = $categoryId;
            }
            
            if (!empty($whereConditions)) {
                $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
            }
        } else if ($userId) {
            $whereConditions = ['t.added_by = ?'];
            $params = [$userId];
            
            if (!empty($search)) {
                $whereConditions[] = '(t.name LIKE ? OR t.description LIKE ? OR t.test_code LIKE ?)';
                $searchParam = '%' . $search . '%';
                $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
            }
            
            if ($categoryId) {
                $whereConditions[] = 't.category_id = ?';
                $params[] = $categoryId;
            }
            
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        } else {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }
        
        // Count total records
        $countQuery = 'SELECT COUNT(*) as total FROM tests t ' . $whereClause;
        $stmt = $pdo->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Fetch tests with category information
        $query = 'SELECT t.*, c.name as category_name, u.username AS added_by_username
                 FROM tests t 
                 LEFT JOIN categories c ON t.category_id = c.id
                 LEFT JOIN users u ON t.added_by = u.id 
                 ' . $whereClause . ' 
                 ORDER BY t.name ASC 
                 LIMIT ? OFFSET ?';
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'success' => true, 
            'data' => $tests, 
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    if ($action === 'get') {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Test ID is required'], 400);
        }
        
        $stmt = $pdo->prepare('SELECT t.*, c.name as category_name, u.username AS added_by_username
                              FROM tests t 
                              LEFT JOIN categories c ON t.category_id = c.id
                              LEFT JOIN users u ON t.added_by = u.id 
                              WHERE t.id = ?');
        $stmt->execute([$id]);
        $test = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$test) {
            json_response(['success' => false, 'message' => 'Test not found'], 404);
        }
        
        json_response(['success' => true, 'data' => $test]);
    }

    if ($action === 'create' || $action === 'save') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate input
        $errors = validateTestData($input);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        // Validate category exists if provided
        if (isset($input['category_id']) && !empty($input['category_id'])) {
            $stmt = $pdo->prepare('SELECT id FROM categories WHERE id = ?');
            $stmt->execute([$input['category_id']]);
            if (!$stmt->fetch()) {
                json_response(['success' => false, 'message' => 'Invalid category ID'], 400);
            }
        }

        // Prepare data for insertion
        $data = [
            'name' => trim($input['name']),
            'description' => trim($input['description']),
            'category_id' => isset($input['category_id']) && !empty($input['category_id']) ? intval($input['category_id']) : null,
            'price' => floatval($input['price'] ?? 0),
            'unit' => trim($input['unit'] ?? ''),
            'default_result' => trim($input['default_result'] ?? ''),
            'reference_range' => trim($input['reference_range'] ?? ''),
            'min' => isset($input['min']) && $input['min'] !== '' ? floatval($input['min']) : null,
            'max' => isset($input['max']) && $input['max'] !== '' ? floatval($input['max']) : null,
            'sub_heading' => isset($input['sub_heading']) ? intval($input['sub_heading']) : 0,
            'test_code' => trim($input['test_code'] ?? ''),
            'method' => trim($input['method'] ?? ''),
            'print_new_page' => isset($input['print_new_page']) ? intval($input['print_new_page']) : 0,
            'shortcut' => trim($input['shortcut'] ?? ''),
            'min_male' => isset($input['min_male']) && $input['min_male'] !== '' ? floatval($input['min_male']) : null,
            'max_male' => isset($input['max_male']) && $input['max_male'] !== '' ? floatval($input['max_male']) : null,
            'min_female' => isset($input['min_female']) && $input['min_female'] !== '' ? floatval($input['min_female']) : null,
            'max_female' => isset($input['max_female']) && $input['max_female'] !== '' ? floatval($input['max_female']) : null,
            'specimen' => trim($input['specimen'] ?? ''),
            'added_by' => $authenticatedUserId
        ];

        // Insert test
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $query = 'INSERT INTO tests (' . implode(', ', $fields) . ') VALUES (' . $placeholders . ')';
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($data);
        
        $testId = $pdo->lastInsertId();
        
        // Fetch the created test
        $stmt = $pdo->prepare('SELECT t.*, c.name as category_name, u.username AS added_by_username
                              FROM tests t 
                              LEFT JOIN categories c ON t.category_id = c.id
                              LEFT JOIN users u ON t.added_by = u.id 
                              WHERE t.id = ?');
        $stmt->execute([$testId]);
        $test = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Test created successfully', 'data' => $test]);
    }

    if ($action === 'update') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Get test ID from URL or input
        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Test ID is required'], 400);
        }

        // Check if test exists
        $stmt = $pdo->prepare('SELECT * FROM tests WHERE id = ?');
        $stmt->execute([$id]);
        $existingTest = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$existingTest) {
            json_response(['success' => false, 'message' => 'Test not found'], 404);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate input
        $errors = validateTestData($input, true);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        // Validate category exists if provided
        if (isset($input['category_id']) && !empty($input['category_id'])) {
            $stmt = $pdo->prepare('SELECT id FROM categories WHERE id = ?');
            $stmt->execute([$input['category_id']]);
            if (!$stmt->fetch()) {
                json_response(['success' => false, 'message' => 'Invalid category ID'], 400);
            }
        }

        // Prepare update data
        $updateData = [];
        $allowedFields = [
            'name', 'description', 'category_id', 'price', 'unit', 'default_result', 
            'reference_range', 'min', 'max', 'sub_heading', 'test_code', 'method', 
            'print_new_page', 'shortcut', 'min_male', 'max_male', 'min_female', 
            'max_female', 'specimen'
        ];
        
        foreach ($allowedFields as $field) {
            if (isset($input[$field])) {
                if (in_array($field, ['price', 'min', 'max', 'min_male', 'max_male', 'min_female', 'max_female'])) {
                    $updateData[$field] = $input[$field] !== '' ? floatval($input[$field]) : null;
                } elseif (in_array($field, ['category_id', 'sub_heading', 'print_new_page'])) {
                    $updateData[$field] = $input[$field] !== '' ? intval($input[$field]) : null;
                } else {
                    $updateData[$field] = trim($input[$field]);
                }
            }
        }

        if (empty($updateData)) {
            json_response(['success' => false, 'message' => 'No valid fields to update'], 400);
        }

        // Build update query
        $setParts = [];
        foreach ($updateData as $field => $value) {
            $setParts[] = "$field = :$field";
        }
        
        $query = 'UPDATE tests SET ' . implode(', ', $setParts) . ' WHERE id = :id';
        $updateData['id'] = $id;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($updateData);

        // Fetch updated test
        $stmt = $pdo->prepare('SELECT t.*, c.name as category_name, u.username AS added_by_username
                              FROM tests t 
                              LEFT JOIN categories c ON t.category_id = c.id
                              LEFT JOIN users u ON t.added_by = u.id 
                              WHERE t.id = ?');
        $stmt->execute([$id]);
        $test = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Test updated successfully', 'data' => $test]);
    }

    if ($action === 'delete') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Test ID is required'], 400);
        }

        // Check if test exists
        $stmt = $pdo->prepare('SELECT * FROM tests WHERE id = ?');
        $stmt->execute([$id]);
        $test = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$test) {
            json_response(['success' => false, 'message' => 'Test not found'], 404);
        }

        // Check if test has associated entries
        $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM entries WHERE test_id = ?');
        $stmt->execute([$id]);
        $entryCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        if ($entryCount > 0) {
            json_response(['success' => false, 'message' => 'Cannot delete test with associated entries'], 400);
        }

        // Delete test
        $stmt = $pdo->prepare('DELETE FROM tests WHERE id = ?');
        $stmt->execute([$id]);

        json_response(['success' => true, 'message' => 'Test deleted successfully']);
    }

    // Invalid action
    json_response(['success' => false, 'message' => 'Invalid action'], 400);

} catch (Exception $e) {
    error_log('Test API Error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
}
?>
// patho_api/test.php - public API for tests (JSON)
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';

$action = $_REQUEST['action'] ?? 'list';

try {
    if ($action === 'list') {
        // return all relevant columns so UI can render full test table
        $stmt = $pdo->query("SELECT t.id,
            tc.name as category_name,
            t.category_id,
            t.name,
            t.description,
            t.price,
            t.unit,
            t.default_result,
            t.reference_range as normal_range,
            t.min,
            t.max,
            t.min_male,
            t.max_male,
            t.min_female,
            t.max_female,
            t.sub_heading,
            t.test_code,
            t.method,
            t.print_new_page,
            t.shortcut,
            t.added_by,
            u.username as added_by_username
            FROM tests t
            LEFT JOIN categories tc ON t.category_id = tc.id
            LEFT JOIN users u ON t.added_by = u.id
            ORDER BY t.id DESC");
        $rows = $stmt->fetchAll();
        json_response(['success'=>true,'data'=>$rows]);
    }

    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT t.*, tc.name as category_name, u.username as added_by_username
            FROM tests t
            LEFT JOIN categories tc ON t.category_id = tc.id
            LEFT JOIN users u ON t.added_by = u.id
            WHERE t.id = ?");
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        if (!$row) json_response(['success'=>false,'message'=>'Test not found'],404);
        json_response(['success'=>true,'data'=>$row]);
    }

    if ($action === 'save') {
        // require admin or master
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

        // Server-side validation for ranges
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
            $stmt = $pdo->prepare('UPDATE tests SET category_id=?, name=?, description=?, price=?, unit=?, default_result=?, reference_range=?, min=?, max=?, min_male=?, max_male=?, min_female=?, max_female=?, sub_heading=?, test_code=?, method=?, print_new_page=?, shortcut=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$category_id, $name, $description, $price, $unit, $default_result, $reference_range, $min, $max, $min_male, $max_male, $min_female, $max_female, $sub_heading, $test_code, $method, $print_new_page, $shortcut, $id]);
            json_response(['success'=>true,'message'=>'Test updated']);
        } else {
                $added_by = $_SESSION['user_id'] ?? null;
                if (empty($added_by) && !empty($PATHO_API_DEFAULT_USER_ID)) {
                    $added_by = (int)$PATHO_API_DEFAULT_USER_ID;
                }
                $data = ['category_id'=>$category_id, 'name'=>$name, 'description'=>$description, 'price'=>$price, 'unit'=>$unit, 'default_result'=>$default_result, 'reference_range'=>$reference_range, 'min'=>$min, 'max'=>$max, 'min_male'=>$min_male, 'max_male'=>$max_male, 'min_female'=>$min_female, 'max_female'=>$max_female, 'sub_heading'=>$sub_heading, 'test_code'=>$test_code, 'method'=>$method, 'print_new_page'=>$print_new_page, 'shortcut'=>$shortcut, 'added_by'=>$added_by];
                if ($test_code !== '') $unique = ['test_code'=>$test_code]; else $unique = ['name'=>$name, 'category_id'=>$category_id];
                $res = upsert_or_skip($pdo, 'tests', $unique, $data);

                // return the newly created/updated record with joined fields
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
                $stmt->execute([$res['id']]);
                $newRecord = $stmt->fetch();
                json_response(['success'=>true,'message'=>'Test '.$res['action'], 'data'=>$newRecord]);
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $stmt = $pdo->prepare('DELETE FROM tests WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success'=>true,'message'=>'Test deleted']);
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);
} catch (Throwable $e) {
    json_response(['success'=>false,'message'=>$e->getMessage()],500);
}
