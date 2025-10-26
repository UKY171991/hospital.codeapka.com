<?php
// Simple test script to verify the entry data fix
require_once __DIR__ . '/inc/connection.php';

echo "<h2>Testing Entry Data Fix</h2>\n";

// Test entry 17 specifically
$entryId = 17;

echo "<h3>1. Direct entry_tests query for entry $entryId:</h3>\n";
$stmt = $pdo->prepare("SELECT * FROM entry_tests WHERE entry_id = ? ORDER BY id");
$stmt->execute([$entryId]);
$directTests = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>" . print_r($directTests, true) . "</pre>\n";

echo "<h3>2. JOIN query (like the fixed API):</h3>\n";
$testSql = "SELECT et.id as entry_test_id,
                   et.entry_id,
                   et.test_id,
                   et.result_value,
                   et.status,
                   et.price,
                   t.name AS test_name, 
                   t.category_id,
                   c.name AS category_name
            FROM entry_tests et
            LEFT JOIN tests t ON et.test_id = t.id
            LEFT JOIN categories c ON t.category_id = c.id
            WHERE et.entry_id = ?
            ORDER BY et.id";

$stmt = $pdo->prepare($testSql);
$stmt->execute([$entryId]);
$joinedTests = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>" . print_r($joinedTests, true) . "</pre>\n";

echo "<h3>3. Test aggregation function:</h3>\n";
function build_entry_tests_aggregation_sql($pdo) {
    $sql = "SELECT et.entry_id,
                   COUNT(DISTINCT et.test_id) as tests_count,
                   GROUP_CONCAT(DISTINCT COALESCE(t.name, CONCAT('Test_', et.test_id)) ORDER BY t.name SEPARATOR ', ') as test_names,
                   GROUP_CONCAT(DISTINCT et.test_id ORDER BY et.test_id) as test_ids,
                   SUM(COALESCE(et.price, 0)) as total_price,
                   SUM(COALESCE(et.discount_amount, 0)) as total_discount
            FROM entry_tests et
            LEFT JOIN tests t ON et.test_id = t.id
            WHERE et.entry_id IS NOT NULL AND et.test_id IS NOT NULL
            GROUP BY et.entry_id";
    return $sql;
}

$aggSql = build_entry_tests_aggregation_sql($pdo);
$stmt = $pdo->prepare("SELECT * FROM (" . $aggSql . ") agg WHERE entry_id = ?");
$stmt->execute([$entryId]);
$aggData = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<pre>" . print_r($aggData, true) . "</pre>\n";

echo "<h3>Summary:</h3>\n";
echo "Direct tests count: " . count($directTests) . "<br>\n";
echo "Joined tests count: " . count($joinedTests) . "<br>\n";
echo "Aggregated tests count: " . ($aggData['tests_count'] ?? 0) . "<br>\n";
echo "Test IDs from aggregation: " . ($aggData['test_ids'] ?? 'none') . "<br>\n";
echo "Test names from aggregation: " . ($aggData['test_names'] ?? 'none') . "<br>\n";

if (count($directTests) === count($joinedTests) && count($directTests) == ($aggData['tests_count'] ?? 0)) {
    echo "<p style='color: green;'><strong>✓ Fix appears to be working correctly!</strong></p>\n";
} else {
    echo "<p style='color: red;'><strong>✗ There are still inconsistencies in the data.</strong></p>\n";
}
?>