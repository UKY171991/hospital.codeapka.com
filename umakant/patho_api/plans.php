<?php
/**
 * Plans API - Comprehensive CRUD operations for subscription plans
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

    // Check if user has permission to manage plans
    function checkPlanPermission($pdo, $userId) {
        $stmt = $pdo->prepare('SELECT role FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user && in_array($user['role'], ['master', 'admin']);
    }

    // Validate plan data
    function validatePlanData($data, $isUpdate = false) {
        $errors = [];
        
        if (!$isUpdate || isset($data['name'])) {
            if (empty(trim($data['name'] ?? ''))) {
                $errors[] = 'Plan name is required';
            }
        }
        
        if (isset($data['price'])) {
            $price = floatval($data['price']);
            if ($price < 0) {
                $errors[] = 'Price cannot be negative';
            }
        }
        
        if (isset($data['time_type'])) {
            $validTypes = ['monthly', 'yearly'];
            if (!in_array($data['time_type'], $validTypes)) {
                $errors[] = 'Time type must be monthly or yearly';
            }
        }
        
        if (isset($data['start_date']) && isset($data['end_date'])) {
            $startDate = $data['start_date'];
            $endDate = $data['end_date'];
            
            if (!empty($startDate) && !empty($endDate)) {
                $start = new DateTime($startDate);
                $end = new DateTime($endDate);
                
                if ($start > $end) {
                    $errors[] = 'Start date cannot be after end date';
                }
            }
        }
        
        return $errors;
    }

    if ($action === 'list') {
        // Plans can be viewed publicly or by authenticated users
        $search = $_GET['search'] ?? '';
        $timeType = $_GET['time_type'] ?? '';
        $limit = intval($_GET['limit'] ?? 50);
        $offset = intval($_GET['offset'] ?? 0);
        
        $whereConditions = [];
        $params = [];
        
        if (!empty($search)) {
            $whereConditions[] = '(name LIKE ? OR description LIKE ?)';
            $searchParam = '%' . $search . '%';
            $params = array_merge($params, [$searchParam, $searchParam]);
        }
        
        if (!empty($timeType)) {
            $whereConditions[] = 'time_type = ?';
            $params[] = $timeType;
        }
        
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        
        // Count total records
        $countQuery = 'SELECT COUNT(*) as total FROM plans ' . $whereClause;
        $stmt = $pdo->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Fetch plans
        $query = 'SELECT p.*, u.username AS added_by_username 
                 FROM plans p 
                 LEFT JOIN users u ON p.added_by = u.id 
                 ' . $whereClause . ' 
                 ORDER BY p.price ASC 
                 LIMIT ? OFFSET ?';
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'success' => true, 
            'data' => $plans, 
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    if ($action === 'get') {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Plan ID is required'], 400);
        }
        
        $stmt = $pdo->prepare('SELECT p.*, u.username AS added_by_username 
                              FROM plans p 
                              LEFT JOIN users u ON p.added_by = u.id 
                              WHERE p.id = ?');
        $stmt->execute([$id]);
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$plan) {
            json_response(['success' => false, 'message' => 'Plan not found'], 404);
        }
        
        json_response(['success' => true, 'data' => $plan]);
    }

    if ($action === 'create') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Check permission
        if (!checkPlanPermission($pdo, $authenticatedUserId)) {
            json_response(['success' => false, 'message' => 'Insufficient permissions'], 403);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate input
        $errors = validatePlanData($input);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        // Prepare data for insertion
        $data = [
            'name' => trim($input['name']),
            'description' => trim($input['description'] ?? ''),
            'price' => floatval($input['price'] ?? 0),
            'upi' => trim($input['upi'] ?? ''),
            'time_type' => $input['time_type'] ?? 'monthly',
            'start_date' => isset($input['start_date']) && !empty($input['start_date']) ? $input['start_date'] : null,
            'end_date' => isset($input['end_date']) && !empty($input['end_date']) ? $input['end_date'] : null,
            'qr_code' => trim($input['qr_code'] ?? ''),
            'added_by' => $authenticatedUserId
        ];

        // Insert plan
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $query = 'INSERT INTO plans (' . implode(', ', $fields) . ') VALUES (' . $placeholders . ')';
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($data);
        
        $planId = $pdo->lastInsertId();
        
        // Fetch the created plan
        $stmt = $pdo->prepare('SELECT p.*, u.username AS added_by_username 
                              FROM plans p 
                              LEFT JOIN users u ON p.added_by = u.id 
                              WHERE p.id = ?');
        $stmt->execute([$planId]);
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Plan created successfully', 'data' => $plan]);
    }

    if ($action === 'update') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Check permission
        if (!checkPlanPermission($pdo, $authenticatedUserId)) {
            json_response(['success' => false, 'message' => 'Insufficient permissions'], 403);
        }

        // Get plan ID from URL or input
        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Plan ID is required'], 400);
        }

        // Check if plan exists
        $stmt = $pdo->prepare('SELECT * FROM plans WHERE id = ?');
        $stmt->execute([$id]);
        $existingPlan = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$existingPlan) {
            json_response(['success' => false, 'message' => 'Plan not found'], 404);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate input
        $errors = validatePlanData($input, true);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        // Prepare update data
        $updateData = [];
        $allowedFields = ['name', 'description', 'price', 'upi', 'time_type', 'start_date', 'end_date', 'qr_code'];
        
        foreach ($allowedFields as $field) {
            if (isset($input[$field])) {
                if ($field === 'price') {
                    $updateData[$field] = floatval($input[$field]);
                } elseif (in_array($field, ['start_date', 'end_date'])) {
                    $updateData[$field] = !empty($input[$field]) ? $input[$field] : null;
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
        
        $query = 'UPDATE plans SET ' . implode(', ', $setParts) . ' WHERE id = :id';
        $updateData['id'] = $id;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($updateData);

        // Fetch updated plan
        $stmt = $pdo->prepare('SELECT p.*, u.username AS added_by_username 
                              FROM plans p 
                              LEFT JOIN users u ON p.added_by = u.id 
                              WHERE p.id = ?');
        $stmt->execute([$id]);
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Plan updated successfully', 'data' => $plan]);
    }

    if ($action === 'delete') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Check permission
        if (!checkPlanPermission($pdo, $authenticatedUserId)) {
            json_response(['success' => false, 'message' => 'Insufficient permissions'], 403);
        }

        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Plan ID is required'], 400);
        }

        // Check if plan exists
        $stmt = $pdo->prepare('SELECT * FROM plans WHERE id = ?');
        $stmt->execute([$id]);
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$plan) {
            json_response(['success' => false, 'message' => 'Plan not found'], 404);
        }

        // Delete plan
        $stmt = $pdo->prepare('DELETE FROM plans WHERE id = ?');
        $stmt->execute([$id]);

        json_response(['success' => true, 'message' => 'Plan deleted successfully']);
    }

    // Invalid action
    json_response(['success' => false, 'message' => 'Invalid action'], 400);

} catch (Exception $e) {
    error_log('Plans API Error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
}
?>
