<?php
// inc/ajax_helpers.php
// Set CORS headers for development
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

function json_response($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Comprehensive authentication function for all APIs
 * Supports multiple authentication methods as described in documentation
 */
function authenticateApiUser($pdo) {
    global $_SESSION;
    
    // Include API config to ensure variables are loaded
    require_once __DIR__ . '/api_config.php';
    global $PATHO_API_SECRET, $PATHO_API_DEFAULT_USER_ID;
    
    // For debugging - uncomment to check values
    // error_log("DEBUG: PATHO_API_SECRET = " . ($PATHO_API_SECRET ?? 'NULL'));
    // error_log("DEBUG: PATHO_API_DEFAULT_USER_ID = " . ($PATHO_API_DEFAULT_USER_ID ?? 'NULL'));
    
    // Method 1: Check session cookie (PHPSESSID)
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        return [
            'user_id' => $_SESSION['user_id'],
            'role' => $_SESSION['role'] ?? 'user',
            'username' => $_SESSION['username'] ?? '',
            'auth_method' => 'session'
        ];
    }
    
    // Method 2: Check Authorization header for Bearer token
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        $authHeader = $headers['Authorization'];
        if (strpos($authHeader, 'Bearer ') === 0) {
            $token = substr($authHeader, 7); // Remove "Bearer " prefix
            $stmt = $pdo->prepare('SELECT id, username, role FROM users WHERE api_token = ? AND is_active = 1');
            $stmt->execute([$token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                return [
                    'user_id' => $user['id'],
                    'role' => $user['role'] ?? 'user',
                    'username' => $user['username'],
                    'auth_method' => 'bearer_token'
                ];
            }
        }
    }
    
    // Method 3: Check api_key request parameter
    $apiKey = $_REQUEST['api_key'] ?? null;
    if ($apiKey) {
        $stmt = $pdo->prepare('SELECT id, username, role FROM users WHERE api_token = ? AND is_active = 1');
        $stmt->execute([$apiKey]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            return [
                'user_id' => $user['id'],
                'role' => $user['role'] ?? 'user',
                'username' => $user['username'],
                'auth_method' => 'api_key_param'
            ];
        }
    }
    
    // Method 4: Check username/password in the same request (credential fallback)
    $username = $_REQUEST['username'] ?? null;
    $password = $_REQUEST['password'] ?? null;
    if ($username && $password) {
        $stmt = $pdo->prepare('SELECT id, username, role, password FROM users WHERE username = ? AND is_active = 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return [
                'user_id' => $user['id'],
                'role' => $user['role'] ?? 'user',
                'username' => $user['username'],
                'auth_method' => 'credentials'
            ];
        }
    }
    
    // Method 5: Check shared secret via X-Api-Key header (server-to-server)
    $sharedSecret = getenv('PATHO_API_SECRET') ?: 'hospital-api-secret-2024';
    $defaultUserId = getenv('PATHO_API_DEFAULT_USER_ID') !== false ? (int)getenv('PATHO_API_DEFAULT_USER_ID') : 1;
    
    if (isset($headers['X-Api-Key']) && !empty($sharedSecret)) {
        if ($headers['X-Api-Key'] === $sharedSecret) {
            return [
                'user_id' => $defaultUserId,
                'role' => 'master',
                'username' => 'api_system',
                'auth_method' => 'shared_secret_header'
            ];
        }
    }
    
    // Method 6: Check secret_key parameter (server-to-server)
    $secretKey = $_REQUEST['secret_key'] ?? null;
    if ($secretKey && !empty($sharedSecret)) {
        if ($secretKey === $sharedSecret) {
            return [
                'user_id' => $defaultUserId,
                'role' => 'master',
                'username' => 'api_system',
                'auth_method' => 'shared_secret_param'
            ];
        }
    }
    
    // No authentication found - return fallback for anonymous inserts if configured
    // For development/testing purposes, allow anonymous access with default user
    if (!empty($defaultUserId)) {
        return [
            'user_id' => $defaultUserId,
            'role' => 'admin', // Grant admin role for testing
            'username' => 'api_user',
            'auth_method' => 'fallback'
        ];
    }
    
    return null;
}

