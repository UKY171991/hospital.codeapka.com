<?php
require_once 'inc/connection.php';
require_once 'inc/auth.php';

if (!isset($_SESSION['user_id'])) {
    die('Please login first');
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Prices</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #4CAF50; color: white; }
        .zero { background: #ffcccc; font-weight: bold; }
        .has-price { background: #ccffcc; }
    </style>
</head>
<body>
    <h1>🔍 Test Prices in Database</h1>
    
    <?php
    $stmt = $pdo->query("SELECT id, name, price, category_id, added_by FROM tests ORDER BY id");
    $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $withPrice = 0;
    $withoutPrice = 0;
    
    foreach ($tests as $test) {
        if ($test['price'] > 0) {
            $withPrice++;
        } else {
            $withoutPrice++;
        }
    }
    ?>
    
    <p><strong>Total Tests:</strong> <?= count($tests) ?></p>
    <p style="color: green;"><strong>Tests WITH prices:</strong> <?= $withPrice ?></p>
    <p style="color: red;"><strong>Tests WITHOUT prices (0.00):</strong> <?= $withoutPrice ?></p>
    
    <?php if ($withoutPrice > 0): ?>
        <div style="background: #fff3cd; padding: 15px; margin: 20px 0; border-left: 4px solid #ffc107;">
            <h3>⚠️ WARNING</h3>
            <p>Some tests have NO PRICE (0.00). This is why pricing fields show 0.00 when you add entries!</p>
            <p><strong>Solution:</strong> Go to Tests page and add prices to your tests.</p>
        </div>
    <?php endif; ?>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Test Name</th>
            <th>Price (₹)</th>
            <th>Category ID</th>
            <th>Added By</th>
            <th>Status</th>
        </tr>
        <?php foreach ($tests as $test): ?>
            <?php 
            $rowClass = $test['price'] > 0 ? 'has-price' : 'zero';
            $status = $test['price'] > 0 ? '✓ Has Price' : '✗ NO PRICE';
            ?>
            <tr class="<?= $rowClass ?>">
                <td><?= $test['id'] ?></td>
                <td><?= htmlspecialchars($test['name']) ?></td>
                <td><strong>₹<?= number_format($test['price'], 2) ?></strong></td>
                <td><?= $test['category_id'] ?></td>
                <td><?= $test['added_by'] ?></td>
                <td><?= $status ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    
    <hr>
    
    <h2>💡 How to Fix</h2>
    
    <?php if ($withoutPrice > 0): ?>
        <h3>Option 1: Update via SQL (Quick Fix)</h3>
        <p>Run this SQL to set default prices for tests without prices:</p>
        <pre style="background: #f5f5f5; padding: 15px; border-radius: 5px;">
-- Set price to 100 for all tests that currently have 0
UPDATE tests SET price = 100.00 WHERE price = 0 OR price IS NULL;

-- Or set specific prices for specific tests:
UPDATE tests SET price = 980.00 WHERE id = 1;
UPDATE tests SET price = 100.00 WHERE id = 2;
        </pre>
        
        <h3>Option 2: Update via UI</h3>
        <ol>
            <li>Go to <a href="test.php">Tests Management Page</a></li>
            <li>Edit each test</li>
            <li>Set a price</li>
            <li>Save</li>
        </ol>
    <?php else: ?>
        <div style="background: #d4edda; padding: 15px; margin: 20px 0; border-left: 4px solid #28a745;">
            <h3>✅ All Tests Have Prices!</h3>
            <p>All tests have prices set. The pricing calculation should work correctly.</p>
        </div>
    <?php endif; ?>
    
    <p style="margin-top: 30px;">
        <a href="entry-list.php" style="background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">← Back to Entry List</a>
        <a href="test.php" style="background: #2196F3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;">Go to Tests Page</a>
    </p>
</body>
</html>

