<?php
// ajax/main_dashboard_api.php - Main Dashboard API for all modules
try {
    require_once __DIR__ . '/../inc/connection.php';
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Database connection error',
        'error' => $e->getMessage()
    ]);
    exit;
}

require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

try {
    $action = $_REQUEST['action'] ?? 'overview';

    if ($action === 'overview') {
        try {
            $stats = [];
            
            // OPD Stats
            $opdDoctorsStmt = $pdo->query("SELECT COUNT(*) FROM opd_doctors");
            $stats['opd']['doctors'] = $opdDoctorsStmt->fetchColumn();
            
            $opdPatientsStmt = $pdo->query("SELECT COUNT(DISTINCT patient_name) FROM opd_reports");
            $stats['opd']['patients'] = $opdPatientsStmt->fetchColumn();
            
            $opdReportsStmt = $pdo->query("SELECT COUNT(*) FROM opd_reports");
            $stats['opd']['reports'] = $opdReportsStmt->fetchColumn();
            
            $opdRevenueStmt = $pdo->query("SELECT COALESCE(SUM(paid_amount), 0) FROM opd_billing");
            $stats['opd']['revenue'] = $opdRevenueStmt->fetchColumn();
            
            // Pathology Stats
            try {
                $pathoEntriesStmt = $pdo->query("SELECT COUNT(*) FROM entry_list");
                $stats['pathology']['entries'] = $pathoEntriesStmt->fetchColumn();
                
                $pathoTestsStmt = $pdo->query("SELECT COUNT(*) FROM test_category");
                $stats['pathology']['tests'] = $pathoTestsStmt->fetchColumn();
            } catch (PDOException $e) {
                $stats['pathology']['entries'] = 0;
                $stats['pathology']['tests'] = 0;
            }
            
            // Client Stats
            $clientsStmt = $pdo->query("SELECT COUNT(*) FROM clients");
            $stats['clients']['total'] = $clientsStmt->fetchColumn();
            
            // Email Stats
            try {
                $emailInboxStmt = $pdo->query("SELECT COUNT(*) FROM emails WHERE folder = 'inbox'");
                $stats['email']['inbox'] = $emailInboxStmt->fetchColumn();
                
                $emailUnreadStmt = $pdo->query("SELECT COUNT(*) FROM emails WHERE folder = 'inbox' AND is_read = 0");
                $stats['email']['unread'] = $emailUnreadStmt->fetchColumn();
            } catch (PDOException $e) {
                $stats['email']['inbox'] = 0;
                $stats['email']['unread'] = 0;
            }
            
            // Inventory Stats
            try {
                $inventoryIncomeStmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM inventory_income");
                $stats['inventory']['income'] = $inventoryIncomeStmt->fetchColumn();
                
                $inventoryExpenseStmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM inventory_expense");
                $stats['inventory']['expense'] = $inventoryExpenseStmt->fetchColumn();
                
                $stats['inventory']['balance'] = $stats['inventory']['income'] - $stats['inventory']['expense'];
            } catch (PDOException $e) {
                $stats['inventory']['income'] = 0;
                $stats['inventory']['expense'] = 0;
                $stats['inventory']['balance'] = 0;
            }
            
            json_response(['success' => true, 'data' => $stats]);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error fetching overview: ' . $e->getMessage()], 500);
        }
    }

    if ($action === 'today_stats') {
        try {
            $stats = [];
            
            // Today's OPD
            $todayOpdReportsStmt = $pdo->query("SELECT COUNT(*) FROM opd_reports WHERE DATE(report_date) = CURDATE()");
            $stats['opd']['today_reports'] = $todayOpdReportsStmt->fetchColumn();
            
            $todayOpdRevenueStmt = $pdo->query("SELECT COALESCE(SUM(paid_amount), 0) FROM opd_billing WHERE DATE(bill_date) = CURDATE()");
            $stats['opd']['today_revenue'] = $todayOpdRevenueStmt->fetchColumn();
            
            // Today's Pathology
            try {
                $todayPathoStmt = $pdo->query("SELECT COUNT(*) FROM entry_list WHERE DATE(created_at) = CURDATE()");
                $stats['pathology']['today_entries'] = $todayPathoStmt->fetchColumn();
            } catch (PDOException $e) {
                $stats['pathology']['today_entries'] = 0;
            }
            
            // Today's Emails
            try {
                $todayEmailsStmt = $pdo->query("SELECT COUNT(*) FROM emails WHERE DATE(received_date) = CURDATE()");
                $stats['email']['today_received'] = $todayEmailsStmt->fetchColumn();
            } catch (PDOException $e) {
                $stats['email']['today_received'] = 0;
            }
            
            json_response(['success' => true, 'data' => $stats]);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error fetching today stats: ' . $e->getMessage()], 500);
        }
    }

    if ($action === 'recent_activities') {
        try {
            $activities = [];
            
            // Recent OPD Reports
            $recentReportsStmt = $pdo->query("SELECT 'OPD Report' as type, patient_name as title, report_date as date FROM opd_reports ORDER BY report_date DESC LIMIT 5");
            $recentReports = $recentReportsStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Recent Bills
            $recentBillsStmt = $pdo->query("SELECT 'OPD Bill' as type, patient_name as title, bill_date as date FROM opd_billing ORDER BY bill_date DESC LIMIT 5");
            $recentBills = $recentBillsStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Recent Clients
            $recentClientsStmt = $pdo->query("SELECT 'New Client' as type, name as title, created_at as date FROM clients ORDER BY created_at DESC LIMIT 5");
            $recentClients = $recentClientsStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Merge and sort
            $activities = array_merge($recentReports, $recentBills, $recentClients);
            usort($activities, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
            
            $activities = array_slice($activities, 0, 10);
            
            json_response(['success' => true, 'data' => $activities]);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error fetching activities: ' . $e->getMessage()], 500);
        }
    }

    if ($action === 'monthly_revenue') {
        try {
            $stmt = $pdo->query("
                SELECT 
                    DATE_FORMAT(bill_date, '%Y-%m') as month,
                    SUM(paid_amount) as revenue
                FROM opd_billing 
                WHERE bill_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(bill_date, '%Y-%m')
                ORDER BY month ASC
            ");
            $revenue = $stmt->fetchAll(PDO::FETCH_ASSOC);
            json_response(['success' => true, 'data' => $revenue]);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error fetching revenue: ' . $e->getMessage()], 500);
        }
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $t) {
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
