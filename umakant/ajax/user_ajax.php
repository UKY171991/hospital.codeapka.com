<?php
// ajax/user_ajax.php
require_once '../inc/auth.php';
require_once '../inc/connection.php';

$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    $stmt = $pdo->query('SELECT id, username, password_hash, full_name, email, role, added_by, created_at, updated_at, expire FROM users ORDER BY created_at DESC, id DESC');
    while ($row = $stmt->fetch()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['username']) . '</td>';
        echo '<td>' . (isset($row['password_hash']) ? htmlspecialchars(substr($row['password_hash'], 0, 20) . '...') : 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($row['full_name'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($row['email'] ?? 'N/A') . '</td>';
        echo '<td><span class="badge badge-' . ($row['role'] === 'admin' ? 'danger' : 'info') . '">' . htmlspecialchars($row['role']) . '</span></td>';
        echo '<td>' . htmlspecialchars($row['added_by'] ?? 'N/A') . '</td>';
        echo '<td>' . ($row['created_at'] ? date('Y-m-d H:i:s', strtotime($row['created_at'])) : 'N/A') . '</td>';
        echo '<td>' . ($row['updated_at'] ? date('Y-m-d H:i:s', strtotime($row['updated_at'])) : 'N/A') . '</td>';
        echo '<td>' . ($row['expire'] ? date('Y-m-d H:i:s', strtotime($row['expire'])) : 'N/A') . '</td>';
        echo '<td>';
        echo '<button class="btn btn-sm btn-info" onclick="viewUser(' . $row['id'] . ')"><i class="fas fa-eye"></i> View</button> ';
        echo '<button class="btn btn-sm btn-warning" onclick="editUser(' . $row['id'] . ')"><i class="fas fa-edit"></i> Edit</button> ';
        echo '<button class="btn btn-sm btn-danger" onclick="deleteUser(' . $row['id'] . ')"><i class="fas fa-trash"></i> Delete</button>';
        echo '</td>';
        echo '</tr>';
    }
    exit;
}

// This will be used for user.php table output
if ($action === 'list_basic') {
    $stmt = $pdo->query('SELECT id, username, email, full_name, role, expire FROM users ORDER BY created_at DESC, id DESC');
    while ($row = $stmt->fetch()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['username']) . '</td>';
        echo '<td>' . htmlspecialchars($row['email'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($row['full_name'] ?? 'N/A') . '</td>';
        echo '<td><span class="badge badge-' . ($row['role'] === 'admin' ? 'danger' : 'info') . '">' . htmlspecialchars($row['role']) . '</span></td>';
        echo '<td>' . ($row['expire'] ? date('Y-m-d H:i:s', strtotime($row['expire'])) : 'N/A') . '</td>';
        echo '<td>';
        echo '<button class="btn btn-sm btn-warning edit-btn" data-id="' . $row['id'] . '"><i class="fas fa-edit"></i> Edit</button> ';
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
    $role = trim($_POST['role'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $expire = !empty($_POST['expire']) ? $_POST['expire'] : null;
    
    if ($id) {
        // Edit
        if ($password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET username=?, email=?, full_name=?, role=?, password_hash=?, expire=? WHERE id=?');
            $stmt->execute([$username, $email, $full_name, $role, $hashed_password, $expire, $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET username=?, email=?, full_name=?, role=?, expire=? WHERE id=?');
            $stmt->execute([$username, $email, $full_name, $role, $expire, $id]);
        }
        $message = 'User updated successfully!';
    } else {
        // Add
        if (!$password) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Password is required for new users']);
            exit;
        }
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, email, full_name, role, password_hash, added_by, expire) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$username, $email, $full_name, $role, $hashed_password, $_SESSION['user_id'], $expire]);
        $message = 'User added successfully!';
    }
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => $message]);
    exit;
}

if ($action === 'delete' && isset($_POST['id'])) {
    try {
        // Prevent deleting own account
        if ($_POST['id'] == $_SESSION['user_id']) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'You cannot delete your own account']);
            exit;
        }
        
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'User deleted successfully!']);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Error deleting user: ' . $e->getMessage()]);
    }
    exit;
}
