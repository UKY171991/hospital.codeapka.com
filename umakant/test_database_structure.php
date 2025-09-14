<?php
// Test database structure and API
require_once __DIR__ . '/inc/connection.php';

header('Content-Type: application/json');

try {
    // Test 1: Check table structure
    $stmt = $pdo->query("DESCRIBE entries");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Test 2: Check if test_date column exists
    $has_test_date = false;
    $has_entry_date = false;
    $has_created_at = false;
    
    foreach ($columns as $column) {
        if ($column['Field'] === 'test_date') $has_test_date = true;
        if ($column['Field'] === 'entry_date') $has_entry_date = true;
        if ($column['Field'] === 'created_at') $has_created_at = true;
    }
    
    // Test 3: Try the exact query from entry_api.php
    $sql = "SELECT e.*, 
               p.name AS patient_name, p.uhid, p.age, p.sex AS gender,
               t.name AS test_name, COALESCE(t.unit, '') AS units,
               t.reference_range, t.min_male, t.max_male, t.min_female, t.max_female,
               d.name AS doctor_name,
               u.username AS added_by_username
        FROM entries e 
        LEFT JOIN patients p ON e.patient_id = p.id 
        LEFT JOIN tests t ON e.test_id = t.id 
        LEFT JOIN doctors d ON e.doctor_id = d.id 
        LEFT JOIN users u ON e.added_by = u.id
        ORDER BY COALESCE(e.entry_date, e.created_at) DESC, e.id DESC
        LIMIT 5";
    
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Database structure test completed',
        'table_structure' => [
            'has_test_date' => $has_test_date,
            'has_entry_date' => $has_entry_date,
            'has_created_at' => $has_created_at,
            'all_columns' => array_column($columns, 'Field')
        ],
        'query_test' => [
            'success' => true,
            'rows_count' => count($rows),
            'sample_data' => $rows
        ],
        'recommendations' => [
            'run_alter_sql' => !$has_entry_date || !$has_created_at,
            'alter_file' => '015_immediate_fix_entries.sql'
        ]
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'error_code' => $e->getCode(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}
?>
