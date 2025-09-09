<?php
/**
 * Owner API - CRUD aligned to DB schema (owners: id, name, phone, whatsapp, email, address, added_by,...)
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

// Entity Configuration for Owners (match DB)
$entity_config = [
    'table_name' => 'owners',
    'id_field' => 'id',
    'required_fields' => ['name', 'phone'],
    'allowed_fields' => ['name', 'phone', 'whatsapp', 'email', 'address', 'added_by'],
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
            handleList($pdo, $entity_config);
            break;

        case 'get':
            handleGet($pdo, $entity_config);
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
    error_log("Owner API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
}

function handleList($pdo, $config) {
    try {
        $sql = "SELECT o.*, u.username AS added_by_username
                FROM {$config['table_name']} o
                LEFT JOIN users u ON o.added_by = u.id
                ORDER BY o.name";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $owners = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => $owners,
            'total' => count($owners)
        ]);
    } catch (Exception $e) {
        error_log("List owners error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch owners']);
    }
}

function handleGet($pdo, $config) {
    try {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Owner ID is required']);
            return;
        }

        $sql = "SELECT o.*, u.username AS added_by_username
                FROM {$config['table_name']} o
                LEFT JOIN users u ON o.added_by = u.id
                WHERE o.{$config['id_field']} = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $owner = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$owner) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Owner not found']);
            return;
        }

        echo json_encode(['success' => true, 'data' => $owner]);
    } catch (Exception $e) {
        error_log("Get owner error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch owner']);
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

        // Validate email format if provided
        if (!empty($input['email']) && !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid email format']);
            return;
        }

        // Validate phone (allow +, digits, spaces, dashes, parentheses)
        if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $input['phone'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid phone format']);
            return;
        }

        $id = $input['id'] ?? null;
        $is_update = !empty($id);

        // Check for duplicate phone
        $check_sql = "SELECT id FROM {$config['table_name']} WHERE phone = ?";
        if ($is_update) {
            $check_sql .= " AND id != ?";
            $stmt = $pdo->prepare($check_sql);
            $stmt->execute([$input['phone'], $id]);
        } else {
            $stmt = $pdo->prepare($check_sql);
            $stmt->execute([$input['phone']]);
        }

        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Phone already exists']);
            return;
        }

        // Check for duplicate email if provided
        if (!empty($input['email'])) {
            $check_sql = "SELECT id FROM {$config['table_name']} WHERE email = ?";
            if ($is_update) {
                $check_sql .= " AND id != ?";
                $stmt = $pdo->prepare($check_sql);
                $stmt->execute([$input['email'], $id]);
            } else {
                $stmt = $pdo->prepare($check_sql);
                $stmt->execute([$input['email']]);
            }

            if ($stmt->fetch()) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Email already exists']);
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

        // Ensure added_by is set for new records
        if (!$is_update) {
            $data['added_by'] = $data['added_by'] ?? ($user_data['user_id'] ?? ($user_data['id'] ?? null));
        }

        if ($is_update) {
            // Update existing owner
            $set_clause = implode(', ', array_map(fn($field) => "$field = ?", array_keys($data)));
            $sql = "UPDATE {$config['table_name']} SET $set_clause, updated_at = NOW() WHERE {$config['id_field']} = ?";
            $values = array_merge(array_values($data), [$id]);
        } else {
            // Create new owner
            $fields = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $sql = "INSERT INTO {$config['table_name']} ($fields) VALUES ($placeholders)";
            $values = array_values($data);
        }

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($values);

        if ($result) {
            $owner_id = $is_update ? $id : $pdo->lastInsertId();

            // Fetch the saved owner
            $stmt = $pdo->prepare("SELECT o.*, u.username AS added_by_username
                                   FROM {$config['table_name']} o
                                   LEFT JOIN users u ON o.added_by = u.id
                                   WHERE o.{$config['id_field']} = ?");
            $stmt->execute([$owner_id]);
            $saved_owner = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'message' => $is_update ? 'Owner updated successfully' : 'Owner created successfully',
                'data' => $saved_owner
            ]);
        } else {
            throw new Exception('Failed to save owner');
        }

    } catch (Exception $e) {
        error_log("Save owner error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save owner']);
    }
}

function handleDelete($pdo, $config) {
    try {
        $id = $_REQUEST['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Owner ID is required']);
            return;
        }

        // Check if owner exists
        $stmt = $pdo->prepare("SELECT id FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Owner not found']);
            return;
        }

        // Delete the owner
        $stmt = $pdo->prepare("DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $result = $stmt->execute([$id]);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Owner deleted successfully']);
        } else {
            throw new Exception('Failed to delete owner');
        }

    } catch (Exception $e) {
        error_log("Delete owner error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete owner']);
    }
}
?>
