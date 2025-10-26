<?php
// Debug script to check what the API returns for entry #17
require_once 'umakant/inc/connection.php';
require_once 'umakant/inc/simple_auth.php';

// Start session
session_start();

echo "<h2>Debug Entry #17 API Response</h2>\n";

// Simulate the API call for getting entry #17
echo "<h3>1. Direct API simulation for entry #17:</h3>\n";

try {
    // Get entry data with tests (same as the API does)
    $sql = "SELECT e.*, 
                   p.name AS patient_name, p.uhid, p.age, p.sex AS gender, p.contact AS patient_contact, p.address AS patient_address,
                   d.name AS doctor_name, d.specialization AS doctor_specialization,
                   u.username AS added_by_username, u.full_name AS added_by_full_name
            FROM entries e 
            LEFT JOIN patients p ON e.patient_id = p.id 
            LEFT JOIN doctors d ON e.doctor_id = d.id 
            LEFT JOIN users u ON e.added_by = u.id
            WHERE e.id = 17";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $entry = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($entry) {
        echo "<pre>";
        echo "Entry data:\n";
        echo "  - ID: " . $entry['id'] . "\n";
        echo "  - Patient: " . $entry['patient_name'] . "\n";
        echo "  - Doctor: " . ($entry['doctor_name'] ?: 'None') . "\n";
        echo "</pre>";
        
        // Get tests for this entry (same as API does)
        $testSql = "SELECT et.*, 
                           t.name AS test_name, 
                           t.category_id,
                           t.unit, 
                           t.min, 
                           t.max,
                           t.min_male,
                           t.max_male,
                           t.min_female,
                           t.max_female,
                           t.reference_range,
                           c.name AS category_name
                    FROM entry_tests et
                    LEFT JOIN tests t ON et.test_id = t.id
                    LEFT JOIN categories c ON t.category_id = c.id
                    WHERE et.entry_id = 17
                    ORDER BY t.name";
        
        $testStmt = $pdo->prepare($testSql);
        $testStmt->execute();
        $tests = $testStmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>2. Tests data from API query:</h3>\n";
        echo "<pre>";
        echo "Found " . count($tests) . " tests:\n";
        foreach ($tests as $i => $test) {
            echo "Test " . ($i + 1) . ":\n";
            echo "  - Entry Test ID: " . $test['id'] . "\n";
            echo "  - Test ID: " . $test['test_id'] . "\n";
            echo "  - Test Name: '" . ($test['test_name'] ?: 'NULL') . "'\n";
            echo "  - Category: '" . ($test['category_name'] ?: 'NULL') . "'\n";
            echo "  - Unit: '" . ($test['unit'] ?: 'NULL') . "'\n";
            echo "  - Min: '" . ($test['min'] ?: 'NULL') . "'\n";
            echo "  - Max: '" . ($test['max'] ?: 'NULL') . "'\n";
            echo "  - Price: " . $test['price'] . "\n";
            echo "  - Result: '" . ($test['result_value'] ?: 'NULL') . "'\n";
            echo "\n";
        }
        echo "</pre>";
        
        // Format the response as the API would
        $entry['tests'] = $tests;
        $entry['tests_count'] = count($tests);
        
        echo "<h3>3. Final API response structure:</h3>\n";
        echo "<pre>";
        echo "Response structure:\n";
        echo "  - entry.id: " . $entry['id'] . "\n";
        echo "  - entry.tests_count: " . $entry['tests_count'] . "\n";
        echo "  - entry.tests: Array with " . count($entry['tests']) . " items\n";
        
        foreach ($entry['tests'] as $i => $test) {
            echo "    - tests[" . $i . "].test_id: " . $test['test_id'] . "\n";
            echo "    - tests[" . $i . "].test_name: '" . ($test['test_name'] ?: 'NULL') . "'\n";
            echo "    - tests[" . $i . "].price: " . $test['price'] . "\n";
        }
        echo "</pre>";
        
        // Check if there are any NULL test names
        $nullTestNames = array_filter($tests, function($test) {
            return empty($test['test_name']);
        });
        
        if (count($nullTestNames) > 0) {
            echo "<h3>⚠️ WARNING: Found tests with NULL names:</h3>\n";
            echo "<pre>";
            foreach ($nullTestNames as $test) {
                echo "  - Test ID " . $test['test_id'] . " has NULL test_name\n";
            }
            echo "</pre>";
            
            // Check the tests table directly for these IDs
            $testIds = array_column($nullTestNames, 'test_id');
            $placeholders = implode(',', array_fill(0, count($testIds), '?'));
            $checkStmt = $pdo->prepare("SELECT id, name FROM tests WHERE id IN ($placeholders)");
            $checkStmt->execute($testIds);
            $testTableData = $checkStmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<h3>4. Check tests table directly for NULL names:</h3>\n";
            echo "<pre>";
            foreach ($testTableData as $testData) {
                echo "  - Test ID " . $testData['id'] . " in tests table: '" . ($testData['name'] ?: 'NULL') . "'\n";
            }
            echo "</pre>";
        } else {
            echo "<h3>✅ All tests have valid names</h3>\n";
        }
        
    } else {
        echo "<pre>Entry #17 not found</pre>\n";
    }
    
} catch (Exception $e) {
    echo "<pre>Error: " . $e->getMessage() . "</pre>\n";
}

// Test the actual API endpoint
echo "<h3>5. Test actual API endpoint:</h3>\n";
echo "<pre>";
echo "You can test the actual API by visiting:\n";
echo "https://hospital.codeapka.com/umakant/ajax/entry_api_fixed.php?action=get&id=17\n";
echo "</pre>";

?>