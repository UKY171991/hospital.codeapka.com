<?php
// opd_api/users.php - OPD Users API
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

try {
    $action = $_REQUEST['action'] ?? 'list';

    // List users
    if ($action === 'list') {
        $draw = (int)($_REQUEST['draw'] ?? 1);
        $start = max(0, (int)($_REQUEST['start'] ?? 0));
        $length = max(1, min(100, (int)($_REQUEST['length'] ?? 25)));
        $search = trim($_REQUEST['search']['value'] ?? '');
        
        $baseQuery = "FROM opd_users u LEFT JOIN opd_users cu ON u.created_by = cu.id";
        $whereClause = " WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $whereClause .= " AND (u.username LIKE ? OR u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ? OR u.role LIKE ?)";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm];
        }

        $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_users");
        $totalRecords = $totalStmt->fetchColumn();

        $filteredStmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery . $whereClause);
        $filteredStmt->execute($params);
        $filteredRecords = $filteredStmt->fetchColumn();

        $orderBy = " ORDER BY id DESC";
        $limit = " LIMIT $start, $length";
        
        $dataQuery = "SELECT u.*, cu.name AS created_by_name " . $baseQuery . $whereClause . $orderBy . $limit;
        $dataStmt = $pdo->prepare($dataQuery);
        $dataStmt->execute($params);
        $data = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Remove password from response
        foreach ($data as &$row) {
            unset($row['password']);
        }
        
        json_response([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'success' => true,
            'data' => $data
        ]);
    }

    // Get single user
    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT u.*, cu.name AS created_by_name FROM opd_users u LEFT JOIN opd_users cu ON u.created_by = cu.id WHERE u.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            unset($row['password']);
        }
        json_response(['success' => true, 'data' => $row]);
    }

    // Save user
    if ($action === 'save') {
        $id = $_POST['id'] ?? '';
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $role = trim($_POST['role'] ?? 'patient');
        $specialization = trim($_POST['specialization'] ?? '');
        $license_number = trim($_POST['license_number'] ?? '');
        $is_active = (int)($_POST['is_active'] ?? 1);
        $created_by = (int)($_POST['created_by'] ?? $_SESSION['user_id'] ?? 0);

        $password = trim($_POST['password'] ?? '');
        
        if (empty($username) || empty($email) || empty($name)) {
            json_response(['success' => false, 'message' => 'Username, email, and name are required'], 400);
        }

        if ($id) {
            // Update
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE opd_users SET username=?, email=?, password=?, name=?, phone=?, role=?, specialization=?, license_number=?, is_active=?, updated_at=NOW() WHERE id=?');
                $stmt->execute([$username, $email, $hashedPassword, $name, $phone, $role, $specialization, $license_number, $is_active, $id]);
            } else {
                $stmt = $pdo->prepare('UPDATE opd_users SET username=?, email=?, name=?, phone=?, role=?, specialization=?, license_number=?, is_active=?, updated_at=NOW() WHERE id=?');
                $stmt->execute([$username, $email, $name, $phone, $role, $specialization, $license_number, $is_active, $id]);
            }
            json_response(['success' => true, 'message' => 'User updated successfully']);
        } else {
            // Insert
            if (empty($password)) {
                json_response(['success' => false, 'message' => 'Password is required for new users'], 400);
            }
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO opd_users (username, email, password, name, phone, role, specialization, license_number, is_active, created_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
            $stmt->execute([$username, $email, $hashedPassword, $name, $phone, $role, $specialization, $license_number, $is_active, $created_by]);
            json_response(['success' => true, 'message' => 'User added successfully']);
        }
    }

    // Delete user
    if ($action === 'delete' && isset($_POST['id'])) {
        $stmt = $pdo->prepare('DELETE FROM opd_users WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success' => true, 'message' => 'User deleted successfully']);
    }

    // Stats
    if ($action === 'stats') {
        $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_users");
        $total = $totalStmt->fetchColumn();
        
        $activeStmt = $pdo->query("SELECT COUNT(*) FROM opd_users WHERE is_active = 1");
        $active = $activeStmt->fetchColumn();
        
        $doctorStmt = $pdo->query("SELECT COUNT(*) FROM opd_users WHERE role = 'doctor'");
        $doctor = $doctorStmt->fetchColumn();
        
        $nurseStmt = $pdo->query("SELECT COUNT(*) FROM opd_users WHERE role = 'nurse'");
        $nurse = $nurseStmt->fetchColumn();
        
        json_response([
            'success' => true,
            'data' => [
                'total' => $total,
                'active' => $active,
                'doctor' => $doctor,
                'nurse' => $nurse
            ]
        ]);
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $t) {
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
