<?php
/**
 * Simple Test API - bypasses authentication for testing
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../inc/connection.php';

$action = $_REQUEST['action'] ?? 'list';

try {
    switch($action) {
        case 'list':
            $stmt = $pdo->prepare('SELECT id, name as test_name, category_id, price as rate FROM tests LIMIT 10');
            $stmt->execute();
            $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $tests,
                'count' => count($tests),
                'message' => 'Tests retrieved successfully'
            ]);
            break;
            
        case 'test_auth':
            // Include API config to test variables
            require_once __DIR__ . '/../inc/api_config.php';
            
            $headers = getallheaders();
            
            echo json_encode([
                'success' => true,
                'message' => 'Auth test endpoint',
                'data' => [
                    'headers' => $headers,
                    'secret_configured' => isset($PATHO_API_SECRET) ? 'YES' : 'NO',
                    'secret_value' => $PATHO_API_SECRET ?? 'NOT SET',
                    'default_user_id' => $PATHO_API_DEFAULT_USER_ID ?? 'NOT SET',
                    'x_api_key_header' => $headers['X-Api-Key'] ?? 'NOT PROVIDED',
                    'header_match' => ($headers['X-Api-Key'] ?? '') === ($PATHO_API_SECRET ?? ''),
                ]
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
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
