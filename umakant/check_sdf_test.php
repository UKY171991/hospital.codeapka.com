<?php
// Check what test has name "sdf"
require_once __DIR__ . '/inc/connection.php';

echo "<h2>Checking for 'sdf' test</h2>\n";

// Find tests with name containing "sdf"
$stmt = $pdo->prepare("SELECT * FROM tests WHERE name LIKE '%sdf%'");
$stmt->execute();
$sdfTests = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Tests containing 'sdf':</h3>\n";
echo "<pre>" . print_r($sdfTests, true) . "</pre>\n";

// Check entry_tests for entry 17
echo "<h3>Entry_tests records for entry 17:</h3>\n";
$stmt = $pdo->prepare("SELECT et.*, t.name as test_name FROM entry_tests et LEFT JOIN tests t ON et.test_id = t.id WHERE et.entry_id = 17 ORDER BY et.id");
$stmt->execute();
$entry17Tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>" . print_r($entry17Tests, true) . "</pre>\n";

// Check what tests have IDs 2, 5, 10 (from your screenshot)
echo "<h3>Tests with IDs 2, 5, 10:</h3>\n";
$stmt = $pdo->prepare("SELECT * FROM tests WHERE id IN (2, 5, 10) ORDER BY id");
$stmt->execute();
$specificTests = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>" . print_r($specificTests, true) . "</pre>\n";
?>