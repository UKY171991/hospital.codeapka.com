<?php
// Debug script to check test data in entries
require_once __DIR__ . '/inc/connection.php';

echo "<h2>Debug: Entry Test Data</h2>";

// Check entries table structure
echo "<h3>Entries Table Structure:</h3>";
try {
    $stmt = $pdo->query("DESCRIBE entries");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Key']}</td><td>{$col['Default']}</td></tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Check if entry_tests table exists
echo "<h3>Entry Tests Table:</h3>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'entry_tests'");
    $exists = $stmt->fetch();
    if ($exists) {
        echo "✓ entry_tests table exists<br>";
        
        // Show structure
        $stmt = $pdo->query("DESCRIBE entry_tests");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $col) {
            echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Key']}</td><td>{$col['Default']}</td></tr>";
        }
        echo "</table>";
        
        // Count records
        $stmt = $pdo->query("SELECT COUNT(*) FROM entry_tests");
        $count = $stmt->fetchColumn();
        echo "<p>Total entry_tests records: $count</p>";
        
        if ($count > 0) {
            // Show sample data
            $stmt = $pdo->query("SELECT * FROM entry_tests LIMIT 5");
            $samples = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<h4>Sample entry_tests data:</h4>";
            echo "<pre>" . print_r($samples, true) . "</pre>";
        }
    } else {
        echo "❌ entry_tests table does not exist";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Check sample entries
echo "<h3>Sample Entries Data:</h3>";
try {
    $stmt = $pdo->query("SELECT id, patient_id, test_names, tests_count, test_ids, grouped FROM entries ORDER BY id DESC LIMIT 10");
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Patient ID</th><th>Test Names</th><th>Tests Count</th><th>Test IDs</th><th>Grouped</th></tr>";
    foreach ($entries as $entry) {
        echo "<tr>";
        echo "<td>{$entry['id']}</td>";
        echo "<td>{$entry['patient_id']}</td>";
        echo "<td>" . ($entry['test_names'] ?? 'NULL') . "</td>";
        echo "<td>" . ($entry['tests_count'] ?? 'NULL') . "</td>";
        echo "<td>" . ($entry['test_ids'] ?? 'NULL') . "</td>";
        echo "<td>" . ($entry['grouped'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Check if there are any tests in the tests table
echo "<h3>Tests Table:</h3>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM tests");
    $count = $stmt->fetchColumn();
    echo "<p>Total tests available: $count</p>";
    
    if ($count > 0) {
        $stmt = $pdo->query("SELECT id, name FROM tests LIMIT 5");
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<h4>Sample tests:</h4>";
        foreach ($tests as $test) {
            echo "ID: {$test['id']}, Name: {$test['name']}<br>";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>