<?php
// Simple test script to debug the entry API
session_start();

// Set a test user session if not already set
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'test_user';
    $_SESSION['role'] = 'admin';
}

// Test the API
$_GET['action'] = 'get';
$_GET['id'] = '17';

echo "Testing entry API with action=get&id=17\n";
echo "Session data: " . print_r($_SESSION, true) . "\n";
echo "GET data: " . print_r($_GET, true) . "\n";
echo "Response:\n";

// Capture output
ob_start();
include 'ajax/entry_api_fixed.php';
$output = ob_get_clean();

echo $output;
echo "\n\nOutput length: " . strlen($output);
echo "\nIs valid JSON: " . (json_decode($output) !== null ? 'Yes' : 'No');
?>