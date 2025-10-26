<?php
// Test script to check API response for entry #17
require_once 'umakant/inc/connection.php';

echo "<h2>Testing API Response for Entry #17</h2>\n";

// Test 1: Check entry_tests table directly
echo "<h3>1. Direct entry_tests table query:</h3>\n";
$stmt = $pdo->prepare("SELECT et.*, t.name as test_name FROM entry_tests et LEFT JOIN tests t ON et.test_id = t.id WHERE et.entry_id = 17");
$stmt->execute();
$directTests = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
echo "Found " . count($directTests) . " tests in entry_tests table:\n";
foreach ($directTests as $i => $test) {
    echo "Test " . ($i + 1) . ":\n";
    echo "  - ID: " . $test['id'] . "\n";
    echo "  - Test ID: " . $test['test_id'] . "\n";
    echo "  - Test Name: " . ($test['test_name'] ?: 'NULL') . "\n";
    echo "  - Result: " . ($test['result_value'] ?: 'NULL') . "\n";
    echo "  - Price: " . $test['price'] . "\n";
    echo "\n";
}
echo "</pre>";

// Test 2: Check aggregation SQL
echo "<h3>2. Test aggregation SQL:</h3>\n";
$aggSql = "SELECT et.entry_id,
               COUNT(*) as tests_count,
               GROUP_CONCAT(DISTINCT t.name ORDER BY t.name SEPARATOR ', ') as test_names,
               GROUP_CONCAT(DISTINCT et.test_id ORDER BY et.test_id) as test_ids,
               SUM(et.price) as total_price,
               SUM(et.discount_amount) as total_discount
        FROM entry_tests et
        LEFT JOIN tests t ON et.test_id = t.id
        WHERE et.entry_id = 17
        GROUP BY et.entry_id";

echo "<pre>SQL: " . $aggSql . "</pre>\n";

$stmt = $pdo->prepare($aggSql);
$stmt->execute();
$aggResult = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<pre>";
echo "Aggregation result:\n";
if ($aggResult) {
    echo "  - Tests Count: " . $aggResult['tests_count'] . "\n";
    echo "  - Test Names: '" . $aggResult['test_names'] . "'\n";
    echo "  - Test IDs: '" . $aggResult['test_ids'] . "'\n";
    echo "  - Total Price: " . $aggResult['total_price'] . "\n";
} else {
    echo "  - No aggregation result found\n";
}
echo "</pre>";

// Test 3: Check what's in the tests table for these test IDs
echo "<h3>3. Check tests table for the test IDs:</h3>\n";
if (!empty($directTests)) {
    $testIds = array_column($directTests, 'test_id');
    $placeholders = implode(',', array_fill(0, count($testIds), '?'));
    $stmt = $pdo->prepare("SELECT id, name, price FROM tests WHERE id IN ($placeholders)");
    $stmt->execute($testIds);
    $testsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<pre>";
    echo "Tests table data:\n";
    foreach ($testsData as $test) {
        echo "  - Test ID " . $test['id'] . ": '" . $test['name'] . "' (₹" . $test['price'] . ")\n";
    }
    echo "</pre>";
}

// Test 4: Check entries table current state
echo "<h3>4. Check entries table current aggregated data:</h3>\n";
$stmt = $pdo->prepare("SELECT id, tests_count, test_names, test_ids, total_price FROM entries WHERE id = 17");
$stmt->execute();
$entryData = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<pre>";
if ($entryData) {
    echo "Entries table stored data:\n";
    echo "  - Stored Tests Count: " . ($entryData['tests_count'] ?: 'NULL') . "\n";
    echo "  - Stored Test Names: '" . ($entryData['test_names'] ?: 'NULL') . "'\n";
    echo "  - Stored Test IDs: '" . ($entryData['test_ids'] ?: 'NULL') . "'\n";
    echo "  - Stored Total Price: " . ($entryData['total_price'] ?: 'NULL') . "\n";
} else {
    echo "Entry #17 not found in entries table\n";
}
echo "</pre>";

// Test 5: Simulate the API call
echo "<h3>5. Simulate API list call (simplified):</h3>\n";
$listSql = "SELECT e.id, e.tests_count, e.test_names, e.test_ids,
                   COALESCE(agg.tests_count, 0) AS agg_tests_count,
                   COALESCE(agg.test_names, '') AS agg_test_names,
                   COALESCE(agg.test_ids, '') AS agg_test_ids
            FROM entries e 
            LEFT JOIN (
                SELECT et.entry_id,
                       COUNT(*) as tests_count,
                       GROUP_CONCAT(DISTINCT t.name ORDER BY t.name SEPARATOR ', ') as test_names,
                       GROUP_CONCAT(DISTINCT et.test_id ORDER BY et.test_id) as test_ids
                FROM entry_tests et
                LEFT JOIN tests t ON et.test_id = t.id
                GROUP BY et.entry_id
            ) agg ON agg.entry_id = e.id
            WHERE e.id = 17";

$stmt = $pdo->prepare($listSql);
$stmt->execute();
$apiResult = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<pre>";
if ($apiResult) {
    echo "API-style result:\n";
    echo "  - Entry ID: " . $apiResult['id'] . "\n";
    echo "  - Stored tests_count: " . ($apiResult['tests_count'] ?: 'NULL') . "\n";
    echo "  - Stored test_names: '" . ($apiResult['test_names'] ?: 'NULL') . "'\n";
    echo "  - Aggregated tests_count: " . $apiResult['agg_tests_count'] . "\n";
    echo "  - Aggregated test_names: '" . $apiResult['agg_test_names'] . "'\n";
    echo "  - Final tests_count (agg || stored): " . ($apiResult['agg_tests_count'] ?: $apiResult['tests_count']) . "\n";
    echo "  - Final test_names (agg || stored): '" . ($apiResult['agg_test_names'] ?: $apiResult['test_names']) . "'\n";
} else {
    echo "No API result found\n";
}
echo "</pre>";

?>