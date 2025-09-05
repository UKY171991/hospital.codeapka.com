<?php
require_once '../inc/connection.php';

header('Content-Type: application/json');

try {
    $action = $_REQUEST['action'] ?? 'test';
    
    switch ($action) {
        case 'test':
            echo json_encode([
                'success' => true,
                'message' => 'API is working',
                'timestamp' => date('Y-m-d H:i:s'),
                'database' => $pdo ? 'connected' : 'not connected'
            ]);
            break;
            
        case 'stats':
            if (!$pdo) {
                throw new Exception('Database not connected');
            }
            
            // Simple stats without error handling
            $total = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'total' => (int)$total,
                    'today' => 0,
                    'male' => 0,
                    'female' => 0
                ]
            ]);
            break;
            
        case 'list':
            if (!$pdo) {
                throw new Exception('Database not connected');
            }
            
            $stmt = $pdo->query("SELECT * FROM patients LIMIT 10");
            $patients = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'data' => $patients,
                'pagination' => [
                    'current_page' => 1,
                    'total_pages' => 1,
                    'total_records' => count($patients),
                    'records_per_page' => 10
                ]
            ]);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Unknown action: ' . $action
            ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'action' => $action ?? 'unknown'
    ]);
}
?>
