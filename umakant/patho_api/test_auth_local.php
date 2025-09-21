<?php
/**
 * Local Authentication Test - Uses SQLite for testing
 * This will help identify and fix the 401 authentication issues
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Use SQLite connection for local testing
require_once __DIR__ . '/../inc/connection_sqlite.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
require_once __DIR__ . '/../inc/api_config.php';

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
    
    // Create a test admin user if none exists
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $adminToken = bin2hex(random_bytes(32));
        
        $stmt = $pdo->prepare('
            INSERT INTO users (username, password, full_name, role, is_active, api_token)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute(['admin', $adminPassword, 'System Administrator', 'admin', 1, $adminToken]);
        
        echo "Created test admin user: admin / admin123\n";
    }
    
    // Create a test regular user if none exists
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
        $userPassword = password_hash('user123', PASSWORD_DEFAULT);
        $userToken = bin2hex(random_bytes(32));
        
        $stmt = $pdo->prepare('
            INSERT INTO users (username, password, full_name, role, is_active, api_token)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute(['user', $userPassword, 'Test User', 'user', 1, $userToken]);
        
        echo "Created test user: user / user123\n";
    }
    
} catch (Exception $e) {
    echo "Error setting up users table: " . $e->getMessage() . "\n";
}

$action = $_REQUEST['action'] ?? 'test';

try {
    switch($action) {
        case 'test':
            // Test authentication
            $user_data = authenticateApiUser($pdo);
            
            if (!$user_data) {
                http_response_code(401);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Authentication failed',
                    'debug_info' => [
                        'headers' => getallheaders(),
                        'request_data' => $_REQUEST,
                        'session' => $_SESSION ?? [],
                        'api_secret_configured' => isset($PATHO_API_SECRET) ? 'YES' : 'NO',
                        'api_secret_value' => $PATHO_API_SECRET ?? 'NOT SET',
                        'default_user_id' => $PATHO_API_DEFAULT_USER_ID ?? 'NOT SET'
                    ]
                ]);
                exit;
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Authentication successful',
                'user_data' => $user_data,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            break;
            
        case 'login':
            // Test login endpoint
            $method = $_SERVER['REQUEST_METHOD'];
            if ($method !== 'POST') {
                json_response(['success' => false, 'message' => 'Method not allowed'], 405);
            }
            
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            
            $username = trim($input['username'] ?? '');
            $password = $input['password'] ?? '';
            
            if ($username === '' || $password === '') {
                json_response(['success' => false, 'message' => 'Username and password are required'], 400);
            }
            
            $stmt = $pdo->prepare('SELECT id, username, password, full_name, role, is_active, api_token FROM users WHERE username = ? LIMIT 1');
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                json_response(['success' => false, 'message' => 'Invalid credentials'], 401);
            }
            
            if (!$user['is_active']) {
                json_response(['success' => false, 'message' => 'User account is inactive'], 403);
            }
            
            if (!password_verify($password, $user['password'])) {
                json_response(['success' => false, 'message' => 'Invalid credentials'], 401);
            }
            
            // Ensure API token exists
            if (empty($user['api_token'])) {
                $newToken = bin2hex(random_bytes(32));
                $upd = $pdo->prepare('UPDATE users SET api_token = ? WHERE id = ?');
                $upd->execute([$newToken, $user['id']]);
                $user['api_token'] = $newToken;
            }
            
            json_response([
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
            // List users (for debugging)
            $stmt = $pdo->query('SELECT id, username, full_name, role, is_active, api_token FROM users');
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $users,
                'message' => 'Users retrieved successfully'
            ]);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action: ' . $action
            ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
        'line' => $e->getLine(),
        'file' => basename($e->getFile())
    ]);
}
?>
