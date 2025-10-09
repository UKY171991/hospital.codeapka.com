<?php
require_once __DIR__ . '/inc/connection.php';

echo "Checking and fixing entry_tests table...\n";

try {
    // Check if entry_tests table exists
    $tables = $pdo->query("SHOW TABLES LIKE 'entry_tests'")->fetchAll();
    
    if (count($tables) == 0) {
        echo "entry_tests table does not exist. Creating it...\n";
        
        // Create the table
        $createTable = "
        CREATE TABLE `entry_tests` (
          `id` int(10) UNSIGNED NOT NULL,
          `entry_id` int(10) UNSIGNED NOT NULL,
          `test_id` int(10) UNSIGNED NOT NULL,
          `result_value` varchar(255) DEFAULT NULL,
          `unit` varchar(64) DEFAULT NULL,
          `remarks` text DEFAULT NULL,
          `status` varchar(32) NOT NULL DEFAULT 'pending',
          `price` decimal(10,2) DEFAULT 0.00,
          `discount_amount` decimal(10,2) DEFAULT 0.00,
          `total_price` decimal(10,2) DEFAULT 0.00,
          `created_at` datetime DEFAULT current_timestamp()
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        $pdo->exec($createTable);
        
        // Add indexes
        $pdo->exec("ALTER TABLE `entry_tests` ADD PRIMARY KEY (`id`)");
        $pdo->exec("ALTER TABLE `entry_tests` ADD KEY `idx_entry_id` (`entry_id`)");
        $pdo->exec("ALTER TABLE `entry_tests` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1");
        
        echo "entry_tests table created successfully!\n";
        
        // Insert sample data
        $sampleData = "
        INSERT INTO `entry_tests` (`id`, `entry_id`, `test_id`, `result_value`, `unit`, `remarks`, `status`, `price`, `discount_amount`, `total_price`, `created_at`) VALUES
        (1, 1, 2, '40', 'etc', NULL, 'pending', 300.00, 0.00, 300.00, '2025-10-06 14:13:29'),
        (2, 2, 2, '40', 'etc', NULL, 'pending', 100.00, 0.00, 100.00, '2025-10-08 11:09:43'),
        (3, 5, 2, NULL, 'etc', NULL, 'pending', 200.00, 0.00, 200.00, '2025-10-06 16:21:47'),
        (4, 5, 1, NULL, 'abc', NULL, 'pending', 300.00, 0.00, 300.00, '2025-10-06 16:21:47');
        ";
        
        $pdo->exec($sampleData);
        echo "Sample data inserted!\n";
        
    } else {
        echo "entry_tests table exists.\n";
        
        // Check count
        $count = $pdo->query("SELECT COUNT(*) FROM entry_tests")->fetchColumn();
        echo "Current records: " . $count . "\n";
        
        if ($count == 0) {
            echo "Table is empty. Inserting sample data...\n";
            
            // Insert sample data
            $sampleData = "
            INSERT INTO `entry_tests` (`id`, `entry_id`, `test_id`, `result_value`, `unit`, `remarks`, `status`, `price`, `discount_amount`, `total_price`, `created_at`) VALUES
            (1, 1, 2, '40', 'etc', NULL, 'pending', 300.00, 0.00, 300.00, '2025-10-06 14:13:29'),
            (2, 2, 2, '40', 'etc', NULL, 'pending', 100.00, 0.00, 100.00, '2025-10-08 11:09:43'),
            (3, 5, 2, NULL, 'etc', NULL, 'pending', 200.00, 0.00, 200.00, '2025-10-06 16:21:47'),
            (4, 5, 1, NULL, 'abc', NULL, 'pending', 300.00, 0.00, 300.00, '2025-10-06 16:21:47');
            ";
            
            $pdo->exec($sampleData);
            echo "Sample data inserted!\n";
        }
    }
    
    // Test the API query
    echo "Testing API query...\n";
    $stmt = $pdo->prepare("
        SELECT et.*, t.name as test_name, t.rate as test_rate, t.unit as test_unit
        FROM entry_tests et
        LEFT JOIN tests t ON et.test_id = t.id
        WHERE et.entry_id = ?
        ORDER BY et.id ASC
    ");
    $stmt->execute([1]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Query result for entry_id 1:\n";
    print_r($result);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
