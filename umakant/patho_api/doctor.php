<?php
/**
 * Doctor API - Comprehensive CRUD operations for doctors
 * Supports: CREATE, READ, UPDATE, DELETE operations with statistics
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
require_once __DIR__ . '/../inc/simple_auth.php';

// Entity Configuration for Doctors
$entity_config = [
    'table_name' => 'doctors',
    'id_field' => 'id',
    'required_fields' => ['name'],
    'allowed_fields' => [
        'server_id', 'name', 'qualification', 'specialization', 'hospital',
        'contact_no', 'phone', 'percent', 'email', 'address', 'registration_no', 'added_by'
    ],
    'list_fields' => 'd.id, d.server_id, d.name, d.hospital, d.contact_no, d.phone, d.email, d.percent, d.added_by, u.username as added_by_username, d.created_at',
    'get_fields' => 'd.*, u.username as added_by_username'
];

// Authenticate user at the beginning of the script
$user_data = simpleAuthenticate($pdo);
if (!$user_data) {
    json_response(['success' => false, 'message' => 'Authentication required', 'debug_info' => getAuthDebugInfo()], 401);
}

// Determine action - Fixed to properly handle explicit action parameters
$requestMethod = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? null;

// If no explicit action is provided, determine from HTTP method and parameters
if (!$action) {
    if ($requestMethod === 'GET' && isset($_GET['id'])) {
        $action = 'get';
    } elseif ($requestMethod === 'GET' && !isset($_GET['id'])) {
        $action = 'list';
    } elseif ($requestMethod === 'POST' || $requestMethod === 'PUT') {
        $action = 'save';
    } elseif ($requestMethod === 'DELETE') {
        $action = 'delete';
    } else {
        $action = 'list'; // default fallback
    }
}

// Debug: Log the determined action and store for debug endpoint
error_log("Doctor API: Processing action '$action' for method '$requestMethod'");
$GLOBALS['current_action'] = $action;

try {
    switch($action) {
        case 'list':
        case 'simple_list':
            handleList($pdo, $entity_config, $user_data, $action === 'simple_list');
            break;
        case 'get':
            handleGet($pdo, $entity_config, $user_data);
            break;
        case 'save':
            handleSave($pdo, $entity_config, $user_data);
            break;
        case 'specializations':
            handleSpecializations($pdo, $user_data);
            break;
        case 'hospitals':
            handleHospitals($pdo, $user_data);
            break;
        case 'delete':
            handleDelete($pdo, $entity_config, $user_data);
            break;
        case 'stats':
            handleStats($pdo, $user_data);
            break;
        case 'cleanup_duplicates':
            handleCleanupDuplicates($pdo, $user_data);
            break;
        case 'debug':
            handleDebug($pdo, $user_data);
            break;
        default:
            json_response(['success' => false, 'message' => 'Invalid action specified'], 400);
    }
} catch (Exception $e) {
    error_log("Doctor API Uncaught Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An internal server error occurred.'], 500);
}

function handleList($pdo, $config, $user_data, $simpleList = false) {
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list doctors'], 403);
    }

    if ($simpleList) {
        $stmt = $pdo->query('SELECT id, name FROM doctors ORDER BY name ASC');
        json_response(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        return;
    }

    $scopeIds = getScopedUserIds($pdo, $user_data);
    $where = '';
    $params = [];
    if (is_array($scopeIds)) {
        $placeholders = implode(',', array_fill(0, count($scopeIds), '?'));
        $where = ' WHERE d.added_by IN (' . $placeholders . ')';
        $params = $scopeIds;
    }

    $draw = $_POST['draw'] ?? 1;
    $start = $_POST['start'] ?? 0;
    $length = $_POST['length'] ?? 25;
    $search = $_POST['search']['value'] ?? '';

    $baseQuery = "FROM {$config['table_name']} d LEFT JOIN users u ON d.added_by = u.id";
    $whereClause = $where;

    if (!empty($search)) {
        $whereClause .= (empty($where) ? ' WHERE ' : ' AND ') . "(d.name LIKE ? OR d.specialization LIKE ? OR d.hospital LIKE ?)";
        $searchTerm = "%$search%";
        array_push($params, $searchTerm, $searchTerm, $searchTerm);
    }

    $totalStmt = $pdo->query("SELECT COUNT(*) FROM {$config['table_name']}");
    $totalRecords = $totalStmt->fetchColumn();

    $filteredStmt = $pdo->prepare("SELECT COUNT(*) $baseQuery $whereClause");
    $filteredStmt->execute($params);
    $filteredRecords = $filteredStmt->fetchColumn();

    $dataQuery = "SELECT {$config['list_fields']} $baseQuery $whereClause ORDER BY d.id DESC LIMIT $start, $length";
    $dataStmt = $pdo->prepare($dataQuery);
    $dataStmt->execute($params);
    $data = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

    json_response([
        'draw' => intval($draw),
        'recordsTotal' => intval($totalRecords),
        'recordsFiltered' => intval($filteredRecords),
        'success' => true,
        'data' => $data
    ]);
}

function handleGet($pdo, $config, $user_data) {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Doctor ID is required'], 400);
    }

    $stmt = $pdo->prepare("SELECT {$config['get_fields']} FROM {$config['table_name']} d LEFT JOIN users u ON d.added_by = u.id WHERE d.id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        json_response(['success' => false, 'message' => 'Doctor not found'], 404);
    }

    $scopeIds = getScopedUserIds($pdo, $user_data);
    if (is_array($scopeIds) && !in_array((int)$row['added_by'], $scopeIds, true)) {
        json_response(['success' => false, 'message' => 'Permission denied to view this doctor'], 403);
    }

    json_response(['success' => true, 'data' => $row]);
}

function handleSave($pdo, $config, $user_data) {
    if (!simpleCheckPermission($user_data, 'save')) {
        json_response(['success' => false, 'message' => 'Permission denied to save doctors'], 403);
    }

    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = $input['id'] ?? null;
    $server_id = $input['server_id'] ?? null;

    foreach ($config['required_fields'] as $field) {
        if (empty($input[$field])) {
            json_response(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'], 400);
        }
    }

    $data = array_intersect_key($input, array_flip($config['allowed_fields']));
    
    // For updates, be more aggressive about preventing duplicates
    if ($id) {
        // First check if this exact record exists
        $stmt = $pdo->prepare("SELECT * FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$existing) {
            json_response(['success' => false, 'message' => 'Doctor not found for update'], 404);
        }
        
        $scopeIds = getScopedUserIds($pdo, $user_data);
        if (is_array($scopeIds) && !in_array((int)$existing['added_by'], $scopeIds, true)) {
            json_response(['success' => false, 'message' => 'Permission denied to update this doctor'], 403);
        }
        
        // Check for duplicates excluding current record
        $duplicateCheck = checkForDuplicates($pdo, $data, $user_data['user_id'], $server_id, $id);
        
        if ($duplicateCheck['has_duplicates']) {
            // If duplicates exist, update the primary existing record instead of creating new
            $existingId = $duplicateCheck['existing_id'];
            if ($existingId && $existingId != $id) {
                // Update the existing record instead of the current one
                $id = $existingId;
                // Clean up the old record
                deleteDoctorRecord($pdo, $config['table_name'], $config['id_field'], $input['id'], $user_data['user_id']);
            }
        }
    } else {
        // For new records, check duplicates normally
        $duplicateCheck = checkForDuplicates($pdo, $data, $user_data['user_id'], $server_id, $id);
        
        if ($duplicateCheck['has_duplicates']) {
            // Clean up duplicates first
            cleanupDuplicates($pdo, $duplicateCheck['duplicate_ids'], $user_data['user_id']);
            
            // If we found an existing record to update
            if ($duplicateCheck['existing_id']) {
                $id = $duplicateCheck['existing_id'];
            }
        }
    }
    
    if ($id) { // Update existing record
        $stmt = $pdo->prepare("SELECT * FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$existing) {
            json_response(['success' => false, 'message' => 'Doctor not found'], 404);
        }
        
        $scopeIds = getScopedUserIds($pdo, $user_data);
        if (is_array($scopeIds) && !in_array((int)$existing['added_by'], $scopeIds, true)) {
            json_response(['success' => false, 'message' => 'Permission denied to update this doctor'], 403);
        }
        
        // Check if data has actually changed
        $hasChanges = false;
        foreach ($data as $key => $newValue) {
            $oldValue = $existing[$key] ?? null;
            if ($oldValue !== $newValue) {
                $hasChanges = true;
                break;
            }
        }
        
        if (!$hasChanges) {
            // No changes needed, return existing data
            json_response([
                'success' => true,
                'message' => 'Doctor data is already up to date',
                'data' => $existing,
                'id' => $id,
                'updated' => false
            ]);
        }
        
        // For updates, preserve original added_by and server_id
        unset($data['added_by']);
        unset($data['server_id']);
        
        $set_clause = implode(', ', array_map(fn($field) => "`$field` = ?", array_keys($data)));
        $sql = "UPDATE {$config['table_name']} SET $set_clause, updated_at = NOW() WHERE {$config['id_field']} = ?";
        $values = array_merge(array_values($data), [$id]);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        $doctor_id = $id;
        $action_status = 'updated';
        
    } else {
        // Insert new record
        $data['added_by'] = $data['added_by'] ?? $user_data['user_id'];
        
        $cols = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$config['table_name']} ($cols) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));
        $doctor_id = $pdo->lastInsertId();
        $action_status = 'inserted';
    }

    $stmt = $pdo->prepare("SELECT {$config['get_fields']} FROM {$config['table_name']} d LEFT JOIN users u ON d.added_by = u.id WHERE d.id = ?");
    $stmt->execute([$doctor_id]);
    $saved_doctor = $stmt->fetch(PDO::FETCH_ASSOC);

    json_response([
        'success' => true,
        'message' => "Doctor {$action_status} successfully",
        'data' => $saved_doctor,
        'id' => $doctor_id,
        'updated' => ($action_status === 'updated')
    ]);
}

function handleDelete($pdo, $config, $user_data) {
    // Enhanced permission check
    if (!simpleCheckPermission($user_data, 'delete')) {
        json_response([
            'success' => false, 
            'message' => 'Permission denied to delete doctors',
            'debug' => [
                'user_role' => $user_data['role'] ?? 'unknown',
                'user_id' => $user_data['user_id'] ?? 'unknown',
                'auth_method' => $user_data['auth_method'] ?? 'unknown'
            ]
        ], 403);
    }

    // Get and validate doctor ID
    $id = $_REQUEST['id'] ?? $_GET['id'] ?? $_POST['id'] ?? null;
    if (!$id || !is_numeric($id)) {
        json_response([
            'success' => false, 
            'message' => 'Valid doctor ID is required',
            'debug' => [
                'provided_id' => $id,
                'request_method' => $_SERVER['REQUEST_METHOD'],
                'available_params' => array_keys($_REQUEST)
            ]
        ], 400);
    }

    $id = (int)$id;

    // Check if doctor exists and get details
    try {
        $stmt = $pdo->prepare("SELECT id, name, added_by, created_at FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$doctor) {
            json_response([
                'success' => false, 
                'message' => 'Doctor not found',
                'debug' => [
                    'searched_id' => $id,
                    'table' => $config['table_name']
                ]
            ], 404);
        }

        // Check ownership permissions (unless master role)
        $scopeIds = getScopedUserIds($pdo, $user_data);
        if (is_array($scopeIds) && !in_array((int)$doctor['added_by'], $scopeIds, true)) {
            json_response([
                'success' => false, 
                'message' => 'Permission denied to delete this doctor',
                'debug' => [
                    'doctor_owner' => $doctor['added_by'],
                    'allowed_owners' => $scopeIds,
                    'user_role' => $user_data['role']
                ]
            ], 403);
        }

        // Check for foreign key constraints before deletion
        $constraintCheck = checkDoctorConstraints($pdo, $id);
        if (!$constraintCheck['can_delete']) {
            json_response([
                'success' => false,
                'message' => 'Cannot delete doctor: ' . $constraintCheck['reason'],
                'debug' => [
                    'constraints' => $constraintCheck['constraints'],
                    'doctor_name' => $doctor['name']
                ]
            ], 409);
        }

        // Perform the deletion
        $stmt = $pdo->prepare("DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $result = $stmt->execute([$id]);
        $rowsAffected = $stmt->rowCount();

        if ($result && $rowsAffected > 0) {
            json_response([
                'success' => true, 
                'message' => 'Doctor deleted successfully',
                'data' => [
                    'deleted_id' => $id,
                    'deleted_name' => $doctor['name'],
                    'rows_affected' => $rowsAffected
                ]
            ]);
        } else {
            json_response([
                'success' => false, 
                'message' => 'Failed to delete doctor - no rows affected',
                'debug' => [
                    'sql_result' => $result,
                    'rows_affected' => $rowsAffected,
                    'doctor_id' => $id
                ]
            ], 500);
        }

    } catch (PDOException $e) {
        error_log("Doctor Delete Error: " . $e->getMessage());
        json_response([
            'success' => false, 
            'message' => 'Database error occurred while deleting doctor',
            'debug' => [
                'error_code' => $e->getCode(),
                'sql_state' => $e->errorInfo[0] ?? 'unknown'
            ]
        ], 500);
    } catch (Exception $e) {
        error_log("Doctor Delete Unexpected Error: " . $e->getMessage());
        json_response([
            'success' => false, 
            'message' => 'An unexpected error occurred',
            'debug' => [
                'error_type' => get_class($e)
            ]
        ], 500);
    }
}

/**
 * Check if a doctor can be safely deleted (no foreign key constraints)
 */
