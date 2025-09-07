<?php
// Quick test to verify database connection and data
require_once __DIR__ . '/inc/connection.php';

echo "=== Database Connection Test ===\n";
echo "Connected to database successfully\n\n";

echo "=== Tables Check ===\n";
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "Available tables: " . implode(', ', $tables) . "\n\n";

echo "=== Categories Table ===\n";
if (in_array('categories', $tables)) {
    $stmt = $pdo->query("SELECT id, name FROM categories LIMIT 5");
    $categories = $stmt->fetchAll();
    foreach ($categories as $cat) {
        echo "Category {$cat['id']}: {$cat['name']}\n";
    }
} else {
    echo "Categories table not found\n";
}

echo "\n=== Tests Table ===\n";
if (in_array('tests', $tables)) {
    $stmt = $pdo->query("SELECT id, name, category_id FROM tests LIMIT 5");
    $tests = $stmt->fetchAll();
    foreach ($tests as $test) {
        echo "Test {$test['id']}: '{$test['name']}' (category: {$test['category_id']})\n";
    }
} else {
    echo "Tests table not found\n";
}

echo "\n=== Joined Query Test ===\n";
if (in_array('tests', $tables) && in_array('categories', $tables)) {
    $stmt = $pdo->query("
        SELECT t.id, t.name as test_name, tc.name as category_name, t.price 
        FROM tests t 
        LEFT JOIN categories tc ON t.category_id = tc.id 
        ORDER BY t.id DESC 
        LIMIT 5
    ");
    $joined = $stmt->fetchAll();
    foreach ($joined as $row) {
        echo "Test {$row['id']}: '{$row['test_name']}' in '{$row['category_name']}' - ₹{$row['price']}\n";
    }
}

echo "\n=== Test Complete ===\n";
?>
