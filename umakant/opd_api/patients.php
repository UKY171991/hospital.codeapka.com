<?php
// opd_api/patients.php - OPD Patients API
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

try {
    $action = $_REQUEST['action'] ?? 'list';

    // List patients
    if ($action === 'list') {
        $doctorFilter = $_GET['doctor'] ?? '';
        
        // Base query to get patients and their visit stats
        // opd_patients table has 'dob', not 'age'. We calculate age.
        
        if (!empty($doctorFilter)) {
            $sql = "
                SELECT 
                    p.id,
                    p.name as patient_name,
                    p.phone as patient_phone,
                    TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) as patient_age,
                    p.gender as patient_gender,
                    COUNT(r.id) as visit_count,
                    MAX(r.report_date) as last_visit,
                    MIN(r.report_date) as first_visit
                FROM opd_patients p
                JOIN opd_reports r ON p.name = r.patient_name
                WHERE r.doctor_name = ?
                GROUP BY p.id, p.name, p.phone, p.dob, p.gender
                ORDER BY last_visit DESC
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$doctorFilter]);
        } else {
            $sql = "
                SELECT 
                    p.id,
                    p.name as patient_name,
                    p.phone as patient_phone,
                    TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) as patient_age,
                    p.gender as patient_gender,
                    COUNT(r.id) as visit_count,
                    MAX(r.report_date) as last_visit,
                    MIN(r.report_date) as first_visit
                FROM opd_patients p
                LEFT JOIN opd_reports r ON p.name = r.patient_name
                GROUP BY p.id, p.name, p.phone, p.dob, p.gender
                ORDER BY p.id DESC
            ";
            $stmt = $pdo->query($sql);
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
            SELECT  
                p.id,
                p.name as patient_name,
                p.phone as patient_phone,
                TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) as patient_age,
                p.gender as patient_gender,
                (SELECT COUNT(*) FROM opd_reports r WHERE r.patient_name = p.name) as visit_count
            FROM opd_patients p
            WHERE p.name LIKE ? OR p.phone LIKE ?
            LIMIT 10
        ");
        $stmt->execute([$query, $query]);
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $patients]);
    }

    // Patient stats
    if ($action === 'stats') {
        $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_patients");
        $total = $totalStmt->fetchColumn();
        
        $todayStmt = $pdo->query("SELECT COUNT(*) FROM opd_patients WHERE DATE(created_at) = CURDATE()");
        $today = $todayStmt->fetchColumn();

        $weekStmt = $pdo->query("SELECT COUNT(*) FROM opd_patients WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
        $week = $weekStmt->fetchColumn();

        $monthStmt = $pdo->query("SELECT COUNT(*) FROM opd_patients WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
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
                $stmt = $pdo->query("SELECT id, name, specialization FROM opd_doctors WHERE status = 'Active' OR status IS NULL ORDER BY name ASC");
            } else {
                $stmt = $pdo->query("SELECT id, name, specialization FROM opd_doctors ORDER BY name ASC");
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
