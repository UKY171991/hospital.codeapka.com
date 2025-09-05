<?php
// test_db_connection.php - Simple database connection test
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";

// Test the main connection
echo "<h2>Testing Main Connection (connection.php)</h2>";
try {
    require_once 'inc/connection.php';
    echo "<p>✅ Connection file loaded</p>";
    
    // Test if $pdo is available
    if (isset($pdo) && $pdo instanceof PDO) {
        echo "<p>✅ PDO object created successfully</p>";
        
        // Test a simple query
        $stmt = $pdo->query("SELECT 1 as test");
        $result = $stmt->fetch();
        if ($result && $result['test'] == 1) {
            echo "<p>✅ Database connection working - test query successful</p>";
        } else {
            echo "<p>❌ Test query failed</p>";
        }
        
        // Check if patients table exists
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE 'patients'");
            $table = $stmt->fetch();
            if ($table) {
                echo "<p>✅ Patients table exists</p>";
                
                // Count patients
                $stmt = $pdo->query("SELECT COUNT(*) as total FROM patients");
                $count = $stmt->fetch();
                echo "<p>📊 Total patients in database: " . $count['total'] . "</p>";
                
            } else {
                echo "<p>❌ Patients table does not exist</p>";
            }
        } catch (Exception $e) {
            echo "<p>❌ Error checking patients table: " . $e->getMessage() . "</p>";
        }
        
    } else {
        echo "<p>❌ PDO object not created</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Connection failed: " . $e->getMessage() . "</p>";
    
    // Try development connection
    echo "<h2>Testing Development Connection (connection_dev.php)</h2>";
    try {
        require_once 'inc/connection_dev.php';
        echo "<p>✅ Development connection file loaded</p>";
        
        if (isset($pdo) && $pdo instanceof PDO) {
            echo "<p>✅ Development PDO object created successfully</p>";
        } else {
            echo "<p>❌ Development PDO object not created</p>";
        }
        
    } catch (Exception $e2) {
        echo "<p>❌ Development connection also failed: " . $e2->getMessage() . "</p>";
    }
}

echo "<h2>System Information</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>PDO Available: " . (extension_loaded('pdo') ? 'Yes' : 'No') . "</p>";
echo "<p>PDO MySQL Available: " . (extension_loaded('pdo_mysql') ? 'Yes' : 'No') . "</p>";
?>
