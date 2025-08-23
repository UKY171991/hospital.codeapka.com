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
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
    /* Critical dropdown menu fixes */
    .dropdown-menu.show {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
        z-index: 1000 !important;
    }
    
    /* Fix dropdown toggle arrow */
    .dropdown-toggle::after {
        display: inline-block;
        margin-left: 0.255em;
        vertical-align: 0.255em;
        content: "";
        border-top: 0.3em solid;
        border-right: 0.3em solid transparent;
        border-bottom: 0;
        border-left: 0.3em solid transparent;
    }
    
    /* Fix sidebar menu arrow rotation */
    .nav-sidebar .nav-item.menu-open > .nav-link > .right {
        transform: rotate(-90deg);
    }
    
    /* Ensure submenus appear on top of content */
    .nav-treeview {
        z-index: 10;
        position: relative;
    }
    
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
    
    /* Dropdown styling fixes */
    .dropdown-menu {
        z-index: 1000;
    }
    .dropdown.show .dropdown-menu {
        display: block;
    }
    
    /* Fix for user menu dropdown */
    .user-menu.show .dropdown-menu {
        display: block;
    }
    .user-header {
        padding: 10px;
        text-align: center;
    }
    .user-body {
        padding: 10px;
    }
    .user-footer {
        padding: 10px;
        display: flex;
        justify-content: space-between;
    }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">