<?php
// Quick test for entry_api.php list action
require_once __DIR__ . '/../inc/connection.php';

header('Content-Type: application/json');

try {
    // Test the same query that entry_api.php uses
    $stmt = $pdo->query("SELECT e.id, p.name AS patient_name, d.name AS doctor_name, t.name AS test_name, e.entry_date, e.result_value, COALESCE(e.unit, t.unit, '') AS unit, e.remarks, e.status FROM entries e LEFT JOIN patients p ON e.patient_id = p.id LEFT JOIN doctors d ON e.doctor_id = d.id LEFT JOIN tests t ON e.test_id = t.id ORDER BY e.entry_date DESC, e.id DESC LIMIT 5");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Query executed successfully',
        'count' => count($rows),
        'data' => $rows
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>
