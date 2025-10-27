<?php
/**
 * Verification script for categories table
 * This script checks if the categories table is properly set up
 */

require_once 'inc/connection.php';

header('Content-Type: application/json');

try {
    $result = [
        'success' => true,
        'checks' => [],
        'data' => []
    ];
    
    // Check 1: Table exists
    $tableExists = $pdo->query("SHOW TABLES LIKE 'categories'")->fetch() !== false;
    $result['checks']['table_exists'] = $tableExists;
    
    if (!$tableExists) {
        $result['success'] = false;
        $result['message'] = 'Categories table does not exist';
        echo json_encode($result);
        exit;
    }
    
    // Check 2: Table structure
    $columns = $pdo->query("DESCRIBE categories")->fetchAll(PDO::FETCH_ASSOC);
    $columnNames = array_column($columns, 'Field');
    
    $requiredColumns = ['id', 'name', 'description', 'is_active', 'created_at', 'updated_at', 'created_by'];
    $missingColumns = array_diff($requiredColumns, $columnNames);
    
    $result['checks']['required_columns'] = empty($missingColumns);
    $result['checks']['missing_columns'] = $missingColumns;
    $result['checks']['total_columns'] = count($columns);
    
    // Check 3: Indexes
    $indexes = $pdo->query("SHOW INDEX FROM categories")->fetchAll(PDO::FETCH_ASSOC);
    $result['checks']['indexes_count'] = count($indexes);
    
    // Check 4: Initial data
    $categoryCount = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    $result['checks']['category_count'] = $categoryCount;
    $result['checks']['has_initial_data'] = $categoryCount > 0;
    
    // Check 5: View exists
    $viewExists = $pdo->query("SHOW TABLES LIKE 'category_summary'")->fetch() !== false;
    $result['checks']['view_exists'] = $viewExists;
    
    // Check 6: Foreign key constraint
    $foreignKeys = $pdo->query("
        SELECT CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'categories' 
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    $result['checks']['foreign_keys'] = $foreignKeys;
    $result['checks']['has_user_fk'] = !empty($foreignKeys);
    
    // Get sample data
    if ($categoryCount > 0) {
        $result['data']['sample_categories'] = $pdo->query("
            SELECT id, name, description, is_active, created_at 
            FROM categories 
            ORDER BY name 
            LIMIT 10
        ")->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Overall success check
    $result['success'] = $result['checks']['table_exists'] && 
                        $result['checks']['required_columns'] && 
                        $result['checks']['has_initial_data'];
    
    if ($result['success']) {
        $result['message'] = 'Categories table is properly configured';
    } else {
        $result['message'] = 'Categories table has configuration issues';
    }
    
    echo json_encode($result, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Verification failed: ' . $e->getMessage(),
        'error' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}
?>