<?php
// Script to fix duplicate test names and ensure unique test identification
require_once 'umakant/inc/connection.php';

echo "<h2>Fixing Duplicate Test Names Issue</h2>\n";

// Step 1: Check what tests are actually in entry #17
echo "<h3>1. Current tests in Entry #17:</h3>\n";
$stmt = $pdo->prepare("
    SELECT et.id as entry_test_id, et.test_id, et.result_value, et.price, 
           t.name as test_name, t.price as test_base_price
    FROM entry_tests et 
    LEFT JOIN tests t ON et.test_id = t.id 
    WHERE et.entry_id = 17 
    ORDER BY et.id
");
$stmt->execute();
$entryTests = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
foreach ($entryTests as $i => $test) {
    echo "Test " . ($i + 1) . ":\n";
    echo "  - Entry Test ID: " . $test['entry_test_id'] . "\n";
    echo "  - Test ID: " . $test['test_id'] . "\n";
    echo "  - Test Name: '" . $test['test_name'] . "'\n";
    echo "  - Result: '" . ($test['result_value'] ?: 'NULL') . "'\n";
    echo "  - Price: ₹" . $test['price'] . "\n";
    echo "  - Base Price: ₹" . $test['test_base_price'] . "\n";
    echo "\n";
}
echo "</pre>";

// Step 2: Check if there are duplicate test names in the tests table
echo "<h3>2. Check for duplicate test names in tests table:</h3>\n";
$stmt = $pdo->prepare("
    SELECT name, COUNT(*) as count, GROUP_CONCAT(id) as test_ids, GROUP_CONCAT(price) as prices
    FROM tests 
    GROUP BY name 
    HAVING count > 1 
    ORDER BY count DESC
");
$stmt->execute();
$duplicateNames = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
if (count($duplicateNames) > 0) {
    echo "Found " . count($duplicateNames) . " duplicate test names:\n";
    foreach ($duplicateNames as $dup) {
        echo "  - '" . $dup['name'] . "': " . $dup['count'] . " tests (IDs: " . $dup['test_ids'] . ", Prices: " . $dup['prices'] . ")\n";
    }
} else {
    echo "No duplicate test names found.\n";
}
echo "</pre>";

// Step 3: Fix the duplicate names by making them unique
echo "<h3>3. Fix duplicate test names:</h3>\n";
if (count($duplicateNames) > 0) {
    foreach ($duplicateNames as $dup) {
        $testIds = explode(',', $dup['test_ids']);
        $prices = explode(',', $dup['prices']);
        
        echo "<pre>";
        echo "Fixing duplicate name: '" . $dup['name'] . "'\n";
        
        for ($i = 0; $i < count($testIds); $i++) {
            $testId = trim($testIds[$i]);
            $price = trim($prices[$i]);
            
            if ($i == 0) {
                // Keep the first one as is
                echo "  - Test ID $testId: Keeping original name '" . $dup['name'] . "'\n";
            } else {
                // Make others unique
                $newName = $dup['name'] . " (Variant " . ($i + 1) . ")";
                if ($price != $prices[0]) {
                    $newName = $dup['name'] . " - ₹" . $price;
                }
                
                $updateStmt = $pdo->prepare("UPDATE tests SET name = ? WHERE id = ?");
                $result = $updateStmt->execute([$newName, $testId]);
                
                if ($result) {
                    echo "  - Test ID $testId: Updated to '" . $newName . "'\n";
                } else {
                    echo "  - Test ID $testId: Failed to update\n";
                }
            }
        }
        echo "</pre>";
    }
    
    // Step 4: Refresh aggregates for affected entries
    echo "<h3>4. Refreshing aggregates for affected entries:</h3>\n";
    
    // Find all entries that use these test IDs
    $allTestIds = [];
    foreach ($duplicateNames as $dup) {
        $allTestIds = array_merge($allTestIds, explode(',', $dup['test_ids']));
    }
    
    if (!empty($allTestIds)) {
        $placeholders = implode(',', array_fill(0, count($allTestIds), '?'));
        $stmt = $pdo->prepare("SELECT DISTINCT entry_id FROM entry_tests WHERE test_id IN ($placeholders)");
        $stmt->execute($allTestIds);
        $affectedEntries = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<pre>";
        echo "Found " . count($affectedEntries) . " entries to refresh: " . implode(', ', $affectedEntries) . "\n";
        
        // Include the refresh_entry_aggregates function
        function refresh_entry_aggregates_local($pdo, $entryId) {
            // Get aggregated data
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as tests_count,
                       GROUP_CONCAT(DISTINCT t.name ORDER BY t.name SEPARATOR ', ') as test_names,
                       GROUP_CONCAT(DISTINCT et.test_id ORDER BY et.test_id) as test_ids,
                       SUM(et.price) as total_price,
                       SUM(et.discount_amount) as total_discount
                FROM entry_tests et
                LEFT JOIN tests t ON et.test_id = t.id
                WHERE et.entry_id = ?
                GROUP BY et.entry_id
            ");
            $stmt->execute([$entryId]);
            $aggRow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($aggRow) {
                $testsCount = (int)($aggRow['tests_count'] ?? 0);
                $testIds = $aggRow['test_ids'] ?? '';
                $testNames = $aggRow['test_names'] ?? '';
                $totalPrice = (float)($aggRow['total_price'] ?? 0);
                $totalDiscount = (float)($aggRow['total_discount'] ?? 0);
                $netAmount = max($totalPrice - $totalDiscount, 0);
                
                // Update entries table
                $updateStmt = $pdo->prepare("
                    UPDATE entries SET 
                        tests_count = ?, 
                        test_ids = ?, 
                        test_names = ?, 
                        price = ?, 
                        discount_amount = ?, 
                        total_price = ?,
                        grouped = ?,
                        updated_at = NOW()
                    WHERE id = ?
                ");
                
                $result = $updateStmt->execute([
                    $testsCount,
                    $testIds,
                    $testNames,
                    $totalPrice,
                    $totalDiscount,
                    $netAmount,
                    $testsCount > 1 ? 1 : 0,
                    $entryId
                ]);
                
                return $result;
            }
            return false;
        }
        
        foreach ($affectedEntries as $entryId) {
            $result = refresh_entry_aggregates_local($pdo, $entryId);
            echo "  - Entry $entryId: " . ($result ? "Refreshed successfully" : "Failed to refresh") . "\n";
        }
        echo "</pre>";
    }
} else {
    echo "<pre>No duplicate names to fix.</pre>\n";
}

// Step 5: Verify the fix
echo "<h3>5. Verify Entry #17 after fix:</h3>\n";
$stmt = $pdo->prepare("
    SELECT et.id as entry_test_id, et.test_id, et.result_value, et.price, 
           t.name as test_name
    FROM entry_tests et 
    LEFT JOIN tests t ON et.test_id = t.id 
    WHERE et.entry_id = 17 
    ORDER BY et.id
");
$stmt->execute();
$fixedTests = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
echo "Entry #17 tests after fix:\n";
foreach ($fixedTests as $i => $test) {
    echo "  Test " . ($i + 1) . ": '" . $test['test_name'] . "' (ID: " . $test['test_id'] . ", Price: ₹" . $test['price'] . ")\n";
}
echo "</pre>";

// Step 6: Check aggregated data in entries table
echo "<h3>6. Check aggregated data in entries table:</h3>\n";
$stmt = $pdo->prepare("SELECT id, tests_count, test_names FROM entries WHERE id = 17");
$stmt->execute();
$entryData = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<pre>";
if ($entryData) {
    echo "Entry #17 aggregated data:\n";
    echo "  - Tests Count: " . $entryData['tests_count'] . "\n";
    echo "  - Test Names: '" . $entryData['test_names'] . "'\n";
} else {
    echo "Entry #17 not found.\n";
}
echo "</pre>";

echo "<h3>✅ Fix completed! Please refresh the entry list page to see the changes.</h3>\n";
?>