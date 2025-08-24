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
<?php
try {
    $conn = new PDO('sqlite:pathology_lab.db');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check existing schema first
    $stmt = $conn->query('PRAGMA table_info(entries)');
    echo "<h3>Current Entries Table Schema</h3>";
    echo "<pre>";
    $has_referring_doctor = false;
    foreach($stmt as $row) {
        print_r($row);
        if ($row['name'] === 'referring_doctor') {
            $has_referring_doctor = true;
        }
    }
    echo "</pre>";
    
    // If referring_doctor column doesn't exist, alter the table to add it
    if (!$has_referring_doctor) {
        echo "<h3>Adding referring_doctor column</h3>";
        // SQLite doesn't support ADD COLUMN with ALTER TABLE directly
        // We need to create a new table, copy data, and replace the old table
        
        // Start a transaction
        $conn->beginTransaction();
        
        try {
            // Create new table with correct schema
            $conn->exec("CREATE TABLE entries_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                patient_id INTEGER,
                doctor_id INTEGER,
                test_id INTEGER,
                referring_doctor TEXT,
                entry_date TEXT,
                result_value TEXT,
                unit TEXT,
                remarks TEXT,
                status TEXT DEFAULT 'pending',
                added_by INTEGER,
                created_at TEXT NOT NULL DEFAULT (datetime('now'))
            )");
            
            // Copy data from old table to new table
            $conn->exec("INSERT INTO entries_new 
                (id, patient_id, doctor_id, test_id, entry_date, result_value, unit, remarks, status, added_by, created_at)
                SELECT id, patient_id, doctor_id, test_id, entry_date, result_value, unit, remarks, status, added_by, created_at 
                FROM entries");
            
            // Drop old table
            $conn->exec("DROP TABLE entries");
            
            // Rename new table to old name
            $conn->exec("ALTER TABLE entries_new RENAME TO entries");
            
            // Recreate indexes
            $conn->exec("CREATE INDEX IF NOT EXISTS idx_entries_patient ON entries (patient_id)");
            $conn->exec("CREATE INDEX IF NOT EXISTS idx_entries_doctor ON entries (doctor_id)");
            $conn->exec("CREATE INDEX IF NOT EXISTS idx_entries_test ON entries (test_id)");
            $conn->exec("CREATE INDEX IF NOT EXISTS idx_entries_added_by ON entries (added_by)");
            $conn->exec("CREATE INDEX IF NOT EXISTS idx_entries_date ON entries (entry_date)");
            
            // Commit transaction
            $conn->commit();
            
            echo "<p>Table structure updated successfully!</p>";
        } catch(PDOException $e) {
            // Rollback transaction on error
            $conn->rollBack();
            echo "<p>Error updating table: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>The referring_doctor column already exists.</p>";
    }
    
    // Show updated schema
    $stmt = $conn->query('PRAGMA table_info(entries)');
    echo "<h3>Updated Entries Table Schema</h3>";
    echo "<pre>";
    foreach($stmt as $row) {
        print_r($row);
    }
    echo "</pre>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>