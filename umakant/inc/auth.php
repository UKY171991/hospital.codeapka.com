<?php
// inc/auth.php - Role-based authentication guard
if (session_status() === PHP_SESSION_NONE) session_start();

// allowlist: don't redirect AJAX/API requests or the login page
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$isAjaxHeader = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$allowPatterns = [
    'login.php',
    'register.php',
    'forgot_password.php',
    'reset_password.php',
    '/patho_api/',
    '/ajax/',
    '/assets/',
    '/api/',
];

$allowed = $isAjaxHeader;
foreach ($allowPatterns as $pat) {
    if (stripos($requestUri, $pat) !== false) { $allowed = true; break; }
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) && !$allowed) {
    header('Location: login.php');
    exit;
}

// Role-based access control
// Only 'master' and 'admin' roles can access the admin panel
// 'user' role is blocked from accessing the admin panel
if (isset($_SESSION['user_id']) && !$allowed) {
    $userRole = $_SESSION['role'] ?? 'user';
    
    // Block 'user' role from accessing admin panel
    if ($userRole === 'user') {
        session_destroy();
        header('Location: login.php?error=access_denied');
        exit;
    }
    
    // Only 'master' and 'admin' can proceed
    if (!in_array($userRole, ['master', 'admin'])) {
        session_destroy();
        header('Location: login.php?error=invalid_role');
        exit;
    }
}

// Helper function to check if current user is master
function isMaster() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'master';
}

// Helper function to check if current user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Helper function to get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Helper function to get users under current admin
function getUsersUnderAdmin($pdo) {
    if (isMaster()) {
        // Master can see all users
        return null; // null means no filter
    } elseif (isAdmin()) {
        // Admin can see only users added by them
        $adminId = getCurrentUserId();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE added_by = ?");
        $stmt->execute([$adminId]);
        $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $users[] = $adminId; // Include admin themselves
        return $users;
    } else {
        // Regular user can only see their own data
        $userId = getCurrentUserId();
        return $userId ? [$userId] : [];
    }
}
