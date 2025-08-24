<?php
// Reset database script
$dbPath = __DIR__ . '/pathology_lab.db';

echo "<h1>Database Reset</h1>";

if (file_exists($dbPath)) {
    if (unlink($dbPath)) {
        echo "<p>✓ Database file deleted successfully</p>";
        echo "<p>Run <a href='create_database.php'>create_database.php</a> to create a new database.</p>";
    } else {
        echo "<p>✗ Failed to delete database file. Check file permissions.</p>";
    }
} else {
    echo "<p>Database file does not exist.</p>";
}

echo "<p><a href='index.php'>Go to Dashboard</a></p>";
?>