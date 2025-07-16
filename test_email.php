<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering
ob_start();

// Function to output debug messages
function debug_log($message, $type = 'info') {
    $color = 'black';
    switch($type) {
        case 'error':
            $color = '#dc3545';
            break;
        case 'success':
            $color = '#28a745';
            break;
        case 'info':
            $color = '#17a2b8';
            break;
        case 'warning':
            $color = '#ffc107';
            break;
    }
    echo "<div style='font-family: monospace; margin: 5px 0; padding: 10px; background: #f8f9fa; border-left: 4px solid {$color};'>";
    echo "[" . date('Y-m-d H:i:s') . "] ";
    echo htmlspecialchars($message);
    echo "</div>";
    
    // Only flush if buffer exists
    if (ob_get_level() > 0) {
        ob_flush();
        flush();
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f0f0f0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #07353f;
            margin-bottom: 20px;
        }
        .debug-container {
            background: white;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Email Test Console</h1>
        <div class="debug-container">
<?php

require_once 'config/mailer.php';

try {
    debug_log("Starting email test...");
    debug_log("Initializing mailer...");
    
    $mailer = Mailer::getInstance();
    
    // Test email parameters
    $to_email = 'donotreplyairgo@gmail.com'; // Send to the same email for testing
    $name = 'Test User';
    $otp = '123456';
    
    debug_log("Attempting to send test email to: " . $to_email);
    
    $result = $mailer->sendVerificationEmail($to_email, $name, $otp);
    
    if ($result) {
        debug_log("Test email sent successfully!", 'success');
    } else {
        debug_log("Failed to send test email.", 'error');
    }
} catch (Exception $e) {
    debug_log("Error: " . $e->getMessage(), 'error');
    debug_log("Stack trace:", 'error');
    debug_log($e->getTraceAsString(), 'error');
}
?>
        </div>
    </div>
</body>
</html> 