<?php
/**
 * Notice API - Comprehensive CRUD operations for notices
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

    // Validate notice data
    function validateNoticeData($data, $isUpdate = false) {
        $errors = [];
        
        if (!$isUpdate || isset($data['title'])) {
            if (empty(trim($data['title'] ?? ''))) {
                $errors[] = 'Notice title is required';
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
        // For notices, we can allow public access to active notices
        $search = $_GET['search'] ?? '';
        $activeOnly = $_GET['active_only'] ?? '0';
        $limit = intval($_GET['limit'] ?? 50);
        $offset = intval($_GET['offset'] ?? 0);
        
        $whereConditions = [];
        $params = [];
        
        // Filter active notices
        if ($activeOnly === '1') {
            $whereConditions[] = 'active = 1';
            $whereConditions[] = '(start_date IS NULL OR start_date <= NOW())';
            $whereConditions[] = '(end_date IS NULL OR end_date >= NOW())';
        }
        
        if (!empty($search)) {
            $whereConditions[] = '(title LIKE ? OR content LIKE ?)';
            $searchParam = '%' . $search . '%';
            $params = array_merge($params, [$searchParam, $searchParam]);
        }
        
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        
        // Count total records
        $countQuery = 'SELECT COUNT(*) as total FROM notices ' . $whereClause;
        $stmt = $pdo->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Fetch notices
        $query = 'SELECT n.*, u.username AS added_by_username 
                 FROM notices n 
                 LEFT JOIN users u ON n.added_by = u.id 
                 ' . $whereClause . ' 
                 ORDER BY n.start_date DESC, n.id DESC 
                 LIMIT ? OFFSET ?';
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'success' => true, 
            'data' => $notices, 
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    if ($action === 'get') {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Notice ID is required'], 400);
        }
        
        $stmt = $pdo->prepare('SELECT n.*, u.username AS added_by_username 
                              FROM notices n 
                              LEFT JOIN users u ON n.added_by = u.id 
                              WHERE n.id = ?');
        $stmt->execute([$id]);
        $notice = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$notice) {
            json_response(['success' => false, 'message' => 'Notice not found'], 404);
        }
        
        json_response(['success' => true, 'data' => $notice]);
    }

    if ($action === 'create' || $action === 'save') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate input
        $errors = validateNoticeData($input);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        // Prepare data for insertion
        $data = [
            'title' => trim($input['title']),
            'content' => trim($input['content'] ?? ''),
            'start_date' => isset($input['start_date']) && !empty($input['start_date']) ? $input['start_date'] : null,
            'end_date' => isset($input['end_date']) && !empty($input['end_date']) ? $input['end_date'] : null,
            'active' => isset($input['active']) ? intval($input['active']) : 1,
            'added_by' => $authenticatedUserId,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Insert notice
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $query = 'INSERT INTO notices (' . implode(', ', $fields) . ') VALUES (' . $placeholders . ')';
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($data);
        
        $noticeId = $pdo->lastInsertId();
        
        // Fetch the created notice
        $stmt = $pdo->prepare('SELECT n.*, u.username AS added_by_username 
                              FROM notices n 
                              LEFT JOIN users u ON n.added_by = u.id 
                              WHERE n.id = ?');
        $stmt->execute([$noticeId]);
        $notice = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Notice created successfully', 'data' => $notice]);
    }

    if ($action === 'update') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Get notice ID from URL or input
        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Notice ID is required'], 400);
        }

        // Check if notice exists
        $stmt = $pdo->prepare('SELECT * FROM notices WHERE id = ?');
        $stmt->execute([$id]);
        $existingNotice = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$existingNotice) {
            json_response(['success' => false, 'message' => 'Notice not found'], 404);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate input
        $errors = validateNoticeData($input, true);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        // Prepare update data
        $updateData = [];
        $allowedFields = ['title', 'content', 'start_date', 'end_date', 'active'];
        
        foreach ($allowedFields as $field) {
            if (isset($input[$field])) {
                if ($field === 'active') {
                    $updateData[$field] = intval($input[$field]);
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

        // Add updated_at timestamp
        $updateData['updated_at'] = date('Y-m-d H:i:s');

        // Build update query
        $setParts = [];
        foreach ($updateData as $field => $value) {
            $setParts[] = "$field = :$field";
        }
        
        $query = 'UPDATE notices SET ' . implode(', ', $setParts) . ' WHERE id = :id';
        $updateData['id'] = $id;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($updateData);

        // Fetch updated notice
        $stmt = $pdo->prepare('SELECT n.*, u.username AS added_by_username 
                              FROM notices n 
                              LEFT JOIN users u ON n.added_by = u.id 
                              WHERE n.id = ?');
        $stmt->execute([$id]);
        $notice = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Notice updated successfully', 'data' => $notice]);
    }

    if ($action === 'delete') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Notice ID is required'], 400);
        }

        // Check if notice exists
        $stmt = $pdo->prepare('SELECT * FROM notices WHERE id = ?');
        $stmt->execute([$id]);
        $notice = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$notice) {
            json_response(['success' => false, 'message' => 'Notice not found'], 404);
        }

        // Delete notice
        $stmt = $pdo->prepare('DELETE FROM notices WHERE id = ?');
        $stmt->execute([$id]);

        json_response(['success' => true, 'message' => 'Notice deleted successfully']);
    }

    // Invalid action
    json_response(['success' => false, 'message' => 'Invalid action'], 400);

} catch (Exception $e) {
    error_log('Notice API Error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
}
?>
// patho_api/notice.php - public API for notices (JSON)
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';

$action = $_REQUEST['action'] ?? 'list';

try {
    if ($action === 'list') {
        $stmt = $pdo->query('SELECT n.*, u.username as added_by_username FROM notices n LEFT JOIN users u ON n.added_by = u.id ORDER BY n.id DESC');
        $rows = $stmt->fetchAll();
        json_response(['success' => true, 'data' => $rows]);
    }

    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT n.*, u.username as added_by_username FROM notices n LEFT JOIN users u ON n.added_by = u.id WHERE n.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        if (!$row) json_response(['success'=>false,'message'=>'Notice not found'],404);
        json_response(['success' => true, 'data' => $row]);
    }

    if ($action === 'save') {
        // write operations require admin/master
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);

        $id = $_POST['id'] ?? '';
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $start = $_POST['start_date'] ?? null;
        $end = $_POST['end_date'] ?? null;
        $active = isset($_POST['active']) ? (int)$_POST['active'] : 0;

        if ($title === '') json_response(['success'=>false,'message'=>'Title required'],400);

        if ($id) {
            $stmt = $pdo->prepare('UPDATE notices SET title=?, content=?, start_date=?, end_date=?, active=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$title, $content, $start, $end, $active, $id]);
            $stmt2 = $pdo->prepare('SELECT n.*, u.username as added_by_username FROM notices n LEFT JOIN users u ON n.added_by = u.id WHERE n.id = ?');
            $stmt2->execute([$id]);
            $row = $stmt2->fetch();
            json_response(['success'=>true,'message'=>'Notice updated','data'=>$row]);
        } else {
            $added_by = $_SESSION['user_id'] ?? null;
            if (empty($added_by) && !empty($PATHO_API_DEFAULT_USER_ID)) {
                $added_by = (int)$PATHO_API_DEFAULT_USER_ID;
            }
            $data = ['title'=>$title, 'content'=>$content, 'start_date'=>$start, 'end_date'=>$end, 'active'=>$active, 'added_by'=>$added_by];
            $unique = ['title'=>$title, 'start_date'=>$start];
            $res = upsert_or_skip($pdo, 'notices', $unique, $data);
            if ($res['action'] === 'inserted') {
                $stmt2 = $pdo->prepare('SELECT n.*, u.username as added_by_username FROM notices n LEFT JOIN users u ON n.added_by = u.id WHERE n.id = ?');
                $stmt2->execute([$res['id']]);
                $row = $stmt2->fetch();
                json_response(['success'=>true,'message'=>'Notice created','data'=>$row]);
            }
            json_response(['success'=>true,'message'=>'Notice '.$res['action'],'id'=>$res['id']]);
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $stmt = $pdo->prepare('DELETE FROM notices WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success'=>true,'message'=>'Notice deleted']);
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);
} catch (Throwable $e) {
    json_response(['success'=>false,'message'=>'Server error: '.$e->getMessage()],500);
}

