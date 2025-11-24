<?php
// opd_api/patients.php - OPD Patients API
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

try {
    $action = $_REQUEST['action'] ?? 'list';

    // List patients (from reports)
    if ($action === 'list') {
        $doctorFilter = $_GET['doctor'] ?? '';
        
        if (!empty($doctorFilter)) {
            $stmt = $pdo->prepare("
                SELECT 
                    patient_name,
                    patient_phone,
                    patient_age,
                    patient_gender,
                    COUNT(*) as visit_count,
                    MAX(report_date) as last_visit,
                    MIN(report_date) as first_visit
                FROM opd_reports 
                WHERE doctor_name = ?
                GROUP BY patient_name, patient_phone
                ORDER BY last_visit DESC
            ");
            $stmt->execute([$doctorFilter]);
        } else {
            $stmt = $pdo->query("
                SELECT 
                    patient_name,
                    patient_phone,
                    patient_age,
                    patient_gender,
                    COUNT(*) as visit_count,
                    MAX(report_date) as last_visit,
                    MIN(report_date) as first_visit
                FROM opd_reports 
                GROUP BY patient_name, patient_phone
                ORDER BY last_visit DESC
            ");
        }
        
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $patients]);
    }

    // Get patient history
    if ($action === 'history' && isset($_GET['name'])) {
        $name = $_GET['name'];
        
        // Get reports
        $reportsStmt = $pdo->prepare("
            SELECT * FROM opd_reports 
            WHERE patient_name = ? 
            ORDER BY report_date DESC
        ");
        $reportsStmt->execute([$name]);
        $reports = $reportsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get bills
        $billsStmt = $pdo->prepare("
            SELECT * FROM opd_billing 
            WHERE patient_name = ? 
            ORDER BY bill_date DESC
        ");
        $billsStmt->execute([$name]);
        $bills = $billsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'success' => true,
            'data' => [
                'reports' => $reports,
                'bills' => $bills
            ]
        ]);
    }

    // Search patients
    if ($action === 'search' && isset($_GET['query'])) {
        $query = '%' . $_GET['query'] . '%';
        $stmt = $pdo->prepare("
            SELECT DISTINCT 
                patient_name,
                patient_phone,
                patient_age,
                patient_gender
            FROM opd_reports 
            WHERE patient_name LIKE ? OR patient_phone LIKE ?
            LIMIT 10
        ");
        $stmt->execute([$query, $query]);
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $patients]);
    }

    // Patient stats
    if ($action === 'stats') {
        $totalStmt = $pdo->query("SELECT COUNT(DISTINCT patient_name) FROM opd_reports");
        $total = $totalStmt->fetchColumn();
        
        $todayStmt = $pdo->query("SELECT COUNT(DISTINCT patient_name) FROM opd_reports WHERE DATE(report_date) = CURDATE()");
        $today = $todayStmt->fetchColumn();
        
        $weekStmt = $pdo->query("SELECT COUNT(DISTINCT patient_name) FROM opd_reports WHERE report_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
        $week = $weekStmt->fetchColumn();
        
        $monthStmt = $pdo->query("SELECT COUNT(DISTINCT patient_name) FROM opd_reports WHERE report_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
        $month = $monthStmt->fetchColumn();
        
        json_response([
            'success' => true,
            'data' => [
                'total' => $total,
                'today' => $today,
                'week' => $week,
                'month' => $month
            ]
        ]);
    }

    // Get doctors list
    if ($action === 'get_doctors') {
        try {
            $checkColumn = $pdo->query("SHOW COLUMNS FROM opd_doctors LIKE 'status'");
            $statusExists = $checkColumn->rowCount() > 0;
            
            if ($statusExists) {
                $stmt = $pdo->query("SELECT id, name, specialization, hospital FROM opd_doctors WHERE status = 'Active' OR status IS NULL ORDER BY name ASC");
            } else {
                $stmt = $pdo->query("SELECT id, name, specialization, hospital FROM opd_doctors ORDER BY name ASC");
            }
            
            $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            json_response(['success' => true, 'data' => $doctors]);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error fetching doctors: ' . $e->getMessage()], 500);
        }
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $t) {
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
