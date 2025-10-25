<?php
// Simple email test script
require_once 'inc/connection.php';
session_start();

// Set user session for testing
$_SESSION['user_id'] = 1; // Adjust as needed

echo "<h2>Email Test Script</h2>";

// Test 1: Check if Gmail credentials are stored
echo "<h3>Test 1: Gmail Credentials</h3>";
try {
    $stmt = $pdo->prepare("SELECT setting_value FROM user_settings WHERE user_id = ? AND setting_key = 'gmail_password'");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "✅ Gmail credentials found in database<br>";
        $password = base64_decode($result['setting_value']);
        echo "Password length: " . strlen($password) . " characters<br>";
    } else {
        echo "❌ No Gmail credentials found. Please configure in Email Settings first.<br>";
        exit;
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
    exit;
}

// Test 2: Test Gmail SMTP connection
echo "<h3>Test 2: Gmail SMTP Connection</h3>";
$smtp_server = 'smtp.gmail.com';
$smtp_port = 587;
$username = 'umakant171991@gmail.com';

echo "Connecting to $smtp_server:$smtp_port...<br>";

$socket = fsockopen($smtp_server, $smtp_port, $errno, $errstr, 10);
if ($socket) {
    echo "✅ SMTP connection successful<br>";
    
    // Read server response
    $response = fgets($socket, 512);
    echo "Server response: " . htmlspecialchars($response) . "<br>";
    
    fclose($socket);
} else {
    echo "❌ SMTP connection failed: $errstr ($errno)<br>";
}

// Test 3: Test simple email sending
echo "<h3>Test 3: Simple Email Test</h3>";

// Configure PHP mail settings
ini_set('SMTP', $smtp_server);
ini_set('smtp_port', $smtp_port);
ini_set('sendmail_from', $username);

$to = $username; // Send to self for testing
$subject = "Test Email from Hospital System - " . date('Y-m-d H:i:s');
$message = "This is a test email sent from the hospital management system.\n\nTime: " . date('Y-m-d H:i:s');

$headers = [
    "From: $username",
    "Reply-To: $username",
    "MIME-Version: 1.0",
    "Content-Type: text/plain; charset=UTF-8",
    "X-Mailer: Hospital Test Script"
];

$headerString = implode("\r\n", $headers);

echo "Attempting to send test email...<br>";
echo "To: $to<br>";
echo "Subject: $subject<br>";

if (mail($to, $subject, $message, $headerString)) {
    echo "✅ Email sent successfully using PHP mail()<br>";
} else {
    echo "❌ Email sending failed using PHP mail()<br>";
    $error = error_get_last();
    if ($error) {
        echo "Error: " . htmlspecialchars($error['message']) . "<br>";
    }
}

// Test 4: Check server configuration
echo "<h3>Test 4: Server Configuration</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Mail function available: " . (function_exists('mail') ? '✅ Yes' : '❌ No') . "<br>";
echo "OpenSSL extension: " . (extension_loaded('openssl') ? '✅ Loaded' : '❌ Not loaded') . "<br>";
echo "Socket functions: " . (function_exists('fsockopen') ? '✅ Available' : '❌ Not available') . "<br>";

// Display current SMTP settings
echo "<br><strong>Current SMTP Settings:</strong><br>";
echo "SMTP Server: " . ini_get('SMTP') . "<br>";
echo "SMTP Port: " . ini_get('smtp_port') . "<br>";
echo "Sendmail From: " . ini_get('sendmail_from') . "<br>";

// Test 5: Alternative sending method
echo "<h3>Test 5: Alternative Method (cURL)</h3>";

if (function_exists('curl_init')) {
    echo "✅ cURL is available<br>";
    
    // Test Gmail API endpoint accessibility
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://smtp.gmail.com:587");
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "cURL Error: " . htmlspecialchars($error) . "<br>";
    } else {
        echo "✅ Gmail SMTP server is accessible via cURL<br>";
    }
} else {
    echo "❌ cURL is not available<br>";
}

echo "<h3>Recommendations:</h3>";
echo "<ul>";
echo "<li>Make sure your Gmail App Password is correctly configured</li>";
echo "<li>Verify that your server allows outbound SMTP connections on port 587</li>";
echo "<li>Check if your hosting provider blocks Gmail SMTP</li>";
echo "<li>Consider using a local SMTP server or email service like SendGrid</li>";
echo "</ul>";

echo "<br><a href='email_compose.php'>← Back to Email Compose</a>";
?>