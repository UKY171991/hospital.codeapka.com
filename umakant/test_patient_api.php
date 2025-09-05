<?php
// test_patient_api.php - Debug script to test patient API functionality
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Patient API Debug Test</h1>";

// Test if connection file exists
if (!file_exists('inc/connection.php')) {
    echo "<p>❌ Connection file not found</p>";
    exit;
}

try {
    require_once 'inc/connection.php';
    echo "<p>✅ Connection file loaded successfully</p>";
} catch (Exception $e) {
    echo "<p>❌ Error loading connection file: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h1>Patient API Debug Test</h1>";

// Test database connection
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM patients");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<h2>Database Connection: ✅ SUCCESS</h2>";
    echo "<p>Total patients in database: " . $result['total'] . "</p>";
} catch (Exception $e) {
    echo "<h2>Database Connection: ❌ FAILED</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    exit;
}

// Test if patients table exists and check columns
try {
    $stmt = $pdo->query("PRAGMA table_info(patients)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<h2>Patients Table Structure:</h2>";
    echo "<ul>";
    foreach ($columns as $column) {
        echo "<li>" . $column['name'] . " (" . $column['type'] . ")</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "<h2>Table Structure Check: ❌ FAILED</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

// Test getting some sample patients
try {
    $stmt = $pdo->prepare("SELECT * FROM patients LIMIT 5");
    $stmt->execute();
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Sample Patients Data:</h2>";
    if (empty($patients)) {
        echo "<p>No patients found in database</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr>";
        foreach (array_keys($patients[0]) as $column) {
            echo "<th style='padding: 8px; background: #f0f0f0;'>" . htmlspecialchars($column) . "</th>";
        }
        echo "</tr>";
        
        foreach ($patients as $patient) {
            echo "<tr>";
            foreach ($patient as $value) {
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($value ?: 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<h2>Sample Data Fetch: ❌ FAILED</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

// Test the actual API logic (simulating the handleList function)
echo "<h2>Testing API List Logic:</h2>";

$page = 1;
$limit = 10;
$offset = ($page - 1) * $limit;

try {
    // Count total records
    $countSql = "SELECT COUNT(*) as total FROM patients";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute();
    $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo "<p><strong>Total Count Query Result:</strong> " . $totalCount . "</p>";
    
    if ($totalCount == 0) {
        echo "<p>❌ No patients found - database is empty</p>";
    } else {
        // Get patients with pagination
        $sql = "SELECT * FROM patients ORDER BY id DESC LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p><strong>Paginated Query Result:</strong> " . count($patients) . " patients retrieved</p>";
        
        // Simulate the API response
        $totalPages = ceil($totalCount / $limit);
        $response = [
            'success' => true,
            'data' => $patients,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $totalCount,
                'per_page' => $limit
            ]
        ];
        
        echo "<h3>Simulated API Response:</h3>";
        echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ API Logic Test Failed: " . $e->getMessage() . "</p>";
}
?>
