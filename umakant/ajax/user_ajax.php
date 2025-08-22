<?php
// ajax/user_ajax.php
require_once '../inc/auth.php';
require_once '../inc/connection.php';

$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    $stmt = $pdo->query('SELECT id, username, email, full_name, role, created_at FROM users ORDER BY id DESC');
    while ($row = $stmt->fetch()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['username']) . '</td>';
        echo '<td>' . htmlspecialchars($row['email'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($row['full_name'] ?? 'N/A') . '</td>';
        echo '<td><span class="badge badge-' . ($row['role'] === 'admin' ? 'danger' : 'info') . '">' . htmlspecialchars($row['role']) . '</span></td>';
        echo '<td>' . date('d M Y', strtotime($row['created_at'])) . '</td>';
        echo '<td>';
        echo '<button class="btn btn-sm btn-info" onclick="viewUser(' . $row['id'] . ')"><i class="fas fa-eye"></i> View</button> ';
        echo '<a href="../user.php?id=' . $row['id'] . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a> ';
        echo '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $row['id'] . '"><i class="fas fa-trash"></i> Delete</button>';
        echo '</td>';
        echo '</tr>';
    }
    exit;
}

if ($action === 'get' && isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $user = $stmt->fetch();
    header('Content-Type: application/json');
    echo json_encode($user);
    exit;
}

if ($action === 'save') {
    $id = $_POST['id'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $role = $_POST['role'] ?? 'user';
    $password = $_POST['password'] ?? '';
    
    if ($id) {
        // Edit
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET username=?, email=?, full_name=?, role=?, password_hash=? WHERE id=?');
            $stmt->execute([$username, $email, $full_name, $role, $hash, $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET username=?, email=?, full_name=?, role=? WHERE id=?');
            $stmt->execute([$username, $email, $full_name, $role, $id]);
        }
    } else {
        // Add
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, email, full_name, role, password_hash, added_by) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$username, $email, $full_name, $role, $hash, $_SESSION['user_id']]);
    }
    exit('success');
}

if ($action === 'delete' && isset($_POST['id'])) {
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$_POST['id']]);
    exit('success');
}
