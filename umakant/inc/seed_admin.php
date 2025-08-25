<?php
// inc/seed_admin.php
// Safe idempotent seeding of default admin and user accounts.
require_once __DIR__ . '/connection.php';

try {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
    // Admin
    $stmt->execute(['admin']);
    $admin = $stmt->fetch();
    if (!$admin) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $ins = $pdo->prepare('INSERT INTO users (username, password_hash, full_name, email, role, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, datetime("now"))');
        $ins->execute(['admin', $hash, 'Administrator', null, 'admin', 1]);
    }
    // Normal user
    $stmt->execute(['user']);
    $user = $stmt->fetch();
    if (!$user) {
        $hash = password_hash('user123', PASSWORD_DEFAULT);
        $ins->execute(['user', $hash, 'Demo User', null, 'user', 1]);
    }
} catch (Exception $e) {
    // fail silently, don't block UI
}
