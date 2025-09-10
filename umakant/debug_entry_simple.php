<?php
// Simple test to isolate Entry API issue
header('Content-Type: text/plain; charset=utf-8');

echo "Testing Entry API Step by Step...\n\n";

// Step 1: Test includes
try {
    echo "1. Including connection...\n";
    require_once __DIR__ . '/inc/connection.php';
    echo "   ✓ Connection included\n";
    
    echo "2. Including helpers...\n";
    require_once __DIR__ . '/inc/ajax_helpers.php';
    require_once __DIR__ . '/inc/api_config.php';
    echo "   ✓ Helpers included\n";
    
} catch (Exception $e) {
    echo "   ✗ Include error: " . $e->getMessage() . "\n";
    exit;
}

// Step 2: Test database
try {
    echo "3. Testing database...\n";
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM entries");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "   ✓ Entries count: " . $result['count'] . "\n";
} catch (Exception $e) {
    echo "   ✗ Database error: " . $e->getMessage() . "\n";
}

// Step 3: Test authentication with API key
try {
    echo "4. Testing API key authentication...\n";
    $_SERVER['HTTP_X_API_KEY'] = 'hospital-api-secret-2024';
    $_REQUEST['action'] = 'list';
    $_SERVER['REQUEST_METHOD'] = 'GET';
    
    $user_data = authenticateApiUser($pdo);
    if ($user_data) {
        echo "   ✓ Auth success: User {$user_data['user_id']}, Role {$user_data['role']}, Method {$user_data['auth_method']}\n";
    } else {
        echo "   ✗ Auth failed\n";
    }
    
} catch (Exception $e) {
    echo "   ✗ Auth error: " . $e->getMessage() . "\n";
}

// Step 4: Test the SQL query directly
try {
    echo "5. Testing entries query...\n";
    $sql = "SELECT e.*, 
               p.patient_name, p.uhid,
               t.test_name, t.units,
               t.normal_value_male, t.normal_value_female, t.normal_value_child,
               d.doctor_name
        FROM entries e 
        LEFT JOIN patients p ON e.patient_id = p.id 
        LEFT JOIN tests t ON e.test_id = t.id 
        LEFT JOIN doctors d ON e.doctor_id = d.id 
        ORDER BY COALESCE(e.entry_date, e.created_at) DESC, e.id DESC
        LIMIT 5";
        
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "   ✓ Query successful, " . count($entries) . " entries found\n";
    
    if (count($entries) > 0) {
        echo "   Sample entry columns: " . implode(', ', array_keys($entries[0])) . "\n";
    }
    
} catch (Exception $e) {
    echo "   ✗ Query error: " . $e->getMessage() . "\n";
}

echo "\nTest completed.\n";
?>
