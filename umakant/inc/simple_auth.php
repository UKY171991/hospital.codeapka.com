<?php
/**
 * Simple Authentication Helper - Works reliably without header issues
 * 
 * This function provides multiple authentication methods:
 * 1. secret_key parameter (most reliable)
 * 2. X-Api-Key header (if available)
 * 3. Session-based authentication
 * 4. Bearer token (if available)
 */

/**
 * Simple authentication function that works reliably
 * 
 * @param PDO $pdo Database connection
 * @return array|false User data if authenticated, false otherwise
 */
function simpleAuthenticate($pdo) {
    // Prefer configured secret from api_config if available
    $configuredSecret = null;
    if (isset($GLOBALS['PATHO_API_SECRET']) && !empty($GLOBALS['PATHO_API_SECRET'])) {
        $configuredSecret = $GLOBALS['PATHO_API_SECRET'];
    }
    $sharedSecret = $configuredSecret ?: 'hospital-api-secret-2024';
    // Method 1: Check for secret_key parameter (most reliable)
    $secretKey = $_REQUEST['secret_key'] ?? $_GET['secret_key'] ?? $_POST['secret_key'] ?? null;
    if ($secretKey && hash_equals($sharedSecret, $secretKey)) {
        return [
            'user_id' => 1,
            'role' => 'master',
            'username' => 'api_system',
            'auth_method' => 'secret_key_param'
        ];
    }
    
    // Method 2: Check for session-based authentication
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
        return [
            'user_id' => $_SESSION['user_id'],
            'role' => $_SESSION['role'],
            'username' => $_SESSION['username'] ?? 'session_user',
            'auth_method' => 'session'
        ];
    }
    
    // Method 3: Check headers (if available)
    $headers = [];
    foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
            $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
            $headers[$name] = $value;
        }
    }
    
    // Check X-Api-Key header
    if (isset($headers['X-Api-Key']) && hash_equals($sharedSecret, $headers['X-Api-Key'])) {
        return [
            'user_id' => 1,
            'role' => 'master',
            'username' => 'api_system',
            'auth_method' => 'x_api_key_header'
        ];
    }
    
    // Method 4: Check Bearer token (if available)
    if (isset($headers['Authorization']) && strpos($headers['Authorization'], 'Bearer ') === 0) {
        $token = substr($headers['Authorization'], 7);
        try {
            $stmt = $pdo->prepare('SELECT id, username, role FROM users WHERE api_token = ? AND is_active = 1');
            $stmt->execute([$token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                return [
                    'user_id' => $user['id'],
                    'role' => $user['role'],
                    'username' => $user['username'],
                    'auth_method' => 'bearer_token'
                ];
            }
        } catch (Exception $e) {
            // Token check failed, continue to other methods
        }
    }
    
    // Method 5: Check api_key parameter
    $apiKey = $_REQUEST['api_key'] ?? $_GET['api_key'] ?? $_POST['api_key'] ?? null;
    if ($apiKey) {
        try {
            $stmt = $pdo->prepare('SELECT id, username, role FROM users WHERE api_token = ? AND is_active = 1');
            $stmt->execute([$apiKey]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                return [
                    'user_id' => $user['id'],
                    'role' => $user['role'],
                    'username' => $user['username'],
                    'auth_method' => 'api_key_param'
                ];
            }
        } catch (Exception $e) {
            // API key check failed, continue
        }
    }
    
    // No authentication method worked
    return false;
}

/**
 * Simple permission check
 * 
 * @param array $user_data User data from authentication
 * @param string $action Action to check (list, save, delete, etc.)
 * @param int|null $resource_owner_id Owner of the resource (for ownership checks)
 * @return bool True if permission granted
 */
function simpleCheckPermission($user_data, $action, $resource_owner_id = null) {
    if (!$user_data || !isset($user_data['role'])) {
        return false;
    }
    
    $role = $user_data['role'];
    $user_id = $user_data['user_id'];
    
    // Master role can do everything
    if ($role === 'master') {
        return true;
    }
    
    // Admin role can do most things
    if ($role === 'admin') {
        return true;
    }
    
    // User role has limited permissions
    if ($role === 'user') {
        switch ($action) {
            case 'list':
            case 'get':
                return true; // Users can view data
            case 'save':
            case 'create':
            case 'update':
                // Users can create/update their own data
                return $resource_owner_id === null || $resource_owner_id == $user_id;
            case 'delete':
                // Users can only delete their own data
                return $resource_owner_id !== null && $resource_owner_id == $user_id;
            default:
                return false;
        }
    }
    
    // Default deny
    return false;
}

/**
 * JSON response helper
 * 
 * @param array $data Response data
 * @param int $status_code HTTP status code
 */
if (!function_exists('json_response')) {
    function json_response($data, $status_code = 200) {
        http_response_code($status_code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
}

/**
 * Get authentication debug info
 * 
 * @return array Debug information about available auth methods
 */
if (!function_exists('getAuthDebugInfo')) {
    function getAuthDebugInfo() {
        return [
            'available_auth_methods' => [
                '1. Add secret_key=hospital-api-secret-2024 parameter (RECOMMENDED)',
                '2. Add X-Api-Key: hospital-api-secret-2024 header',
                '3. Login with session authentication',
                '4. Add Authorization: Bearer <token> header',
                '5. Add api_key=<token> parameter'
            ],
            'current_request' => [
                'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
                'has_secret_key' => isset($_REQUEST['secret_key']),
                'has_session' => isset($_SESSION['user_id']),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]
        ];
    }
}
?>
