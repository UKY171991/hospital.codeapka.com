<?php
// Simple test script to test the user API
echo "Testing User API...\n";

// Test 1: Try to access the API without authentication
echo "\n1. Testing without authentication:\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://hospital.codeapka.com/umakant/patho_api/user.php?action=list");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'User-Agent: PHP Test Script'
]);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $http_code\n";
echo "Response: " . substr($response, 0, 200) . "...\n";

// Test 2: Try to access with secret key authentication
echo "\n2. Testing with secret key authentication:\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://hospital.codeapka.com/umakant/patho_api/user.php?action=list&secret_key=hospital-api-secret-2024");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'User-Agent: PHP Test Script'
]);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $http_code\n";
echo "Response: " . substr($response, 0, 200) . "...\n";

echo "\nTest completed.\n";
?>
