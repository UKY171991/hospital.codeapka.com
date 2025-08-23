<?php
// adminlte3/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    
    <title>Pathology Lab Management System</title>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom CSS for Sidebar -->
    <style>
    /* Menu open/close state - Display submenu only when menu-open class is present */
    .nav-sidebar .nav-item.menu-open > .nav-treeview {
        display: block !important;
    }
    
    /* Arrow rotation - Show rotated for open state */
    .nav-sidebar .menu-open > .nav-link > .right,
    .rotate-90 {
        transform: rotate(-90deg);
        transition: transform 0.3s ease;
    }
    
    /* Active menu styling */
    .nav-sidebar .nav-link.active {
        background-color: #007bff !important;
        color: #fff !important;
    }
    
    /* Content Header styling */
    .content-header {
        padding: 15px 0.5rem;
    }
    
    /* Card header styling */
    .card-header {
        padding: 0.75rem 1.25rem;
        margin-bottom: 0;
        background-color: rgba(0,0,0,.03);
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    
    /* Ensure submenu items are clickable */
    .nav-sidebar .nav-treeview .nav-item {
        z-index: 1;
        position: relative;
    }
    
    /* Smooth transition for menu items */
    .nav-treeview {
        transition: all 0.3s ease;
    }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">