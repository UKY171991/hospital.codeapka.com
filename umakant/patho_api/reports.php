<?php
/**
 * Reports API - Comprehensive CRUD operations for reports
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
    error_log("Reports API Uncaught Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An internal server error occurred.'], 500);
}

function validateReportData($data, $isUpdate = false) {
    $errors = [];
    if (!$isUpdate && empty($data['data'])) {
        $errors[] = 'Report data is required';
    }
    return $errors;
}

function handleList($pdo) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }
    if (!checkPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list reports'], 403);
    }

    try {
        // Role-based scoping
        $scopeIds = getScopedUserIds($pdo, $user_data); // null => master, no restriction
        $viewerRole = $user_data['role'] ?? 'user';

        $userId = $_GET['user_id'] ?? $user_data['user_id'];
        $search = $_GET['search'] ?? '';
        $dateFrom = $_GET['date_from'] ?? null;
        $dateTo = $_GET['date_to'] ?? null;
        $limit = intval($_GET['limit'] ?? 50);
        $offset = intval($_GET['offset'] ?? 0);

        $whereConditions = [];
        $params = [];

        // all=1 only for master; otherwise enforce scoping
        if (isset($_GET['all']) && $_GET['all'] == '1' && ($viewerRole === 'master')) {
            // Master can see all
        } else {
            if (is_array($scopeIds)) {
                $placeholders = implode(',', array_fill(0, count($scopeIds), '?'));
                $whereConditions[] = 'r.added_by IN (' . $placeholders . ')';
                $params = array_merge($params, $scopeIds);
            }
        }
        
        if (!empty($search)) {
            $whereConditions[] = 'r.data LIKE ?';
            $searchParam = '%' . $search . '%';
            $params[] = $searchParam;
        }
        
        if ($dateFrom) {
            $whereConditions[] = 'DATE(r.created_at) >= ?';
            $params[] = $dateFrom;
        }
        
        if ($dateTo) {
            $whereConditions[] = 'DATE(r.created_at) <= ?';
            $params[] = $dateTo;
        }
        
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        
        $countQuery = 'SELECT COUNT(*) as total FROM reports r ' . $whereClause;
        $stmt = $pdo->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        $query = 'SELECT r.*, u.username AS added_by_username 
                 FROM reports r 
                 LEFT JOIN users u ON r.added_by = u.id 
                 ' . $whereClause . ' 
                 ORDER BY r.created_at DESC 
                 LIMIT ? OFFSET ?';
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'success' => true, 
            'data' => $reports, 
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ]);
    } catch (Exception $e) {
        error_log("List reports error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch reports'], 500);
    }
}

function handleGet($pdo) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }

    $id = $_GET['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Report ID is required'], 400);
    }

    try {
        $stmt = $pdo->prepare('SELECT r.*, u.username AS added_by_username 
                             FROM reports r 
                             LEFT JOIN users u ON r.added_by = u.id 
                             WHERE r.id = ?');
        $stmt->execute([$id]);
        $report = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$report) {
            json_response(['success' => false, 'message' => 'Report not found'], 404);
        }

        // Scoped visibility
        $scopeIds = getScopedUserIds($pdo, $user_data);
        if (is_array($scopeIds)) {
            if (!in_array((int)$report['added_by'], $scopeIds, true)) {
                json_response(['success' => false, 'message' => 'Permission denied to view this report'], 403);
            }
        }
        
        json_response(['success' => true, 'data' => $report]);
    } catch (Exception $e) {
        error_log("Get report error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch report'], 500);
    }
}

function handleCreate($pdo) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }

    if (!checkPermission($user_data, 'create')) {
        json_response(['success' => false, 'message' => 'Permission denied to create reports'], 403);
    }

    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    
    $errors = validateReportData($input);
    if (!empty($errors)) {
        json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
    }

    try {
        $data = [
            'data' => trim($input['data']),
            'added_by' => $user_data['user_id']
        ];

        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $query = 'INSERT INTO reports (' . implode(', ', $fields) . ') VALUES (' . $placeholders . ')';
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($data);
        
        $reportId = $pdo->lastInsertId();
        
        $stmt = $pdo->prepare('SELECT r.*, u.username AS added_by_username 
                              FROM reports r 
                              LEFT JOIN users u ON r.added_by = u.id 
                              WHERE r.id = ?');
        $stmt->execute([$reportId]);
        $report = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Report created successfully', 'data' => $report]);
    } catch (Exception $e) {
        error_log("Create report error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to create report'], 500);
    }
}

function handleUpdate($pdo) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }

    $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Report ID is required'], 400);
    }

    try {
        $stmt = $pdo->prepare('SELECT added_by FROM reports WHERE id = ?');
        $stmt->execute([$id]);
        $existingReport = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$existingReport) {
            json_response(['success' => false, 'message' => 'Report not found'], 404);
        }
        // Scoped visibility
        $scopeIds = getScopedUserIds($pdo, $user_data);
        if (is_array($scopeIds)) {
            if (!in_array((int)$existingReport['added_by'], $scopeIds, true)) {
                json_response(['success' => false, 'message' => 'Permission denied to update this report'], 403);
            }
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        $errors = validateReportData($input, true);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        $updateData = [];
        if (isset($input['data'])) {
            $updateData['data'] = trim($input['data']);
        }

        if (empty($updateData)) {
            json_response(['success' => false, 'message' => 'No valid fields to update'], 400);
        }

        $setParts = [];
        foreach ($updateData as $field => $value) {
            $setParts[] = "$field = :$field";
        }
        
        $query = 'UPDATE reports SET ' . implode(', ', $setParts) . ', updated_at = NOW() WHERE id = :id';
        $updateData['id'] = $id;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($updateData);

        $stmt = $pdo->prepare('SELECT r.*, u.username AS added_by_username 
                              FROM reports r 
                              LEFT JOIN users u ON r.added_by = u.id 
                              WHERE r.id = ?');
        $stmt->execute([$id]);
        $report = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Report updated successfully', 'data' => $report]);
    } catch (Exception $e) {
        error_log("Update report error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to update report'], 500);
    }
}

function handleDelete($pdo) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }

    $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Report ID is required'], 400);
    }

    try {
        $stmt = $pdo->prepare('SELECT added_by FROM reports WHERE id = ?');
        $stmt->execute([$id]);
        $report = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$report) {
            json_response(['success' => false, 'message' => 'Report not found'], 404);
        }
        // Scoped visibility
        $scopeIds = getScopedUserIds($pdo, $user_data);
        if (is_array($scopeIds)) {
            if (!in_array((int)$report['added_by'], $scopeIds, true)) {
                json_response(['success' => false, 'message' => 'Permission denied to delete this report'], 403);
            }
        }

        $stmt = $pdo->prepare('DELETE FROM reports WHERE id = ?');
        $result = $stmt->execute([$id]);

        if ($result) {
            json_response(['success' => true, 'message' => 'Report deleted successfully']);
        } else {
            json_response(['success' => false, 'message' => 'Failed to delete report'], 500);
        }
    } catch (Exception $e) {
        error_log("Delete report error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to delete report'], 500);
    }
}
?>