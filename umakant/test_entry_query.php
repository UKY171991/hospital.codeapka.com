<?php
// Test entry_api.php endpoints
require_once __DIR__ . '/inc/connection.php';

header('Content-Type: application/json');

try {
    // Test the updated query that matches entry_api.php
    $sql = "SELECT e.*, 
               p.name AS patient_name, p.uhid, p.age, p.sex AS gender,
               t.name AS test_name, COALESCE(t.unit, '') AS units,
               t.reference_range, t.min_male, t.max_male, t.min_female, t.max_female,
               d.name AS doctor_name
        FROM entries e 
        LEFT JOIN patients p ON e.patient_id = p.id 
        LEFT JOIN tests t ON e.test_id = t.id 
        LEFT JOIN doctors d ON e.doctor_id = d.id 
        ORDER BY COALESCE(e.test_date, e.entry_date, e.created_at) DESC, e.id DESC 
        LIMIT 5";
    
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Test stats query
    $stats = [];
    $stmt = $pdo->query("SELECT COUNT(*) FROM entries");
    $stats['total'] = (int) $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM entries WHERE status = 'pending'");
    $stats['pending'] = (int) $stmt->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'message' => 'Entry API queries executed successfully',
        'stats' => $stats,
        'entries_count' => count($rows),
        'sample_entries' => $rows,
        'test_urls' => [
            'list' => '/umakant/ajax/entry_api.php?action=list&ajax=1',
            'stats' => '/umakant/ajax/entry_api.php?action=stats',
            'patho_api_list' => '/umakant/patho_api/entry.php?action=list',
            'api_docs' => '/umakant/patho_api/api.html'
        ]
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}
?>
