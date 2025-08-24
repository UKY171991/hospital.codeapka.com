<?php
// Check if database file exists and is writable
$dbPath = __DIR__ . '/pathology_lab.db';

echo "<h1>Database File Check</h1>";

if (file_exists($dbPath)) {
    echo "<p>✓ Database file exists</p>";
    
    if (is_writable($dbPath)) {
        echo "<p>✓ Database file is writable</p>";
    } else {
        echo "<p>✗ Database file is not writable. Please check file permissions.</p>";
    }
    
    // Check database connection
    try {
        $conn = new PDO("sqlite:" . $dbPath);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Test simple query
        $stmt = $conn->prepare("SELECT name FROM sqlite_master WHERE type='table'");
        $stmt->execute();
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<p>✓ Database connection successful</p>";
        echo "<p>Tables found: " . implode(', ', $tables) . "</p>";
        
    } catch(PDOException $e) {
        echo "<p>✗ Database connection failed: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>✗ Database file does not exist. Run <a href='create_database.php'>create_database.php</a> to create it.</p>";
}

echo "<p><a href='setup_database.php'>Run Database Setup</a></p>";
?>