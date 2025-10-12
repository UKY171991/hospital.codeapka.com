<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Centralized error handler
function handle_error($errno, $errstr, $errfile, $errline) {
    $error_data = [
        'success' => false,
        'message' => 'An unexpected error occurred.',
        'error' => [
            'type' => 'PHP Error',
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline
        ]
    ];
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($error_data);
    exit;
}
set_error_handler('handle_error');

// Centralized exception handler
function handle_exception($exception) {
    $error_data = [
        'success' => false,
        'message' => 'An unexpected exception occurred.',
        'error' => [
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]
    ];
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($error_data);
    exit;
}
set_exception_handler('handle_exception');

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
require_once __DIR__ . '/../inc/simple_auth.php';

try {
    // Test database connection
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM tests");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Test authentication
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        json_response([
            'success' => false,
            'message' => 'Authentication required',
            'debug_info' => getAuthDebugInfo()
        ], 401);
    }
    
    // Simple test query
    $stmt = $pdo->query("SELECT t.id, t.name, t.price, c.name as category_name FROM tests t LEFT JOIN categories c ON t.category_id = c.id ORDER BY t.id DESC LIMIT 10");
    $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    json_response([
        'success' => true,
        'data' => $tests,
        'count' => $result['count']
    ]);
    
} catch (Exception $e) {
    error_log("Test API Debug Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An internal server error occurred: ' . $e->getMessage()], 500);
}
?>