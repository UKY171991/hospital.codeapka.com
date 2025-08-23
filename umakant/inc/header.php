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
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">