<?php
// opd_api/doctors.php - OPD Doctors API
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

try {
    $action = $_REQUEST['action'] ?? 'list';

    // List doctors
    if ($action === 'list') {
        $draw = (int)($_REQUEST['draw'] ?? 1);
        $start = max(0, (int)($_REQUEST['start'] ?? 0));
        $length = max(1, min(100, (int)($_REQUEST['length'] ?? 25)));
        $search = trim($_REQUEST['search']['value'] ?? '');
        
        // Ensure user_id column exists
        $checkUserId = $pdo->query("SHOW COLUMNS FROM opd_doctors LIKE 'user_id'");
        if ($checkUserId->rowCount() == 0) {
            $pdo->exec("ALTER TABLE opd_doctors ADD COLUMN user_id INT NULL AFTER id");
        }
        
        $baseQuery = "FROM opd_doctors d LEFT JOIN users u ON d.user_id = u.id";
        
        $whereClause = "";
        $params = [];
        
        if (!empty($search)) {
            $whereClause = " WHERE (d.name LIKE ? OR d.specialization LIKE ? OR d.contact_no LIKE ? OR d.hospital LIKE ?)";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
        }

        $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_doctors");
        $totalRecords = $totalStmt->fetchColumn();

        $filteredStmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery . $whereClause);
        $filteredStmt->execute($params);
        $filteredRecords = $filteredStmt->fetchColumn();

        $orderBy = " ORDER BY d.id DESC";
        $limit = " LIMIT $start, $length";
        
        $dataQuery = "SELECT d.*, COALESCE(d.status, 'Active') as status, u.username as username " . $baseQuery . $whereClause . $orderBy . $limit;

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

    // Get single doctor
    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT d.*, u.username FROM opd_doctors d LEFT JOIN users u ON d.user_id = u.id WHERE d.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $row]);
    }

    // Save doctor
    if ($action === 'save') {
        $id = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $qualification = trim($_POST['qualification'] ?? '');
        $specialization = trim($_POST['specialization'] ?? '');
        $hospital = trim($_POST['hospital'] ?? '');
        $contact_no = trim($_POST['contact_no'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $registration_no = trim($_POST['registration_no'] ?? '');
        $status = trim($_POST['status'] ?? 'Active');
        $added_by = $_SESSION['user_id'] ?? null;

        if (empty($name)) {
            json_response(['success' => false, 'message' => 'Doctor name is required'], 400);
        }
        
        if (empty($username)) {
            json_response(['success' => false, 'message' => 'Username is required'], 400);
        }

        if ($emptyCheck = empty($name)) {
             json_response(['success' => false, 'message' => 'Doctor name is required'], 400);
        }

        $pdo->beginTransaction();
        try {
            // Check columns and add user_id if needed
            $checkUserId = $pdo->query("SHOW COLUMNS FROM opd_doctors LIKE 'user_id'");
            if ($checkUserId->rowCount() == 0) {
                $pdo->exec("ALTER TABLE opd_doctors ADD COLUMN user_id INT NULL AFTER id");
            } else {
                 try { $pdo->exec("ALTER TABLE opd_doctors DROP FOREIGN KEY opd_doctors_ibfk_1"); } catch(Exception $e){}
            }

            // Manage User Account
            $user_id = null;
            
            if ($id) {
                // Updating existing doctor
                $stmt = $pdo->prepare("SELECT user_id FROM opd_doctors WHERE id = ?");
                $stmt->execute([$id]);
                $currentData = $stmt->fetch(PDO::FETCH_ASSOC);
                $user_id = $currentData['user_id'] ?? null;
            }

            // Check if username already exists
            $userCheck = $pdo->prepare("SELECT id, role, full_name FROM users WHERE username = ?");
            $userCheck->execute([$username]);
            $existingUser = $userCheck->fetch(PDO::FETCH_ASSOC);

            if ($existingUser) {
                // Username exists
                if ($user_id && $existingUser['id'] == $user_id) {
                    // It's OUR user, proceed
                } elseif (!$user_id && $existingUser['role'] === 'doctor' && $existingUser['full_name'] === $name) {
                    // It's an orphaned user matching us (probably from a failed previous save). Link it.
                    $user_id = $existingUser['id'];
                } else {
                    $pdo->rollBack();
                    json_response(['success' => false, 'message' => 'Username already exists'], 400);
                }
            }

            if ($user_id) {
                // Update existing user
                if (!empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $updateUser = $pdo->prepare("UPDATE users SET username = ?, password = ?, full_name = ?, email = ?, is_active = ? WHERE id = ?");
                    $updateUser->execute([$username, $hashed_password, $name, $email, ($status === 'Active' ? 1 : 0), $user_id]);
                } else {
                    $updateUser = $pdo->prepare("UPDATE users SET username = ?, full_name = ?, email = ?, is_active = ? WHERE id = ?");
                    $updateUser->execute([$username, $name, $email, ($status === 'Active' ? 1 : 0), $user_id]);
                }
            } else {
                 // Create new user
                 $tempPass = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : password_hash('password123', PASSWORD_DEFAULT);
                 $insertUser = $pdo->prepare("INSERT INTO users (username, password, full_name, role, email, is_active, created_at) VALUES (?, ?, ?, 'doctor', ?, ?, NOW())");
                 $insertUser->execute([$username, $tempPass, $name, $email, ($status === 'Active' ? 1 : 0)]);
                 $user_id = $pdo->lastInsertId();
            }

            // Check if added_by column exists
            $checkAddedBy = $pdo->query("SHOW COLUMNS FROM opd_doctors LIKE 'added_by'");
            $addedByExists = $checkAddedBy->rowCount() > 0;

            if ($id) {
                $stmt = $pdo->prepare('UPDATE opd_doctors SET user_id=?, name=?, qualification=?, specialization=?, hospital=?, contact_no=?, phone=?, email=?, address=?, registration_no=?, status=?, updated_at=NOW() WHERE id=?');
                $stmt->execute([$user_id, $name, $qualification, $specialization, $hospital, $contact_no, $phone, $email, $address, $registration_no, $status, $id]);
                $message = 'Doctor updated successfully';
            } else {
                if ($addedByExists) {
                    $stmt = $pdo->prepare('INSERT INTO opd_doctors (user_id, name, qualification, specialization, hospital, contact_no, phone, email, address, registration_no, status, added_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
                    $stmt->execute([$user_id, $name, $qualification, $specialization, $hospital, $contact_no, $phone, $email, $address, $registration_no, $status, $added_by]);
                } else {
                    $stmt = $pdo->prepare('INSERT INTO opd_doctors (user_id, name, qualification, specialization, hospital, contact_no, phone, email, address, registration_no, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
                    $stmt->execute([$user_id, $name, $qualification, $specialization, $hospital, $contact_no, $phone, $email, $address, $registration_no, $status]);
                }
                $message = 'Doctor added successfully';
            }
            
            $pdo->commit();
            json_response(['success' => true, 'message' => $message]);
            
        } catch (Exception $e) {
            $pdo->rollBack();
            json_response(['success' => false, 'message' => 'Error saving doctor: ' . $e->getMessage()], 500);
        }
    }

    // Delete doctor
    if ($action === 'delete' && isset($_POST['id'])) {
        // Also delete user account or deactivate? For now just delete doctor
        // Maybe we should delete the user account too?
        $stmt = $pdo->prepare('SELECT user_id FROM opd_doctors WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        $user_id = $stmt->fetchColumn();
        
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('DELETE FROM opd_doctors WHERE id = ?');
            $stmt->execute([$_POST['id']]);
            
            if ($user_id) {
                $delUser = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $delUser->execute([$user_id]);
            }
            $pdo->commit();
            json_response(['success' => true, 'message' => 'Doctor and associated user account deleted successfully']);
        } catch (Exception $e) {
            $pdo->rollBack();
            json_response(['success' => false, 'message' => 'Error deleting: ' . $e->getMessage()], 500);
        }
    }

    // Toggle status
    if ($action === 'toggle_status' && isset($_POST['id'])) {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('UPDATE opd_doctors SET status = IF(status = "Active", "Inactive", "Active"), updated_at = NOW() WHERE id = ?');
            $stmt->execute([$_POST['id']]);
            
            $getStmt = $pdo->prepare('SELECT status, user_id FROM opd_doctors WHERE id = ?');
            $getStmt->execute([$_POST['id']]);
            $row = $getStmt->fetch(PDO::FETCH_ASSOC);
            $newStatus = $row['status'];
            $user_id = $row['user_id'];
            
            if ($user_id) {
                $updateUser = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
                $updateUser->execute([($newStatus === 'Active' ? 1 : 0), $user_id]);
            }
            
            $pdo->commit();
            json_response(['success' => true, 'message' => 'Status updated successfully', 'status' => $newStatus]);
        } catch (Exception $e) {
            $pdo->rollBack();
            json_response(['success' => false, 'message' => 'Error updating status: ' . $e->getMessage()], 500);
        }
    }

    // Stats
    if ($action === 'stats') {
        $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_doctors");
        $total = $totalStmt->fetchColumn();
        
        $checkColumn = $pdo->query("SHOW COLUMNS FROM opd_doctors LIKE 'status'");
        $statusExists = $checkColumn->rowCount() > 0;
        
        if ($statusExists) {
            $activeStmt = $pdo->query("SELECT COUNT(*) FROM opd_doctors WHERE status = 'Active' OR status IS NULL");
            $active = $activeStmt->fetchColumn();
            $inactiveStmt = $pdo->query("SELECT COUNT(*) FROM opd_doctors WHERE status = 'Inactive'");
            $inactive = $inactiveStmt->fetchColumn();
        } else {
            $active = $total;
            $inactive = 0;
        }
        
        $specializationsStmt = $pdo->query("SELECT COUNT(DISTINCT specialization) FROM opd_doctors WHERE specialization IS NOT NULL AND specialization != ''");
        $specializations = $specializationsStmt->fetchColumn();
        
        $hospitalsStmt = $pdo->query("SELECT COUNT(DISTINCT hospital) FROM opd_doctors WHERE hospital IS NOT NULL AND hospital != ''");
        $hospitals = $hospitalsStmt->fetchColumn();
        
        json_response([
            'success' => true,
            'data' => [
                'total' => $total,
                'active' => $active,
                'inactive' => $inactive,
                'specializations' => $specializations,
                'hospitals' => $hospitals
            ]
        ]);
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $t) {
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
