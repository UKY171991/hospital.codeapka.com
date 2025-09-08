<?php
/**
 * Simple auth test script - test different authentication methods
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';

try {
    // Test authentication
    $user_data = authenticateApiUser($pdo);
    
    if (!$user_data) {
        http_response_code(401);
        echo json_encode([
            'success' => false, 
            'message' => 'Authentication failed',
            'request_info' => [
                'headers' => getallheaders(),
                'request_data' => $_REQUEST,
                'session' => $_SESSION
            ]
        ]);
        exit;
    }

    // Test permissions
    $permissions = [
        'read' => checkPermission($user_data, 'read'),
        'write' => checkPermission($user_data, 'write'),
        'delete' => checkPermission($user_data, 'delete')
    ];

    echo json_encode([
        'success' => true,
        'message' => 'Authentication successful',
        'user_data' => $user_data,
        'permissions' => $permissions,
        'timestamp' => date('Y-m-d H:i:s')
    ]);

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
