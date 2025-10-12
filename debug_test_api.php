<?php
// Debug script to test the test API locally
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting debug test...\n";

// Test database connection
try {
    require_once '/workspace/umakant/inc/connection.php';
    echo "Database connection: OK\n";
    
    // Test if tests table exists and has data
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM tests");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Tests table count: " . $result['count'] . "\n";
    
    // Test if categories table exists
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Categories table count: " . $result['count'] . "\n";
    
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

// Test authentication
try {
    require_once '/workspace/umakant/inc/simple_auth.php';
    echo "Simple auth loaded: OK\n";
    
    // Simulate a request with secret_key
    $_REQUEST['secret_key'] = 'hospital-api-secret-2024';
    $user_data = simpleAuthenticate($pdo);
    if ($user_data) {
        echo "Authentication: OK - " . json_encode($user_data) . "\n";
    } else {
        echo "Authentication: FAILED\n";
    }
    
} catch (Exception $e) {
    echo "Auth error: " . $e->getMessage() . "\n";
}

// Test the actual API functions
try {
    require_once '/workspace/umakant/inc/ajax_helpers.php';
    echo "Ajax helpers loaded: OK\n";
    
    // Test json_response function
    if (function_exists('json_response')) {
        echo "json_response function: OK\n";
    } else {
        echo "json_response function: MISSING\n";
    }
    
} catch (Exception $e) {
    echo "Ajax helpers error: " . $e->getMessage() . "\n";
}

echo "Debug test completed.\n";
?>