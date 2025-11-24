<?php
// opd_api/dashboard.php - OPD Dashboard API
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

try {
    $action = $_REQUEST['action'] ?? 'stats';

    // Dashboard stats
    if ($action === 'stats') {
        // Doctors stats
        $totalDoctorsStmt = $pdo->query("SELECT COUNT(*) FROM opd_doctors");
        $totalDoctors = $totalDoctorsStmt->fetchColumn();
        
        $checkColumn = $pdo->query("SHOW COLUMNS FROM opd_doctors LIKE 'status'");
        $statusExists = $checkColumn->rowCount() > 0;
        
        if ($statusExists) {
            $activeDoctorsStmt = $pdo->query("SELECT COUNT(*) FROM opd_doctors WHERE status = 'Active' OR status IS NULL");
            $activeDoctors = $activeDoctorsStmt->fetchColumn();
        } else {
            $activeDoctors = $totalDoctors;
        }
        
        // Patients stats
        $totalPatientsStmt = $pdo->query("SELECT COUNT(DISTINCT patient_name) FROM opd_reports");
        $totalPatients = $totalPatientsStmt->fetchColumn();
        
        $todayPatientsStmt = $pdo->query("SELECT COUNT(*) FROM opd_reports WHERE DATE(report_date) = CURDATE()");
        $todayPatients = $todayPatientsStmt->fetchColumn();
        
        // Reports stats
        $totalReportsStmt = $pdo->query("SELECT COUNT(*) FROM opd_reports");
        $totalReports = $totalReportsStmt->fetchColumn();
        
        $weekReportsStmt = $pdo->query("SELECT COUNT(*) FROM opd_reports WHERE report_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
        $weekReports = $weekReportsStmt->fetchColumn();
        
        // Billing stats
        $totalBillsStmt = $pdo->query("SELECT COUNT(*) FROM opd_billing");
        $totalBills = $totalBillsStmt->fetchColumn();
        
        $totalRevenueStmt = $pdo->query("SELECT COALESCE(SUM(paid_amount), 0) FROM opd_billing");
        $totalRevenue = $totalRevenueStmt->fetchColumn();
        
        $todayRevenueStmt = $pdo->query("SELECT COALESCE(SUM(paid_amount), 0) FROM opd_billing WHERE DATE(bill_date) = CURDATE()");
        $todayRevenue = $todayRevenueStmt->fetchColumn();
        
        $pendingAmountStmt = $pdo->query("SELECT COALESCE(SUM(balance_amount), 0) FROM opd_billing WHERE payment_status != 'Paid'");
        $pendingAmount = $pendingAmountStmt->fetchColumn();
        
        // Follow-ups
        $upcomingFollowUpsStmt = $pdo->query("SELECT COUNT(*) FROM opd_reports WHERE follow_up_date >= CURDATE() AND follow_up_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)");
        $upcomingFollowUps = $upcomingFollowUpsStmt->fetchColumn();
        
        $overdueFollowUpsStmt = $pdo->query("SELECT COUNT(*) FROM opd_reports WHERE follow_up_date < CURDATE()");
        $overdueFollowUps = $overdueFollowUpsStmt->fetchColumn();
        
        json_response([
            'success' => true,
            'data' => [
                'doctors' => [
                    'total' => $totalDoctors,
                    'active' => $activeDoctors
                ],
                'patients' => [
                    'total' => $totalPatients,
                    'today' => $todayPatients
                ],
                'reports' => [
                    'total' => $totalReports,
                    'week' => $weekReports
                ],
                'billing' => [
                    'total' => $totalBills,
                    'revenue' => number_format($totalRevenue, 2),
                    'todayRevenue' => number_format($todayRevenue, 2),
                    'pending' => number_format($pendingAmount, 2)
                ],
                'followUps' => [
                    'upcoming' => $upcomingFollowUps,
                    'overdue' => $overdueFollowUps
                ]
            ]
        ]);
    }

    // Recent reports
    if ($action === 'recent_reports') {
        $stmt = $pdo->query("SELECT id, patient_name, doctor_name, report_date, diagnosis FROM opd_reports ORDER BY report_date DESC, id DESC LIMIT 5");
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $reports]);
    }

    // Recent bills
    if ($action === 'recent_bills') {
        $stmt = $pdo->query("SELECT id, patient_name, total_amount, paid_amount, payment_status, bill_date FROM opd_billing ORDER BY bill_date DESC, id DESC LIMIT 5");
        $bills = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $bills]);
    }

    // Upcoming follow-ups
    if ($action === 'upcoming_followups') {
        $stmt = $pdo->query("SELECT id, patient_name, doctor_name, follow_up_date FROM opd_reports WHERE follow_up_date >= CURDATE() ORDER BY follow_up_date ASC LIMIT 5");
        $followups = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $followups]);
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $t) {
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
