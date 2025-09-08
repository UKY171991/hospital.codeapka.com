<?php
// Debug API issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>API Debug Report</h2>";

try {
    require_once 'inc/connection.php';
    echo "<p>✅ Database connection: SUCCESS</p>";
    
    // Test basic tables
    $tables = ['tests', 'categories', 'patients', 'entries', 'doctors', 'users'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<p>✅ Table '$table': {$result['count']} records</p>";
        } catch (Exception $e) {
            echo "<p>❌ Table '$table': {$e->getMessage()}</p>";
        }
    }
    
    // Test the specific query from test API
    echo "<h3>Testing Test API Query:</h3>";
    try {
        $sql = "SELECT t.*, c.name as category_name 
                FROM tests t 
                LEFT JOIN categories c ON t.category_id = c.id 
                ORDER BY t.name";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p>✅ Test query successful: " . count($tests) . " tests found</p>";
        
        if (count($tests) > 0) {
            echo "<h4>Sample test:</h4>";
            echo "<pre>" . json_encode($tests[0], JSON_PRETTY_PRINT) . "</pre>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Test query failed: {$e->getMessage()}</p>";
    }
    
    // Test authentication function
    echo "<h3>Testing Authentication:</h3>";
    require_once 'inc/ajax_helpers.php';
    require_once 'inc/api_config.php';
    
    try {
        $auth = authenticateApiUser($pdo);
        if ($auth) {
            echo "<p>✅ Authentication successful</p>";
            echo "<pre>" . json_encode($auth, JSON_PRETTY_PRINT) . "</pre>";
        } else {
            echo "<p>❌ Authentication failed</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Authentication error: {$e->getMessage()}</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database connection failed: {$e->getMessage()}</p>";
}
?>
