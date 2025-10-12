<?php
// Simple test script for the test API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/umakant/patho_api/test.php?action=list&secret_key=hospital-api-secret-2024');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo 'HTTP Code: ' . $http_code . PHP_EOL;
echo 'Response: ' . $response . PHP_EOL;
?>
