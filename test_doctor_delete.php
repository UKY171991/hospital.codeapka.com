<?php
/**
 * Test script for doctor delete API
 * This will help us debug the delete functionality
 */

// Include necessary files
require_once __DIR__ . '/umakant/inc/connection.php';

echo "<h2>Doctor Delete API Test</h2>";

// Test 1: Check if doctors table exists and has data
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'doctors'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Doctors table exists<br>";
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM doctors");
        $count = $stmt->fetchColumn();
        echo "📊 Total doctors: $count<br>";
        
        if ($count > 0) {
            $stmt = $pdo->query("SELECT id, name, added_by FROM doctors LIMIT 5");
            $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<h3>Sample doctors:</h3>";
            foreach ($doctors as $doctor) {
                echo "ID: {$doctor['id']}, Name: {$doctor['name']}, Added by: {$doctor['added_by']}<br>";
            }
        }
    } else {
        echo "❌ Doctors table does not exist<br>";
    }
} catch (Exception $e) {
    echo "❌ Error checking doctors table: " . $e->getMessage() . "<br>";
}

// Test 2: Check for foreign key constraints
try {
    $stmt = $pdo->query("
        SELECT 
            TABLE_NAME,
            COLUMN_NAME,
            CONSTRAINT_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM 
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE 
            REFERENCED_TABLE_NAME = 'doctors'
            AND TABLE_SCHEMA = DATABASE()
    ");
    $constraints = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($constraints) > 0) {
        echo "<h3>⚠️ Foreign key constraints found:</h3>";
        foreach ($constraints as $constraint) {
            echo "Table: {$constraint['TABLE_NAME']}, Column: {$constraint['COLUMN_NAME']} references doctors.{$constraint['REFERENCED_COLUMN_NAME']}<br>";
        }
        echo "<p><strong>Note:</strong> These constraints might prevent doctor deletion if referenced data exists.</p>";
    } else {
        echo "✅ No foreign key constraints found on doctors table<br>";
    }
} catch (Exception $e) {
    echo "❌ Error checking constraints: " . $e->getMessage() . "<br>";
}

// Test 3: Test the API endpoint directly
echo "<h3>Testing API Endpoint:</h3>";

$testUrl = "http://" . $_SERVER['HTTP_HOST'] . "/umakant/patho_api/doctor.php";
echo "API URL: $testUrl<br>";

// Test authentication
$secretKey = 'hospital-api-secret-2024';
echo "Using secret key: $secretKey<br>";

// Test with a sample doctor ID (if exists)
try {
    $stmt = $pdo->query("SELECT id FROM doctors LIMIT 1");
    $testDoctor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($testDoctor) {
        $testId = $testDoctor['id'];
        echo "<h4>Testing delete for doctor ID: $testId</h4>";
        
        // Create a test request
        $postData = http_build_query([
            'action' => 'delete',
            'id' => $testId,
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
        
        echo "Request data: $postData<br>";
        echo "Making API call...<br>";
        
        // Note: Uncomment the line below to actually test the delete
        // $response = file_get_contents($testUrl, false, $context);
        // echo "Response: $response<br>";
        
        echo "⚠️ Delete test commented out to prevent accidental deletion. Uncomment line in test_doctor_delete.php to run actual test.<br>";
        
    } else {
        echo "❌ No doctors found to test with<br>";
    }
} catch (Exception $e) {
    echo "❌ Error testing API: " . $e->getMessage() . "<br>";
}

// Test 4: Manual delete test (safer)
echo "<h3>Manual Delete Test (Safe):</h3>";
try {
    // Create a test doctor first
    $stmt = $pdo->prepare("INSERT INTO doctors (name, added_by, created_at) VALUES (?, ?, NOW())");
    $stmt->execute(['Test Doctor for Delete', 1]);
    $testId = $pdo->lastInsertId();
    echo "✅ Created test doctor with ID: $testId<br>";
    
    // Now try to delete it
    $stmt = $pdo->prepare("DELETE FROM doctors WHERE id = ?");
    $result = $stmt->execute([$testId]);
    
    if ($result && $stmt->rowCount() > 0) {
        echo "✅ Successfully deleted test doctor<br>";
    } else {
        echo "❌ Failed to delete test doctor<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error in manual delete test: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h3>Recommendations:</h3>";
echo "<ul>";
echo "<li>✅ Use secret_key parameter: <code>secret_key=hospital-api-secret-2024</code></li>";
echo "<li>✅ Use X-Api-Key header: <code>X-Api-Key: hospital-api-secret-2024</code></li>";
echo "<li>✅ Ensure the doctor ID exists and user has permission to delete it</li>";
echo "<li>✅ Check for foreign key constraints that might prevent deletion</li>";
echo "</ul>";

echo "<h3>Sample API Calls:</h3>";
echo "<h4>Using cURL:</h4>";
echo "<pre>";
echo "curl -X POST '$testUrl' \\\n";
echo "  -H 'Content-Type: application/x-www-form-urlencoded' \\\n";
echo "  -H 'X-Api-Key: hospital-api-secret-2024' \\\n";
echo "  -d 'action=delete&id=DOCTOR_ID'";
echo "</pre>";

echo "<h4>Using URL parameters:</h4>";
echo "<pre>";
echo "$testUrl?action=delete&id=DOCTOR_ID&secret_key=hospital-api-secret-2024";
echo "</pre>";
?>