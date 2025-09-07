<?php
// Simple test to check if test data is being returned correctly
header('Content-Type: application/json');

try {
    require_once 'inc/connection.php';
    
    // Check which categories table exists
    $categories_table = 'categories'; // Based on the SQL dump, this should be 'categories'
    
    // Simple query to get test data
    $query = "SELECT 
        t.id,
        t.name,
        t.category_id,
        tc.name as category_name,
        t.description,
        t.price,
        t.unit
        FROM tests t 
        LEFT JOIN {$categories_table} tc ON t.category_id = tc.id 
        ORDER BY t.id DESC 
        LIMIT 10";
    
    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'count' => count($data),
        'data' => $data,
        'categories_table' => $categories_table
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
