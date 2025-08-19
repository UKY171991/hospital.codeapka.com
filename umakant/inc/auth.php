<?php
// auth.php
// Use this file to protect pages that require login
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
