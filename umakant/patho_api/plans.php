<?php
/**
 * Plans API - Comprehensive CRUD operations for subscription plans
 * Supports: CREATE, READ, UPDATE, DELETE operations
 * Authentication: Session-based or API token
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key, X-API-Secret');

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

// Support unified 'save' action: choose create vs update by presence of id
if ($action === 'save') {
    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true);
    if (!is_array($input) || empty($input)) { $input = $_POST; }
    $id = $_GET['id'] ?? $_REQUEST['id'] ?? ($input['id'] ?? null);
    if ($id) {
        $_REQUEST['id'] = $id;
        $action = 'update';
    } else {
        $action = 'create';
    }
}

try {
    switch($action) {
        case 'list':
            handleList($pdo);
            break;
        case 'get':
            handleGet($pdo);
            break;
        case 'create':
            handleCreate($pdo);
            break;
        case 'update':
            handleUpdate($pdo);
            break;
        case 'delete':
            handleDelete($pdo);
            break;
        default:
            json_response(['success' => false, 'message' => 'Invalid action specified'], 400);
    }
} catch (Exception $e) {
    error_log("Plans API Uncaught Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An internal server error occurred.'], 500);
}

function validatePlanData($data, $isUpdate = false) {
    $errors = [];
    if (!$isUpdate && empty($data['name'])) {
        $errors[] = 'Plan name is required';
    }
    if (!$isUpdate && !isset($data['price'])) {
        $errors[] = 'Plan price is required';
    }
    if (isset($data['price']) && !is_numeric($data['price'])) {
        $errors[] = 'Plan price must be a number';
    }
    return $errors;
}

function handleList($pdo) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }
    if (!checkPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list plans'], 403);
    }

    try {
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
        
        $countQuery = 'SELECT COUNT(*) as total FROM plans ' . $whereClause;
        $stmt = $pdo->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
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
    } catch (Exception $e) {
        error_log("List plans error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch plans'], 500);
    }
}

function handleGet($pdo) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }

    $id = $_GET['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Plan ID is required'], 400);
    }

    try {
        $stmt = $pdo->prepare('SELECT p.*, u.username AS added_by_username 
                              FROM plans p 
                              LEFT JOIN users u ON p.added_by = u.id 
                              WHERE p.id = ?');
        $stmt->execute([$id]);
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$plan) {
            json_response(['success' => false, 'message' => 'Plan not found'], 404);
        }

        if (!checkPermission($user_data, 'get', $plan['added_by'])) {
            json_response(['success' => false, 'message' => 'Permission denied to view this plan'], 403);
        }
        
        json_response(['success' => true, 'data' => $plan]);
    } catch (Exception $e) {
        error_log("Get plan error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch plan'], 500);
    }
}

function handleCreate($pdo) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }

    if (!checkPermission($user_data, 'create')) {
        json_response(['success' => false, 'message' => 'Permission denied to create plans'], 403);
    }

    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    
    $errors = validatePlanData($input);
    if (!empty($errors)) {
        json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
    }

    try {
        $data = [
            'name' => trim($input['name']),
            'description' => trim($input['description'] ?? ''),
            'price' => floatval($input['price'] ?? 0),
            'upi' => trim($input['upi'] ?? ''),
            'time_type' => $input['time_type'] ?? 'monthly',
            'start_date' => isset($input['start_date']) && !empty($input['start_date']) ? $input['start_date'] : null,
            'end_date' => isset($input['end_date']) && !empty($input['end_date']) ? $input['end_date'] : null,
            'qr_code' => trim($input['qr_code'] ?? ''),
            'added_by' => $user_data['user_id']
        ];

        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $query = 'INSERT INTO plans (' . implode(', ', $fields) . ') VALUES (' . $placeholders . ')';
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($data);
        
        $planId = $pdo->lastInsertId();
        
        $stmt = $pdo->prepare('SELECT p.*, u.username AS added_by_username 
                              FROM plans p 
                              LEFT JOIN users u ON p.added_by = u.id 
                              WHERE p.id = ?');
        $stmt->execute([$planId]);
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Plan created successfully', 'data' => $plan]);
    } catch (Exception $e) {
        error_log("Create plan error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to create plan'], 500);
    }
}

function handleUpdate($pdo) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }

    $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Plan ID is required'], 400);
    }

    try {
        $stmt = $pdo->prepare('SELECT added_by FROM plans WHERE id = ?');
        $stmt->execute([$id]);
        $existingPlan = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$existingPlan) {
            json_response(['success' => false, 'message' => 'Plan not found'], 404);
        }

        if (!checkPermission($user_data, 'update', $existingPlan['added_by'])) {
            json_response(['success' => false, 'message' => 'Permission denied to update this plan'], 403);
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        $errors = validatePlanData($input, true);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

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

        $setParts = [];
        foreach ($updateData as $field => $value) {
            $setParts[] = "$field = :$field";
        }
        
        $query = 'UPDATE plans SET ' . implode(', ', $setParts) . ', updated_at = NOW() WHERE id = :id';
        $updateData['id'] = $id;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($updateData);

        $stmt = $pdo->prepare('SELECT p.*, u.username AS added_by_username 
                              FROM plans p 
                              LEFT JOIN users u ON p.added_by = u.id 
                              WHERE p.id = ?');
        $stmt->execute([$id]);
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Plan updated successfully', 'data' => $plan]);
    } catch (Exception $e) {
        error_log("Update plan error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to update plan'], 500);
    }
}

function handleDelete($pdo) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }

    $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Plan ID is required'], 400);
    }

    try {
        $stmt = $pdo->prepare('SELECT added_by FROM plans WHERE id = ?');
        $stmt->execute([$id]);
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$plan) {
            json_response(['success' => false, 'message' => 'Plan not found'], 404);
        }

        if (!checkPermission($user_data, 'delete', $plan['added_by'])) {
            json_response(['success' => false, 'message' => 'Permission denied to delete this plan'], 403);
        }

        $stmt = $pdo->prepare('DELETE FROM plans WHERE id = ?');
        $result = $stmt->execute([$id]);

        if ($result) {
            json_response(['success' => true, 'message' => 'Plan deleted successfully']);
        } else {
            json_response(['success' => false, 'message' => 'Failed to delete plan'], 500);
        }
    } catch (Exception $e) {
        error_log("Delete plan error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to delete plan'], 500);
    }
}
?>