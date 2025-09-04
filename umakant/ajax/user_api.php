<?php
// ajax/user_api.php - CRUD for users via AJAX
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

$action = $_REQUEST['action'] ?? 'list';

if ($action === 'list') {
    // Support DataTables server-side processing
    $draw = $_POST['draw'] ?? 1;
    $start = $_POST['start'] ?? 0;
    $length = $_POST['length'] ?? 25;
    $search = $_POST['search']['value'] ?? '';
    
    // Role-based visibility
    $viewerRole = $_SESSION['role'] ?? 'user';
    $viewerId = $_SESSION['user_id'] ?? null;

    // Base query
    $baseQuery = "FROM users";
    $whereClause = "";
    $params = [];
    
    // Role-based filtering
    if ($viewerRole === 'master') {
        // master sees everything
        $whereClause = "";
    } elseif ($viewerRole === 'admin') {
        // admin sees users they added and themselves
        $whereClause = " WHERE added_by = ? OR id = ?";
        $params = [$viewerId, $viewerId];
    } else {
        // regular user sees users they added and themselves
        $whereClause = " WHERE added_by = ? OR id = ?";
        $params = [$viewerId, $viewerId];
    }
    
    // Add search conditions
    if (!empty($search)) {
        $searchClause = " (username LIKE ? OR email LIKE ? OR full_name LIKE ?)";
        $searchTerm = "%$search%";
        $searchParams = [$searchTerm, $searchTerm, $searchTerm];
        
        if (empty($whereClause)) {
            $whereClause = " WHERE " . $searchClause;
            $params = $searchParams;
        } else {
            $whereClause .= " AND " . $searchClause;
            $params = array_merge($params, $searchParams);
        }
    }
    
    // Get total records
    $totalStmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery . $whereClause);
    $totalStmt->execute($params);
    $totalRecords = $totalStmt->fetchColumn();
    
    // Get filtered records
    $orderBy = " ORDER BY id DESC";
    $limit = " LIMIT $start, $length";
    
    $dataQuery = "SELECT id, username, email, full_name, role, added_by, is_active as status, last_login, expire_date " . 
                  $baseQuery . $whereClause . $orderBy . $limit;
    
    $dataStmt = $pdo->prepare($dataQuery);
    $dataStmt->execute($params);
    $data = $dataStmt->fetchAll();
    
    // Format data for DataTables
    foreach ($data as &$row) {
        $row['status'] = $row['status'] ? 'Active' : 'Inactive';
        $row['expire_date'] = $row['expire_date'] ? date('Y-m-d', strtotime($row['expire_date'])) : '-';
    }
    
    // Return DataTables format
    json_response([
        'draw' => intval($draw),
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $totalRecords,
        'success' => true,
        'data' => $data
    ]);
}

if ($action === 'get' && isset($_GET['id'])) {
    $viewerRole = $_SESSION['role'] ?? 'user';
    $viewerId = $_SESSION['user_id'] ?? null;
    $stmt = $pdo->prepare('SELECT id, username, email, full_name, role, added_by, is_active, last_login, expire_date FROM users WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $row = $stmt->fetch();
    if (!$row) json_response(['success' => false, 'message' => 'User not found'],404);
    // enforce visibility: master sees all, admin/user only see their added users or themselves
    if ($viewerRole === 'master' || $row['added_by'] == $viewerId || $row['id'] == $viewerId) {
        json_response(['success' => true, 'data' => $row]);
    } else {
        json_response(['success' => false, 'message' => 'Unauthorized'],403);
    }
}

if ($action === 'save') {
    // allow any authenticated user to create/update users, but enforce visibility elsewhere
    if (!isset($_SESSION['user_id'])) json_response(['success'=>false,'message'=>'Unauthorized'],401);

    $id = $_POST['id'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'user';
    $is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 0;
    // allow optional added_by from form (but fallback to session user)
    $creatorId = isset($_POST['added_by']) && is_numeric($_POST['added_by']) ? (int)$_POST['added_by'] : ($_SESSION['user_id']);
    $last_login = trim($_POST['last_login'] ?? '');
    $expire_date = trim($_POST['expire_date'] ?? '');

    if ($id) {
        // update (only change password if provided)
        // update (only change password if provided)
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET username=?, password=?, full_name=?, email=?, role=?, is_active=?, added_by=?, last_login=?, expire_date=? WHERE id=?');
            $stmt->execute([$username, $hash, $full_name, $email, $role, $is_active, $creatorId, $last_login ?: null, $expire_date ?: null, $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET username=?, full_name=?, email=?, role=?, is_active=?, added_by=?, last_login=?, expire_date=? WHERE id=?');
            $stmt->execute([$username, $full_name, $email, $role, $is_active, $creatorId, $last_login ?: null, $expire_date ?: null, $id]);
        }
        json_response(['success' => true, 'message' => 'User updated']);
    } else {
        // create
        $hash = password_hash($password ?: 'password', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, password, full_name, email, role, added_by, is_active, last_login, expire_date, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$username, $hash, $full_name, $email, $role, $creatorId, $is_active, $last_login ?: null, $expire_date ?: null]);
        
        // Get the newly inserted record
        $newId = $pdo->lastInsertId();
        $stmt = $pdo->prepare('SELECT id, username, full_name, email, role, is_active FROM users WHERE id = ?');
        $stmt->execute([$newId]);
        $newRecord = $stmt->fetch();
        
        json_response(['success' => true, 'message' => 'User created', 'data' => $newRecord]);
    }
}

if ($action === 'delete' && isset($_POST['id'])) {
    // only master may delete users
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'master') json_response(['success'=>false,'message'=>'Unauthorized'],401);
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$_POST['id']]);
    json_response(['success' => true, 'message' => 'User deleted']);
}

json_response(['success' => false, 'message' => 'Invalid action'], 400);
