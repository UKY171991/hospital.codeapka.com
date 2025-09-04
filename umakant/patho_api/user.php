<?php
/**
 * User API - Comprehensive CRUD operations for users
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

    // Check if user has permission to manage users
    function checkUserPermission($pdo, $userId) {
        $stmt = $pdo->prepare('SELECT role FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user && in_array($user['role'], ['master', 'admin']);
    }

    // Validate user data
    function validateUserData($data, $pdo, $isUpdate = false, $currentUserId = null) {
        $errors = [];
        
        if (!$isUpdate || isset($data['username'])) {
            $username = trim($data['username'] ?? '');
            if (empty($username)) {
                $errors[] = 'Username is required';
            } elseif (strlen($username) < 3) {
                $errors[] = 'Username must be at least 3 characters long';
            } else {
                // Check for duplicate username
                $query = 'SELECT id FROM users WHERE username = ?';
                $params = [$username];
                
                if ($isUpdate && $currentUserId) {
                    $query .= ' AND id != ?';
                    $params[] = $currentUserId;
                }
                
                $stmt = $pdo->prepare($query);
                $stmt->execute($params);
                if ($stmt->fetch()) {
                    $errors[] = 'Username already exists';
                }
            }
        }
        
        if (!$isUpdate || isset($data['password'])) {
            $password = $data['password'] ?? '';
            if (!$isUpdate && empty($password)) {
                $errors[] = 'Password is required';
            } elseif (!empty($password) && strlen($password) < 6) {
                $errors[] = 'Password must be at least 6 characters long';
            }
        }
        
        if (!$isUpdate || isset($data['full_name'])) {
            if (empty(trim($data['full_name'] ?? ''))) {
                $errors[] = 'Full name is required';
            }
        }
        
        if (isset($data['email']) && !empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            }
        }
        
        if (isset($data['role'])) {
            $validRoles = ['master', 'admin', 'user'];
            if (!in_array($data['role'], $validRoles)) {
                $errors[] = 'Role must be master, admin, or user';
            }
        }
        
        return $errors;
    }

    if ($action === 'list') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Check permission - only master and admin can list users
        if (!checkUserPermission($pdo, $authenticatedUserId)) {
            json_response(['success' => false, 'message' => 'Insufficient permissions'], 403);
        }
        
        // Search functionality
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';
        $isActive = $_GET['is_active'] ?? '';
        $limit = intval($_GET['limit'] ?? 50);
        $offset = intval($_GET['offset'] ?? 0);
        
        $whereConditions = [];
        $params = [];
        
        if (!empty($search)) {
            $whereConditions[] = '(username LIKE ? OR full_name LIKE ? OR email LIKE ?)';
            $searchParam = '%' . $search . '%';
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        }
        
        if (!empty($role)) {
            $whereConditions[] = 'role = ?';
            $params[] = $role;
        }
        
        if ($isActive !== '') {
            $whereConditions[] = 'is_active = ?';
            $params[] = intval($isActive);
        }
        
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        
        // Count total records
        $countQuery = 'SELECT COUNT(*) as total FROM users ' . $whereClause;
        $stmt = $pdo->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Fetch users (exclude password)
        $query = 'SELECT id, username, full_name, email, role, is_active, created_at, last_login, expire_date, added_by
                 FROM users 
                 ' . $whereClause . ' 
                 ORDER BY created_at DESC 
                 LIMIT ? OFFSET ?';
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'success' => true, 
            'data' => $users, 
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

        $id = $_GET['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'User ID is required'], 400);
        }
        
        // Users can only see their own data unless they are admin/master
        if ($id != $authenticatedUserId && !checkUserPermission($pdo, $authenticatedUserId)) {
            json_response(['success' => false, 'message' => 'Insufficient permissions'], 403);
        }
        
        $stmt = $pdo->prepare('SELECT id, username, full_name, email, role, is_active, created_at, last_login, expire_date, added_by
                              FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            json_response(['success' => false, 'message' => 'User not found'], 404);
        }
        
        json_response(['success' => true, 'data' => $user]);
    }

    if ($action === 'create') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Check permission - only master and admin can create users
        if (!checkUserPermission($pdo, $authenticatedUserId)) {
            json_response(['success' => false, 'message' => 'Insufficient permissions'], 403);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate input
        $errors = validateUserData($input, $pdo);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        // Hash password
        $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);

        // Prepare data for insertion
        $data = [
            'username' => trim($input['username']),
            'password' => $hashedPassword,
            'full_name' => trim($input['full_name']),
            'email' => trim($input['email'] ?? ''),
            'role' => $input['role'] ?? 'user',
            'added_by' => $authenticatedUserId,
            'is_active' => isset($input['is_active']) ? intval($input['is_active']) : 1,
            'expire_date' => isset($input['expire_date']) && !empty($input['expire_date']) ? $input['expire_date'] : null
        ];

        // Insert user
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $query = 'INSERT INTO users (' . implode(', ', $fields) . ') VALUES (' . $placeholders . ')';
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($data);
        
        $userId = $pdo->lastInsertId();
        
        // Fetch the created user (exclude password)
        $stmt = $pdo->prepare('SELECT id, username, full_name, email, role, is_active, created_at, last_login, expire_date, added_by
                              FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'User created successfully', 'data' => $user]);
    }

    if ($action === 'update') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Get user ID from URL or input
        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'User ID is required'], 400);
        }

        // Users can only update their own data unless they are admin/master
        if ($id != $authenticatedUserId && !checkUserPermission($pdo, $authenticatedUserId)) {
            json_response(['success' => false, 'message' => 'Insufficient permissions'], 403);
        }

        // Check if user exists
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$existingUser) {
            json_response(['success' => false, 'message' => 'User not found'], 404);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate input
        $errors = validateUserData($input, $pdo, true, $id);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        // Prepare update data
        $updateData = [];
        $allowedFields = ['username', 'full_name', 'email', 'role', 'is_active', 'expire_date'];
        
        // Non-admin users cannot change role or is_active
        if (!checkUserPermission($pdo, $authenticatedUserId)) {
            $allowedFields = array_diff($allowedFields, ['role', 'is_active']);
        }
        
        foreach ($allowedFields as $field) {
            if (isset($input[$field])) {
                if ($field === 'is_active') {
                    $updateData[$field] = intval($input[$field]);
                } elseif ($field === 'expire_date') {
                    $updateData[$field] = !empty($input[$field]) ? $input[$field] : null;
                } else {
                    $updateData[$field] = trim($input[$field]);
                }
            }
        }

        // Handle password update
        if (isset($input['password']) && !empty($input['password'])) {
            if (strlen($input['password']) < 6) {
                json_response(['success' => false, 'message' => 'Password must be at least 6 characters long'], 400);
            }
            $updateData['password'] = password_hash($input['password'], PASSWORD_DEFAULT);
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
        
        $query = 'UPDATE users SET ' . implode(', ', $setParts) . ' WHERE id = :id';
        $updateData['id'] = $id;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($updateData);

        // Fetch updated user (exclude password)
        $stmt = $pdo->prepare('SELECT id, username, full_name, email, role, is_active, created_at, last_login, expire_date, added_by
                              FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'User updated successfully', 'data' => $user]);
    }

    if ($action === 'delete') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Check permission - only master and admin can delete users
        if (!checkUserPermission($pdo, $authenticatedUserId)) {
            json_response(['success' => false, 'message' => 'Insufficient permissions'], 403);
        }

        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'User ID is required'], 400);
        }

        // Cannot delete self
        if ($id == $authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Cannot delete your own account'], 400);
        }

        // Check if user exists
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            json_response(['success' => false, 'message' => 'User not found'], 404);
        }

        // Cannot delete master user
        if ($user['role'] === 'master') {
            json_response(['success' => false, 'message' => 'Cannot delete master user'], 400);
        }

        // Check for associated data
        $associatedTables = [
            'doctors' => 'added_by',
            'patients' => 'added_by',
            'tests' => 'added_by',
            'categories' => 'added_by',
            'entries' => 'added_by'
        ];

        foreach ($associatedTables as $table => $column) {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM $table WHERE $column = ?");
            $stmt->execute([$id]);
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            if ($count > 0) {
                json_response(['success' => false, 'message' => "Cannot delete user with associated data in $table"], 400);
            }
        }

        // Delete user
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);

        json_response(['success' => true, 'message' => 'User deleted successfully']);
    }

    // Invalid action
    json_response(['success' => false, 'message' => 'Invalid action'], 400);

} catch (Exception $e) {
    error_log('User API Error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
}
?>
// patho_api/user.php - public API for users (JSON)
// Mirrors ajax/user_api.php behavior but served from /patho_api/
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';

$method = $_SERVER['REQUEST_METHOD'];
action:
$action = $_REQUEST['action'] ?? 'list';

// Simple authentication: allow listing if authenticated. For public endpoints adjust as needed.
$viewerRole = $_SESSION['role'] ?? 'user';
$viewerId = $_SESSION['user_id'] ?? null;

try {
    if ($action === 'list') {
        // Public API: only return users with role = 'user'
    // Select all columns visible in the users table structure
    $stmt = $pdo->prepare("SELECT id, username, password, full_name, email, role, added_by, is_active, created_at, last_login, expire_date, updated_at FROM users WHERE role = 'user' ORDER BY id DESC");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        json_response(['success'=>true,'data'=>$rows]);
    }

    if ($action === 'get' && isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT id, username, password, full_name, email, role, added_by, is_active, created_at, last_login, expire_date, updated_at FROM users WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        if (!$row) json_response(['success'=>false,'message'=>'User not found'],404);
        if ($viewerRole === 'master' || $row['added_by'] == $viewerId || $row['id'] == $viewerId) {
            json_response(['success'=>true,'data'=>$row]);
        } else {
            json_response(['success'=>false,'message'=>'Unauthorized'],403);
        }
    }

    if ($action === 'save') {
        if (!isset($_SESSION['user_id'])) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $id = $_POST['id'] ?? '';
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'user';
        $is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 0;
        $creatorId = $_SESSION['user_id'];

        if ($id) {
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE users SET username=?, password=?, full_name=?, email=?, role=?, is_active=? WHERE id=?');
                $stmt->execute([$username, $hash, $full_name, $email, $role, $is_active, $id]);
            } else {
                $stmt = $pdo->prepare('UPDATE users SET username=?, full_name=?, email=?, role=?, is_active=? WHERE id=?');
                $stmt->execute([$username, $full_name, $email, $role, $is_active, $id]);
            }
            json_response(['success'=>true,'message'=>'User updated']);
        } else {
            $hash = password_hash($password ?: 'password', PASSWORD_DEFAULT);
            $data = ['username'=>$username, 'password'=>$hash, 'full_name'=>$full_name, 'email'=>$email, 'role'=>$role, 'added_by'=>$creatorId, 'is_active'=>$is_active];
            // Unique by username first, else email
            if ($username !== '') $unique = ['username'=>$username]; elseif ($email !== '') $unique = ['email'=>$email]; else $unique = ['full_name'=>$full_name];
            $res = upsert_or_skip($pdo, 'users', $unique, $data);
            json_response(['success'=>true,'message'=>'User '.$res['action'],'id'=>$res['id']]);
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'master') json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success'=>true,'message'=>'User deleted']);
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);
} catch (Exception $e) {
    json_response(['success'=>false,'message'=>'Server error: '.$e->getMessage()],500);
}
