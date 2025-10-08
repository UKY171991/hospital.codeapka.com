<?php
/**
 * Quick diagnostic to check database data
 */
require_once 'inc/connection.php';
require_once 'inc/auth.php';

// Require login
if (!isset($_SESSION['user_id'])) {
    die('Please login first');
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Check</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .section { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .good { color: green; font-weight: bold; }
        .bad { color: red; font-weight: bold; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #4CAF50; color: white; }
        .warning { background: #fff3cd; border: 2px solid #ffc107; padding: 15px; margin: 10px 0; }
        .success { background: #d4edda; border: 2px solid #28a745; padding: 15px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>🔍 Database Diagnostic Check</h1>
    
    <?php
    try {
        // Check tests table
        echo '<div class="section">';
        echo '<h2>1. Tests Table</h2>';
        $stmt = $pdo->query("SELECT id, name, price FROM tests ORDER BY id");
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($tests)) {
            echo '<div class="warning">⚠️ NO TESTS FOUND in database!</div>';
        } else {
            $testsWithPrice = array_filter($tests, function($t) { return $t['price'] > 0; });
            $testsWithoutPrice = count($tests) - count($testsWithPrice);
            
            if (count($testsWithPrice) > 0) {
                echo '<div class="success">✅ Found ' . count($testsWithPrice) . ' tests with prices</div>';
            }
            if ($testsWithoutPrice > 0) {
                echo '<div class="warning">⚠️ Found ' . $testsWithoutPrice . ' tests WITHOUT prices (price = 0)</div>';
            }
            
            echo '<table>';
            echo '<tr><th>ID</th><th>Name</th><th>Price</th><th>Status</th></tr>';
            foreach ($tests as $test) {
                $status = $test['price'] > 0 ? '<span class="good">✓ Has Price</span>' : '<span class="bad">✗ NO PRICE</span>';
                echo "<tr>";
                echo "<td>{$test['id']}</td>";
                echo "<td>{$test['name']}</td>";
                echo "<td>₹" . number_format($test['price'], 2) . "</td>";
                echo "<td>$status</td>";
                echo "</tr>";
            }
            echo '</table>';
        }
        echo '</div>';
        
        // Check entries table structure
        echo '<div class="section">';
        echo '<h2>2. Entries Table Structure</h2>';
        $stmt = $pdo->query("SHOW COLUMNS FROM entries");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $requiredColumns = ['subtotal', 'discount_amount', 'total_price'];
        $foundColumns = array_column($columns, 'Field');
        
        echo '<table>';
        echo '<tr><th>Column</th><th>Type</th><th>Status</th></tr>';
        foreach ($requiredColumns as $col) {
            $exists = in_array($col, $foundColumns);
            $status = $exists ? '<span class="good">✓ EXISTS</span>' : '<span class="bad">✗ MISSING</span>';
            $type = '';
            if ($exists) {
                $colInfo = array_filter($columns, function($c) use ($col) { return $c['Field'] === $col; });
                $colInfo = reset($colInfo);
                $type = $colInfo['Type'];
            }
            echo "<tr><td>$col</td><td>$type</td><td>$status</td></tr>";
        }
        echo '</table>';
        echo '</div>';
        
        // Check recent entries
        echo '<div class="section">';
        echo '<h2>3. Recent Entries (Last 5)</h2>';
        $stmt = $pdo->query("SELECT id, patient_id, subtotal, discount_amount, total_price, created_at FROM entries ORDER BY id DESC LIMIT 5");
        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($entries)) {
            echo '<div class="warning">No entries found</div>';
        } else {
            $allZero = true;
            foreach ($entries as $entry) {
                if ($entry['subtotal'] > 0 || $entry['total_price'] > 0) {
                    $allZero = false;
                    break;
                }
            }
            
            if ($allZero) {
                echo '<div class="warning">⚠️ ALL ENTRIES HAVE ZERO PRICING!</div>';
            } else {
                echo '<div class="success">✅ Some entries have pricing data</div>';
            }
            
            echo '<table>';
            echo '<tr><th>ID</th><th>Patient ID</th><th>Subtotal</th><th>Discount</th><th>Total</th><th>Created</th></tr>';
            foreach ($entries as $entry) {
                $rowStyle = ($entry['subtotal'] == 0 && $entry['total_price'] == 0) ? 'style="background: #ffcccc;"' : '';
                echo "<tr $rowStyle>";
                echo "<td>{$entry['id']}</td>";
                echo "<td>{$entry['patient_id']}</td>";
                echo "<td>₹" . number_format($entry['subtotal'], 2) . "</td>";
                echo "<td>₹" . number_format($entry['discount_amount'], 2) . "</td>";
                echo "<td>₹" . number_format($entry['total_price'], 2) . "</td>";
                echo "<td>{$entry['created_at']}</td>";
                echo "</tr>";
            }
            echo '</table>';
        }
        echo '</div>';
        
        // Check entry_tests table
        echo '<div class="section">';
        echo '<h2>4. Entry Tests (Last 5)</h2>';
        $stmt = $pdo->query("SELECT et.id, et.entry_id, et.test_id, t.name as test_name, et.price, et.discount_amount, et.total_price FROM entry_tests et LEFT JOIN tests t ON et.test_id = t.id ORDER BY et.id DESC LIMIT 5");
        $entryTests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($entryTests)) {
            echo '<div class="warning">No entry tests found</div>';
        } else {
            echo '<table>';
            echo '<tr><th>ID</th><th>Entry ID</th><th>Test</th><th>Price</th><th>Discount</th><th>Total</th></tr>';
            foreach ($entryTests as $et) {
                echo "<tr>";
                echo "<td>{$et['id']}</td>";
                echo "<td>{$et['entry_id']}</td>";
                echo "<td>{$et['test_name']} (ID: {$et['test_id']})</td>";
                echo "<td>₹" . number_format($et['price'], 2) . "</td>";
                echo "<td>₹" . number_format($et['discount_amount'], 2) . "</td>";
                echo "<td>₹" . number_format($et['total_price'], 2) . "</td>";
                echo "</tr>";
            }
            echo '</table>';
        }
        echo '</div>';
        
        // Summary
        echo '<div class="section">';
        echo '<h2>5. Summary & Recommendations</h2>';
        
        $issues = [];
        $recommendations = [];
        
        // Check if tests have prices
        if (isset($testsWithoutPrice) && $testsWithoutPrice > 0) {
            $issues[] = "$testsWithoutPrice tests have no price (price = 0)";
            $recommendations[] = "Set prices for tests in the Tests management page";
        }
        
        // Check if columns exist
        foreach ($requiredColumns as $col) {
            if (!in_array($col, $foundColumns)) {
                $issues[] = "Column '$col' is missing from entries table";
                $recommendations[] = "Run ALTER TABLE to add missing columns";
            }
        }
        
        // Check if entries have pricing
        if (isset($allZero) && $allZero && !empty($entries)) {
            $issues[] = "All recent entries have zero pricing";
            $recommendations[] = "The pricing calculation/save function may not be working";
        }
        
        if (empty($issues)) {
            echo '<div class="success">';
            echo '<h3>✅ Everything looks good!</h3>';
            echo '<p>All checks passed. The system should be working correctly.</p>';
            echo '</div>';
        } else {
            echo '<div class="warning">';
            echo '<h3>⚠️ Issues Found:</h3>';
            echo '<ul>';
            foreach ($issues as $issue) {
                echo "<li>$issue</li>";
            }
            echo '</ul>';
            echo '<h3>💡 Recommendations:</h3>';
            echo '<ul>';
            foreach ($recommendations as $rec) {
                echo "<li>$rec</li>";
            }
            echo '</ul>';
            echo '</div>';
        }
        
        echo '</div>';
        
    } catch (Exception $e) {
        echo '<div class="warning">';
        echo '<h3>❌ Error:</h3>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '</div>';
    }
    ?>
    
    <p style="text-align: center; margin-top: 30px;">
        <a href="entry-list.php" style="background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">← Back to Entry List</a>
    </p>
</body>
</html>

