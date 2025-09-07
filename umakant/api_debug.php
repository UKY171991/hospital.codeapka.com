<?php
// Simple debug endpoint to test basic data retrieval
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    require_once __DIR__ . '/inc/connection.php';
    
    // Test basic connection
    $test_connection = $pdo->query("SELECT 1")->fetchColumn();
    
    // Get categories count
    $categories_count = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    
    // Get tests count
    $tests_count = $pdo->query("SELECT COUNT(*) FROM tests")->fetchColumn();
    
    // Get sample test with category
    $sample_query = "SELECT t.id, t.name, t.category_id, c.name as category_name, t.price 
                     FROM tests t 
                     LEFT JOIN categories c ON t.category_id = c.id 
                     ORDER BY t.id DESC 
                     LIMIT 3";
    $sample_tests = $pdo->query($sample_query)->fetchAll();
    
    // Return simple response
    echo json_encode([
        'success' => true,
        'connection_test' => $test_connection,
        'categories_count' => $categories_count,
        'tests_count' => $tests_count,
        'sample_tests' => $sample_tests,
        'message' => 'Basic data retrieval test'
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>
