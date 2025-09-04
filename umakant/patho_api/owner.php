<?php
/**
 * Owner API - Comprehensive CRUD operations for owners
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

    // Check if user has permission to manage owners
    function checkOwnerPermission($pdo, $userId) {
        $stmt = $pdo->prepare('SELECT role FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user && in_array($user['role'], ['master', 'admin']);
    }

    // Validate owner data
    function validateOwnerData($data, $pdo, $isUpdate = false, $currentOwnerId = null) {
        $errors = [];
        
        if (!$isUpdate || isset($data['name'])) {
            if (empty(trim($data['name'] ?? ''))) {
                $errors[] = 'Owner name is required';
            }
        }
        
        if (isset($data['phone']) && !empty($data['phone'])) {
            $phone = trim($data['phone']);
            if (!preg_match('/^[0-9+\-\s()]+$/', $phone)) {
                $errors[] = 'Invalid phone number format';
            }
        }
        
        if (isset($data['whatsapp']) && !empty($data['whatsapp'])) {
            $whatsapp = trim($data['whatsapp']);
            if (!preg_match('/^[0-9+\-\s()]+$/', $whatsapp)) {
                $errors[] = 'Invalid WhatsApp number format';
            }
        }
        
        if (isset($data['email']) && !empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            } else {
                // Check for duplicate email
                $query = 'SELECT id FROM owners WHERE email = ?';
                $params = [$data['email']];
                
                if ($isUpdate && $currentOwnerId) {
                    $query .= ' AND id != ?';
                    $params[] = $currentOwnerId;
                }
                
                $stmt = $pdo->prepare($query);
                $stmt->execute($params);
                if ($stmt->fetch()) {
                    $errors[] = 'Email address already exists';
                }
            }
        }
        
        return $errors;
    }

    if ($action === 'list') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Check permission - only master and admin can list owners
        if (!checkOwnerPermission($pdo, $authenticatedUserId)) {
            json_response(['success' => false, 'message' => 'Insufficient permissions'], 403);
        }
        
        // Search functionality
        $search = $_GET['search'] ?? '';
        $limit = intval($_GET['limit'] ?? 50);
        $offset = intval($_GET['offset'] ?? 0);
        
        $whereConditions = [];
        $params = [];
        
        if (!empty($search)) {
            $whereConditions[] = '(name LIKE ? OR phone LIKE ? OR whatsapp LIKE ? OR email LIKE ?)';
            $searchParam = '%' . $search . '%';
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
        }
        
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        
        // Count total records
        $countQuery = 'SELECT COUNT(*) as total FROM owners ' . $whereClause;
        $stmt = $pdo->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Fetch owners
        $query = 'SELECT o.*, u.username AS added_by_username 
                 FROM owners o 
                 LEFT JOIN users u ON o.added_by = u.id 
                 ' . $whereClause . ' 
                 ORDER BY o.name ASC 
                 LIMIT ? OFFSET ?';
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $owners = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'success' => true, 
            'data' => $owners, 
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    if ($action === 'get') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Check permission
        if (!checkOwnerPermission($pdo, $authenticatedUserId)) {
            json_response(['success' => false, 'message' => 'Insufficient permissions'], 403);
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Owner ID is required'], 400);
        }
        
        $stmt = $pdo->prepare('SELECT o.*, u.username AS added_by_username 
                              FROM owners o 
                              LEFT JOIN users u ON o.added_by = u.id 
                              WHERE o.id = ?');
        $stmt->execute([$id]);
        $owner = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$owner) {
            json_response(['success' => false, 'message' => 'Owner not found'], 404);
        }
        
        json_response(['success' => true, 'data' => $owner]);
    }

    if ($action === 'create') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Check permission
        if (!checkOwnerPermission($pdo, $authenticatedUserId)) {
            json_response(['success' => false, 'message' => 'Insufficient permissions'], 403);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate input
        $errors = validateOwnerData($input, $pdo);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        // Prepare data for insertion
        $data = [
            'name' => trim($input['name']),
            'phone' => trim($input['phone'] ?? ''),
            'whatsapp' => trim($input['whatsapp'] ?? ''),
            'email' => trim($input['email'] ?? ''),
            'address' => trim($input['address'] ?? ''),
            'added_by' => $authenticatedUserId,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Insert owner
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $query = 'INSERT INTO owners (' . implode(', ', $fields) . ') VALUES (' . $placeholders . ')';
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($data);
        
        $ownerId = $pdo->lastInsertId();
        
        // Fetch the created owner
        $stmt = $pdo->prepare('SELECT o.*, u.username AS added_by_username 
                              FROM owners o 
                              LEFT JOIN users u ON o.added_by = u.id 
                              WHERE o.id = ?');
        $stmt->execute([$ownerId]);
        $owner = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Owner created successfully', 'data' => $owner]);
    }

    if ($action === 'update') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Check permission
        if (!checkOwnerPermission($pdo, $authenticatedUserId)) {
            json_response(['success' => false, 'message' => 'Insufficient permissions'], 403);
        }

        // Get owner ID from URL or input
        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Owner ID is required'], 400);
        }

        // Check if owner exists
        $stmt = $pdo->prepare('SELECT * FROM owners WHERE id = ?');
        $stmt->execute([$id]);
        $existingOwner = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$existingOwner) {
            json_response(['success' => false, 'message' => 'Owner not found'], 404);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate input
        $errors = validateOwnerData($input, $pdo, true, $id);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        // Prepare update data
        $updateData = [];
        $allowedFields = ['name', 'phone', 'whatsapp', 'email', 'address'];
        
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
        
        $query = 'UPDATE owners SET ' . implode(', ', $setParts) . ' WHERE id = :id';
        $updateData['id'] = $id;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($updateData);

        // Fetch updated owner
        $stmt = $pdo->prepare('SELECT o.*, u.username AS added_by_username 
                              FROM owners o 
                              LEFT JOIN users u ON o.added_by = u.id 
                              WHERE o.id = ?');
        $stmt->execute([$id]);
        $owner = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Owner updated successfully', 'data' => $owner]);
    }

    if ($action === 'delete') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Check permission
        if (!checkOwnerPermission($pdo, $authenticatedUserId)) {
            json_response(['success' => false, 'message' => 'Insufficient permissions'], 403);
        }

        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Owner ID is required'], 400);
        }

        // Check if owner exists
        $stmt = $pdo->prepare('SELECT * FROM owners WHERE id = ?');
        $stmt->execute([$id]);
        $owner = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$owner) {
            json_response(['success' => false, 'message' => 'Owner not found'], 404);
        }

        // Delete owner
        $stmt = $pdo->prepare('DELETE FROM owners WHERE id = ?');
        $stmt->execute([$id]);

        json_response(['success' => true, 'message' => 'Owner deleted successfully']);
    }

    // Invalid action
    json_response(['success' => false, 'message' => 'Invalid action'], 400);

} catch (Exception $e) {
    error_log('Owner API Error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
}
?>
// patho_api/owner.php - public API for owners (JSON)
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';

$action = $_REQUEST['action'] ?? 'list';

try {
    if ($action === 'list') {
        $stmt = $pdo->query('SELECT o.id, o.name, o.phone, o.whatsapp, o.email, o.address, o.added_by, u.username as added_by_username FROM owners o LEFT JOIN users u ON o.added_by = u.id ORDER BY o.id DESC');
        $rows = $stmt->fetchAll();
        json_response(['success' => true, 'data' => $rows]);
    }

    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT o.*, u.username as added_by_username FROM owners o LEFT JOIN users u ON o.added_by = u.id WHERE o.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        if (!$row) json_response(['success'=>false,'message'=>'Owner not found'],404);
        json_response(['success' => true, 'data' => $row]);
    }

    if ($action === 'save') {
        // only admin/master may write
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);

        $id = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $whatsapp = trim($_POST['whatsapp'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if ($name === '') json_response(['success'=>false,'message'=>'Name is required'],400);

        if ($id) {
            $stmt = $pdo->prepare('UPDATE owners SET name=?, phone=?, whatsapp=?, email=?, address=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$name, $phone, $whatsapp, $email, $address, $id]);
            json_response(['success' => true, 'message' => 'Owner updated']);
        } else {
            $added_by = $_SESSION['user_id'] ?? null;
            if (empty($added_by) && !empty($PATHO_API_DEFAULT_USER_ID)) {
                $added_by = (int)$PATHO_API_DEFAULT_USER_ID;
            }
            $data = ['name'=>$name, 'phone'=>$phone, 'whatsapp'=>$whatsapp, 'email'=>$email, 'address'=>$address, 'added_by'=>$added_by];
            // Unique by phone if present, else email, else name
            if ($phone !== '') $unique = ['phone'=>$phone];
            elseif ($email !== '') $unique = ['email'=>$email];
            else $unique = ['name'=>$name];
            $res = upsert_or_skip($pdo, 'owners', $unique, $data);
            json_response(['success'=>true,'message'=>'Owner '.$res['action'],'id'=>$res['id']]);
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $stmt = $pdo->prepare('DELETE FROM owners WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success' => true, 'message' => 'Owner deleted']);
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);
} catch (Throwable $e) {
    json_response(['success'=>false,'message'=>'Server error: '.$e->getMessage()],500);
}

