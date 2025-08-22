<?php
// This file will be included in sidebar.php as a fallback solution
// if the JavaScript-based menu doesn't work properly

// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);

// Define pages that should open the Pathology Management menu
$pathology_pages = [
    'user-list.php', 'doctor-list.php', 'patient-list.php', 
    'test-list.php', 'test-category-list.php', 'entry-list.php',
    'user.php', 'doctor.php', 'patient.php', 
    'test.php', 'test-category.php', 'entry.php',
    'menu-test.php'
];

// Check if current page is in the pathology pages array
$is_pathology_page = in_array($current_page, $pathology_pages);

// Simple function to check if a menu item should be active
function is_menu_active($page_names) {
    global $current_page;
    if (is_array($page_names)) {
        return in_array($current_page, $page_names);
    } else {
        return $current_page == $page_names;
    }
}