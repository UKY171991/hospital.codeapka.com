<?php
// API Health Check
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Check if required files exist
$requiredFiles = [
    'db_connection.php',
    'categories.php',
    'tests.php',
    'patients.php',
    'doctors.php',
    'reports.php',
    'users.php',
    'entries.php'
];

$missingFiles = [];
foreach ($requiredFiles as $file) {
    if (!file_exists(__DIR__ . '/' . $file)) {
        $missingFiles[] = $file;
    }
}

// Check database connection
$dbStatus = "unknown";
try {
    require_once 'db_connection.php';
    // Test simple query
    $stmt = $conn->prepare("SELECT name FROM sqlite_master WHERE type='table' LIMIT 1");
    $stmt->execute();
    $dbStatus = "connected";
} catch (Exception $e) {
    $dbStatus = "error: " . $e->getMessage();
}

// Prepare response
$response = [
    "status" => empty($missingFiles) ? "healthy" : "unhealthy",
    "timestamp" => date('Y-m-d H:i:s'),
    "api_version" => "1.0.0",
    "endpoints" => [
        "total" => count($requiredFiles),
        "available" => count($requiredFiles) - count($missingFiles),
        "missing" => $missingFiles
    ],
    "database" => [
        "status" => $dbStatus
    ]
];

http_response_code(empty($missingFiles) ? 200 : 500);
echo json_encode($response, JSON_PRETTY_PRINT);
?>