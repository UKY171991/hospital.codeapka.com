<?php
/**
 * API Issues Fix Script
 * This script addresses common API issues and provides debugging information
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Api-Key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../inc/connection.php';

// Test database connection
try {
    $stmt = $pdo->query('SELECT 1');
    $db_status = 'Connected';
} catch (Exception $e) {
    $db_status = 'Error: ' . $e->getMessage();
}

// Check API endpoints
$api_files = [
    'patient.php' => 'Patient Management API',
    'user.php' => 'User Management API',
    'doctor.php' => 'Doctor Management API',
    'test.php' => 'Test Management API',
    'entry.php' => 'Entry Management API',
    'dashboard.php' => 'Dashboard API'
];

$api_status = [];
foreach ($api_files as $file => $description) {
    $file_path = __DIR__ . '/' . $file;
    $api_status[$file] = [
        'description' => $description,
        'exists' => file_exists($file_path),
        'readable' => is_readable($file_path),
        'url' => 'https://hospital.codeapka.com/umakant/patho_api/' . $file
    ];
}

// Check authentication methods
$auth_methods = [
    'secret_key_param' => isset($_REQUEST['secret_key']),
    'x_api_key_header' => isset($_SERVER['HTTP_X_API_KEY']),
    'session_auth' => isset($_SESSION['user_id']),
    'bearer_token' => isset($_SERVER['HTTP_AUTHORIZATION'])
];

// Common API issues and solutions
$common_issues = [
    [
        'issue' => 'Module is required',
        'cause' => 'Missing action parameter or invalid endpoint',
        'solution' => 'Add action parameter: ?action=list or ?action=get&id=1',
        'example' => 'patient.php?action=list&secret_key=hospital-api-secret-2024'
    ],
    [
        'issue' => 'Authentication required',
        'cause' => 'Missing authentication credentials',
        'solution' => 'Add secret_key parameter or X-Api-Key header',
        'example' => 'patient.php?action=list&secret_key=hospital-api-secret-2024'
    ],
    [
        'issue' => 'User ID required',
        'cause' => 'Missing user_id parameter for user-specific data',
        'solution' => 'Add user_id parameter for testing',
        'example' => 'patient.php?action=list&user_id=1&secret_key=hospital-api-secret-2024'
    ],
    [
        'issue' => 'CORS errors',
        'cause' => 'Cross-origin request blocked',
        'solution' => 'APIs include CORS headers, use proper HTTP methods',
        'example' => 'Use POST for create/update, GET for read operations'
    ]
];

// API testing examples
$test_examples = [
    [
        'name' => 'List Patients',
        'method' => 'GET',
        'url' => 'patient.php?action=list&user_id=1&secret_key=hospital-api-secret-2024',
        'description' => 'Get all patients for user ID 1'
    ],
    [
        'name' => 'Get Single Patient',
        'method' => 'GET', 
        'url' => 'patient.php?action=get&id=1&user_id=1&secret_key=hospital-api-secret-2024',
        'description' => 'Get patient with ID 1'
    ],
    [
        'name' => 'Create Patient',
        'method' => 'POST',
        'url' => 'patient.php?action=save&user_id=1&secret_key=hospital-api-secret-2024',
        'body' => json_encode([
            'name' => 'Test Patient',
            'mobile' => '9876543210',
            'age' => '30',
            'gender' => 'Male',
            'address' => 'Test Address'
        ]),
        'description' => 'Create a new patient'
    ],
    [
        'name' => 'Dashboard Overview',
        'method' => 'GET',
        'url' => 'dashboard.php?action=overview&user_id=1&secret_key=hospital-api-secret-2024',
        'description' => 'Get dashboard overview for user ID 1'
    ]
];

// Response
$response = [
    'success' => true,
    'message' => 'API Diagnostic Report',
    'timestamp' => date('Y-m-d H:i:s'),
    'database_status' => $db_status,
    'api_endpoints' => $api_status,
    'authentication_methods' => $auth_methods,
    'common_issues' => $common_issues,
    'test_examples' => $test_examples,
    'base_url' => 'https://hospital.codeapka.com/umakant/patho_api/',
    'secret_key' => 'hospital-api-secret-2024',
    'recommended_testing_steps' => [
        '1. Test with secret_key parameter for authentication',
        '2. Always include user_id parameter for user-specific APIs',
        '3. Use proper HTTP methods (GET for read, POST for write)',
        '4. Check action parameter is correctly specified',
        '5. Verify JSON content-type for POST requests'
    ]
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>