<?php
// Comprehensive Entry API Debug Script
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Simulate the exact same request as the API testing interface
$_SERVER['REQUEST_METHOD'] = 'GET';
$_REQUEST['action'] = 'list';
$_SERVER['HTTP_X_API_KEY'] = 'hospital-api-secret-2024';

// Set headers as the testing interface would
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

echo "=== Entry API Debug Test ===\n";

// Test 1: Check if required files exist
echo "\n1. Checking required files:\n";
$required_files = [
    __DIR__ . '/inc/connection.php',
    __DIR__ . '/inc/ajax_helpers.php', 
    __DIR__ . '/inc/api_config.php',
    __DIR__ . '/patho_api/entry.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "✓ Found: " . basename($file) . "\n";
    } else {
        echo "✗ Missing: " . basename($file) . "\n";
    }
}

// Test 2: Check database connection
echo "\n2. Testing database connection:\n";
try {
    require_once __DIR__ . '/inc/connection.php';
    echo "✓ Database connection successful\n";
    
    // Test entries table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM entries");
    $result = $stmt->fetch();
    echo "✓ Entries table accessible, {$result['count']} records\n";
    
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
}

// Test 3: Check authentication function
echo "\n3. Testing authentication:\n";
try {
    require_once __DIR__ . '/inc/ajax_helpers.php';
    require_once __DIR__ . '/inc/api_config.php';
    
    $user_data = authenticateApiUser($pdo);
    if ($user_data) {
        echo "✓ Authentication successful: User ID {$user_data['user_id']}, Role: {$user_data['role']}, Method: {$user_data['auth_method']}\n";
    } else {
        echo "✗ Authentication failed\n";
    }
    
} catch (Exception $e) {
    echo "✗ Authentication error: " . $e->getMessage() . "\n";
}

// Test 4: Test Entry API directly
echo "\n4. Testing Entry API:\n";
try {
    ob_start();
    $error_output = '';
    
    // Capture any PHP errors
    set_error_handler(function($severity, $message, $file, $line) use (&$error_output) {
        $error_output .= "PHP Error: $message in $file on line $line\n";
    });
    
    // Include the Entry API
    include __DIR__ . '/patho_api/entry.php';
    
    // Restore error handler
    restore_error_handler();
    
    $api_output = ob_get_clean();
    
    if ($error_output) {
        echo "✗ PHP Errors found:\n" . $error_output . "\n";
    }
    
    echo "✓ API Response:\n" . $api_output . "\n";
    
} catch (Exception $e) {
    ob_end_clean();
    echo "✗ API Exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== End Debug Test ===\n";
?>
