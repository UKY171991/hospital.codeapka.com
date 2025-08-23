<?php
// This file will be included in sidebar.php to handle menu state

// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);

// Define pages that should have the Pathology Management menu active
$pathology_pages = [
    'user-list.php', 'doctor-list.php', 'patient-list.php', 
    'test-list.php', 'test-category-list.php', 'entry-list.php',
    'user.php', 'doctor.php', 'patient.php', 
    'test.php', 'test-category.php', 'entry.php',
    'menu-test.php'
];

// Define pages that should have the Reports menu active
$report_pages = [
    'data-export.php'
];

// Define pages that should have the Settings menu active
$settings_pages = [
    // Add settings pages when they're created
];

// Check if current page is in the pathology pages array
$is_pathology_page = in_array($current_page, $pathology_pages);
$is_report_page = in_array($current_page, $report_pages);
$is_settings_page = in_array($current_page, $settings_pages);

// Simple function to check if a menu item should be active
function is_menu_active($page_names) {
    global $current_page;
    if (is_array($page_names)) {
        return in_array($current_page, $page_names);
    } else {
        return $current_page == $page_names;
    }
}