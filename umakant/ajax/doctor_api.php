<?php
// ajax/doctor_api.php - simple CRUD for doctors table (AJAX JSON)
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

try {
    $action = $_REQUEST['action'] ?? ($_SERVER['REQUEST_METHOD'] === 'POST' ? 'save' : 'list');

    if ($action === 'list') {
        $stmt = $pdo->query('SELECT d.id,d.server_id,d.name,d.qualification,d.specialization,d.hospital,d.contact_no,d.phone,d.percent,d.email,d.address,d.registration_no,d.added_by,d.created_at,d.updated_at,u.username AS added_by_username FROM doctors d LEFT JOIN users u ON d.added_by = u.id ORDER BY d.id DESC');
        $rows = $stmt->fetchAll();
        json_response(['success' => true, 'data' => $rows]);
    }

    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT d.id,d.server_id,d.name,d.qualification,d.specialization,d.hospital,d.contact_no,d.phone,d.percent,d.email,d.address,d.registration_no,d.added_by,d.created_at,d.updated_at,u.username as added_by_username FROM doctors d LEFT JOIN users u ON d.added_by = u.id WHERE d.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        json_response(['success' => true, 'data' => $row]);
    }

    if ($action === 'save') {
        // allow master and admin to save
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) {
            json_response(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $id = $_POST['id'] ?? '';
        $server_id = isset($_POST['server_id']) && is_numeric($_POST['server_id']) ? (int)$_POST['server_id'] : null;
        $name = trim($_POST['name'] ?? '');
        $qualification = trim($_POST['qualification'] ?? '');
        $specialization = trim($_POST['specialization'] ?? '');
        $hospital = trim($_POST['hospital'] ?? '');
        $contact_no = trim($_POST['contact_no'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $registration_no = trim($_POST['registration_no'] ?? '');
        $percent = isset($_POST['percent']) ? (float)$_POST['percent'] : 0.00;
        $added_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        if ($id) {
            try {
                $stmt = $pdo->prepare('UPDATE doctors SET server_id=?, name=?, qualification=?, specialization=?, hospital=?, contact_no=?, phone=?, percent=?, email=?, address=?, registration_no=?, updated_at=NOW() WHERE id=?');
                $stmt->execute([$server_id, $name, $qualification, $specialization, $hospital, $contact_no, $phone, $percent, $email, $address, $registration_no, $id]);
                json_response(['success' => true, 'message' => 'Doctor updated']);
            } catch (PDOException $e) {
                json_response(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
            }
        } else {
            try {
                $stmt = $pdo->prepare('INSERT INTO doctors (server_id, name, qualification, specialization, hospital, contact_no, phone, percent, email, address, registration_no, added_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
                $stmt->execute([$server_id, $name, $qualification, $specialization, $hospital, $contact_no, $phone, $percent, $email, $address, $registration_no, $added_by]);
                json_response(['success' => true, 'message' => 'Doctor added']);
            } catch (PDOException $e) {
                json_response(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
            }
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        // allow master and admin to delete
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) {
            json_response(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        try {
            $stmt = $pdo->prepare('DELETE FROM doctors WHERE id = ?');
            $stmt->execute([$_POST['id']]);
            json_response(['success' => true, 'message' => 'Doctor deleted']);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    if ($action === 'stats') {
        // Get doctor statistics
        $totalStmt = $pdo->query('SELECT COUNT(*) FROM doctors');
        $total = $totalStmt->fetchColumn();
        
        $activeStmt = $pdo->query('SELECT COUNT(*) FROM doctors WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)');
        $active = $activeStmt->fetchColumn();
        
        $specializationStmt = $pdo->query('SELECT COUNT(DISTINCT specialization) FROM doctors WHERE specialization IS NOT NULL AND specialization != ""');
        $specializations = $specializationStmt->fetchColumn();
        
        $hospitalStmt = $pdo->query('SELECT COUNT(DISTINCT hospital) FROM doctors WHERE hospital IS NOT NULL AND hospital != ""');
        $hospitals = $hospitalStmt->fetchColumn();
        
        json_response([
            'success' => true,
            'data' => [
                'total' => $total,
                'active' => $active,
                'specializations' => $specializations,
                'hospitals' => $hospitals
            ]
        ]);
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $t) {
    // ensure any uncaught error returns JSON so client sees it
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
