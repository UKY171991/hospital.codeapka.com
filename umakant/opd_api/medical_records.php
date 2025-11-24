<?php
// opd_api/medical_records.php - OPD Medical Records API
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

try {
    $action = $_REQUEST['action'] ?? 'list';

    // List medical records
    if ($action === 'list') {
        $draw = (int)($_REQUEST['draw'] ?? 1);
        $start = max(0, (int)($_REQUEST['start'] ?? 0));
        $length = max(1, min(100, (int)($_REQUEST['length'] ?? 25)));
        $search = trim($_REQUEST['search']['value'] ?? '');
        
        $baseQuery = "FROM opd_medical_records mr 
                      LEFT JOIN opd_patients p ON mr.patient_id = p.id 
                      LEFT JOIN opd_doctors d ON mr.doctor_id = d.id 
                      LEFT JOIN opd_appointments a ON mr.appointment_id = a.id";
        $whereClause = " WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $whereClause .= " AND (p.name LIKE ? OR d.name LIKE ? OR mr.diagnosis LIKE ?)";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm];
        }

        $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_medical_records");
        $totalRecords = $totalStmt->fetchColumn();

        $filteredStmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery . $whereClause);
        $filteredStmt->execute($params);
        $filteredRecords = $filteredStmt->fetchColumn();

        $orderBy = " ORDER BY mr.record_date DESC, mr.id DESC";
        $limit = " LIMIT $start, $length";
        
        $dataQuery = "SELECT mr.*, p.name as patient_name, d.name as doctor_name, a.appointment_number " . $baseQuery . $whereClause . $orderBy . $limit;
        $dataStmt = $pdo->prepare($dataQuery);
        $dataStmt->execute($params);
        $data = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'success' => true,
            'data' => $data
        ]);
    }

    // Get single medical record
    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT mr.*, p.name as patient_name, d.name as doctor_name, a.appointment_number 
                               FROM opd_medical_records mr 
                               LEFT JOIN opd_patients p ON mr.patient_id = p.id 
                               LEFT JOIN opd_doctors d ON mr.doctor_id = d.id 
                               LEFT JOIN opd_appointments a ON mr.appointment_id = a.id
                               WHERE mr.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $row]);
    }

    // Save medical record
    if ($action === 'save') {
        $id = $_POST['id'] ?? '';
        $patient_id = (int)($_POST['patient_id'] ?? 0);
        $doctor_id = (int)($_POST['doctor_id'] ?? 0);
        $appointment_id = $_POST['appointment_id'] ?? null;
        $record_date = trim($_POST['record_date'] ?? '');
        $diagnosis = trim($_POST['diagnosis'] ?? '');
        $symptoms = trim($_POST['symptoms'] ?? '');
        $treatment = trim($_POST['treatment'] ?? '');
        $prescription = trim($_POST['prescription'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if (empty($patient_id) || empty($doctor_id) || empty($record_date)) {
            json_response(['success' => false, 'message' => 'Patient, doctor, and record date are required'], 400);
        }

        if ($id) {
            $stmt = $pdo->prepare('UPDATE opd_medical_records SET patient_id=?, doctor_id=?, appointment_id=?, record_date=?, diagnosis=?, symptoms=?, treatment=?, prescription=?, notes=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$patient_id, $doctor_id, $appointment_id ?: null, $record_date, $diagnosis, $symptoms, $treatment, $prescription, $notes, $id]);
            json_response(['success' => true, 'message' => 'Medical record updated successfully']);
        } else {
            $stmt = $pdo->prepare('INSERT INTO opd_medical_records (patient_id, doctor_id, appointment_id, record_date, diagnosis, symptoms, treatment, prescription, notes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
            $stmt->execute([$patient_id, $doctor_id, $appointment_id ?: null, $record_date, $diagnosis, $symptoms, $treatment, $prescription, $notes]);
            json_response(['success' => true, 'message' => 'Medical record added successfully']);
        }
    }

    // Delete medical record
    if ($action === 'delete' && isset($_POST['id'])) {
        $stmt = $pdo->prepare('DELETE FROM opd_medical_records WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success' => true, 'message' => 'Medical record deleted successfully']);
    }

    // Get patients list for dropdown
    if ($action === 'get_patients') {
        $stmt = $pdo->query("SELECT id, name, phone FROM opd_patients WHERE is_active = 1 ORDER BY name");
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $patients]);
    }

    // Get doctors list for dropdown
    if ($action === 'get_doctors') {
        $stmt = $pdo->query("SELECT id, name, specialization FROM opd_doctors WHERE is_active = 1 ORDER BY name");
        $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $doctors]);
    }

    // Get appointments list for dropdown
    if ($action === 'get_appointments') {
        $patient_id = $_GET['patient_id'] ?? null;
        if ($patient_id) {
            $stmt = $pdo->prepare("SELECT id, appointment_number, appointment_date FROM opd_appointments WHERE patient_id = ? ORDER BY appointment_date DESC LIMIT 20");
            $stmt->execute([$patient_id]);
        } else {
            $stmt = $pdo->query("SELECT id, appointment_number, appointment_date FROM opd_appointments ORDER BY appointment_date DESC LIMIT 50");
        }
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $appointments]);
    }

    // Stats
    if ($action === 'stats') {
        $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_medical_records");
        $total = $totalStmt->fetchColumn();
        
        $todayStmt = $pdo->query("SELECT COUNT(*) FROM opd_medical_records WHERE record_date = CURDATE()");
        $today = $todayStmt->fetchColumn();
        
        $weekStmt = $pdo->query("SELECT COUNT(*) FROM opd_medical_records WHERE record_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
        $week = $weekStmt->fetchColumn();
        
        $patientsStmt = $pdo->query("SELECT COUNT(DISTINCT patient_id) FROM opd_medical_records");
        $patients = $patientsStmt->fetchColumn();
        
        json_response([
            'success' => true,
            'data' => [
                'total' => $total,
                'today' => $today,
                'week' => $week,
                'patients' => $patients
            ]
        ]);
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $t) {
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
