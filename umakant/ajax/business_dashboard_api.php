<?php
// ajax/business_dashboard_api.php - Unified Business & CRM Dashboard API
header('Content-Type: application/json');
require_once '../inc/connection.php';

try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $userRole = $_SESSION['role'] ?? 'user';
    $userId = $_SESSION['user_id'] ?? null;

    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'get_unified_stats':
            getUnifiedStats($pdo, $userRole, $userId);
            break;
        case 'get_duplicates':
            getDuplicates($pdo);
            break;
        default:
            throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getUnifiedStats($pdo, $userRole, $userId) {
    $stats = [];

    // 1. Client Table Counts
    $stmt = $pdo->query("SELECT COUNT(*) FROM clients");
    $stats['main_clients'] = (int)$stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM inventory_clients");
    $stats['inventory_clients'] = (int)$stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM followup_clients");
    $stats['followup_clients'] = (int)$stmt->fetchColumn();

    // 2. Task Stats
    $stmt = $pdo->query("SELECT COUNT(*) FROM tasks WHERE status IN ('Pending', 'In Progress')");
    $stats['active_tasks'] = (int)$stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM tasks WHERE status = 'Pending' AND priority = 'Urgent'");
    $stats['urgent_tasks'] = (int)$stmt->fetchColumn();

    // 3. Inventory Monthly Stats (Current Month)
    $monthStart = date('Y-m-01');
    $monthEnd = date('Y-m-t');

    $stmt = $pdo->prepare("SELECT SUM(amount) FROM inventory_income WHERE date BETWEEN ? AND ? AND payment_status = 'Success'");
    $stmt->execute([$monthStart, $monthEnd]);
    $stats['month_income'] = (float)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT SUM(amount) FROM inventory_expense WHERE date BETWEEN ? AND ? AND payment_status = 'Success'");
    $stmt->execute([$monthStart, $monthEnd]);
    $stats['month_expense'] = (float)$stmt->fetchColumn();

    $stats['month_profit'] = $stats['month_income'] - $stats['month_expense'];

    // 4. Followup Stats
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM followup_clients WHERE next_followup_date = ?");
    $stmt->execute([$today]);
    $stats['today_followups'] = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM followup_clients WHERE next_followup_date < ? AND next_followup_date IS NOT NULL");
    $stmt->execute([$today]);
    $stats['overdue_followups'] = (int)$stmt->fetchColumn();

    // 5. Duplicate Count (Simple heuristic: same phone or email)
    $stmt = $pdo->query("
        SELECT COUNT(*) FROM (
            SELECT phone FROM clients WHERE phone != ''
            UNION ALL
            SELECT phone FROM inventory_clients WHERE phone != ''
            UNION ALL
            SELECT phone FROM followup_clients WHERE phone != ''
        ) as t GROUP BY phone HAVING COUNT(*) > 1
    ");
    $stats['duplicate_phones'] = $stmt->rowCount();

    echo json_encode(['success' => true, 'data' => $stats]);
}

function getDuplicates($pdo) {
    // Find clients with same phone across any table
    $sql = "
        SELECT 'Task Client' as source, id, name, phone, email FROM clients WHERE phone IN (
            SELECT phone FROM (
                SELECT phone FROM clients WHERE phone != ''
                UNION ALL
                SELECT phone FROM inventory_clients WHERE phone != ''
                UNION ALL
                SELECT phone FROM followup_clients WHERE phone != ''
            ) as t GROUP BY phone HAVING COUNT(*) > 1
        )
        UNION ALL
        SELECT 'Inventory Client' as source, id, name, phone, email FROM inventory_clients WHERE phone IN (
            SELECT phone FROM (
                SELECT phone FROM clients WHERE phone != ''
                UNION ALL
                SELECT phone FROM inventory_clients WHERE phone != ''
                UNION ALL
                SELECT phone FROM followup_clients WHERE phone != ''
            ) as t GROUP BY phone HAVING COUNT(*) > 1
        )
        UNION ALL
        SELECT 'Followup Client' as source, id, name, phone, email FROM followup_clients WHERE phone IN (
            SELECT phone FROM (
                SELECT phone FROM clients WHERE phone != ''
                UNION ALL
                SELECT phone FROM inventory_clients WHERE phone != ''
                UNION ALL
                SELECT phone FROM followup_clients WHERE phone != ''
            ) as t GROUP BY phone HAVING COUNT(*) > 1
        )
        ORDER BY phone, source
    ";
    
    $stmt = $pdo->query($sql);
    $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $duplicates]);
}
