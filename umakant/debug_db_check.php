<?php
// Debug script to check database connection and tables
require_once 'inc/connection.php';

header('Content-Type: application/json');

try {
    // Check connection
    echo "Database connection: OK\n";
    
    // Check what tables exist
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Available tables:\n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }
    
    // Check if required tables exist
    $requiredTables = ['tests', 'test_categories', 'patients', 'entries', 'doctors', 'users'];
    $missingTables = [];
    
    foreach ($requiredTables as $requiredTable) {
        if (!in_array($requiredTable, $tables)) {
            $missingTables[] = $requiredTable;
        }
    }
    
    if (!empty($missingTables)) {
        echo "\nMISSING TABLES:\n";
        foreach ($missingTables as $missing) {
            echo "- $missing\n";
        }
    }
    
    // Check tests table structure if it exists
    if (in_array('tests', $tables)) {
        echo "\nTests table structure:\n";
        $stmt = $pdo->query("DESCRIBE tests");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            echo "- {$column['Field']} ({$column['Type']})\n";
        }
        
        // Check count
        $stmt = $pdo->query("SELECT COUNT(*) FROM tests");
        $count = $stmt->fetchColumn();
        echo "Tests count: $count\n";
    }
    
    // Check test_categories table
    if (in_array('test_categories', $tables)) {
        echo "\nTest categories table structure:\n";
        $stmt = $pdo->query("DESCRIBE test_categories");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            echo "- {$column['Field']} ({$column['Type']})\n";
        }
        
        // Check count
        $stmt = $pdo->query("SELECT COUNT(*) FROM test_categories");
        $count = $stmt->fetchColumn();
        echo "Test categories count: $count\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
