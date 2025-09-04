<?php
/**
 * Test Category API - Comprehensive CRUD operations for test categories
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

    // Validate category data
    function validateCategoryData($data, $isUpdate = false) {
        $errors = [];
        
        if (!$isUpdate || isset($data['name'])) {
            if (empty(trim($data['name'] ?? ''))) {
                $errors[] = 'Category name is required';
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
        $limit = intval($_GET['limit'] ?? 100);
        $offset = intval($_GET['offset'] ?? 0);
        
        // Check if user wants to see all (master only)
        $viewerRole = $_SESSION['role'] ?? 'user';
        
        $whereClause = '';
        $params = [];
        
        if (isset($_GET['all']) && $_GET['all'] == '1' && $viewerRole === 'master') {
            // Master can see all categories
            if (!empty($search)) {
                $whereClause = 'WHERE c.name LIKE ? OR c.description LIKE ?';
                $searchParam = '%' . $search . '%';
                $params = [$searchParam, $searchParam];
            }
        } else if ($userId) {
            $whereClause = 'WHERE c.added_by = ?';
            $params = [$userId];
            
            if (!empty($search)) {
                $whereClause .= ' AND (c.name LIKE ? OR c.description LIKE ?)';
                $searchParam = '%' . $search . '%';
                $params = array_merge($params, [$searchParam, $searchParam]);
            }
        } else {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }
        
        // Count total records
        $countQuery = 'SELECT COUNT(*) as total FROM categories c ' . $whereClause;
        $stmt = $pdo->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Fetch categories with test count
        $query = 'SELECT c.*, u.username AS added_by_username,
                        (SELECT COUNT(*) FROM tests t WHERE t.category_id = c.id) as test_count
                 FROM categories c 
                 LEFT JOIN users u ON c.added_by = u.id 
                 ' . $whereClause . ' 
                 ORDER BY c.name ASC 
                 LIMIT ? OFFSET ?';
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'success' => true, 
            'data' => $categories, 
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    if ($action === 'get') {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Category ID is required'], 400);
        }
        
        $stmt = $pdo->prepare('SELECT c.*, u.username AS added_by_username,
                                    (SELECT COUNT(*) FROM tests t WHERE t.category_id = c.id) as test_count
                              FROM categories c 
                              LEFT JOIN users u ON c.added_by = u.id 
                              WHERE c.id = ?');
        $stmt->execute([$id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$category) {
            json_response(['success' => false, 'message' => 'Category not found'], 404);
        }
        
        json_response(['success' => true, 'data' => $category]);
    }

    if ($action === 'create' || $action === 'save') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate input
        $errors = validateCategoryData($input);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        // Check for duplicate category name
        $stmt = $pdo->prepare('SELECT id FROM categories WHERE name = ?');
        $stmt->execute([trim($input['name'])]);
        if ($stmt->fetch()) {
            json_response(['success' => false, 'message' => 'Category with this name already exists'], 400);
        }

        // Prepare data for insertion
        $data = [
            'name' => trim($input['name']),
            'description' => trim($input['description'] ?? ''),
            'added_by' => $authenticatedUserId
        ];

        // Insert category
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $query = 'INSERT INTO categories (' . implode(', ', $fields) . ') VALUES (' . $placeholders . ')';
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($data);
        
        $categoryId = $pdo->lastInsertId();
        
        // Fetch the created category
        $stmt = $pdo->prepare('SELECT c.*, u.username AS added_by_username,
                                    (SELECT COUNT(*) FROM tests t WHERE t.category_id = c.id) as test_count
                              FROM categories c 
                              LEFT JOIN users u ON c.added_by = u.id 
                              WHERE c.id = ?');
        $stmt->execute([$categoryId]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Category created successfully', 'data' => $category]);
    }

    if ($action === 'update') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Get category ID from URL or input
        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Category ID is required'], 400);
        }

        // Check if category exists
        $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        $existingCategory = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$existingCategory) {
            json_response(['success' => false, 'message' => 'Category not found'], 404);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate input
        $errors = validateCategoryData($input, true);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        // Check for duplicate category name (excluding current category)
        if (isset($input['name'])) {
            $stmt = $pdo->prepare('SELECT id FROM categories WHERE name = ? AND id != ?');
            $stmt->execute([trim($input['name']), $id]);
            if ($stmt->fetch()) {
                json_response(['success' => false, 'message' => 'Category with this name already exists'], 400);
            }
        }

        // Prepare update data
        $updateData = [];
        $allowedFields = ['name', 'description'];
        
        foreach ($allowedFields as $field) {
            if (isset($input[$field])) {
                $updateData[$field] = trim($input[$field]);
            }
        }

        if (empty($updateData)) {
            json_response(['success' => false, 'message' => 'No valid fields to update'], 400);
        }

        // Add updated_at timestamp
        $updateData['updated_at'] = date('Y-m-d H:i:s');

        // Build update query
        $setParts = [];
        foreach ($updateData as $field => $value) {
            $setParts[] = "$field = :$field";
        }
        
        $query = 'UPDATE categories SET ' . implode(', ', $setParts) . ' WHERE id = :id';
        $updateData['id'] = $id;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($updateData);

        // Fetch updated category
        $stmt = $pdo->prepare('SELECT c.*, u.username AS added_by_username,
                                    (SELECT COUNT(*) FROM tests t WHERE t.category_id = c.id) as test_count
                              FROM categories c 
                              LEFT JOIN users u ON c.added_by = u.id 
                              WHERE c.id = ?');
        $stmt->execute([$id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Category updated successfully', 'data' => $category]);
    }

    if ($action === 'delete') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Category ID is required'], 400);
        }

        // Check if category exists
        $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$category) {
            json_response(['success' => false, 'message' => 'Category not found'], 404);
        }

        // Check if category has associated tests
        $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM tests WHERE category_id = ?');
        $stmt->execute([$id]);
        $testCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        if ($testCount > 0) {
            json_response(['success' => false, 'message' => 'Cannot delete category with associated tests'], 400);
        }

        // Delete category
        $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->execute([$id]);

        json_response(['success' => true, 'message' => 'Category deleted successfully']);
    }

    // Invalid action
    json_response(['success' => false, 'message' => 'Invalid action'], 400);

} catch (Exception $e) {
    error_log('Category API Error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
}
?>
// patho_api/test_category.php - public API for test categories (JSON)
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';

$action = $_REQUEST['action'] ?? 'list';

try {
    if ($action === 'list') {
        $stmt = $pdo->query('SELECT c.id, c.name, c.description, c.added_by, u.username as added_by_username FROM categories c LEFT JOIN users u ON c.added_by = u.id ORDER BY c.id DESC');
        $rows = $stmt->fetchAll();
        json_response(['success' => true, 'data' => $rows]);
    }

    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT c.*, u.username as added_by_username FROM categories c LEFT JOIN users u ON c.added_by = u.id WHERE c.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        if (!$row) json_response(['success'=>false,'message'=>'Category not found'],404);
        json_response(['success' => true, 'data' => $row]);
    }

    if ($action === 'save') {
        // allow master and admin to create/update categories
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);

        $id = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($name === '') json_response(['success'=>false,'message'=>'Name is required'],400);

        if ($id) {
            $stmt = $pdo->prepare('UPDATE categories SET name=?, description=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$name, $description, $id]);
            json_response(['success' => true, 'message' => 'Category updated']);
        } else {
            $added_by = $_SESSION['user_id'] ?? null;
            if (empty($added_by) && !empty($PATHO_API_DEFAULT_USER_ID)) {
                $added_by = (int)$PATHO_API_DEFAULT_USER_ID;
            }
            $data = ['name'=>$name, 'description'=>$description, 'added_by'=>$added_by];
            $unique = ['name'=>$name];
            $res = upsert_or_skip($pdo, 'categories', $unique, $data);
            json_response(['success' => true, 'message' => 'Category '.$res['action'],'id'=>$res['id']]);
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success' => true, 'message' => 'Category deleted']);
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);
} catch (Throwable $e) {
    json_response(['success'=>false,'message'=>'Server error: '.$e->getMessage()],500);
}
