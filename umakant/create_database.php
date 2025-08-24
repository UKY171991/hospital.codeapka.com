<?php
// Database configuration
define('DB_PATH', __DIR__ . '/pathology_lab.db');

// Create connection
try {
    $conn = new PDO("sqlite:" . DB_PATH);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Database created successfully or already exists<br>";
    
    // Test creating a simple table
    $conn->exec("CREATE TABLE IF NOT EXISTS test_connection (id INTEGER PRIMARY KEY, test TEXT)");
    echo "Connection test table created successfully<br>";
    
    // Clean up test table
    $conn->exec("DROP TABLE IF EXISTS test_connection");
    echo "Test table cleaned up<br>";
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

echo "<p>Now run <a href='setup_database.php'>setup_database.php</a> to create tables.</p>";
?>