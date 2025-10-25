<?php
// ajax/email_debug_api.php - Email debugging and testing API
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../inc/connection.php';
    require_once __DIR__ . '/../inc/ajax_helpers.php';
} catch (Exception $e) {
    json_response([
        'success' => false,
        'message' => 'Database connection error: ' . $e->getMessage()
    ], 500);
    exit;
}

session_start();

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    json_response(['success' => false, 'message' => 'Authentication required'], 401);
    exit;
}

$action = $_REQUEST['action'] ?? 'test_smtp';

try {
    switch ($action) {
        case 'test_smtp':
            handleTestSMTP();
            break;
        case 'check_config':
            handleCheckConfig();
            break;
        case 'test_connection':
            handleTestConnection();
            break;
        default:
            json_response(['success' => false, 'message' => 'Invalid action'], 400);
    }
} catch (Exception $e) {
    error_log("Email Debug API Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
}

function handleTestSMTP() {
    $smtp_server = 'smtp.gmail.com';
    $smtp_port = 587;
    
    try {
        // Test basic connection
        $socket = @fsockopen($smtp_server, $smtp_port, $errno, $errstr, 10);
        
        if (!$socket) {
            json_response([
                'success' => false,
                'message' => "Cannot connect to $smtp_server:$smtp_port - $errstr ($errno)"
            ]);
            return;
        }
        
        // Read server greeting
        $response = fgets($socket, 512);
        fclose($socket);
        
        if (substr($response, 0, 3) == '220') {
            json_response([
                'success' => true,
                'message' => "SMTP server responded: " . trim($response)
            ]);
        } else {
            json_response([
                'success' => false,
                'message' => "Unexpected server response: " . trim($response)
            ]);
        }
        
    } catch (Exception $e) {
        json_response([
            'success' => false,
            'message' => "SMTP test failed: " . $e->getMessage()
        ]);
    }
}

function handleCheckConfig() {
    try {
        $config = [];
        
        // PHP Configuration
        $config['PHP Version'] = phpversion();
        $config['Mail Function'] = function_exists('mail') ? '✅ Available' : '❌ Not available';
        $config['OpenSSL Extension'] = extension_loaded('openssl') ? '✅ Loaded' : '❌ Not loaded';
        $config['Socket Functions'] = function_exists('fsockopen') ? '✅ Available' : '❌ Not available';
        $config['cURL Extension'] = extension_loaded('curl') ? '✅ Loaded' : '❌ Not loaded';
        
        // SMTP Settings
        $config['SMTP Server'] = ini_get('SMTP') ?: 'Not configured';
        $config['SMTP Port'] = ini_get('smtp_port') ?: 'Not configured';
        $config['Sendmail From'] = ini_get('sendmail_from') ?: 'Not configured';
        
        // System Information
        $config['Operating System'] = php_uname('s') . ' ' . php_uname('r');
        $config['Server Software'] = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
        
        // Network Tests
        $config['DNS Resolution'] = checkDNSResolution('smtp.gmail.com') ? '✅ Working' : '❌ Failed';
        $config['Port 587 Access'] = testPortAccess('smtp.gmail.com', 587) ? '✅ Open' : '❌ Blocked';
        
        json_response([
            'success' => true,
            'data' => $config
        ]);
        
    } catch (Exception $e) {
        json_response([
            'success' => false,
            'message' => "Configuration check failed: " . $e->getMessage()
        ]);
    }
}

function handleTestConnection() {
    global $pdo;
    
    try {
        // Get stored Gmail password
        $stmt = $pdo->prepare("SELECT setting_value FROM user_settings WHERE user_id = ? AND setting_key = 'gmail_password'");
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            json_response([
                'success' => false,
                'message' => 'No Gmail credentials found. Please configure in Email Settings.'
            ]);
            return;
        }
        
        $password = base64_decode($result['setting_value']);
        $username = 'umakant171991@gmail.com';
        
        // Test full SMTP authentication
        $smtp_server = 'smtp.gmail.com';
        $smtp_port = 587;
        
        $socket = fsockopen($smtp_server, $smtp_port, $errno, $errstr, 30);
        if (!$socket) {
            json_response([
                'success' => false,
                'message' => "Connection failed: $errstr ($errno)"
            ]);
            return;
        }
        
        // Read greeting
        $response = fgets($socket, 512);
        if (substr($response, 0, 3) != '220') {
            fclose($socket);
            json_response([
                'success' => false,
                'message' => "Server not ready: $response"
            ]);
            return;
        }
        
        // EHLO
        fwrite($socket, "EHLO test.local\r\n");
        $response = fgets($socket, 512);
        
        // STARTTLS
        fwrite($socket, "STARTTLS\r\n");
        $response = fgets($socket, 512);
        if (substr($response, 0, 3) != '220') {
            fclose($socket);
            json_response([
                'success' => false,
                'message' => "STARTTLS failed: $response"
            ]);
            return;
        }
        
        // Enable TLS
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            fclose($socket);
            json_response([
                'success' => false,
                'message' => "TLS encryption failed"
            ]);
            return;
        }
        
        // EHLO again
        fwrite($socket, "EHLO test.local\r\n");
        $response = fgets($socket, 512);
        
        // AUTH LOGIN
        fwrite($socket, "AUTH LOGIN\r\n");
        $response = fgets($socket, 512);
        if (substr($response, 0, 3) != '334') {
            fclose($socket);
            json_response([
                'success' => false,
                'message' => "AUTH LOGIN failed: $response"
            ]);
            return;
        }
        
        // Send username
        fwrite($socket, base64_encode($username) . "\r\n");
        $response = fgets($socket, 512);
        if (substr($response, 0, 3) != '334') {
            fclose($socket);
            json_response([
                'success' => false,
                'message' => "Username authentication failed: $response"
            ]);
            return;
        }
        
        // Send password
        fwrite($socket, base64_encode($password) . "\r\n");
        $response = fgets($socket, 512);
        if (substr($response, 0, 3) != '235') {
            fclose($socket);
            json_response([
                'success' => false,
                'message' => "Password authentication failed: $response. Check your App Password."
            ]);
            return;
        }
        
        // QUIT
        fwrite($socket, "QUIT\r\n");
        fclose($socket);
        
        json_response([
            'success' => true,
            'message' => 'Full SMTP authentication successful!'
        ]);
        
    } catch (Exception $e) {
        json_response([
            'success' => false,
            'message' => "Connection test failed: " . $e->getMessage()
        ]);
    }
}

// Helper functions
function checkDNSResolution($hostname) {
    try {
        $ip = gethostbyname($hostname);
        return $ip !== $hostname; // Returns true if resolution successful
    } catch (Exception $e) {
        return false;
    }
}

function testPortAccess($hostname, $port) {
    try {
        $socket = @fsockopen($hostname, $port, $errno, $errstr, 5);
        if ($socket) {
            fclose($socket);
            return true;
        }
        return false;
    } catch (Exception $e) {
        return false;
    }
}
?>