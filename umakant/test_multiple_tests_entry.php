<?php
/**
 * Test Script for Multiple Tests Entry Functionality
 * This script demonstrates how to create an entry with multiple tests
 */

require_once __DIR__ . '/inc/connection_sqlite.php';
require_once __DIR__ . '/inc/ajax_helpers_fixed.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // First, let's create some sample data if it doesn't exist
    echo "=== Testing Multiple Tests Entry Functionality ===\n\n";
    
    // Check if we have patients
    $stmt = $pdo->query("SELECT COUNT(*) FROM patients");
    $patientCount = $stmt->fetchColumn();
    echo "Patients in database: $patientCount\n";
    
    if ($patientCount == 0) {
        // Insert sample patient
        $stmt = $pdo->prepare("INSERT INTO patients (name, uhid, mobile, age, sex) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['John Doe', 'UHID001', '9876543210', 35, 'Male']);
        echo "Created sample patient: John Doe\n";
    }
    
    // Check if we have tests
    $stmt = $pdo->query("SELECT COUNT(*) FROM tests");
    $testCount = $stmt->fetchColumn();
    echo "Tests in database: $testCount\n";
    
    if ($testCount == 0) {
        // Insert sample tests
        $tests = [
            ['Blood Sugar', 'mg/dL', 150.00],
            ['Blood Pressure', 'mmHg', 200.00],
            ['Cholesterol', 'mg/dL', 300.00],
            ['Hemoglobin', 'g/dL', 100.00]
        ];
        
        foreach ($tests as $test) {
            $stmt = $pdo->prepare("INSERT INTO tests (name, unit, price) VALUES (?, ?, ?)");
            $stmt->execute($test);
        }
        echo "Created sample tests: Blood Sugar, Blood Pressure, Cholesterol, Hemoglobin\n";
    }
    
    // Check if we have doctors
    $stmt = $pdo->query("SELECT COUNT(*) FROM doctors");
    $doctorCount = $stmt->fetchColumn();
    echo "Doctors in database: $doctorCount\n";
    
    if ($doctorCount == 0) {
        // Insert sample doctor
        $stmt = $pdo->prepare("INSERT INTO doctors (name, qualification, specialization) VALUES (?, ?, ?)");
        $stmt->execute(['Dr. Smith', 'MD', 'General Medicine']);
        echo "Created sample doctor: Dr. Smith\n";
    }
    
    // Check if we have users
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    echo "Users in database: $userCount\n";
    
    if ($userCount == 0) {
        // Insert sample user
        $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT), 'Administrator', 'admin']);
        echo "Created sample user: admin\n";
    }
    
    echo "\n=== Creating Entry with Multiple Tests ===\n";
    
    // Get sample data
    $stmt = $pdo->query("SELECT id FROM patients LIMIT 1");
    $patientId = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT id FROM doctors LIMIT 1");
    $doctorId = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT id FROM users LIMIT 1");
    $userId = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT id, name, price, unit FROM tests LIMIT 3");
    $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Using Patient ID: $patientId\n";
    echo "Using Doctor ID: $doctorId\n";
    echo "Using User ID: $userId\n";
    echo "Using Tests: " . implode(', ', array_column($tests, 'name')) . "\n\n";
    
    // Create entry with multiple tests
    $pdo->beginTransaction();
    
    try {
        // Create main entry
        $entryData = [
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'entry_date' => date('Y-m-d'),
            'status' => 'pending',
            'added_by' => $userId,
            'grouped' => 1,
            'tests_count' => count($tests),
            'price' => array_sum(array_column($tests, 'price')),
            'discount_amount' => 50.00,
            'total_price' => array_sum(array_column($tests, 'price')) - 50.00
        ];
        
        $entryFields = implode(', ', array_keys($entryData));
        $entryPlaceholders = ':' . implode(', :', array_keys($entryData));
        $sql = "INSERT INTO entries ($entryFields) VALUES ($entryPlaceholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($entryData);
        $entryId = $pdo->lastInsertId();
        
        echo "Created main entry with ID: $entryId\n";
        
        // Insert individual tests
        $testIds = [];
        $testNames = [];
        foreach ($tests as $test) {
            $testData = [
                'entry_id' => $entryId,
                'test_id' => $test['id'],
                'result_value' => rand(80, 120) . '.' . rand(0, 9), // Random result
                'unit' => $test['unit'],
                'status' => 'pending',
                'price' => $test['price'],
                'discount_amount' => 10.00,
                'total_price' => $test['price'] - 10.00
            ];
            
            $testFields = implode(', ', array_keys($testData));
            $testPlaceholders = ':' . implode(', :', array_keys($testData));
            $testSql = "INSERT INTO entry_tests ($testFields) VALUES ($testPlaceholders)";
            $testStmt = $pdo->prepare($testSql);
            $testStmt->execute($testData);
            
            $testIds[] = $test['id'];
            $testNames[] = $test['name'];
            
            echo "Added test: {$test['name']} with result: {$testData['result_value']} {$test['unit']}\n";
        }
        
        // Update entry with aggregated test data
        $updateSql = "UPDATE entries SET 
            test_ids = :test_ids,
            test_names = :test_names,
            updated_at = NOW()
            WHERE id = :entry_id";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([
            'test_ids' => implode(',', $testIds),
            'test_names' => implode(', ', $testNames),
            'entry_id' => $entryId
        ]);
        
        $pdo->commit();
        
        echo "\n=== Entry Created Successfully ===\n";
        echo "Entry ID: $entryId\n";
        echo "Tests Count: " . count($tests) . "\n";
        echo "Total Price: ₹" . $entryData['price'] . "\n";
        echo "Discount: ₹" . $entryData['discount_amount'] . "\n";
        echo "Final Price: ₹" . $entryData['total_price'] . "\n";
        
        // Verify the entry
        echo "\n=== Verifying Entry ===\n";
        $stmt = $pdo->prepare("SELECT * FROM entries WHERE id = ?");
        $stmt->execute([$entryId]);
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($entry) {
            echo "Entry found:\n";
            echo "- Patient ID: {$entry['patient_id']}\n";
            echo "- Doctor ID: {$entry['doctor_id']}\n";
            echo "- Grouped: {$entry['grouped']}\n";
            echo "- Tests Count: {$entry['tests_count']}\n";
            echo "- Test IDs: {$entry['test_ids']}\n";
            echo "- Test Names: {$entry['test_names']}\n";
        }
        
        // Verify individual tests
        echo "\n=== Verifying Individual Tests ===\n";
        $stmt = $pdo->prepare("SELECT et.*, t.name as test_name FROM entry_tests et JOIN tests t ON et.test_id = t.id WHERE et.entry_id = ?");
        $stmt->execute([$entryId]);
        $entryTests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($entryTests as $test) {
            echo "- {$test['test_name']}: {$test['result_value']} {$test['unit']} (₹{$test['total_price']})\n";
        }
        
        echo "\n=== Test Completed Successfully ===\n";
        echo "You can now test the entry page at: https://hospital.codeapka.com/umakant/entry-list.php\n";
        echo "Look for entry ID: $entryId in the list\n";
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