function checkDoctorConstraints($pdo, $doctorId) {
    $constraints = [];
    $canDelete = true;
    $reason = '';

    try {
        // Check common tables that might reference doctors
        $tablesToCheck = [
            'patients' => 'doctor_id',
            'appointments' => 'doctor_id', 
            'prescriptions' => 'doctor_id',
            'consultations' => 'doctor_id',
            'reports' => 'doctor_id'
        ];

        foreach ($tablesToCheck as $table => $column) {
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE $column = ?");
                $stmt->execute([$doctorId]);
                $count = $stmt->fetchColumn();
                
                if ($count > 0) {
                    $constraints[] = [
                        'table' => $table,
                        'column' => $column,
                        'count' => $count
                    ];
                    $canDelete = false;
                    $reason .= "$count records in $table; ";
                }
            } catch (PDOException $e) {
                // Table might not exist, which is fine
                continue;
            }
        }

        if (!$canDelete) {
            $reason = "Referenced by: " . rtrim($reason, '; ');
        }

    } catch (Exception $e) {
        // If we can't check constraints, allow deletion but log the issue
        error_log("Warning: Could not check doctor constraints: " . $e->getMessage());
    }

    return [
        'can_delete' => $canDelete,
        'reason' => $reason,
        'constraints' => $constraints
    ];
}

