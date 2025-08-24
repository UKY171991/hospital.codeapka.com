<?php
// Simple API test script
header("Content-Type: application/json");

// Test data
$testData = [
    "name" => "Test Category",
    "description" => "This is a test category"
];

// Test creating a category
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/umakant/patho_api/categories.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo json_encode([
    "status" => "success",
    "message" => "API test completed",
    "http_code" => $httpCode,
    "response" => json_decode($response)
]);
?>