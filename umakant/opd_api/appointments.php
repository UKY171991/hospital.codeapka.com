<?php
// opd_api/appointments.php - OPD Appointments API
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

try {
    $action = $_REQUEST['action'] ?? 'list';

    // List appointments
    if ($action === 'list') {
        $draw = (int)($_REQUEST['draw'] ?? 1);
        $start = max(0, (int)($_REQUEST['start'] ?? 0));
        $length = max(1, min(100, (int)($_REQUEST['length'] ?? 25)));
        $search = trim($_REQUEST['search']['value'] ?? '');
        
        $baseQuery = "FROM opd_appointments a 
                      LEFT JOIN opd_patients p ON a.patient_id = p.id 
                      LEFT JOIN opd_doctors d ON a.doctor_id = d.id 
                      LEFT JOIN opd_departments dept ON a.department_id = dept.id
                      LEFT JOIN opd_appointment_types t ON a.appointment_type_id = t.id";
        $whereClause = " WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $whereClause .= " AND (a.appointment_number LIKE ? OR p.name LIKE ? OR d.name LIKE ? OR a.status LIKE ?)";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
        }

        $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_appointments");
        $totalRecords = $totalStmt->fetchColumn();

        $filteredStmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery . $whereClause);
        $filteredStmt->execute($params);
        $filteredRecords = $filteredStmt->fetchColumn();

        $orderBy = " ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        $limit = " LIMIT $start, $length";
        
        $dataQuery = "SELECT a.*, p.name as patient_name, d.name as doctor_name, dept.name as department_name, t.name as type_name " . $baseQuery . $whereClause . $orderBy . $limit;
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

    // Get single appointment
    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT a.*, p.name as patient_name, d.name as doctor_name, dept.name as department_name, t.name as type_name 
                               FROM opd_appointments a 
                               LEFT JOIN opd_patients p ON a.patient_id = p.id 
                               LEFT JOIN opd_doctors d ON a.doctor_id = d.id 
                               LEFT JOIN opd_departments dept ON a.department_id = dept.id
                               LEFT JOIN opd_appointment_types t ON a.appointment_type_id = t.id
                               WHERE a.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $row]);
    }

    // Save appointment
    if ($action === 'save') {
        $id = $_POST['id'] ?? '';
        $patient_id = (int)($_POST['patient_id'] ?? 0);
        $doctor_id = (int)($_POST['doctor_id'] ?? 0);
        $department_id = $_POST['department_id'] ?? null;
        $appointment_date = trim($_POST['appointment_date'] ?? '');
        $appointment_time = trim($_POST['appointment_time'] ?? '');
        $appointment_type_id = $_POST['appointment_type_id'] ?? null;
        $status = trim($_POST['status'] ?? 'scheduled');
        $reason = trim($_POST['reason'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $fee = (float)($_POST['fee'] ?? 0);
        $payment_status = trim($_POST['payment_status'] ?? 'pending');

        if (empty($patient_id) || empty($doctor_id) || empty($appointment_date) || empty($appointment_time)) {
            json_response(['success' => false, 'message' => 'Patient, doctor, date, and time are required'], 400);
        }

        if ($id) {
            $stmt = $pdo->prepare('UPDATE opd_appointments SET patient_id=?, doctor_id=?, department_id=?, appointment_date=?, appointment_time=?, appointment_type_id=?, status=?, reason=?, notes=?, fee=?, payment_status=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$patient_id, $doctor_id, $department_id ?: null, $appointment_date, $appointment_time, $appointment_type_id ?: null, $status, $reason, $notes, $fee, $payment_status, $id]);
            json_response(['success' => true, 'message' => 'Appointment updated successfully']);
        } else {
            // Generate appointment number
            $appointment_number = 'APT' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            $stmt = $pdo->prepare('INSERT INTO opd_appointments (appointment_number, patient_id, doctor_id, department_id, appointment_date, appointment_time, appointment_type_id, status, reason, notes, fee, payment_status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
            $stmt->execute([$appointment_number, $patient_id, $doctor_id, $department_id ?: null, $appointment_date, $appointment_time, $appointment_type_id ?: null, $status, $reason, $notes, $fee, $payment_status]);
            json_response(['success' => true, 'message' => 'Appointment added successfully']);
        }
    }

    // Delete appointment
    if ($action === 'delete' && isset($_POST['id'])) {
        $stmt = $pdo->prepare('DELETE FROM opd_appointments WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success' => true, 'message' => 'Appointment deleted successfully']);
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

    // Get departments list for dropdown
    if ($action === 'get_departments') {
        $stmt = $pdo->query("SELECT id, name FROM opd_departments WHERE is_active = 1 ORDER BY name");
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $departments]);
    }

    // Get appointment types list for dropdown
    if ($action === 'get_appointment_types') {
        $stmt = $pdo->query("SELECT id, name, duration_minutes FROM opd_appointment_types WHERE is_active = 1 ORDER BY name");
        $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $types]);
    }

    // Stats
    if ($action === 'stats') {
        $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_appointments");
        $total = $totalStmt->fetchColumn();
        
        $scheduledStmt = $pdo->query("SELECT COUNT(*) FROM opd_appointments WHERE status = 'scheduled'");
        $scheduled = $scheduledStmt->fetchColumn();
        
        $confirmedStmt = $pdo->query("SELECT COUNT(*) FROM opd_appointments WHERE status = 'confirmed'");
        $confirmed = $confirmedStmt->fetchColumn();
        
        $completedStmt = $pdo->query("SELECT COUNT(*) FROM opd_appointments WHERE status = 'completed'");
        $completed = $completedStmt->fetchColumn();
        
        $cancelledStmt = $pdo->query("SELECT COUNT(*) FROM opd_appointments WHERE status = 'cancelled'");
        $cancelled = $cancelledStmt->fetchColumn();
        
        $todayStmt = $pdo->query("SELECT COUNT(*) FROM opd_appointments WHERE appointment_date = CURDATE()");
        $today = $todayStmt->fetchColumn();
        
        json_response([
            'success' => true,
            'data' => [
                'total' => $total,
                'scheduled' => $scheduled,
                'confirmed' => $confirmed,
                'completed' => $completed,
                'cancelled' => $cancelled,
                'today' => $today
            ]
        ]);
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $t) {
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
