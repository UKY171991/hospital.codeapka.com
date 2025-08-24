<?php
// Test database connection and basic functionality
require_once 'inc/connection.php';

echo "<h1>Database Test</h1>";

// Test connection
try {
    // Test creating a table
    $conn->exec("CREATE TABLE IF NOT EXISTS test_table (id INTEGER PRIMARY KEY, name TEXT)");
    echo "<p>✓ Table creation test passed</p>";
    
    // Test inserting data
    $stmt = $conn->prepare("INSERT INTO test_table (name) VALUES (?)");
    $stmt->execute(["Test Entry"]);
    echo "<p>✓ Data insertion test passed</p>";
    
    // Test querying data
    $stmt = $conn->prepare("SELECT * FROM test_table WHERE name = ?");
    $stmt->execute(["Test Entry"]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "<p>✓ Data query test passed</p>";
        echo "<p>Retrieved: " . $result['name'] . "</p>";
    } else {
        echo "<p>✗ Data query test failed</p>";
    }
    
    // Test updating data
    $stmt = $conn->prepare("UPDATE test_table SET name = ? WHERE name = ?");
    $stmt->execute(["Updated Test Entry", "Test Entry"]);
    echo "<p>✓ Data update test passed</p>";
    
    // Test deleting data
    $stmt = $conn->prepare("DELETE FROM test_table WHERE name = ?");
    $stmt->execute(["Updated Test Entry"]);
    echo "<p>✓ Data deletion test passed</p>";
    
    // Test dropping table
    $conn->exec("DROP TABLE IF EXISTS test_table");
    echo "<p>✓ Table drop test passed</p>";
    
    echo "<h2>All tests passed!</h2>";
    
} catch (PDOException $e) {
    echo "<p>✗ Test failed with error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='setup_database.php'>Run Database Setup</a></p>";
?>