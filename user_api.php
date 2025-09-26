<?php
/**
 * User API - CRUD operations for users
 * Location: Root folder
 * Supports: CREATE, READ, UPDATE, DELETE operations
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include database connection
try {
    require_once __DIR__ . '/umakant/inc/connection.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection error',
        'error' => $e->getMessage()
    ]);
    exit;
}

// Helper function for JSON responses
function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data);
    exit;
}

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
    switch($action) {
        case 'list':
            // Get all users
            $page = (int)($_GET['page'] ?? 1);
            $limit = (int)($_GET['limit'] ?? 50);
            $offset = ($page - 1) * $limit;
            
            $search = $_GET['search'] ?? '';
            $role = $_GET['role'] ?? '';
            
            $whereConditions = [];
            $params = [];
            
            if (!empty($search)) {
                $whereConditions[] = "(username LIKE ? OR full_name LIKE ? OR email LIKE ?)";
                $searchTerm = "%$search%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if (!empty($role)) {
                $whereConditions[] = "role = ?";
                $params[] = $role;
            }
            
            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
            
            // Get total count
            $countSql = "SELECT COUNT(*) FROM users $whereClause";
            $countStmt = $pdo->prepare($countSql);
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();
            
            // Get users
            $sql = "SELECT id, username, full_name, email, role, is_active, user_type, 
                           created_at, last_login, expire_date, added_by, updated_at 
                    FROM users $whereClause 
                    ORDER BY created_at DESC 
                    LIMIT $limit OFFSET $offset";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format dates and remove sensitive data
            foreach ($users as &$user) {
                unset($user['password']); // Never return password
                $user['is_active'] = (bool)$user['is_active'];
                $user['user_type'] = (int)$user['user_type'];
            }
            
            json_response([
                'success' => true,
                'data' => $users,
                'total' => (int)$total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ]);
            break;
            
        case 'get':
            // Get single user
            $id = $_GET['id'] ?? null;
            if (!$id) {
                json_response(['success' => false, 'message' => 'User ID is required'], 400);
            }
            
            $stmt = $pdo->prepare("SELECT id, username, full_name, email, role, is_active, user_type, 
                                          created_at, last_login, expire_date, added_by, updated_at 
                                   FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                json_response(['success' => false, 'message' => 'User not found'], 404);
            }
            
            $user['is_active'] = (bool)$user['is_active'];
            $user['user_type'] = (int)$user['user_type'];
            
            json_response(['success' => true, 'data' => $user]);
            break;
            
        case 'create':
            // Create new user
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            // Validate required fields
            $required = ['username', 'password', 'full_name'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    json_response(['success' => false, 'message' => "Field '$field' is required"], 400);
                }
            }
            
            // Check if username already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$data['username']]);
            if ($stmt->fetch()) {
                json_response(['success' => false, 'message' => 'Username already exists'], 409);
            }
            
            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Prepare data
            $insertData = [
                'username' => $data['username'],
                'password' => $hashedPassword,
                'full_name' => $data['full_name'],
                'email' => $data['email'] ?? null,
                'role' => $data['role'] ?? 'user',
                'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1,
                'user_type' => (int)($data['user_type'] ?? 0),
                'expire_date' => $data['expire_date'] ?? null,
                'added_by' => $data['added_by'] ?? null
            ];
            
            $fields = implode(', ', array_keys($insertData));
            $placeholders = ':' . implode(', :', array_keys($insertData));
            
            $sql = "INSERT INTO users ($fields) VALUES ($placeholders)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($insertData);
            
            $userId = $pdo->lastInsertId();
            
            json_response([
                'success' => true,
                'message' => 'User created successfully',
                'data' => ['id' => (int)$userId]
            ], 201);
            break;
            
        case 'update':
            // Update user
            $id = $_GET['id'] ?? $_POST['id'] ?? null;
            if (!$id) {
                json_response(['success' => false, 'message' => 'User ID is required'], 400);
            }
            
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            // Check if user exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                json_response(['success' => false, 'message' => 'User not found'], 404);
            }
            
            // Prepare update data
            $updateFields = [];
            $updateData = [];
            
            $allowedFields = ['username', 'full_name', 'email', 'role', 'is_active', 'user_type', 'expire_date'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = ?";
                    if ($field === 'is_active') {
                        $updateData[] = (int)$data[$field];
                    } elseif ($field === 'user_type') {
                        $updateData[] = (int)$data[$field];
                    } else {
                        $updateData[] = $data[$field];
                    }
                }
            }
            
            // Handle password update separately
            if (!empty($data['password'])) {
                $updateFields[] = "password = ?";
                $updateData[] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            if (empty($updateFields)) {
                json_response(['success' => false, 'message' => 'No valid fields to update'], 400);
            }
            
            // Check username uniqueness if updating username
            if (isset($data['username'])) {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
                $stmt->execute([$data['username'], $id]);
                if ($stmt->fetch()) {
                    json_response(['success' => false, 'message' => 'Username already exists'], 409);
                }
            }
            
            $updateFields[] = "updated_at = CURRENT_TIMESTAMP";
            $updateData[] = $id;
            
            $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($updateData);
            
            json_response(['success' => true, 'message' => 'User updated successfully']);
            break;
            
        case 'delete':
            // Delete user
            $id = $_GET['id'] ?? $_POST['id'] ?? null;
            if (!$id) {
                json_response(['success' => false, 'message' => 'User ID is required'], 400);
            }
            
            // Check if user exists
            $stmt = $pdo->prepare("SELECT id, username FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch();
            
            if (!$user) {
                json_response(['success' => false, 'message' => 'User not found'], 404);
            }
            
            // Soft delete - set is_active to 0 instead of actual deletion
            $stmt = $pdo->prepare("UPDATE users SET is_active = 0, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$id]);
            
            json_response(['success' => true, 'message' => 'User deactivated successfully']);
            break;
            
        case 'activate':
            // Activate user
            $id = $_POST['id'] ?? null;
            if (!$id) {
                json_response(['success' => false, 'message' => 'User ID is required'], 400);
            }
            
            $stmt = $pdo->prepare("UPDATE users SET is_active = 1, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                json_response(['success' => true, 'message' => 'User activated successfully']);
            } else {
                json_response(['success' => false, 'message' => 'User not found'], 404);
            }
            break;
            
        default:
            json_response(['success' => false, 'message' => 'Invalid action'], 400);
    }
    
} catch (PDOException $e) {
    json_response([
        'success' => false,
        'message' => 'Database error',
        'error' => $e->getMessage()
    ], 500);
} catch (Exception $e) {
    json_response([
        'success' => false,
        'message' => 'Server error',
        'error' => $e->getMessage()
    ], 500);
}
?>
