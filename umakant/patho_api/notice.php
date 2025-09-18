<?php
/**
 * Notice API - Comprehensive CRUD operations for notices
 * Supports: CREATE, READ, UPDATE, DELETE operations
 * Authentication: Multiple methods supported
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

// Entity Configuration for Notices (match DB schema)
$entity_config = [
    'table_name' => 'notices',
    'id_field' => 'id',
    'required_fields' => ['title'],
    'allowed_fields' => [
        'title', 'content', 'start_date', 'end_date', 'active', 'added_by'
    ],
    'permission_map' => [
        'list' => 'read',
        'get' => 'read', 
        'save' => 'write',
        'delete' => 'delete'
    ]
];

// Get action from request
$action = $_REQUEST['action'] ?? $_SERVER['REQUEST_METHOD'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Map HTTP methods to actions
switch($requestMethod) {
    case 'GET':
        $action = isset($_GET['id']) ? 'get' : 'list';
        break;
    case 'POST':
        $action = $_REQUEST['action'] ?? 'save';
        break;
    case 'PUT':
        $action = 'save';
        break;
    case 'DELETE':
        $action = 'delete';
        break;
}

try {
    // Authenticate user
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Authentication required']);
        exit;
    }

    // Check permissions
    $required_permission = $entity_config['permission_map'][$action] ?? 'read';
    if (!checkPermission($user_data, $required_permission)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Insufficient permissions']);
        exit;
    }

    switch($action) {
        case 'list':
            handleList($pdo, $entity_config, $user_data);
            break;
            
        case 'get':
            handleGet($pdo, $entity_config, $user_data);
            break;
            
        case 'save':
            handleSave($pdo, $entity_config, $user_data);
            break;
            
        case 'delete':
            handleDelete($pdo, $entity_config);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }

} catch (Exception $e) {
    error_log("Notice API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
}

function handleList($pdo, $config, $user_data) {
    if (!checkPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied'], 403);
    }
    try {
    $sql = "SELECT n.*, u.username as added_by_username 
        FROM {$config['table_name']} n 
        LEFT JOIN users u ON n.added_by = u.id 
        ORDER BY n.start_date DESC, n.id DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => $notices,
            'total' => count($notices)
        ]);
    } catch (Exception $e) {
        error_log("List notices error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch notices']);
    }
}

function handleGet($pdo, $config, $user_data) {
    try {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Notice ID is required']);
            return;
        }

    $sql = "SELECT n.*, u.username as added_by_username 
        FROM {$config['table_name']} n 
        LEFT JOIN users u ON n.added_by = u.id 
        WHERE n.{$config['id_field']} = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $notice = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$notice) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Notice not found']);
            return;
        }

        if (!checkPermission($user_data, 'get', $notice['added_by'])) {
            json_response(['success' => false, 'message' => 'Permission denied'], 403);
        }

        echo json_encode(['success' => true, 'data' => $notice]);
    } catch (Exception $e) {
        error_log("Get notice error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch notice']);
    }
}

function handleSave($pdo, $config, $user_data) {
    try {
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate required fields
        foreach ($config['required_fields'] as $field) {
            if (empty($input[$field])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
                return;
            }
        }

        // Prepare data for saving
        $data = [];
        foreach ($config['allowed_fields'] as $field) {
            if (isset($input[$field])) {
                $data[$field] = $input[$field];
            }
        }

        // Set default values (DB has 'active' tinyint and timestamps)
        if (!isset($data['active'])) {
            $data['active'] = 1;
        }
        if (!isset($data['start_date'])) {
            $data['start_date'] = date('Y-m-d H:i:s');
        }

        // Set created_by to current user for new notices
        $id = $input['id'] ?? null;
        $is_update = !empty($id);
        
        if (!$is_update) {
            $data['added_by'] = $user_data['user_id'] ?? ($user_data['id'] ?? null);
        }

        if ($is_update) {
            // Update existing notice - only update provided fields
            if (empty($data)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'No valid fields to update']);
                return;
            }
            
            // Check if notice exists
            $stmt = $pdo->prepare("SELECT * FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$existing) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Notice not found']);
                return;
            }
            
            $set_clause = implode(', ', array_map(fn($field) => "$field = ?", array_keys($data)));
            $sql = "UPDATE {$config['table_name']} SET $set_clause, updated_at = NOW() WHERE {$config['id_field']} = ?";
            $values = array_merge(array_values($data), [$id]);
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute($values);
            $notice_id = $id;
            $action = 'updated';
        } else {
            // Create new notice using upsert logic to prevent duplicates
            
            // Define unique criteria for duplicate detection
            $uniqueWhere = [
                'title' => $data['title']
            ];
            
            // Add date range to uniqueness if available to prevent same-day duplicates
            if (isset($data['start_date'])) {
                $uniqueWhere['start_date'] = $data['start_date'];
            }
            
            // Use upsert function to handle duplicates properly
            $result_info = upsert_or_skip($pdo, $config['table_name'], $uniqueWhere, $data);
            $notice_id = $result_info['id'];
            $action = $result_info['action'];
            $result = true;
        }

        if ($result) {            
            // Fetch the saved notice with creator info
            $stmt = $pdo->prepare("SELECT n.*, u.username as added_by_username 
                                   FROM {$config['table_name']} n 
                                   LEFT JOIN users u ON n.added_by = u.id 
                                   WHERE n.{$config['id_field']} = ?");
            $stmt->execute([$notice_id]);
            $saved_notice = $stmt->fetch(PDO::FETCH_ASSOC);

            $message = match($action) {
                'inserted' => 'Notice created successfully',
                'updated' => 'Notice updated successfully', 
                'skipped' => 'Notice already exists (no changes needed)',
                default => 'Notice saved successfully'
            };

            echo json_encode([
                'success' => true,
                'message' => $message,
                'data' => $saved_notice,
                'action' => $action,
                'id' => $notice_id
            ]);
        } else {
            throw new Exception('Failed to save notice');
        }

    } catch (Exception $e) {
        error_log("Save notice error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save notice']);
    }
}

function handleDelete($pdo, $config) {
    try {
        $id = $_REQUEST['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Notice ID is required']);
            return;
        }

        // Check if notice exists
        $stmt = $pdo->prepare("SELECT id FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Notice not found']);
            return;
        }

        // Delete the notice
        $stmt = $pdo->prepare("DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $result = $stmt->execute([$id]);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Notice deleted successfully']);
        } else {
            throw new Exception('Failed to delete notice');
        }

    } catch (Exception $e) {
        error_log("Delete notice error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete notice']);
    }
}
?>
