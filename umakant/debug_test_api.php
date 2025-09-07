<?php
// Debug script to test the test API
require_once 'inc/connection.php';
require_once 'inc/ajax_helpers.php';

echo "<h1>Test API Debug</h1>";

// Test 1: Check if categories table exists
echo "<h2>1. Categories Table Check</h2>";
$categories_table = 'test_categories';
try{
    $stmt = $pdo->query("SHOW TABLES LIKE 'test_categories'");
    if(!$stmt->fetch()){
        echo "test_categories table not found, checking categories...<br>";
        $stmt2 = $pdo->query("SHOW TABLES LIKE 'categories'");
        if($stmt2->fetch()) {
            $categories_table = 'categories';
            echo "Found categories table<br>";
        } else {
            echo "No categories table found<br>";
        }
    } else {
        echo "Found test_categories table<br>";
    }
}catch(Throwable $e){
    echo "Error: " . $e->getMessage() . "<br>";
    $categories_table = 'test_categories';
}
echo "Using table: $categories_table<br>";

// Test 2: Direct query to see what data exists
echo "<h2>2. Raw Test Data</h2>";
try {
    $stmt = $pdo->query("SELECT t.id, t.name, t.category_id, tc.name as category_name, t.price, t.unit 
                         FROM tests t 
                         LEFT JOIN {$categories_table} tc ON t.category_id = tc.id 
                         ORDER BY t.id DESC LIMIT 5");
    $tests = $stmt->fetchAll();
    echo "<pre>";
    print_r($tests);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

// Test 3: Simulate the API call
echo "<h2>3. API Simulation</h2>";
$baseQuery = "FROM tests t LEFT JOIN {$categories_table} tc ON t.category_id = tc.id LEFT JOIN users u ON t.added_by = u.id";
$whereClause = "";
$params = [];

$dataQuery = "SELECT 
    t.id,
    t.name AS name,
    tc.name AS category_name,
    t.category_id,
    t.description,
    t.price,
    t.unit,
    t.min,
    t.max,
    t.min_male,
    t.max_male,
    t.min_female,
    t.max_female,
    t.sub_heading,
    t.print_new_page,
    u.username AS added_by_username
    " . $baseQuery . $whereClause . " ORDER BY t.id DESC LIMIT 5";

try {
    $dataStmt = $pdo->prepare($dataQuery);
    $dataStmt->execute($params);
    $data = $dataStmt->fetchAll();
    
    echo "API Response format:<br>";
    echo "<pre>";
    print_r([
        'success' => true,
        'data' => $data
    ]);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}
?>
