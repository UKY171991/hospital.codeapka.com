<?php
/**
 * Database migration script for categories table
 * Run this script to create the categories table and initial data
 */

require_once 'inc/connection.php';

// Enable error reporting for migration
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Categories Table Migration</h2>\n";
echo "<pre>\n";

try {
    // Check if categories table already exists
    $checkTable = $pdo->query("SHOW TABLES LIKE 'categories'");
    $tableExists = $checkTable->fetch() !== false;
    
    if ($tableExists) {
        echo "✓ Categories table already exists, checking structure...\n";
        
        // Check if table has all required columns
        $columns = $pdo->query("DESCRIBE categories")->fetchAll(PDO::FETCH_ASSOC);
        $columnNames = array_column($columns, 'Field');
        
        $requiredColumns = ['id', 'name', 'description', 'is_active', 'created_at', 'updated_at', 'created_by'];
        $missingColumns = array_diff($requiredColumns, $columnNames);
        
        if (empty($missingColumns)) {
            echo "✓ Categories table structure is complete\n";
        } else {
            echo "⚠ Missing columns: " . implode(', ', $missingColumns) . "\n";
            echo "Please update the table structure manually\n";
        }
    } else {
        echo "Creating categories table...\n";
        
        // Read and execute the SQL file
        $sqlFile = __DIR__ . '/sql/create_categories_table.sql';
        if (!file_exists($sqlFile)) {
            throw new Exception("SQL file not found: $sqlFile");
        }
        
        $sql = file_get_contents($sqlFile);
        
        // Split SQL into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (empty($statement) || strpos($statement, '--') === 0) {
                continue;
            }
            
            try {
                $pdo->exec($statement);
                echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
            } catch (PDOException $e) {
                // Skip if already exists errors
                if (strpos($e->getMessage(), 'already exists') !== false) {
                    echo "⚠ Skipped (already exists): " . substr($statement, 0, 50) . "...\n";
                } else {
                    throw $e;
                }
            }
        }
        
        echo "✓ Categories table created successfully\n";
    }
    
    // Verify the installation
    echo "\nVerifying installation...\n";
    
    // Check table structure
    $columns = $pdo->query("DESCRIBE categories")->fetchAll(PDO::FETCH_ASSOC);
    echo "✓ Table columns: " . count($columns) . "\n";
    
    // Check initial data
    $categoryCount = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    echo "✓ Initial categories: $categoryCount\n";
    
    // Check indexes
    $indexes = $pdo->query("SHOW INDEX FROM categories")->fetchAll(PDO::FETCH_ASSOC);
    echo "✓ Table indexes: " . count($indexes) . "\n";
    
    // Check view
    $viewExists = $pdo->query("SHOW TABLES LIKE 'category_summary'")->fetch() !== false;
    if ($viewExists) {
        echo "✓ Category summary view created\n";
    } else {
        echo "⚠ Category summary view not found\n";
    }
    
    // Display sample data
    echo "\nSample categories:\n";
    $categories = $pdo->query("SELECT id, name, description FROM categories LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($categories as $category) {
        echo "  - {$category['id']}: {$category['name']}\n";
    }
    
    echo "\n✅ Migration completed successfully!\n";
    echo "\nNext steps:\n";
    echo "1. Update tests table to add category_id column\n";
    echo "2. Create category management API\n";
    echo "3. Create category management interface\n";
    
} catch (Exception $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "</pre>\n";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
pre { background: #f5f5f5; padding: 15px; border-radius: 5px; }
h2 { color: #333; }
</style>