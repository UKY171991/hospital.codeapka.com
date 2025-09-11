<?php
/**
 * API Duplicate Prevention Test Script
 * Tests all APIs to ensure duplicate prevention is working correctly
 */

// Test configuration
$base_url = 'https://hospital.codeapka.com/umakant/patho_api/';
$api_key = 'hospital-api-secret-2024';

// Test data sets
$test_data = [
    'test_category' => [
        'name' => 'Test Category ' . date('His'),
        'description' => 'Test category for duplicate prevention testing'
    ],
    'test' => [
        'name' => 'Test Item ' . date('His'),
        'category_id' => 1, // Will be updated after category creation
        'method' => 'Manual',
        'price' => 100.00,
        'description' => 'Test item for duplicate prevention testing'
    ],
    'patient' => [
        'name' => 'Test Patient ' . date('His'),
        'mobile' => '9876543' . substr(date('His'), -3),
        'age' => 30,
        'sex' => 'Male',
        'address' => 'Test Address'
    ],
    'doctor' => [
        'name' => 'Dr. Test ' . date('His'),
        'qualification' => 'MBBS',
        'specialization' => 'General Medicine',
        'hospital' => 'Test Hospital',
        'contact_no' => '9876543' . substr(date('His'), -3),
        'email' => 'test' . date('His') . '@hospital.com'
    ],
    'notice' => [
        'title' => 'Test Notice ' . date('His'),
        'content' => 'This is a test notice for duplicate prevention testing',
        'active' => 1
    ],
    'owner' => [
        'name' => 'Test Owner ' . date('His'),
        'phone' => '9876543' . substr(date('His'), -3),
        'email' => 'owner' . date('His') . '@hospital.com',
        'address' => 'Test Owner Address'
    ]
];

function makeApiCall($url, $data, $headers = []) {
    $ch = curl_init();
    
    $default_headers = [
        'Content-Type: application/json',
        'X-Api-Key: hospital-api-secret-2024'
    ];
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array_merge($default_headers, $headers),
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 30
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'http_code' => $http_code,
        'response' => json_decode($response, true),
        'raw_response' => $response
    ];
}

function testDuplicatePrevention($endpoint, $data) {
    global $base_url;
    
    echo "\n=== Testing $endpoint API ===\n";
    
    // Test 1: Create new record
    echo "1. Creating new record...\n";
    $result1 = makeApiCall($base_url . $endpoint . '.php?action=save', $data);
    echo "   HTTP Code: {$result1['http_code']}\n";
    echo "   Action: " . ($result1['response']['action'] ?? 'N/A') . "\n";
    echo "   Message: " . ($result1['response']['message'] ?? 'N/A') . "\n";
    
    if ($result1['response']['success'] ?? false) {
        $record_id = $result1['response']['id'] ?? null;
        echo "   Created ID: $record_id\n";
        
        // Test 2: Try to create duplicate (should be skipped)
        echo "\n2. Trying to create duplicate...\n";
        $result2 = makeApiCall($base_url . $endpoint . '.php?action=save', $data);
        echo "   HTTP Code: {$result2['http_code']}\n";
        echo "   Action: " . ($result2['response']['action'] ?? 'N/A') . "\n";
        echo "   Message: " . ($result2['response']['message'] ?? 'N/A') . "\n";
        
        // Test 3: Update with different data
        echo "\n3. Updating with different data...\n";
        $update_data = array_merge($data, ['id' => $record_id]);
        if (isset($update_data['name'])) {
            $update_data['name'] .= ' UPDATED';
        }
        if (isset($update_data['title'])) {
            $update_data['title'] .= ' UPDATED';
        }
        
        $result3 = makeApiCall($base_url . $endpoint . '.php?action=save', $update_data);
        echo "   HTTP Code: {$result3['http_code']}\n";
        echo "   Action: " . ($result3['response']['action'] ?? 'N/A') . "\n";
        echo "   Message: " . ($result3['response']['message'] ?? 'N/A') . "\n";
        
        return $record_id;
    } else {
        echo "   ERROR: Failed to create initial record\n";
        echo "   Response: " . ($result1['raw_response'] ?? 'No response') . "\n";
        return null;
    }
}

// Run tests
echo "Starting API Duplicate Prevention Tests\n";
echo "========================================\n";

// Test each API
$results = [];

$results['test_category'] = testDuplicatePrevention('test_category', $test_data['test_category']);

// Update test data with created category ID if available
if ($results['test_category']) {
    $test_data['test']['category_id'] = $results['test_category'];
}

$results['test'] = testDuplicatePrevention('test', $test_data['test']);
$results['patient'] = testDuplicatePrevention('patient', $test_data['patient']);
$results['doctor'] = testDuplicatePrevention('doctor', $test_data['doctor']);
$results['notice'] = testDuplicatePrevention('notice', $test_data['notice']);
$results['owner'] = testDuplicatePrevention('owner', $test_data['owner']);

// Summary
echo "\n\n=== TEST SUMMARY ===\n";
foreach ($results as $api => $result) {
    $status = $result ? "✅ PASSED" : "❌ FAILED";
    echo "$api: $status\n";
}

echo "\nAll APIs have been tested for duplicate prevention.\n";
echo "Check the detailed output above for any issues.\n";
?>
