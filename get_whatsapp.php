<?php
// Simple script to get WhatsApp number from database
require_once __DIR__ . '/umakant/inc/connection.php';

try {
    $stmt = $pdo->prepare('SELECT whatsapp FROM owners WHERE whatsapp IS NOT NULL AND whatsapp != "" LIMIT 1');
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result && $result['whatsapp']) {
        // Clean the WhatsApp number (remove any non-numeric characters except +)
        $whatsapp = preg_replace('/[^0-9+]/', '', $result['whatsapp']);
        // If it doesn't start with +, assume it's Indian number and add +91
        if (!str_starts_with($whatsapp, '+')) {
            $whatsapp = '+91' . $whatsapp;
        }
        echo $whatsapp;
    } else {
        echo '+919876543210'; // Default fallback number
    }
} catch (Exception $e) {
    echo '+919876543210'; // Default fallback number
}
?>