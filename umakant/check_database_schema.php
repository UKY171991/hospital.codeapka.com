<?php
// Script to check if the database columns exist
require_once 'inc/connection.php';

try {
    echo "Checking database schema for entries table...\n\n";
    
    // Check if the columns exist
    $columns = ['priority', 'referral_source', 'patient_contact', 'patient_address', 'gender', 'subtotal', 'discount_amount', 'total_price', 'notes'];
    
    foreach ($columns as $column) {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM `entries` LIKE ?");
        $stmt->execute([$column]);
        $result = $stmt->fetch();
        
        if ($result) {
            echo "✓ Column '$column' exists: {$result['Type']} (Default: {$result['Default']})\n";
        } else {
            echo "❌ Column '$column' MISSING!\n";
        }
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Complete entries table structure:\n";
    echo str_repeat("=", 50) . "\n";
    
    $stmt = $pdo->query("DESCRIBE `entries`");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo sprintf("%-20s %-20s %-10s %-10s %-20s %-10s\n", 
            $column['Field'], 
            $column['Type'], 
            $column['Null'], 
            $column['Key'], 
            $column['Default'], 
            $column['Extra']
        );
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Recent entries (last 3) to check data:\n";
    echo str_repeat("=", 50) . "\n";
    
    $stmt = $pdo->query("SELECT id, subtotal, discount_amount, total_price, priority, referral_source, notes, created_at FROM entries ORDER BY id DESC LIMIT 3");
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($entries as $entry) {
        echo "Entry ID: {$entry['id']}\n";
        echo "  Subtotal: {$entry['subtotal']}\n";
        echo "  Discount: {$entry['discount_amount']}\n";
        echo "  Total: {$entry['total_price']}\n";
        echo "  Priority: {$entry['priority']}\n";
        echo "  Referral Source: {$entry['referral_source']}\n";
        echo "  Notes: " . (empty($entry['notes']) ? 'NULL' : substr($entry['notes'], 0, 50) . '...') . "\n";
        echo "  Created: {$entry['created_at']}\n";
        echo str_repeat("-", 30) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "File: " . $e->getFile() . "\n";
}
?>
