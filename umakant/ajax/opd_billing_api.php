<?php
// ajax/opd_billing_api.php - CRUD for OPD Billing
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
    $action = $_REQUEST['action'] ?? ($_SERVER['REQUEST_METHOD'] === 'POST' ? 'save' : 'list');

    if ($action === 'update') {
        $action = 'save';
    }

    if ($action === 'list') {
        $draw = (int)($_REQUEST['draw'] ?? 1);
        $start = max(0, (int)($_REQUEST['start'] ?? 0));
        $length = max(1, min(100, (int)($_REQUEST['length'] ?? 25)));
        $search = trim($_REQUEST['search']['value'] ?? '');
        
        $baseQuery = "FROM opd_billing b LEFT JOIN users u ON b.added_by = u.id";
        $whereClause = "";
        $params = [];
        
        if (!empty($search)) {
            $whereClause = " WHERE (b.patient_name LIKE ? OR b.patient_phone LIKE ? OR b.doctor_name LIKE ? OR b.payment_status LIKE ?)";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
        }

        try {
            if (empty($whereClause)) {
                $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_billing");
            } else {
                $totalStmt = $pdo->query("SELECT COUNT(*) " . $baseQuery);
            }
            $totalRecords = $totalStmt->fetchColumn();
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error counting records: ' . $e->getMessage()], 500);
        }

        try {
            $filteredStmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery . $whereClause);
            $filteredStmt->execute($params);
            $filteredRecords = $filteredStmt->fetchColumn();
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error counting filtered records: ' . $e->getMessage()], 500);
        }

        $orderBy = " ORDER BY b.id DESC";
        if (isset($_REQUEST['order']) && is_array($_REQUEST['order']) && count($_REQUEST['order']) > 0) {
            $orderColumn = (int)$_REQUEST['order'][0]['column'];
            $orderDir = $_REQUEST['order'][0]['dir'] === 'asc' ? 'ASC' : 'DESC';
            
            $columns = ['b.id', 'b.patient_name', 'b.doctor_name', 'b.total_amount', 'b.payment_status', 'b.bill_date'];
            if (isset($columns[$orderColumn])) {
                $orderBy = " ORDER BY " . $columns[$orderColumn] . " " . $orderDir;
            }
        }
        
        $limit = " LIMIT $start, $length";
        
        try {
            $dataQuery = "SELECT b.*, u.username as added_by_username
                          " . $baseQuery . $whereClause . $orderBy . $limit;

            $dataStmt = $pdo->prepare($dataQuery);
            $dataStmt->execute($params);
            $data = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error fetching data: ' . $e->getMessage()], 500);
        }
        
        json_response([
            'draw' => intval($draw),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($filteredRecords),
            'success' => true,
            'data' => $data
        ]);
    }
    
    if ($action === 'stats') {
        try {
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
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error fetching stats: ' . $e->getMessage()], 500);
        }
    }

    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT b.*, u.username as added_by_username FROM opd_billing b LEFT JOIN users u ON b.added_by = u.id WHERE b.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $row]);
    }

    if ($action === 'save') {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) {
            json_response(['success' => false, 'message' => 'Unauthorized'], 401);
        }

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
        $added_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        // Calculate totals
        $total_amount = $consultation_fee + $medicine_charges + $lab_charges + $other_charges - $discount;
        $balance_amount = $total_amount - $paid_amount;
        
        // Determine payment status
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
            try {
                $stmt = $pdo->prepare('UPDATE opd_billing SET patient_name=?, patient_phone=?, patient_age=?, patient_gender=?, doctor_name=?, consultation_fee=?, medicine_charges=?, lab_charges=?, other_charges=?, discount=?, total_amount=?, paid_amount=?, balance_amount=?, payment_method=?, payment_status=?, bill_date=?, notes=?, updated_at=NOW() WHERE id=?');
                $stmt->execute([$patient_name, $patient_phone, $patient_age, $patient_gender, $doctor_name, $consultation_fee, $medicine_charges, $lab_charges, $other_charges, $discount, $total_amount, $paid_amount, $balance_amount, $payment_method, $payment_status, $bill_date, $notes, $id]);
                json_response(['success' => true, 'message' => 'Billing record updated successfully']);
            } catch (PDOException $e) {
                json_response(['success' => false, 'message' => 'Error updating: ' . $e->getMessage()], 500);
            }
        } else {
            try {
                $stmt = $pdo->prepare('INSERT INTO opd_billing (patient_name, patient_phone, patient_age, patient_gender, doctor_name, consultation_fee, medicine_charges, lab_charges, other_charges, discount, total_amount, paid_amount, balance_amount, payment_method, payment_status, bill_date, notes, added_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
                $stmt->execute([$patient_name, $patient_phone, $patient_age, $patient_gender, $doctor_name, $consultation_fee, $medicine_charges, $lab_charges, $other_charges, $discount, $total_amount, $paid_amount, $balance_amount, $payment_method, $payment_status, $bill_date, $notes, $added_by]);
                json_response(['success' => true, 'message' => 'Billing record added successfully']);
            } catch (PDOException $e) {
                json_response(['success' => false, 'message' => 'Error adding: ' . $e->getMessage()], 500);
            }
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) {
            json_response(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        try {
            $stmt = $pdo->prepare('DELETE FROM opd_billing WHERE id = ?');
            $stmt->execute([$_POST['id']]);
            json_response(['success' => true, 'message' => 'Billing record deleted successfully']);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error deleting: ' . $e->getMessage()], 500);
        }
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $t) {
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
