<?php
// Check database structure
header('Content-Type: text/plain; charset=utf-8');

try {
    require_once __DIR__ . '/inc/connection.php';
    
    echo "=== Database Table Analysis ===\n\n";
    
    // Check what tables exist
    echo "1. Existing tables:\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        echo "   - $table\n";
    }
    
    echo "\n2. Entries table structure:\n";
    $stmt = $pdo->query("DESCRIBE entries");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "   - {$col['Field']} ({$col['Type']}) {$col['Null']} {$col['Key']}\n";
    }
    
    echo "\n3. Sample entries data:\n";
    $stmt = $pdo->query("SELECT * FROM entries LIMIT 2");
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($entries) > 0) {
        foreach ($entries[0] as $key => $value) {
            echo "   $key: " . ($value ?? 'NULL') . "\n";
        }
    } else {
        echo "   No entries found\n";
    }
    
    echo "\n4. Check related tables:\n";
    
    // Check patients table
    if (in_array('patients', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM patients");
        $result = $stmt->fetch();
        echo "   - patients: {$result['count']} records\n";
    } else {
        echo "   - patients: TABLE NOT FOUND\n";
    }
    
    // Check tests table  
    if (in_array('tests', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM tests");
        $result = $stmt->fetch();
        echo "   - tests: {$result['count']} records\n";
    } else {
        echo "   - tests: TABLE NOT FOUND\n";
    }
    
    // Check doctors table
    if (in_array('doctors', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM doctors");
        $result = $stmt->fetch();
        echo "   - doctors: {$result['count']} records\n";
    } else {
        echo "   - doctors: TABLE NOT FOUND\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
