<?php
/**
 * Fixed Test API - Demonstrates proper authentication handling
 * This shows how to fix the 401 authentication issues
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Use SQLite connection for local testing
require_once __DIR__ . '/../inc/connection_sqlite.php';

// Create users table if it doesn't exist
try {
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255),
            full_name VARCHAR(255),
            role VARCHAR(50) DEFAULT "user",
            is_active INTEGER DEFAULT 1,
            api_token VARCHAR(64),
            last_login DATETIME,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');
    
    // Create test users if they don't exist
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
        // Create admin user
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $adminToken = bin2hex(random_bytes(32));
        
        $stmt = $pdo->prepare('
            INSERT INTO users (username, password, full_name, role, is_active, api_token)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute(['admin', $adminPassword, 'System Administrator', 'admin', 1, $adminToken]);
        
        // Create regular user
        $userPassword = password_hash('user123', PASSWORD_DEFAULT);
        $userToken = bin2hex(random_bytes(32));
        
        $stmt = $pdo->prepare('
            INSERT INTO users (username, password, full_name, role, is_active, api_token)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute(['user', $userPassword, 'Test User', 'user', 1, $userToken]);
    }
    
} catch (Exception $e) {
    // Ignore setup errors for now
}

// Simple authentication function that works with the existing system
function simpleAuthenticate($pdo) {
    // Method 1: Check for Authorization header
    $headers = [];
    foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
            $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
            $headers[$name] = $value;
        }
    }
    
    // Check Bearer token
    if (isset($headers['Authorization']) && strpos($headers['Authorization'], 'Bearer ') === 0) {
        $token = substr($headers['Authorization'], 7);
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
    }
    
    // Method 2: Check for API key parameter
    $apiKey = $_REQUEST['api_key'] ?? null;
    if ($apiKey) {
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
    }
    
    // Method 3: Check for shared secret
    $sharedSecret = 'hospital-api-secret-2024';
    
    // Check X-Api-Key header
    if (isset($headers['X-Api-Key']) && $headers['X-Api-Key'] === $sharedSecret) {
        return [
            'user_id' => 1,
            'role' => 'master',
            'username' => 'api_system',
            'auth_method' => 'shared_secret_header'
        ];
    }
    
    // Check secret_key parameter
    $secretKey = $_REQUEST['secret_key'] ?? null;
    if ($secretKey === $sharedSecret) {
        return [
            'user_id' => 1,
            'role' => 'master',
            'username' => 'api_system',
            'auth_method' => 'shared_secret_param'
        ];
    }
    
    return null;
}

$action = $_REQUEST['action'] ?? 'test';

try {
    switch($action) {
        case 'test':
            $auth = simpleAuthenticate($pdo);
            
            if (!$auth) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Authentication required',
                    'debug_info' => [
                        'headers' => $headers ?? [],
                        'request_data' => $_REQUEST,
                        'available_methods' => [
                            '1. Add Authorization: Bearer <token> header',
                            '2. Add api_key=<token> parameter',
                            '3. Add X-Api-Key: hospital-api-secret-2024 header',
                            '4. Add secret_key=hospital-api-secret-2024 parameter'
                        ]
                    ]
                ]);
                exit;
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Authentication successful',
                'user_data' => $auth
            ]);
            break;
            
        case 'login':
            $method = $_SERVER['REQUEST_METHOD'];
            if ($method !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }
            
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            
            $username = trim($input['username'] ?? '');
            $password = $input['password'] ?? '';
            
            if ($username === '' || $password === '') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Username and password are required']);
                exit;
            }
            
            $stmt = $pdo->prepare('SELECT id, username, password, full_name, role, is_active, api_token FROM users WHERE username = ? LIMIT 1');
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user || !password_verify($password, $user['password'])) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
                exit;
            }
            
            if (!$user['is_active']) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'User account is inactive']);
                exit;
            }
            
            // Ensure API token exists
            if (empty($user['api_token'])) {
                $newToken = bin2hex(random_bytes(32));
                $upd = $pdo->prepare('UPDATE users SET api_token = ? WHERE id = ?');
                $upd->execute([$newToken, $user['id']]);
                $user['api_token'] = $newToken;
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role'],
                    'api_token' => $user['api_token']
                ]
            ]);
            break;
            
        case 'users':
            $stmt = $pdo->query('SELECT id, username, full_name, role, is_active, api_token FROM users');
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $users,
                'message' => 'Users retrieved successfully'
            ]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action: ' . $action]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>