function handleStats($pdo, $user_data) {
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to view stats'], 403);
    }

    $stats = [];
    $stmt = $pdo->query("SELECT COUNT(*) FROM doctors");
    $stats['total'] = (int) $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM doctors WHERE phone IS NOT NULL AND phone != ''");
    $stats['active'] = (int) $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(DISTINCT specialization) FROM doctors WHERE specialization IS NOT NULL AND specialization != ''");
    $stats['specializations'] = (int) $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(DISTINCT hospital) FROM doctors WHERE hospital IS NOT NULL AND hospital != ''");
    $stats['hospitals'] = (int) $stmt->fetchColumn();

    json_response(['success' => true, 'data' => $stats]);
}

/**
 * Return a list of distinct specializations from doctors
 */
function handleSpecializations($pdo, $user_data) {
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list specializations'], 403);
    }

    $stmt = $pdo->query("SELECT DISTINCT specialization FROM doctors WHERE specialization IS NOT NULL AND specialization != '' ORDER BY specialization");
    $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
    // Normalize to simple array of strings
    $data = array_values(array_filter(array_map('trim', $rows), fn($v) => $v !== ''));
    json_response(['success' => true, 'data' => $data]);
}

/**
 * Return a list of distinct hospitals from doctors
 */
function handleHospitals($pdo, $user_data) {
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list hospitals'], 403);
    }

    $stmt = $pdo->query("SELECT DISTINCT hospital FROM doctors WHERE hospital IS NOT NULL AND hospital != '' ORDER BY hospital");
    $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $data = array_values(array_filter(array_map('trim', $rows), fn($v) => $v !== ''));
    json_response(['success' => true, 'data' => $data]);
}

