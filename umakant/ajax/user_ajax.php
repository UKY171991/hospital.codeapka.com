if ($action === 'delete' && isset($_POST['id'])) {
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$_POST['id']]);
    exit('success');
}
<?php
// ajax/user_ajax.php
require_once '../inc/auth.php';
require_once '../inc/connection.php';

$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    $stmt = $pdo->query('SELECT id, username, email, role FROM users ORDER BY id DESC');
    while ($row = $stmt->fetch()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['username']) . '</td>';
        echo '<td>' . htmlspecialchars($row['email']) . '</td>';
        echo '<td>' . htmlspecialchars($row['role']) . '</td>';
    echo '<td>';
    echo '<button class="btn btn-sm btn-info edit-btn" data-id="' . $row['id'] . '"><i class="fas fa-edit"></i> Edit</button> ';
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
        $stmt = $pdo->prepare('INSERT INTO users (username, email, full_name, role, password_hash) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$username, $email, $full_name, $role, $hash]);
    }
    exit('success');
}
