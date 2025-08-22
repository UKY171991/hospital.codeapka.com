<?php
// adminlte3/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pathology Lab Management System</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    <!-- Custom CSS for Sidebar -->
    <style>
    /* Menu open/close state */
    .nav-sidebar .nav-item .nav-treeview {
        display: none;
    }
    .nav-sidebar .nav-item.menu-open > .nav-treeview {
        display: block !important;
    }
    
    /* Arrow rotation */
    .nav-sidebar .nav-link > .right {
        transition: transform .3s ease-in-out;
    }
    .nav-sidebar .nav-item.menu-open > .nav-link > .right {
        transform: rotate(-90deg);
    }
    
    /* Active menu styling */
    .nav-sidebar .nav-link.active {
        background-color: #007bff !important;
        color: #fff !important;
    }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">