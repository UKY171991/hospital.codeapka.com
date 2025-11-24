<?php
// opd_api/billing.php - OPD Billing API
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

try {
    $action = $_REQUEST['action'] ?? 'list';

    // Check if opd_billing table exists
    $checkTable = $pdo->query("SHOW TABLES LIKE 'opd_billing'");
    $tableExists = $checkTable->rowCount() > 0;
    
    if (!$tableExists && in_array($action, ['list', 'get', 'save', 'delete', 'stats'])) {
        json_response([
            'success' => false,
            'message' => 'OPD Billing table not found. Please run the SQL schema to create the required tables.',
            'data' => []
        ]);
    }

    // List bills
    if ($action === 'list') {
        $draw = (int)($_REQUEST['draw'] ?? 1);
        $start = max(0, (int)($_REQUEST['start'] ?? 0));
        $length = max(1, min(100, (int)($_REQUEST['length'] ?? 25)));
        $search = trim($_REQUEST['search']['value'] ?? '');
        
        // Check if added_by column exists
        $checkAddedBy = $pdo->query("SHOW COLUMNS FROM opd_billing LIKE 'added_by'");
        $addedByExists = $checkAddedBy->rowCount() > 0;
        
        if ($addedByExists) {
            $baseQuery = "FROM opd_billing b LEFT JOIN users u ON b.added_by = u.id";
        } else {
            $baseQuery = "FROM opd_billing b";
        }
        
        $whereClause = "";
        $params = [];
        
        if (!empty($search)) {
            $whereClause = " WHERE (b.patient_name LIKE ? OR b.patient_phone LIKE ? OR b.doctor_name LIKE ? OR b.payment_status LIKE ?)";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
        }

        $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_billing");
        $totalRecords = $totalStmt->fetchColumn();

        $filteredStmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery . $whereClause);
        $filteredStmt->execute($params);
        $filteredRecords = $filteredStmt->fetchColumn();

        $orderBy = " ORDER BY b.id DESC";
        $limit = " LIMIT $start, $length";
        
        if ($addedByExists) {
            $dataQuery = "SELECT b.*, u.username as added_by_username " . $baseQuery . $whereClause . $orderBy . $limit;
        } else {
            $dataQuery = "SELECT b.*, NULL as added_by_username " . $baseQuery . $whereClause . $orderBy . $limit;
        }
        
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

    // Get single bill
    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT b.*, u.username as added_by_username FROM opd_billing b LEFT JOIN users u ON b.added_by = u.id WHERE b.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $row]);
    }

    // Save bill
    if ($action === 'save') {
        $id = $_POST['id'] ?? '';
        $patient_name = trim($_POST['patient_name'] ?? '');
        $patient_phone = trim($_POST['patient_phone'] ?? '');
        $patient_age = !empty($_POST['patient_age']) ? (int)$_POST['patient_age'] : null;
        $patient_gender = trim($_POST['patient_gender'] ?? '');
        $doctor_name = trim($_POST['doctor_name'] ?? '');
        $consultation_fee = !empty($_POST['consultation_fee']) ? (float)$_POST['consultation_fee'] : 0;
        $medicine_charges = !empty($_POST['medicine_charges']) ? (float)$_POST['medicine_charges'] : 0;
        $lab_charges = !empty($_POST['lab_charges']) ? (float)$_POST['lab_charges'] : 0;
        $other_charges = !empty($_POST['other_charges']) ? (float)$_POST['other_charges'] : 0;
        $discount = !empty($_POST['discount']) ? (float)$_POST['discount'] : 0;
        $paid_amount = !empty($_POST['paid_amount']) ? (float)$_POST['paid_amount'] : 0;
        $payment_method = trim($_POST['payment_method'] ?? 'Cash');
        $bill_date = trim($_POST['bill_date'] ?? date('Y-m-d'));
        $notes = trim($_POST['notes'] ?? '');
        $added_by = $_SESSION['user_id'] ?? null;

        $total_amount = $consultation_fee + $medicine_charges + $lab_charges + $other_charges - $discount;
        $balance_amount = $total_amount - $paid_amount;
        
        if ($paid_amount >= $total_amount) {
            $payment_status = 'Paid';
            $balance_amount = 0;
        } elseif ($paid_amount > 0) {
            $payment_status = 'Partial';
        } else {
            $payment_status = 'Unpaid';
        }

        if (empty($patient_name)) {
            json_response(['success' => false, 'message' => 'Patient name is required'], 400);
        }

        if ($id) {
            $stmt = $pdo->prepare('UPDATE opd_billing SET patient_name=?, patient_phone=?, patient_age=?, patient_gender=?, doctor_name=?, consultation_fee=?, medicine_charges=?, lab_charges=?, other_charges=?, discount=?, total_amount=?, paid_amount=?, balance_amount=?, payment_method=?, payment_status=?, bill_date=?, notes=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$patient_name, $patient_phone, $patient_age, $patient_gender, $doctor_name, $consultation_fee, $medicine_charges, $lab_charges, $other_charges, $discount, $total_amount, $paid_amount, $balance_amount, $payment_method, $payment_status, $bill_date, $notes, $id]);
            json_response(['success' => true, 'message' => 'Bill updated successfully']);
        } else {
            $stmt = $pdo->prepare('INSERT INTO opd_billing (patient_name, patient_phone, patient_age, patient_gender, doctor_name, consultation_fee, medicine_charges, lab_charges, other_charges, discount, total_amount, paid_amount, balance_amount, payment_method, payment_status, bill_date, notes, added_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
            $stmt->execute([$patient_name, $patient_phone, $patient_age, $patient_gender, $doctor_name, $consultation_fee, $medicine_charges, $lab_charges, $other_charges, $discount, $total_amount, $paid_amount, $balance_amount, $payment_method, $payment_status, $bill_date, $notes, $added_by]);
            json_response(['success' => true, 'message' => 'Bill added successfully']);
        }
    }

    // Delete bill
    if ($action === 'delete' && isset($_POST['id'])) {
        $stmt = $pdo->prepare('DELETE FROM opd_billing WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success' => true, 'message' => 'Bill deleted successfully']);
    }

    // Stats
    if ($action === 'stats') {
        $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_billing");
        $total = $totalStmt->fetchColumn();
        
        $paidStmt = $pdo->query("SELECT COUNT(*) FROM opd_billing WHERE payment_status = 'Paid'");
        $paid = $paidStmt->fetchColumn();
        
        $unpaidStmt = $pdo->query("SELECT COUNT(*) FROM opd_billing WHERE payment_status = 'Unpaid'");
        $unpaid = $unpaidStmt->fetchColumn();
        
        $partialStmt = $pdo->query("SELECT COUNT(*) FROM opd_billing WHERE payment_status = 'Partial'");
        $partial = $partialStmt->fetchColumn();
        
        $totalRevenueStmt = $pdo->query("SELECT COALESCE(SUM(paid_amount), 0) FROM opd_billing");
        $totalRevenue = $totalRevenueStmt->fetchColumn();
        
        $pendingStmt = $pdo->query("SELECT COALESCE(SUM(balance_amount), 0) FROM opd_billing WHERE payment_status != 'Paid'");
        $pending = $pendingStmt->fetchColumn();
        
        json_response([
            'success' => true,
            'data' => [
                'total' => $total,
                'paid' => $paid,
                'unpaid' => $unpaid,
                'partial' => $partial,
                'totalRevenue' => number_format($totalRevenue, 2),
                'pending' => number_format($pending, 2)
            ]
        ]);
    }

    // Get doctors list
    if ($action === 'get_doctors') {
        // Check if opd_doctors table exists
        $checkDoctorsTable = $pdo->query("SHOW TABLES LIKE 'opd_doctors'");
        if ($checkDoctorsTable->rowCount() === 0) {
            json_response(['success' => true, 'data' => []]);
        }
        
        $checkColumn = $pdo->query("SHOW COLUMNS FROM opd_doctors LIKE 'status'");
        $statusExists = $checkColumn->rowCount() > 0;
        
        if ($statusExists) {
            $stmt = $pdo->query("SELECT id, name, specialization, hospital FROM opd_doctors WHERE status = 'Active' OR status IS NULL ORDER BY name ASC");
        } else {
            $stmt = $pdo->query("SELECT id, name, specialization, hospital FROM opd_doctors ORDER BY name ASC");
        }
        
        $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $doctors]);
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $t) {
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
