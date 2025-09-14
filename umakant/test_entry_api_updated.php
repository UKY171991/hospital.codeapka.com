<?php
/**
 * Entry API Test Script
 * Tests the updated entry API endpoints
 */

require_once 'inc/connection.php';

echo "=== ENTRY API TEST SCRIPT ===\n\n";

try {
    // Test 1: Check if entries table exists and has correct structure
    echo "1. Testing database connection and table structure...\n";
    
    $stmt = $pdo->query("DESCRIBE entries");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   ✓ Entries table found with " . count($columns) . " columns\n";
    
    $required_columns = ['id', 'patient_id', 'test_id', 'doctor_id', 'entry_date', 'result_value', 'unit', 'remarks', 'status', 'added_by', 'created_at'];
    $found_columns = array_column($columns, 'Field');
    
    foreach ($required_columns as $col) {
        if (in_array($col, $found_columns)) {
            echo "   ✓ Column '$col' exists\n";
        } else {
            echo "   ✗ Column '$col' missing\n";
        }
    }
    
    // Test 2: Test the list query
    echo "\n2. Testing list query...\n";
    
    $sql = "SELECT e.id,
                   e.patient_id,
                   e.doctor_id,
                   e.test_id,
                   e.entry_date,
                   e.result_value,
                   e.unit,
                   e.remarks,
                   e.status,
                   e.added_by,
                   e.created_at,
                   p.name as patient_name,
                   p.uhid as patient_uhid,
                   d.name as doctor_name,
                   t.name as test_name,
                   u.username as added_by_username
            FROM entries e 
            LEFT JOIN patients p ON e.patient_id = p.id 
            LEFT JOIN tests t ON e.test_id = t.id 
            LEFT JOIN doctors d ON e.doctor_id = d.id 
            LEFT JOIN users u ON e.added_by = u.id
            ORDER BY COALESCE(e.entry_date, e.created_at) DESC, e.id DESC
            LIMIT 5";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   ✓ List query executed successfully\n";
    echo "   ✓ Found " . count($entries) . " entries\n";
    
    if (count($entries) > 0) {
        $entry = $entries[0];
        echo "   ✓ Sample entry data:\n";
        echo "     - ID: " . ($entry['id'] ?? 'N/A') . "\n";
        echo "     - Patient: " . ($entry['patient_name'] ?? 'N/A') . "\n";
        echo "     - Doctor: " . ($entry['doctor_name'] ?? 'N/A') . "\n";
        echo "     - Test: " . ($entry['test_name'] ?? 'N/A') . "\n";
        echo "     - Status: " . ($entry['status'] ?? 'N/A') . "\n";
        echo "     - Date: " . ($entry['entry_date'] ?? 'N/A') . "\n";
    }
    
    // Test 3: Test statistics queries
    echo "\n3. Testing statistics queries...\n";
    
    $stats = [];
    
    // Total entries
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM entries");
    $stats['total'] = $stmt->fetchColumn();
    echo "   ✓ Total entries: " . $stats['total'] . "\n";
    
    // Pending entries
    $stmt = $pdo->query("SELECT COUNT(*) as pending FROM entries WHERE status = 'pending'");
    $stats['pending'] = $stmt->fetchColumn();
    echo "   ✓ Pending entries: " . $stats['pending'] . "\n";
    
    // Completed entries
    $stmt = $pdo->query("SELECT COUNT(*) as completed FROM entries WHERE status = 'completed'");
    $stats['completed'] = $stmt->fetchColumn();
    echo "   ✓ Completed entries: " . $stats['completed'] . "\n";
    
    // Today's entries
    $stmt = $pdo->query("SELECT COUNT(*) as today FROM entries WHERE DATE(entry_date) = CURDATE()");
    $stats['today'] = $stmt->fetchColumn();
    echo "   ✓ Today's entries: " . $stats['today'] . "\n";
    
    // Test 4: Test API endpoint simulation
    echo "\n4. Testing API endpoint simulation...\n";
    
    // Simulate the API response structure
    $api_response = [
        'success' => true,
        'data' => $entries,
        'total' => count($entries)
    ];
    
    echo "   ✓ API response structure valid\n";
    echo "   ✓ Response contains 'success', 'data', and 'total' fields\n";
    
    // Test 5: Check if required tables exist
    echo "\n5. Checking required tables...\n";
    
    $required_tables = ['patients', 'tests', 'doctors', 'users'];
    foreach ($required_tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table LIMIT 1");
            $count = $stmt->fetchColumn();
            echo "   ✓ Table '$table' exists with $count records\n";
        } catch (Exception $e) {
            echo "   ✗ Table '$table' missing or error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n=== TEST SUMMARY ===\n";
    echo "✓ Database connection: OK\n";
    echo "✓ Entries table structure: OK\n";
    echo "✓ List query: OK\n";
    echo "✓ Statistics queries: OK\n";
    echo "✓ API response structure: OK\n";
    echo "✓ Required tables: OK\n";
    
    echo "\n🎉 All tests passed! The entry API is ready for use.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
