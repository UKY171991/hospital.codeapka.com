<?php
// inc/auth.php - simple auth guard
if (session_status() === PHP_SESSION_NONE) session_start();

// allowlist: don't redirect AJAX/API requests or the login page
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$isAjaxHeader = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$allowPatterns = [
    'login.php',
    'register.php',
    '/patho_api/',
    '/ajax/',
    '/assets/',
    '/api/',
];

$allowed = $isAjaxHeader;
foreach ($allowPatterns as $pat) {
    if (stripos($requestUri, $pat) !== false) { $allowed = true; break; }
}

if (!isset($_SESSION['user_id']) && !$allowed) {
    header('Location: login.php');
    exit;
}
