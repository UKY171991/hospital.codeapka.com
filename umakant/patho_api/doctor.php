<?php
// patho_api/doctor.php - API to create and list doctors per user
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';

$action = $_REQUEST['action'] ?? 'list';
try {
    if ($action === 'list') {
        // If user_id is provided, return doctors added by that user
        $userId = $_GET['user_id'] ?? null;
        if (!$userId && isset($_SESSION['user_id'])) $userId = $_SESSION['user_id'];
        // If master requests all, return everything
        $viewerRole = $_SESSION['role'] ?? 'user';
        if (isset($_GET['all']) && $_GET['all'] == 1 && $viewerRole === 'master') {
            $stmt = $pdo->query('SELECT d.*, u.username AS added_by_username FROM doctors d LEFT JOIN users u ON d.added_by = u.id ORDER BY d.id DESC');
            $rows = $stmt->fetchAll();
            json_response(['success'=>true,'data'=>$rows]);
        }

        if ($userId) {
            $stmt = $pdo->prepare('SELECT d.*, u.username AS added_by_username FROM doctors d LEFT JOIN users u ON d.added_by = u.id WHERE d.added_by = ? ORDER BY d.id DESC');
            $stmt->execute([$userId]);
            $rows = $stmt->fetchAll();
            json_response(['success'=>true,'data'=>$rows]);
        } else {
            json_response(['success'=>false,'message'=>'user_id required or authenticated'],400);
        }
    }

    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT d.*, u.username AS added_by_username FROM doctors d LEFT JOIN users u ON d.added_by = u.id WHERE d.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        if (!$row) json_response(['success'=>false,'message'=>'Doctor not found'],404);
        json_response(['success'=>true,'data'=>$row]);
    }

    if ($action === 'save') {
        // Authenticate: accept session OR Bearer token in Authorization header OR api_key param
        $authenticatedUserId = null;

        // 1) Session-based
        if (isset($_SESSION['user_id'])) {
            $authenticatedUserId = $_SESSION['user_id'];
        }

        // 2) Token-based: Authorization: Bearer <token> or api_key param
        if (!$authenticatedUserId) {
            $token = null;
            // Check Authorization header
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
            if ($authHeader && preg_match('/Bearer\s+(\S+)/', $authHeader, $m)) {
                $token = $m[1];
            }
            if (!$token && isset($_REQUEST['api_key'])) $token = $_REQUEST['api_key'];

            if ($token) {
                $tstmt = $pdo->prepare('SELECT id FROM users WHERE api_token = ? AND is_active = 1 LIMIT 1');
                $tstmt->execute([$token]);
                $u = $tstmt->fetch();
                if ($u) $authenticatedUserId = $u['id'];
            }
        }

        if (!$authenticatedUserId) json_response(['success'=>false,'message'=>'Unauthorized'],401);
    // Accept JSON body as well as form-encoded
    $input = $_POST;
        if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
            $raw = file_get_contents('php://input');
            $json = json_decode($raw, true);
            if (is_array($json)) $input = array_merge($input, $json);
        }

    // Prevent importing client-supplied id or added_by values — server controls these
    if (isset($input['id'])) unset($input['id']);
    if (isset($input['added_by'])) unset($input['added_by']);

    $name = trim($input['name'] ?? '');
        $qualification = trim($input['qualification'] ?? '');
        $specialization = trim($input['specialization'] ?? '');
        $hospital = trim($input['hospital'] ?? '');
        $contact_no = trim($input['contact_no'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $email = trim($input['email'] ?? '');
        $address = trim($input['address'] ?? '');
        $registration_no = trim($input['registration_no'] ?? '');
    $percent = isset($input['percent']) ? $input['percent'] : null;
    if ($percent === '') $percent = null;
    if ($percent !== null) $percent = (float)$percent;
        $added_by = $authenticatedUserId;

        if ($name === '') {
            json_response(['success'=>false,'message'=>'Name is required'],400);
        }

        // If id provided in input treat as update (id must be integer) — client cannot change added_by
        $updateId = isset($input['id']) && is_numeric($input['id']) ? (int)$input['id'] : null;
        if ($updateId) {
            // update existing
            $stmt = $pdo->prepare('UPDATE doctors SET name=?, qualification=?, specialization=?, hospital=?, contact_no=?, phone=?, email=?, address=?, registration_no=?, percent=?, updated_at = NOW() WHERE id = ?');
            $stmt->execute([$name, $qualification, $specialization, $hospital, $contact_no, $phone, $email, $address, $registration_no, $percent, $updateId]);
            json_response(['success'=>true,'message'=>'Doctor updated','id'=>$updateId]);
        }

        // Use server-side added_by only — ignore any client-provided added_by
        $stmt = $pdo->prepare('INSERT INTO doctors (name, qualification, specialization, hospital, contact_no, phone, email, address, registration_no, percent, added_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$name, $qualification, $specialization, $hospital, $contact_no, $phone, $email, $address, $registration_no, $percent, $added_by]);
        $newId = $pdo->lastInsertId();
        json_response(['success'=>true,'message'=>'Doctor added','id'=>$newId]);
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['user_id'])) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $toDelete = $_POST['id'];
        // fetch row to check ownership
        $stmt = $pdo->prepare('SELECT added_by FROM doctors WHERE id = ?');
        $stmt->execute([$toDelete]);
        $row = $stmt->fetch();
        if (!$row) json_response(['success'=>false,'message'=>'Not found'],404);
        $owner = $row['added_by'];
        $viewerRole = $_SESSION['role'] ?? 'user';
        $viewerId = $_SESSION['user_id'];
        // allow delete if master/admin or owner
        if ($viewerRole !== 'master' && $viewerRole !== 'admin' && $owner != $viewerId) {
            json_response(['success'=>false,'message'=>'Unauthorized'],403);
        }
        $del = $pdo->prepare('DELETE FROM doctors WHERE id = ?');
        $del->execute([$toDelete]);
        json_response(['success'=>true,'message'=>'Doctor deleted']);
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);
} catch (Exception $e) {
    json_response(['success'=>false,'message'=>'Server error: '.$e->getMessage()],500);
}
