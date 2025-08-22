<?php
// ajax/doctor_ajax.php
require_once '../inc/auth.php';
require_once '../inc/connection.php';

$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    try {
        $stmt = $pdo->query('SELECT d.*, u.username AS added_by_username FROM doctors d LEFT JOIN users u ON d.added_by = u.id ORDER BY d.id DESC');
        while ($row = $stmt->fetch()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['specialization']) . '</td>';
        echo '<td>' . htmlspecialchars($row['email']) . '</td>';
        echo '<td>' . htmlspecialchars($row['added_by_username'] ?? '') . '</td>';
        echo '<td>';
        echo '<button class="btn btn-sm btn-info edit-btn" data-id="' . $row['id'] . '"><i class="fas fa-edit"></i> Edit</button> ';
        echo '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $row['id'] . '"><i class="fas fa-trash"></i> Delete</button>';
        echo '</td>';
        echo '</tr>';
        }
    } catch (PDOException $e) {
        echo '<tr><td colspan="6" class="text-center text-danger">Error loading doctors: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
    }
    exit;
}

if ($action === 'get' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM doctors WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $doctor = $stmt->fetch();
        header('Content-Type: application/json');
        echo json_encode($doctor);
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'save') {
    try {
        $id = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $qualification = trim($_POST['qualification'] ?? '');
        $specialization = trim($_POST['specialization'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $registration_no = trim($_POST['registration_no'] ?? '');
        if ($id) {
            $stmt = $pdo->prepare('UPDATE doctors SET name=?, qualification=?, specialization=?, phone=?, email=?, address=?, registration_no=? WHERE id=?');
            $stmt->execute([$name, $qualification, $specialization, $phone, $email, $address, $registration_no, $id]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO doctors (name, qualification, specialization, phone, email, address, registration_no) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$name, $qualification, $specialization, $phone, $email, $address, $registration_no]);
        }
        exit('success');
    } catch (PDOException $e) {
        exit('error: ' . $e->getMessage());
    }
}

if ($action === 'delete' && isset($_POST['id'])) {
    try {
        $stmt = $pdo->prepare('DELETE FROM doctors WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        exit('success');
    } catch (PDOException $e) {
        exit('error: ' . $e->getMessage());
    }
}
