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
    .nav-treeview {
        display: none;
    }
    .menu-open .nav-treeview {
        display: block;
    }
    .nav-sidebar .nav-item > .nav-link .right {
        transition: transform 0.3s;
    }
    .nav-sidebar .nav-item.menu-open > .nav-link .right {
        transform: rotate(-90deg);
    }
    .brand-link {
        display: flex;
        align-items: center;
        padding: 0.8125rem 0.5rem;
        font-size: 1.25rem;
        line-height: 1.2;
        color: rgba(255, 255, 255, 0.8);
        white-space: nowrap;
        border-bottom: 1px solid #4f5962;
    }
    .brand-link:hover {
        color: #fff;
        text-decoration: none;
    }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
