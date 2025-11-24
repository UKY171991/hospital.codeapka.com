<?php
// opd_api/dashboard.php - OPD Dashboard API
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

try {
    $action = $_REQUEST['action'] ?? 'stats';

    // Dashboard stats
    if ($action === 'stats') {
        // Check if tables exist
        $checkTables = $pdo->query("SHOW TABLES LIKE 'opd_doctors'");
        if ($checkTables->rowCount() === 0) {
            json_response([
                'success' => false,
                'message' => 'OPD tables not found. Please run the SQL schema to create the required tables.',
                'data' => [
                    'doctors' => ['total' => 0, 'active' => 0],
                    'patients' => ['total' => 0, 'today' => 0],
                    'reports' => ['total' => 0, 'week' => 0],
                    'billing' => ['total' => 0, 'revenue' => '0.00', 'todayRevenue' => '0.00', 'pending' => '0.00'],
                    'followUps' => ['upcoming' => 0, 'overdue' => 0]
                ]
            ]);
        }
        
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
        $totalPatientsStmt = $pdo->query("SELECT COUNT(*) FROM opd_patients WHERE is_active = 1");
        $totalPatients = $totalPatientsStmt->fetchColumn();
        
        $todayPatientsStmt = $pdo->query("SELECT COUNT(*) FROM opd_appointments WHERE DATE(appointment_date) = CURDATE()");
        $todayPatients = $todayPatientsStmt->fetchColumn();
        
        // Medical Records stats (using opd_medical_records instead of opd_reports)
        $totalReportsStmt = $pdo->query("SELECT COUNT(*) FROM opd_medical_records");
        $totalReports = $totalReportsStmt->fetchColumn();
        
        $weekReportsStmt = $pdo->query("SELECT COUNT(*) FROM opd_medical_records WHERE record_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
        $weekReports = $weekReportsStmt->fetchColumn();
        
        // Billing stats (check if opd_billing table exists, otherwise use appointments)
        $checkBilling = $pdo->query("SHOW TABLES LIKE 'opd_billing'");
        if ($checkBilling->rowCount() > 0) {
            $totalBillsStmt = $pdo->query("SELECT COUNT(*) FROM opd_billing");
            $totalBills = $totalBillsStmt->fetchColumn();
            
            $totalRevenueStmt = $pdo->query("SELECT COALESCE(SUM(paid_amount), 0) FROM opd_billing");
            $totalRevenue = $totalRevenueStmt->fetchColumn();
            
            $todayRevenueStmt = $pdo->query("SELECT COALESCE(SUM(paid_amount), 0) FROM opd_billing WHERE DATE(bill_date) = CURDATE()");
            $todayRevenue = $todayRevenueStmt->fetchColumn();
            
            $pendingAmountStmt = $pdo->query("SELECT COALESCE(SUM(balance_amount), 0) FROM opd_billing WHERE payment_status != 'Paid'");
            $pendingAmount = $pendingAmountStmt->fetchColumn();
        } else {
            // Use appointments fee as fallback
            $totalBillsStmt = $pdo->query("SELECT COUNT(*) FROM opd_appointments WHERE payment_status = 'paid'");
            $totalBills = $totalBillsStmt->fetchColumn();
            
            $totalRevenueStmt = $pdo->query("SELECT COALESCE(SUM(fee), 0) FROM opd_appointments WHERE payment_status = 'paid'");
            $totalRevenue = $totalRevenueStmt->fetchColumn();
            
            $todayRevenueStmt = $pdo->query("SELECT COALESCE(SUM(fee), 0) FROM opd_appointments WHERE payment_status = 'paid' AND DATE(appointment_date) = CURDATE()");
            $todayRevenue = $todayRevenueStmt->fetchColumn();
            
            $pendingAmountStmt = $pdo->query("SELECT COALESCE(SUM(fee), 0) FROM opd_appointments WHERE payment_status = 'pending'");
            $pendingAmount = $pendingAmountStmt->fetchColumn();
        }
        
        // Follow-ups (using appointments for now)
        $upcomingFollowUpsStmt = $pdo->query("SELECT COUNT(*) FROM opd_appointments WHERE appointment_date >= CURDATE() AND appointment_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND status IN ('scheduled', 'confirmed')");
        $upcomingFollowUps = $upcomingFollowUpsStmt->fetchColumn();
        
        $overdueFollowUpsStmt = $pdo->query("SELECT COUNT(*) FROM opd_appointments WHERE appointment_date < CURDATE() AND status IN ('scheduled', 'confirmed')");
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

    // Recent reports (medical records)
    if ($action === 'recent_reports') {
        $stmt = $pdo->query("SELECT mr.id, p.name as patient_name, d.name as doctor_name, mr.record_date as report_date, mr.diagnosis 
                             FROM opd_medical_records mr 
                             LEFT JOIN opd_patients p ON mr.patient_id = p.id 
                             LEFT JOIN opd_doctors d ON mr.doctor_id = d.id 
                             ORDER BY mr.record_date DESC, mr.id DESC LIMIT 5");
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $reports]);
    }

    // Recent bills
    if ($action === 'recent_bills') {
        // Check if opd_billing table exists
        $checkBilling = $pdo->query("SHOW TABLES LIKE 'opd_billing'");
        if ($checkBilling->rowCount() > 0) {
            $stmt = $pdo->query("SELECT id, patient_name, total_amount, paid_amount, payment_status, bill_date FROM opd_billing ORDER BY bill_date DESC, id DESC LIMIT 5");
        } else {
            // Use appointments as fallback
            $stmt = $pdo->query("SELECT a.id, p.name as patient_name, a.fee as total_amount, a.fee as paid_amount, a.payment_status, a.appointment_date as bill_date 
                                 FROM opd_appointments a 
                                 LEFT JOIN opd_patients p ON a.patient_id = p.id 
                                 WHERE a.payment_status = 'paid'
                                 ORDER BY a.appointment_date DESC, a.id DESC LIMIT 5");
        }
        $bills = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $bills]);
    }

    // Upcoming follow-ups (using appointments)
    if ($action === 'upcoming_followups') {
        $stmt = $pdo->query("SELECT a.id, p.name as patient_name, d.name as doctor_name, a.appointment_date as follow_up_date 
                             FROM opd_appointments a 
                             LEFT JOIN opd_patients p ON a.patient_id = p.id 
                             LEFT JOIN opd_doctors d ON a.doctor_id = d.id 
                             WHERE a.appointment_date >= CURDATE() AND a.status IN ('scheduled', 'confirmed')
                             ORDER BY a.appointment_date ASC LIMIT 5");
        $followups = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $followups]);
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $t) {
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