/**
 * Handle manual cleanup of duplicate doctor records
 */
function handleCleanupDuplicates($pdo, $user_data) {
    if (!simpleCheckPermission($user_data, 'delete')) {
        json_response(['success' => false, 'message' => 'Permission denied to cleanup duplicates'], 403);
    }

    $userId = $user_data['user_id'];
    $scopeIds = getScopedUserIds($pdo, $user_data);
    
    // Build user scope condition
    if (is_array($scopeIds)) {
        $placeholders = implode(',', array_fill(0, count($scopeIds), '?'));
        $userCondition = "added_by IN ($placeholders)";
        $userParams = $scopeIds;
    } else {
        $userCondition = "1=1"; // Admin can see all
        $userParams = [];
    }

    try {
        // Find potential duplicates by grouping on key fields
        $sql = "
            SELECT GROUP_CONCAT(id) as duplicate_ids, 
                   COUNT(*) as duplicate_count,
                   name, contact_no, email, registration_no, server_id,
                   MAX(created_at) as latest_created_at
            FROM doctors 
            WHERE $userCondition
            AND (name IS NOT NULL AND name != '' OR contact_no IS NOT NULL AND contact_no != '')
            GROUP BY name, contact_no, email, registration_no, server_id
            HAVING duplicate_count > 1
            ORDER BY duplicate_count DESC, latest_created_at DESC
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($userParams);
        $duplicateGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $totalDuplicates = 0;
        $totalCleaned = 0;
        $cleanupResults = [];
        
        foreach ($duplicateGroups as $group) {
            $duplicateIds = explode(',', $group['duplicate_ids']);
            $totalDuplicates += count($duplicateIds);
            
            // Keep the most recent one, delete the rest
            $primaryId = array_shift($duplicateIds); // Remove first (most recent) from duplicates
            
            if (!empty($duplicateIds)) {
                $cleanupResult = [
                    'group_info' => [
                        'name' => $group['name'],
                        'contact_no' => $group['contact_no'],
                        'duplicate_count' => $group['duplicate_count']
                    ],
                    'kept_id' => $primaryId,
                    'deleted_ids' => $duplicateIds,
                    'deleted_count' => 0
                ];
                
                // Delete duplicates one by one to check constraints
                foreach ($duplicateIds as $duplicateId) {
                    $constraintCheck = checkDoctorConstraints($pdo, $duplicateId);
                    if ($constraintCheck['can_delete']) {
                        $stmt = $pdo->prepare("DELETE FROM doctors WHERE id = ?");
                        $stmt->execute([$duplicateId]);
                        $deleted = $stmt->rowCount();
                        if ($deleted > 0) {
                            $totalCleaned++;
                            $cleanupResult['deleted_count']++;
                        }
                    } else {
                        $cleanupResult['skipped_ids'][] = [
                            'id' => $duplicateId,
                            'reason' => $constraintCheck['reason']
                        ];
                    }
                }
                
                $cleanupResults[] = $cleanupResult;
            }
        }
        
        json_response([
            'success' => true,
            'message' => "Duplicate cleanup completed",
            'data' => [
                'total_duplicate_groups' => count($duplicateGroups),
                'total_duplicates_found' => $totalDuplicates,
                'total_duplicates_cleaned' => $totalCleaned,
                'cleanup_results' => $cleanupResults
            ]
        ]);
        
    } catch (Exception $e) {
        error_log("Error in cleanup duplicates: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Error during cleanup: ' . $e->getMessage()], 500);
    }
}

/**
 * Debug endpoint to help troubleshoot API issues
 */
function handleDebug($pdo, $user_data) {
    $debugInfo = [
        'success' => true,
        'message' => 'Debug information',
        'authentication' => [
            'authenticated' => !empty($user_data),
            'user_data' => $user_data,
            'auth_method' => $user_data['auth_method'] ?? 'none'
        ],
        'request_info' => [
            'method' => $_SERVER['REQUEST_METHOD'],
            'determined_action' => $GLOBALS['current_action'] ?? 'not set',
            'explicit_action_param' => $_REQUEST['action'] ?? 'not provided',
            'query_params' => $_GET,
            'post_params' => $_POST,
            'request_params' => $_REQUEST,
            'headers' => getallheaders() ?: 'not available'
        ],
        'database_info' => [
            'connected' => !empty($pdo),
            'doctors_table_exists' => false,
            'sample_doctors' => []
        ],
        'permissions' => [
            'can_list' => simpleCheckPermission($user_data, 'list'),
            'can_save' => simpleCheckPermission($user_data, 'save'),
            'can_delete' => simpleCheckPermission($user_data, 'delete')
        ]
    ];

    // Check database info
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'doctors'");
        $debugInfo['database_info']['doctors_table_exists'] = $stmt->rowCount() > 0;
        
        if ($debugInfo['database_info']['doctors_table_exists']) {
            $stmt = $pdo->query("SELECT id, name, added_by FROM doctors LIMIT 3");
            $debugInfo['database_info']['sample_doctors'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stmt = $pdo->query("SELECT COUNT(*) FROM doctors");
            $debugInfo['database_info']['total_doctors'] = $stmt->fetchColumn();
        }
    } catch (Exception $e) {
        $debugInfo['database_info']['error'] = $e->getMessage();
    }

    json_response($debugInfo);
}

/**
 * Check for duplicate doctor records based on key fields
 */
function checkForDuplicates($pdo, $data, $userId, $serverId = null, $excludeId = null) {
    $duplicateFields = ['name', 'contact_no', 'email', 'registration_no'];
    $conditions = [];
    $params = [];
    
    // Build conditions for each field that has a value
    foreach ($duplicateFields as $field) {
        if (!empty($data[$field])) {
            $conditions[] = "$field = ?";
            $params[] = $data[$field];
        }
    }
    
    if (empty($conditions)) {
        return ['has_duplicates' => false, 'duplicate_ids' => [], 'existing_id' => null];
    }
    
    // Add user scope condition
    $conditions[] = "added_by = ?";
    $params[] = $userId;
    
    // Add server_id condition if provided
    if ($serverId !== null) {
        $conditions[] = "(server_id = ? OR server_id IS NULL)";
        $params[] = $serverId;
    }
    
    // Exclude current ID if updating
    if ($excludeId) {
        $conditions[] = "id != ?";
        $params[] = $excludeId;
    }
    
    $whereClause = implode(' AND ', $conditions);
    $sql = "SELECT id, name, contact_no, email, registration_no, server_id, created_at FROM doctors WHERE $whereClause ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($duplicates)) {
        return ['has_duplicates' => false, 'duplicate_ids' => [], 'existing_id' => null];
    }
    
    // Keep the most recent record as the primary one
    $primaryRecord = $duplicates[0];
    $duplicateIds = [];
    
    // Find all other duplicates (excluding the most recent one)
    foreach ($duplicates as $index => $record) {
        if ($index > 0) { // Skip the first (most recent) record
            $duplicateIds[] = $record['id'];
        }
    }
    
    return [
        'has_duplicates' => true,
        'duplicate_ids' => $duplicateIds,
        'existing_id' => $primaryRecord['id'],
        'primary_record' => $primaryRecord
    ];
}

