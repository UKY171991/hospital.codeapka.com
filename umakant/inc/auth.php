<?php
session_start();

// If user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user info from session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Check if user still exists and is active in database
require_once 'inc/connection.php';

$stmt = $conn->prepare("SELECT id, username, role, is_active FROM users WHERE id = ? AND is_active = 1");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // User not found or not active, destroy session and redirect to login
    session_destroy();
    header('Location: login.php');
    exit();
}
?>