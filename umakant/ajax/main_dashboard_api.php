<?php
// ajax/main_dashboard_api.php - Main Dashboard API for all modules
try {
    require_once __DIR__ . '/../inc/connection.php';
    require_once __DIR__ . '/../inc/auth.php';
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

$userIds = getUsersUnderAdmin($pdo);

try {
    $action = $_REQUEST['action'] ?? 'overview';

    if ($action === 'overview') {
        try {
            $stats = [];
            
            // OPD Stats - check if tables exist first
            try {
                $checkOpdTables = $pdo->query("SHOW TABLES LIKE 'opd_doctors'");
                if ($checkOpdTables->rowCount() > 0) {
                    $stats['opd']['doctors'] = queryWithFilter($pdo, "SELECT COUNT(*) FROM opd_doctors", 'opd_doctors', $userIds)->fetchColumn();
                    
                    $stats['opd']['patients'] = queryWithFilter($pdo, "SELECT COUNT(*) FROM opd_patients WHERE is_active = 1", 'opd_patients', $userIds)->fetchColumn();
                    
                    $stats['opd']['reports'] = queryWithFilter($pdo, "SELECT COUNT(*) FROM opd_medical_records", 'opd_medical_records', $userIds)->fetchColumn();
                    
                    // Check if opd_billing exists, otherwise use appointments
                    $checkBilling = $pdo->query("SHOW TABLES LIKE 'opd_billing'");
                    if ($checkBilling->rowCount() > 0) {
                        $stats['opd']['revenue'] = queryWithFilter($pdo, "SELECT COALESCE(SUM(paid_amount), 0) FROM opd_billing", 'opd_billing', $userIds)->fetchColumn();
                    } else {
                        $stats['opd']['revenue'] = queryWithFilter($pdo, "SELECT COALESCE(SUM(fee), 0) FROM opd_appointments WHERE payment_status = 'paid'", 'opd_appointments', $userIds)->fetchColumn();
                    }
                } else {
                    $stats['opd']['doctors'] = 0;
                    $stats['opd']['patients'] = 0;
                    $stats['opd']['reports'] = 0;
                    $stats['opd']['revenue'] = 0;
                }
            } catch (PDOException $e) {
                $stats['opd']['doctors'] = 0;
                $stats['opd']['patients'] = 0;
                $stats['opd']['reports'] = 0;
                $stats['opd']['revenue'] = 0;
            }
            
            // Pathology Stats
            try {
                $stats['pathology']['entries'] = queryWithFilter($pdo, "SELECT COUNT(*) FROM entry_list", 'entry_list', $userIds)->fetchColumn();
                $stats['pathology']['tests'] = queryWithFilter($pdo, "SELECT COUNT(*) FROM test_category", 'test_category', $userIds)->fetchColumn();
            } catch (PDOException $e) {
                $stats['pathology']['entries'] = 0;
                $stats['pathology']['tests'] = 0;
            }
            
            // Client Stats
            $stats['clients']['total'] = queryWithFilter($pdo, "SELECT COUNT(*) FROM clients", 'clients', $userIds)->fetchColumn();
            
            // Email Stats
            try {
                $stats['email']['inbox'] = queryWithFilter($pdo, "SELECT COUNT(*) FROM emails WHERE folder = 'inbox'", 'emails', $userIds)->fetchColumn();
                $stats['email']['unread'] = queryWithFilter($pdo, "SELECT COUNT(*) FROM emails WHERE folder = 'inbox' AND is_read = 0", 'emails', $userIds)->fetchColumn();
            } catch (PDOException $e) {
                $stats['email']['inbox'] = 0;
                $stats['email']['unread'] = 0;
            }
            
            // Inventory Stats
            try {
                $stats['inventory']['income'] = queryWithFilter($pdo, "SELECT COALESCE(SUM(amount), 0) FROM inventory_income WHERE payment_status = 'Success'", 'inventory_income', $userIds)->fetchColumn();
                $stats['inventory']['expense'] = queryWithFilter($pdo, "SELECT COALESCE(SUM(amount), 0) FROM inventory_expense WHERE payment_status = 'Success'", 'inventory_expense', $userIds)->fetchColumn();
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
            
            // Today's OPD - check if tables exist
            try {
                $checkOpdTables = $pdo->query("SHOW TABLES LIKE 'opd_medical_records'");
                if ($checkOpdTables->rowCount() > 0) {
                    $stats['opd']['today_reports'] = queryWithFilter($pdo, "SELECT COUNT(*) FROM opd_medical_records WHERE DATE(record_date) = CURDATE()", 'opd_medical_records', $userIds)->fetchColumn();
                    
                    // Check if opd_billing exists
                    $checkBilling = $pdo->query("SHOW TABLES LIKE 'opd_billing'");
                    if ($checkBilling->rowCount() > 0) {
                        $stats['opd']['today_revenue'] = queryWithFilter($pdo, "SELECT COALESCE(SUM(paid_amount), 0) FROM opd_billing WHERE DATE(bill_date) = CURDATE()", 'opd_billing', $userIds)->fetchColumn();
                    } else {
                        $stats['opd']['today_revenue'] = queryWithFilter($pdo, "SELECT COALESCE(SUM(fee), 0) FROM opd_appointments WHERE payment_status = 'paid' AND DATE(appointment_date) = CURDATE()", 'opd_appointments', $userIds)->fetchColumn();
                    }
                } else {
                    $stats['opd']['today_reports'] = 0;
                    $stats['opd']['today_revenue'] = 0;
                }
            } catch (PDOException $e) {
                $stats['opd']['today_reports'] = 0;
                $stats['opd']['today_revenue'] = 0;
            }
            
            // Today's Pathology
            try {
                $stats['pathology']['today_entries'] = queryWithFilter($pdo, "SELECT COUNT(*) FROM entry_list WHERE DATE(created_at) = CURDATE()", 'entry_list', $userIds)->fetchColumn();
            } catch (PDOException $e) {
                $stats['pathology']['today_entries'] = 0;
            }
            
            // Today's Emails
            try {
                $stats['email']['today_received'] = queryWithFilter($pdo, "SELECT COUNT(*) FROM emails WHERE DATE(received_date) = CURDATE()", 'emails', $userIds)->fetchColumn();
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
            $recentReports = queryWithFilter($pdo, "SELECT 'OPD Report' as type, patient_name as title, report_date as date FROM opd_reports", 'opd_reports', $userIds)->fetchAll(PDO::FETCH_ASSOC);
            
            // Recent Bills
            $recentBills = queryWithFilter($pdo, "SELECT 'OPD Bill' as type, patient_name as title, bill_date as date FROM opd_billing", 'opd_billing', $userIds)->fetchAll(PDO::FETCH_ASSOC);
            
            // Recent Clients
            $recentClients = queryWithFilter($pdo, "SELECT 'New Client' as type, name as title, created_at as date FROM clients", 'clients', $userIds)->fetchAll(PDO::FETCH_ASSOC);
            
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
            $sql = "SELECT 
                        DATE_FORMAT(bill_date, '%Y-%m') as month,
                        SUM(paid_amount) as revenue
                    FROM opd_billing 
                    WHERE bill_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
            
            // Manual filter for aggregate query
            if ($userIds !== null) {
                $placeholders = implode(',', array_fill(0, count($userIds), '?'));
                $sql .= " AND added_by IN ($placeholders)";
            }
            
            $sql .= " GROUP BY DATE_FORMAT(bill_date, '%Y-%m') ORDER BY month ASC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($userIds ?? []);
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
