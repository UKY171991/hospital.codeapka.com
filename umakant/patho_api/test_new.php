<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

try {
    require_once __DIR__ . '/../inc/connection.php';
    require_once __DIR__ . '/../inc/simple_auth.php';
    
    // Test authentication
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        echo json_encode([
            'success' => false,
            'message' => 'Authentication required'
        ]);
        exit;
    }
    
    // Simple test query
    $stmt = $pdo->query("SELECT id, name, price FROM tests LIMIT 5");
    $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $tests,
        'count' => count($tests)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>