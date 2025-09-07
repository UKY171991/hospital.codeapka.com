<?php
// Test the exact query being built by test_api.php
require_once __DIR__ . '/inc/connection.php';
header('Content-Type: application/json');

try {
    // Same logic as test_api.php for table detection
    $categories_table = 'categories';
    try{
        $stmt = $pdo->query("SHOW TABLES LIKE 'test_categories'");
        if($stmt->fetch()){
            $categories_table = 'test_categories';
        } else {
            // Verify categories table exists
            $stmt2 = $pdo->query("SHOW TABLES LIKE 'categories'");
            if(!$stmt2->fetch()) {
                $categories_table = 'test_categories'; // fallback
            }
        }
    }catch(Throwable $e){
        $categories_table = 'categories';
    }
    
    // Build the same query as test_api.php
    $whereClause = "";
    $params = [];
    $orderBy = " ORDER BY t.id DESC";
    $limit = " LIMIT 5";
    
    $dataQuery = "SELECT 
        t.id,
        COALESCE(t.name, '') AS name,
        COALESCE(tc.name, '') AS category_name,
        t.category_id,
        COALESCE(t.description, '') AS description,
        COALESCE(t.price, 0) AS price,
        COALESCE(t.unit, '') AS unit,
        t.min,
        t.max,
        t.min_male,
        t.max_male,
        t.min_female,
        t.max_female,
        COALESCE(t.sub_heading, 0) AS sub_heading,
        COALESCE(t.print_new_page, 0) AS print_new_page,
        COALESCE(u.username, '') AS added_by_username
        FROM tests t 
        LEFT JOIN {$categories_table} tc ON t.category_id = tc.id 
        LEFT JOIN users u ON t.added_by = u.id" . $whereClause . $orderBy . $limit;
    
    $dataStmt = $pdo->prepare($dataQuery);
    $dataStmt->execute($params);
    $data = $dataStmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'categories_table' => $categories_table,
        'query' => $dataQuery,
        'data_count' => count($data),
        'sample_data' => $data
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'line' => $e->getLine(),
        'file' => $e->getFile()
    ]);
}
?>
