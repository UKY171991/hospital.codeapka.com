<?php
// Test Entry API with proper authentication
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulate API key authentication
$_SERVER['HTTP_X_API_KEY'] = 'hospital-api-secret-2024';
$_REQUEST['action'] = 'list';
$_SERVER['REQUEST_METHOD'] = 'GET';

// Set JSON headers
header('Content-Type: application/json; charset=utf-8');

echo "Testing Entry API...\n\n";

try {
    // Include the API file
    ob_start();
    include __DIR__ . '/patho_api/entry.php';
    $output = ob_get_clean();
    
    echo $output;
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
