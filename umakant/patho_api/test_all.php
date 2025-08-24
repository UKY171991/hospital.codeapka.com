<?php
// Test all API endpoints
header("Content-Type: application/json");

$baseURL = "http://localhost/umakant/patho_api";

// Test data
$testData = [
    "categories" => [
        "name" => "Test Category",
        "description" => "This is a test category"
    ],
    "tests" => [
        "name" => "Test Test",
        "price" => 100.00,
        "unit" => "mg/dL"
    ],
    "patients" => [
        "name" => "Test Patient",
        "mobile" => "1234567890"
    ],
    "doctors" => [
        "name" => "Test Doctor",
        "specialization" => "General Physician"
    ],
    "users" => [
        "username" => "testuser",
        "password" => "testpassword",
        "full_name" => "Test User"
    ]
];

$results = [];

// Test each endpoint
foreach ($testData as $endpoint => $data) {
    $url = "$baseURL/$endpoint.php";
    
    // Test POST request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $results[$endpoint] = [
        "http_code" => $httpCode,
        "response" => json_decode($response, true)
    ];
    
    // If POST was successful, test GET request
    if ($httpCode == 200 && isset($results[$endpoint]['response']['id'])) {
        $getId = $results[$endpoint]['response']['id'];
        $getUrl = "$url?id=$getId";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $getUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $getResponse = curl_exec($ch);
        $getHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $results[$endpoint]['get'] = [
            "http_code" => $getHttpCode,
            "response" => json_decode($getResponse, true)
        ];
    }
}

echo json_encode([
    "status" => "success",
    "message" => "API tests completed",
    "results" => $results
], JSON_PRETTY_PRINT);
?>