<?php
// Debug entry API - Check what's actually being called
require_once __DIR__ . '/inc/connection.php';

header('Content-Type: application/json');

// Log the request
error_log("Entry API Debug - Action: " . ($_GET['action'] ?? 'none'));

try {
    $action = $_GET['action'] ?? 'list';
    
    if ($action === 'list') {
        // Test the exact query from entry_api.php
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
            ORDER BY COALESCE(e.entry_date, e.created_at) DESC, e.id DESC";
        
        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'message' => 'Debug query executed successfully',
            'count' => count($rows),
            'data' => $rows
        ], JSON_PRETTY_PRINT);
        
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Unknown action: ' . $action
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error',
        'error' => $e->getMessage(),
        'error_code' => $e->getCode()
    ], JSON_PRETTY_PRINT);
}
?>
