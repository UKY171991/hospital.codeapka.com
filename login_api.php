<?php
/**
 * Login API - User authentication and session management
 * Location: Root folder
 * Supports: LOGIN, LOGOUT, SESSION_CHECK, PASSWORD_RESET
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key, X-Api-Key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection and simple auth
try {
    require_once __DIR__ . '/umakant/inc/connection.php';
    require_once __DIR__ . '/umakant/inc/api_config.php';
    require_once __DIR__ . '/umakant/inc/simple_auth.php';
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
$action = $_REQUEST['action'] ?? 'login';

try {
    switch($action) {
        case 'login':
            // User login
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            $username = trim($data['username'] ?? '');
            $password = $data['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                json_response(['success' => false, 'message' => 'Username and password are required'], 400);
            }
            
            // Check user credentials
            $stmt = $pdo->prepare("
                SELECT id, username, password, role, is_active, full_name, email, 
                       created_at, updated_at, expire_date, last_login, added_by, user_type
                FROM users 
                WHERE username = ? AND is_active = 1
            ");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                json_response(['success' => false, 'message' => 'Invalid username or password'], 401);
            }
            
            // Check if account is expired
            if ($user['expire_date'] && strtotime($user['expire_date']) < time()) {
                json_response(['success' => false, 'message' => 'Account has expired'], 401);
            }
            
            // Verify password
            if (!password_verify($password, $user['password'])) {
                json_response(['success' => false, 'message' => 'Invalid username or password'], 401);
            }
            
            // Update last login
            $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW(), updated_at = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            // Remove sensitive data
            unset($user['password']);
            $user['is_active'] = (bool)$user['is_active'];
            $user['user_type'] = (int)$user['user_type'];
            
            json_response([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'session_id' => session_id(),
                    'expires_in' => 3600 // 1 hour
                ]
            ]);
            break;
            
        case 'health':
            // Simple health check
            $auth = function_exists('simpleAuthenticate') ? simpleAuthenticate($pdo) : null;
            json_response([
                'success' => true,
                'message' => 'Login API is healthy',
                'auth' => $auth ? ['method' => $auth['auth_method'] ?? null, 'role' => $auth['role'] ?? null] : null,
                'time' => date('Y-m-d H:i:s')
            ]);
            break;

        case 'logout':
            // User logout
            if (isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];
                
                // Clear session
                session_unset();
                session_destroy();
                
                json_response([
                    'success' => true,
                    'message' => 'Logout successful'
                ]);
            } else {
                json_response([
                    'success' => false,
                    'message' => 'No active session found'
                ], 400);
            }
            break;
            
        case 'check_session':
            // Check if user is logged in
            if (isset($_SESSION['user_id']) && $_SESSION['logged_in']) {
                // Get fresh user data
                $stmt = $pdo->prepare("
                    SELECT id, username, role, is_active, full_name, email, 
                           created_at, updated_at, expire_date, last_login, user_type
                    FROM users 
                    WHERE id = ? AND is_active = 1
                ");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$user) {
                    // User no longer exists or is inactive
                    session_unset();
                    session_destroy();
                    json_response(['success' => false, 'message' => 'Session invalid'], 401);
                }
                
                // Check if account is expired
                if ($user['expire_date'] && strtotime($user['expire_date']) < time()) {
                    session_unset();
                    session_destroy();
                    json_response(['success' => false, 'message' => 'Account has expired'], 401);
                }
                
                $user['is_active'] = (bool)$user['is_active'];
                $user['user_type'] = (int)$user['user_type'];
                
                json_response([
                    'success' => true,
                    'message' => 'Session is valid',
                    'data' => [
                        'user' => $user,
                        'session_id' => session_id(),
                        'login_time' => $_SESSION['login_time'] ?? null
                    ]
                ]);
            }

            // Allow secret_key auth as fallback for diagnostics
            if (function_exists('simpleAuthenticate')) {
                $auth = simpleAuthenticate($pdo);
                if ($auth) {
                    json_response([
                        'success' => true,
                        'message' => 'Authenticated via secret (no active PHP session)',
                        'data' => [
                            'user' => [ 'id' => $auth['user_id'] ?? 0, 'username' => $auth['username'] ?? 'api', 'role' => $auth['role'] ?? 'master' ],
                            'session_id' => null,
                            'login_time' => null
                        ]
                    ]);
                }
            }

            json_response(['success' => false, 'message' => 'No active session'], 401);
            break;
            
        case 'change_password':
            // Change user password
            if (!isset($_SESSION['user_id'])) {
                json_response(['success' => false, 'message' => 'Authentication required'], 401);
            }
            
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            $currentPassword = $data['current_password'] ?? '';
            $newPassword = $data['new_password'] ?? '';
            $confirmPassword = $data['confirm_password'] ?? '';
            
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                json_response(['success' => false, 'message' => 'All password fields are required'], 400);
            }
            
            if ($newPassword !== $confirmPassword) {
                json_response(['success' => false, 'message' => 'New passwords do not match'], 400);
            }
            
            if (strlen($newPassword) < 6) {
                json_response(['success' => false, 'message' => 'New password must be at least 6 characters long'], 400);
            }
            
            // Get current user
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($currentPassword, $user['password'])) {
                json_response(['success' => false, 'message' => 'Current password is incorrect'], 400);
            }
            
            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$hashedPassword, $_SESSION['user_id']]);
            
            json_response(['success' => true, 'message' => 'Password changed successfully']);
            break;
            
        case 'forgot_password':
            // Initiate password reset (basic implementation)
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            $username = trim($data['username'] ?? '');
            $email = trim($data['email'] ?? '');
            
            if (empty($username) && empty($email)) {
                json_response(['success' => false, 'message' => 'Username or email is required'], 400);
            }
            
            // Find user
            if (!empty($email)) {
                $stmt = $pdo->prepare("SELECT id, username, email, full_name FROM users WHERE email = ? AND is_active = 1");
                $stmt->execute([$email]);
            } else {
                $stmt = $pdo->prepare("SELECT id, username, email, full_name FROM users WHERE username = ? AND is_active = 1");
                $stmt->execute([$username]);
            }
            
            $user = $stmt->fetch();
            
            if (!$user) {
                // Don't reveal if user exists or not for security
                json_response(['success' => true, 'message' => 'If the account exists, password reset instructions have been sent']);
            }
            
            // Generate reset token (store in database in real implementation)
            $resetToken = bin2hex(random_bytes(32));
            $resetExpiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // In a real implementation, you would:
            // 1. Store the reset token in database with expiry
            // 2. Send email with reset link
            // For now, just return the token (NOT recommended for production)
            
            json_response([
                'success' => true,
                'message' => 'Password reset instructions have been sent',
                'debug' => [
                    'reset_token' => $resetToken,
                    'user_id' => $user['id'],
                    'expires_at' => $resetExpiry
                ]
            ]);
            break;
            
        case 'profile':
            // Get user profile
            if (!isset($_SESSION['user_id'])) {
                json_response(['success' => false, 'message' => 'Authentication required'], 401);
            }
            
            $stmt = $pdo->prepare("
                SELECT id, username, full_name, email, role, is_active, user_type,
                       created_at, updated_at, expire_date, last_login
                FROM users 
                WHERE id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                json_response(['success' => false, 'message' => 'User not found'], 404);
            }
            
            $user['is_active'] = (bool)$user['is_active'];
            $user['user_type'] = (int)$user['user_type'];
            
            json_response(['success' => true, 'data' => $user]);
            break;
            
        case 'update_profile':
            // Update user profile
            if (!isset($_SESSION['user_id'])) {
                json_response(['success' => false, 'message' => 'Authentication required'], 401);
            }
            
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            $updateFields = [];
            $updateData = [];
            
            $allowedFields = ['full_name', 'email'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = ?";
                    $updateData[] = $data[$field];
                }
            }
            
            if (empty($updateFields)) {
                json_response(['success' => false, 'message' => 'No valid fields to update'], 400);
            }
            
            $updateFields[] = "updated_at = NOW()";
            $updateData[] = $_SESSION['user_id'];
            
            $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($updateData);
            
            // Update session data if full_name was changed
            if (isset($data['full_name'])) {
                $_SESSION['full_name'] = $data['full_name'];
            }
            if (isset($data['email'])) {
                $_SESSION['email'] = $data['email'];
            }
            
            json_response(['success' => true, 'message' => 'Profile updated successfully']);
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