/**
 * Clean up duplicate doctor records
 */
function deleteDoctorRecord($pdo, $table, $idField, $id, $userId) {
    $stmt = $pdo->prepare("DELETE FROM $table WHERE $idField = ? AND added_by = ?");
    return $stmt->execute([$id, $userId]);
}

function cleanupDuplicates($pdo, $duplicateIds, $userId) {
    if (empty($duplicateIds)) {
        return;
    }
    
    $placeholders = implode(',', array_fill(0, count($duplicateIds), '?'));
    
    try {
        $deletedCount = 0;
        // Check for foreign key constraints before deletion
        foreach ($duplicateIds as $doctorId) {
            $constraintCheck = checkDoctorConstraints($pdo, $doctorId);
            if (!$constraintCheck['can_delete']) {
                error_log("Cannot cleanup duplicate doctor ID $doctorId due to constraints: " . $constraintCheck['reason']);
                continue; // Skip this doctor but try others
            }
            
            // Delete this specific duplicate
            $stmt = $pdo->prepare("DELETE FROM doctors WHERE id = ? AND added_by = ?");
            $stmt->execute([$doctorId, $userId]);
            $deletedCount += $stmt->rowCount();
        }
        if ($deletedCount > 0) {
            error_log("Cleaned up $deletedCount duplicate doctor records for user $userId");
        }
        
    } catch (Exception $e) {
        error_log("Error cleaning up duplicate doctors: " . $e->getMessage());
    }
}

?>