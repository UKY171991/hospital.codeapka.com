<?php
// ajax/doctor_api.php - simple CRUD for doctors table (AJAX JSON)
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

$action = $_REQUEST['action'] ?? ($_SERVER['REQUEST_METHOD'] === 'POST' ? 'save' : 'list');

if ($action === 'list') {
    $stmt = $pdo->query('SELECT d.id,d.name,d.qualification,d.specialization,d.hospital,d.contact_no,d.phone,d.percent,d.email,d.registration_no,d.added_by,d.created_at,u.username AS added_by_username FROM doctors d LEFT JOIN users u ON d.added_by = u.id ORDER BY d.id DESC');
    $rows = $stmt->fetchAll();
    json_response(['success' => true, 'data' => $rows]);
}

if ($action === 'get' && isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT d.id,d.name,d.qualification,d.specialization,d.hospital,d.contact_no,d.phone,d.percent,d.email,d.address,d.registration_no,d.added_by,d.created_at,u.username as added_by_username FROM doctors d LEFT JOIN users u ON d.added_by = u.id WHERE d.id = ?');
    $stmt->execute([$_GET['id']]);
    $row = $stmt->fetch();
    json_response(['success' => true, 'data' => $row]);
}

if ($action === 'save') {
    // only admin can save
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        json_response(['success' => false, 'message' => 'Unauthorized'], 401);
    }

    $id = $_POST['id'] ?? '';
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
    $stmt = $pdo->prepare('UPDATE doctors SET name=?, qualification=?, specialization=?, hospital=?, contact_no=?, phone=?, percent=?, email=?, address=?, registration_no=?, updated_at=NOW() WHERE id=?');
    $stmt->execute([$name, $qualification, $specialization, $hospital, $contact_no, $phone, $percent, $email, $address, $registration_no, $id]);
        json_response(['success' => true, 'message' => 'Doctor updated']);
    } else {
    $stmt = $pdo->prepare('INSERT INTO doctors (name, qualification, specialization, hospital, contact_no, phone, percent, email, address, registration_no, added_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
    $stmt->execute([$name, $qualification, $specialization, $hospital, $contact_no, $phone, $percent, $email, $address, $registration_no, $added_by]);
        json_response(['success' => true, 'message' => 'Doctor added']);
    }
}

if ($action === 'delete' && isset($_POST['id'])) {
    // only admin can delete
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        json_response(['success' => false, 'message' => 'Unauthorized'], 401);
    }
    $stmt = $pdo->prepare('DELETE FROM doctors WHERE id = ?');
    $stmt->execute([$_POST['id']]);
    json_response(['success' => true, 'message' => 'Doctor deleted']);
}

json_response(['success' => false, 'message' => 'Invalid action'], 400);
