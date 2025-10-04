<?php
session_start();

// Simulate session for testing
$_SESSION['user_id'] = '1';
$_SESSION['role'] = 'master';

// Test GET request
$_GET['action'] = 'list';
$_GET['ajax'] = 1;

echo "Testing user API with GET request...\n";
echo "Session: " . json_encode($_SESSION) . "\n";
echo "GET params: " . json_encode($_GET) . "\n";

// Capture output
ob_start();
include 'ajax/user_api.php';
$output = ob_get_clean();

echo "API Response: " . $output . "\n";
