<?php
/**
 * Quick test to verify the doctor delete fix
 */

echo "<h2>🔧 Doctor Delete Fix Test</h2>";

// Test the API endpoint with the exact same parameters as Postman
$apiUrl = "http://" . $_SERVER['HTTP_HOST'] . "/umakant/patho_api/doctor.php";
$secretKey = "hospital-api-secret-2024";

echo "<h3>Testing API Action Determination:</h3>";

// Test 1: POST with action=delete (this was the failing case)
echo "<h4>Test 1: POST with action=delete</h4>";
$postData = http_build_query([
    'action' => 'delete',
    'id' => '999', // Use a non-existent ID for safe testing
    'secret_key' => $secretKey
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n" .
                   "X-Api-Key: $secretKey\r\n",
        'content' => $postData
    ]
]);

echo "Request: POST $apiUrl<br>";
echo "Data: $postData<br>";
echo "Making API call...<br>";

try {
    $response = file_get_contents($apiUrl, false, $context);
    $responseData = json_decode($response, true);
    
    echo "<strong>Response:</strong><br>";
    echo "<pre>" . json_encode($responseData, JSON_PRETTY_PRINT) . "</pre>";
    
    if (isset($responseData['message']) && strpos($responseData['message'], 'Name is required') !== false) {
        echo "<div style='color: red; font-weight: bold;'>❌ STILL BROKEN: Getting 'Name is required' error</div>";
    } elseif (isset($responseData['message']) && strpos($responseData['message'], 'Doctor not found') !== false) {
        echo "<div style='color: green; font-weight: bold;'>✅ FIXED: Correctly processing delete action (doctor not found is expected for ID 999)</div>";
    } elseif (isset($responseData['message']) && strpos($responseData['message'], 'Permission denied') !== false) {
        echo "<div style='color: orange; font-weight: bold;'>⚠️ AUTHENTICATION ISSUE: Check secret key</div>";
    } else {
        echo "<div style='color: blue; font-weight: bold;'>ℹ️ UNEXPECTED RESPONSE: Check the response above</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>❌ ERROR: " . $e->getMessage() . "</div>";
}

echo "<hr>";

// Test 2: Debug endpoint to verify action determination
echo "<h4>Test 2: Debug endpoint</h4>";
$debugUrl = $apiUrl . "?action=debug&secret_key=" . urlencode($secretKey);
echo "Request: GET $debugUrl<br>";

try {
    $debugResponse = file_get_contents($debugUrl);
    $debugData = json_decode($debugResponse, true);
    
    echo "<strong>Debug Response (Authentication & Permissions):</strong><br>";
    if (isset($debugData['authentication'])) {
        echo "Authenticated: " . ($debugData['authentication']['authenticated'] ? 'Yes' : 'No') . "<br>";
        echo "Auth Method: " . ($debugData['authentication']['auth_method'] ?? 'None') . "<br>";
        echo "User Role: " . ($debugData['authentication']['user_data']['role'] ?? 'None') . "<br>";
    }
    
    if (isset($debugData['permissions'])) {
        echo "Can Delete: " . ($debugData['permissions']['can_delete'] ? 'Yes' : 'No') . "<br>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>❌ DEBUG ERROR: " . $e->getMessage() . "</div>";
}

echo "<hr>";
echo "<h3>📋 Summary:</h3>";
echo "<ul>";
echo "<li><strong>Issue:</strong> POST requests with action=delete were being treated as save operations</li>";
echo "<li><strong>Cause:</strong> Action determination logic was overriding explicit action parameter</li>";
echo "<li><strong>Fix:</strong> Modified action determination to respect explicit action parameter</li>";
echo "<li><strong>Expected:</strong> Delete requests should now work without requiring 'name' field</li>";
echo "</ul>";

echo "<h3>🔧 For Postman Testing:</h3>";
echo "<ul>";
echo "<li><strong>URL:</strong> $apiUrl</li>";
echo "<li><strong>Method:</strong> POST</li>";
echo "<li><strong>Headers:</strong> X-Api-Key: $secretKey</li>";
echo "<li><strong>Body (form-data):</strong></li>";
echo "<ul>";
echo "<li>action: delete</li>";
echo "<li>id: [doctor_id_to_delete]</li>";
echo "<li>secret_key: $secretKey (optional if using header)</li>";
echo "</ul>";
echo "</ul>";
?>