/**
 * Check if current user can perform specific action
 */
function checkPermission($auth, $action, $resourceOwnerId = null) {
    if (!$auth) return false;
    
    $userId = $auth['user_id'];
    $role = $auth['role'];
    
    // Map permission types to actions
    switch ($action) {
        case 'read':
        case 'list':
        case 'get':
            return true; // Anyone authenticated can view
            
        case 'write':
        case 'create':
        case 'save':
        case 'update':
            return true; // Anyone authenticated can create/modify
            
        case 'delete':
            // Masters and admins can delete anything, others can only delete their own records
            return ($role === 'master' || $role === 'admin') || ($resourceOwnerId && $userId == $resourceOwnerId);
            
        default:
            return false;
    }
}

/**
 * Find existing row by unique criteria, compare with provided data, and either skip, update or insert.
 *
 * @param PDO $pdo
 * @param string $table
 * @param array $uniqueWhere associative column=>value used to find existing row
 * @param array $data associative column=>value to insert/update
 * @return array ['action'=>'skipped'|'updated'|'inserted', 'id'=>int|null]
 */
function upsert_or_skip($pdo, $table, $uniqueWhere, $data) {
    // Build WHERE clause and params
    $whereParts = [];
    $params = [];
    foreach ($uniqueWhere as $col => $val) {
        $whereParts[] = "$col = ?";
        $params[] = $val;
    }
    $whereSql = implode(' AND ', $whereParts);

    $stmt = $pdo->prepare("SELECT * FROM $table WHERE $whereSql LIMIT 1");
    $stmt->execute($params);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Compare fields - if all provided data fields are identical to existing, skip
        $changed = [];
        foreach ($data as $col => $val) {
            // Do not allow upsert to change the original creator of the row by default
            if ($col === 'added_by') continue;
            // Normalize null/empty strings
            $e = isset($existing[$col]) ? $existing[$col] : null;
            if ((string)$e !== (string)$val) $changed[$col] = $val;
        }

        // If no other fields changed, consider whether added_by should be set when missing
        if (empty($changed)) {
            if (isset($data['added_by']) && ($data['added_by'] !== null && $data['added_by'] !== '') && (empty($existing['added_by']) || $existing['added_by'] === null || $existing['added_by'] === '')) {
                // Set added_by where it was previously empty
                $up = $pdo->prepare("UPDATE $table SET added_by = ?, updated_at = NOW() WHERE id = ?");
                $up->execute([$data['added_by'], $existing['id']]);
                return ['action'=>'updated','id'=>(int)$existing['id']];
            }
            return ['action'=>'skipped','id'=>(int)$existing['id']];
        }
        // Build UPDATE statement for changed fields
        $setParts = [];
        $setParams = [];
        foreach ($changed as $col => $val) {
            $setParts[] = "$col = ?";
            $setParams[] = $val;
        }

        // If added_by is provided in data and existing added_by is empty, include it in update
        if (isset($data['added_by']) && ($data['added_by'] !== null && $data['added_by'] !== '') && (empty($existing['added_by']) || $existing['added_by'] === null || $existing['added_by'] === '')) {
            $setParts[] = "added_by = ?";
            $setParams[] = $data['added_by'];
        }

        $setSql = implode(', ', $setParts);
        $setParams[] = $existing['id'];
        $up = $pdo->prepare("UPDATE $table SET $setSql, updated_at = NOW() WHERE id = ?");
        $up->execute($setParams);
        return ['action'=>'updated','id'=>(int)$existing['id']];
    }

    // Insert new row - build columns and placeholders
    $cols = array_keys($data);
    $placeholders = implode(', ', array_fill(0, count($cols), '?'));
    $colSql = implode(', ', $cols);
    $ins = $pdo->prepare("INSERT INTO $table ($colSql, created_at) VALUES ($placeholders, NOW())");
    $ins->execute(array_values($data));
    return ['action'=>'inserted','id'=> (int)$pdo->lastInsertId()];
}
