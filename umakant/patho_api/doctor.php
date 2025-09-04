<?php
// patho_api/doctor.php - API to create and list doctors per user
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
// Optional API config for secret-based direct insert
require_once __DIR__ . '/../inc/api_config.php';

$action = $_REQUEST['action'] ?? 'list';
try {
    if ($action === 'list') {
        // If user_id is provided, return doctors added by that user
        $userId = $_GET['user_id'] ?? null;
        if (!$userId && isset($_SESSION['user_id'])) $userId = $_SESSION['user_id'];
        // If master requests all, return everything
        $viewerRole = $_SESSION['role'] ?? 'user';
        if (isset($_GET['all']) && $_GET['all'] == 1 && $viewerRole === 'master') {
            $stmt = $pdo->query('SELECT d.id, d.server_id, d.name, d.qualification, d.specialization, d.hospital, d.contact_no, d.phone, d.email, d.address, d.registration_no, d.percent, d.added_by, d.created_at, d.updated_at, u.username AS added_by_username FROM doctors d LEFT JOIN users u ON d.added_by = u.id ORDER BY d.id DESC');
            $rows = $stmt->fetchAll();
            json_response(['success'=>true,'data'=>$rows]);
        }

        if ($userId) {
            $stmt = $pdo->prepare('SELECT d.id, d.server_id, d.name, d.qualification, d.specialization, d.hospital, d.contact_no, d.phone, d.email, d.address, d.registration_no, d.percent, d.added_by, d.created_at, d.updated_at, u.username AS added_by_username FROM doctors d LEFT JOIN users u ON d.added_by = u.id WHERE d.added_by = ? ORDER BY d.id DESC');
            $stmt->execute([$userId]);
            $rows = $stmt->fetchAll();
            json_response(['success'=>true,'data'=>$rows]);
        } else {
            json_response(['success'=>false,'message'=>'user_id required or authenticated'],400);
        }
    }

    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT d.id, d.server_id, d.name, d.qualification, d.specialization, d.hospital, d.contact_no, d.phone, d.email, d.address, d.registration_no, d.percent, d.added_by, d.created_at, d.updated_at, u.username AS added_by_username FROM doctors d LEFT JOIN users u ON d.added_by = u.id WHERE d.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        if (!$row) json_response(['success'=>false,'message'=>'Doctor not found'],404);
        json_response(['success'=>true,'data'=>$row]);
    }

    if ($action === 'save') {
        // Authenticate: accept session OR Bearer token in Authorization header OR api_key param
        $authenticatedUserId = null;

        // Decode JSON body early so credentials can be supplied as JSON as well as form-data
        $rawInput = file_get_contents('php://input');
        $bodyJson = null;
        if ($rawInput) {
            $tmp = json_decode($rawInput, true);
            if (is_array($tmp)) $bodyJson = $tmp;
        }

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

        // 3) Credentials in request: accept username & password in the POST (form-data/json)
        if (!$authenticatedUserId) {
            // Look for username/password in request (POST/form-data or query)
            $reqUsername = $_REQUEST['username'] ?? ($bodyJson['username'] ?? null);
            $reqPassword = $_REQUEST['password'] ?? ($bodyJson['password'] ?? null);
            if ($reqUsername && $reqPassword) {
                // Fetch user row and verify password using same rules as login.php
                $ustmt = $pdo->prepare('SELECT id, password, is_active FROM users WHERE username = ? LIMIT 1');
                $ustmt->execute([$reqUsername]);
                $urow = $ustmt->fetch();
                if ($urow && $urow['is_active']) {
                    $stored = $urow['password'] ?? '';
                    $passOk = false;
                    if (is_string($stored) && (strpos($stored, '$2y$') === 0 || strpos($stored, '$2a$') === 0 || strpos($stored, '$argon') === 0 || password_needs_rehash($stored, PASSWORD_DEFAULT) || password_verify($reqPassword, $stored))) {
                        if (password_verify($reqPassword, $stored)) $passOk = true;
                    }
                    if (!$passOk && is_string($stored)) {
                        $len = strlen($stored);
                        if ($len === 32) { // MD5
                            if (hash_equals($stored, md5($reqPassword))) $passOk = true;
                        } elseif ($len === 40) { // SHA1
                            if (hash_equals($stored, sha1($reqPassword))) $passOk = true;
                        }
                    }
                    if (!$passOk) {
                        if (hash_equals((string)$stored, (string)$reqPassword)) $passOk = true;
                    }
                    if ($passOk) $authenticatedUserId = $urow['id'];
                }
            }
        }

                // 4) Secret-based direct insert (server-to-server): X-Api-Key header or secret_key param
                if (!$authenticatedUserId && !empty($PATHO_API_SECRET)) {
                    $reqSecret = $_SERVER['HTTP_X_API_KEY'] ?? $_REQUEST['secret_key'] ?? ($bodyJson['secret_key'] ?? null);
                    if ($reqSecret && hash_equals($PATHO_API_SECRET, $reqSecret)) {
                        // Use configured default user id for added_by
                        $authenticatedUserId = $PATHO_API_DEFAULT_USER_ID ?: null;
                    }
                }

        // Allow unauthenticated inserts: if there's no authenticated user, we will insert with added_by = NULL
    // (Keep other auth flows like delete unchanged.)

    // If we have an authenticated user id, fetch their role so we can allow privileged fields like added_by
    $authenticatedUserRole = null;
    if ($authenticatedUserId) {
        $rstmt = $pdo->prepare('SELECT role FROM users WHERE id = ? LIMIT 1');
        $rstmt->execute([$authenticatedUserId]);
        $rrow = $rstmt->fetch();
        if ($rrow && isset($rrow['role'])) $authenticatedUserRole = $rrow['role'];
    }
    // Accept JSON body as well as form-encoded
    $input = $_POST;
        // If we already decoded JSON earlier into $bodyJson, reuse it; avoids reading php://input twice
        if (is_array($bodyJson)) {
            $input = array_merge($input, $bodyJson);
        } elseif (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
            $raw = file_get_contents('php://input');
            $json = json_decode($raw, true);
            if (is_array($json)) $input = array_merge($input, $json);
        }

    // Prevent importing client-supplied id values — server controls id. added_by is handled below based on role.
    if (isset($input['id'])) unset($input['id']);

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
            // If an admin/master explicitly provided added_by in the input, allow it. Otherwise use authenticated user id (may be null).
            $added_by = $authenticatedUserId;
            if (isset($input['added_by']) && is_numeric($input['added_by']) && in_array($authenticatedUserRole, ['master','admin'])) {
                $added_by = (int)$input['added_by'];
            }
            // If no authenticated user and a default API user id is configured, use it so records created via secret/anonymous flows
            // still have a sensible added_by value instead of NULL. This helps when clients insert without session cookies.
            if (empty($added_by) && !empty($PATHO_API_DEFAULT_USER_ID)) {
                $added_by = (int)$PATHO_API_DEFAULT_USER_ID;
            }

        if ($name === '') {
            json_response(['success'=>false,'message'=>'Name is required'],400);
        }

        // If id provided in input treat as update (id must be integer) — client cannot change added_by
        $updateId = isset($input['id']) && is_numeric($input['id']) ? (int)$input['id'] : null;
        if ($updateId) {
            // update existing (preserve added_by)
            $stmt = $pdo->prepare('UPDATE doctors SET server_id = ?, name=?, qualification=?, specialization=?, hospital=?, contact_no=?, phone=?, email=?, address=?, registration_no=?, percent=?, updated_at = NOW() WHERE id = ?');
            $stmt->execute([$input['server_id'] ?? null, $name, $qualification, $specialization, $hospital, $contact_no, $phone, $email, $address, $registration_no, $percent, $updateId]);
            json_response(['success'=>true,'message'=>'Doctor updated','id'=>$updateId]);
        }

        // Prepare data for insert/update
        // Include server_id if client provides one (useful for sync)
        $server_id = isset($input['server_id']) ? (is_numeric($input['server_id']) ? (int)$input['server_id'] : null) : null;
        $data = [
            'server_id' => $server_id,
            'name'=>$name,
            'qualification'=>$qualification,
            'specialization'=>$specialization,
            'hospital'=>$hospital,
            'contact_no'=>$contact_no,
            'phone'=>$phone,
            'email'=>$email,
            'address'=>$address,
            'registration_no'=>$registration_no,
            'percent'=>$percent,
            'added_by'=>$added_by
        ];

        // Determine unique criteria: prefer registration_no if provided, else name+phone+hospital
        if ($registration_no !== '') {
            $unique = ['registration_no'=>$registration_no];
        } else {
            // prefer contact_no, fallback to phone
            $contactKey = $contact_no !== '' ? $contact_no : $phone;
            $unique = ['name'=>$name, 'contact_no'=>$contactKey, 'hospital'=>$hospital];
        }

        $res = upsert_or_skip($pdo, 'doctors', $unique, $data);
        json_response(['success'=>true,'message'=>'Doctor '.$res['action'],'id'=>$res['id']]);
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
