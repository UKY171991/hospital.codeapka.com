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
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    // User not found or not active, destroy session and redirect to login
    session_destroy();
    header('Location: login.php');
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>