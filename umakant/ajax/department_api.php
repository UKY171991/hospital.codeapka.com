<?php
// ajax/department_api.php - CRUD for OPD departments
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
        
        $baseQuery = "FROM opd_departments d LEFT JOIN users u ON d.added_by = u.id";
        $whereClause = "";
        $params = [];
        
        if (!empty($search)) {
            $whereClause = " WHERE (d.name LIKE ? OR d.description LIKE ? OR d.head_of_department LIKE ? OR d.contact_number LIKE ?)";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
        }

        try {
            if (empty($whereClause)) {
                $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_departments");
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

        $orderBy = " ORDER BY d.id DESC";
        if (isset($_REQUEST['order']) && is_array($_REQUEST['order']) && count($_REQUEST['order']) > 0) {
            $orderColumn = (int)$_REQUEST['order'][0]['column'];
            $orderDir = $_REQUEST['order'][0]['dir'] === 'asc' ? 'ASC' : 'DESC';
            
            $columns = ['d.id', 'd.name', 'd.head_of_department', 'd.contact_number', 'd.created_at'];
            if (isset($columns[$orderColumn])) {
                $orderBy = " ORDER BY " . $columns[$orderColumn] . " " . $orderDir;
            }
        }
        
        $limit = " LIMIT $start, $length";
        
        try {
            // Check if status column exists
            $checkColumn = $pdo->query("SHOW COLUMNS FROM opd_departments LIKE 'status'");
            $statusExists = $checkColumn->rowCount() > 0;
            
            if ($statusExists) {
                $dataQuery = "SELECT d.id, d.name, d.description, d.head_of_department, 
                             d.contact_number, d.email, d.location,
                             COALESCE(d.status, 'Active') as status,
                             d.added_by, u.username as added_by_username, d.created_at, d.updated_at
                          " . $baseQuery . $whereClause . $orderBy . $limit;
            } else {
                $dataQuery = "SELECT d.id, d.name, d.description, d.head_of_department, 
                             d.contact_number, d.email, d.location,
                             'Active' as status,
                             d.added_by, u.username as added_by_username, d.created_at, d.updated_at
                          " . $baseQuery . $whereClause . $orderBy . $limit;
            }

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
            $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_departments");
            $total = $totalStmt->fetchColumn();
            
            // Check if status column exists
            $checkColumn = $pdo->query("SHOW COLUMNS FROM opd_departments LIKE 'status'");
            $statusExists = $checkColumn->rowCount() > 0;
            
            if ($statusExists) {
                $activeStmt = $pdo->query("SELECT COUNT(*) FROM opd_departments WHERE status = 'Active' OR status IS NULL");
                $active = $activeStmt->fetchColumn();
                
                $inactiveStmt = $pdo->query("SELECT COUNT(*) FROM opd_departments WHERE status = 'Inactive'");
                $inactive = $inactiveStmt->fetchColumn();
            } else {
                $active = $total;
                $inactive = 0;
            }
            
            // Get total doctors count
            $doctorsStmt = $pdo->query("SELECT COUNT(*) FROM opd_doctors");
            $totalDoctors = $doctorsStmt->fetchColumn();
            
            json_response([
                'success' => true,
                'data' => [
                    'total' => $total,
                    'active' => $active,
                    'inactive' => $inactive,
                    'total_doctors' => $totalDoctors
                ]
            ]);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error fetching stats: ' . $e->getMessage()], 500);
        }
    }

    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT d.*, u.username as added_by_username FROM opd_departments d LEFT JOIN users u ON d.added_by = u.id WHERE d.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $row]);
    }

    if ($action === 'save') {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) {
            json_response(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $id = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $head_of_department = trim($_POST['head_of_department'] ?? '');
        $contact_number = trim($_POST['contact_number'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $status = trim($_POST['status'] ?? 'Active');
        $added_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        if (empty($name)) {
            json_response(['success' => false, 'message' => 'Department name is required'], 400);
        }

        if ($id) {
            try {
                $stmt = $pdo->prepare('UPDATE opd_departments SET name=?, description=?, head_of_department=?, contact_number=?, email=?, location=?, status=?, updated_at=NOW() WHERE id=?');
                $stmt->execute([$name, $description, $head_of_department, $contact_number, $email, $location, $status, $id]);
                json_response(['success' => true, 'message' => 'Department updated successfully']);
            } catch (PDOException $e) {
                json_response(['success' => false, 'message' => 'Error updating: ' . $e->getMessage()], 500);
            }
        } else {
            try {
                $stmt = $pdo->prepare('INSERT INTO opd_departments (name, description, head_of_department, contact_number, email, location, status, added_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
                $stmt->execute([$name, $description, $head_of_department, $contact_number, $email, $location, $status, $added_by]);
                json_response(['success' => true, 'message' => 'Department added successfully']);
            } catch (PDOException $e) {
                json_response(['success' => false, 'message' => 'Error adding: ' . $e->getMessage()], 500);
            }
        }
    }

    if ($action === 'toggle_status' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) {
            json_response(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        try {
            $stmt = $pdo->prepare('UPDATE opd_departments SET status = IF(status = "Active", "Inactive", "Active"), updated_at = NOW() WHERE id = ?');
            $stmt->execute([$_POST['id']]);
            
            $getStmt = $pdo->prepare('SELECT status FROM opd_departments WHERE id = ?');
            $getStmt->execute([$_POST['id']]);
            $newStatus = $getStmt->fetchColumn();
            
            json_response(['success' => true, 'message' => 'Status updated successfully', 'status' => $newStatus]);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error updating status: ' . $e->getMessage()], 500);
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) {
            json_response(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        try {
            $stmt = $pdo->prepare('DELETE FROM opd_departments WHERE id = ?');
            $stmt->execute([$_POST['id']]);
            json_response(['success' => true, 'message' => 'Department deleted successfully']);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error deleting: ' . $e->getMessage()], 500);
        }
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $t) {
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
