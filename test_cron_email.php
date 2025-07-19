<?php
// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/cron_test_log');

// Get the base path
define('BASE_PATH', dirname(__FILE__));

// Include required files
require BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/config/database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Mailer configuration
class Mailer {
    private static $instance = null;
    private $mailer;

    private function __construct() {
        $this->mailer = new PHPMailer(true);
        
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = 'mail.airgoaircon.com';  // Your cPanel mail server
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = 'donotreply@airgoaircon.com';  // Your cPanel email
            $this->mailer->Password = 'jX^)U_Pp,V_({Mj9';  // Your email password
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;  // SSL encryption
            $this->mailer->Port = 465;  // SSL port
            
            // Connection options for SSL verification
            $this->mailer->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            // Timeout settings
            $this->mailer->Timeout = 60;
            $this->mailer->SMTPKeepAlive = true;
            
            // Default settings
            $this->mailer->isHTML(true);
            $this->mailer->setFrom('donotreply@airgoaircon.com', 'AirGo');
            $this->mailer->CharSet = 'UTF-8';
            
            // Debug mode
            $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;
            $this->mailer->Debugoutput = function($str, $level) {
                error_log("[TEST] SMTP Debug: $str");
            };
            
        } catch (Exception $e) {
            error_log("[TEST] SMTP Setup Error: " . $e->getMessage());
            throw $e;
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function sendEmail($to, $subject, $message) {
        try {
            // Clear all addresses and attachments
            $this->mailer->clearAllRecipients();
            $this->mailer->clearAttachments();
            
            // Add recipient
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $message;
            
            $result = $this->mailer->send();
            
            if ($result) {
                error_log("[TEST] Email sent successfully to: $to");
            } else {
                error_log("[TEST] Failed to send email to: $to");
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("[TEST] Mailer Error: " . $e->getMessage());
            return false;
        }
    }
}

// Specify the booking ID to test
$TEST_BOOKING_ID = 1; // Change this to your desired booking ID

// Create database connection
try {
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8mb4
    $conn->set_charset("utf8mb4");
    error_log("[TEST] Database connection established successfully");
} catch (Exception $e) {
    error_log("[TEST] Database connection error: " . $e->getMessage());
    exit();
}

// Get current date and time in Philippines timezone
date_default_timezone_set('Asia/Manila');
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');

try {
    if (!$conn) {
        throw new Exception("Database connection not available");
    }

    error_log("[TEST] Starting test run at " . date('Y-m-d H:i:s') . " for booking ID: " . $TEST_BOOKING_ID);

    // Query for specific booking - join with user table to get email
    $query = "SELECT b.*, u.email, u.fname, u.lname, u.contact 
             FROM bookings b 
             JOIN user u ON b.user_id = u.id 
             WHERE b.id = ?";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $TEST_BOOKING_ID);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();
        error_log("[TEST] Found booking with these details: " . print_r($booking, true));
        
        $appointmentTime = date('h:i A', strtotime($booking['appointment_time']));
        $appointmentDate = date('Y-m-d', strtotime($booking['appointment_date']));
        
        // Get mailer instance
        $mailer = Mailer::getInstance();
        
        // Test both today and tomorrow templates
        $subjects = [
            "today" => "[TEST] Today's Airgo Appointment Reminder",
            "tomorrow" => "[TEST] Tomorrow's Airgo Appointment Reminder"
        ];
        
        foreach ($subjects as $type => $subject) {
            $message = "
                <html>
                <body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background-color: #07353f; padding: 20px; text-align: center;'>
                        <h1 style='color: #ffffff; margin: 0;'>AirGo Appointment Reminder</h1>
                    </div>
                    <div style='padding: 20px; background-color: #ffffff; border: 1px solid #e0e0e0;'>
                        <h2 style='color: #07353f;'>Test " . ($type == 'today' ? "Today" : "Tomorrow") . "'s Appointment Reminder</h2>
                        <p>Dear {$booking['fname']} {$booking['lname']},</p>
                        <p>This is a TEST reminder that you have an appointment scheduled for " . 
                        ($type == 'today' ? "today" : "tomorrow") . " at {$appointmentTime}.</p>
                        <div style='background-color: #f5f5f5; padding: 15px; margin: 20px 0;'>
                            <p><strong>Service:</strong> {$booking['service']}</p>
                            <p><strong>Location:</strong> {$booking['location']}</p>
                            <p><strong>Contact:</strong> {$booking['contact']}</p>
                        </div>
                        <p>Please ensure you are available at the scheduled time.</p>
                        <p>Thank you for choosing Airgo Aircon Cleaning!</p>
                        <p style='color: #ff0000;'><strong>Note: This is a test email for cron job verification.</strong></p>
                        <p><em>Booking ID: {$TEST_BOOKING_ID}</em></p>
                        <p><em>Test Type: " . ucfirst($type) . " template</em></p>
                        <p><em>Actual Appointment Date: {$appointmentDate}</em></p>
                    </div>
                    <div style='text-align: center; padding: 20px; color: #666666; font-size: 12px;'>
                        <p>This is an automated message, please do not reply to this email.</p>
                    </div>
                </body>
                </html>
            ";
            
            error_log("[TEST] Attempting to send {$type} template test email to: {$booking['email']}");
            
            try {
                if ($mailer->sendEmail($booking['email'], $subject, $message)) {
                    error_log("[TEST] Successfully sent {$type} template email to: {$booking['email']}");
                } else {
                    error_log("[TEST] Failed to send {$type} template email to: {$booking['email']}");
                }
            } catch (Exception $e) {
                error_log("[TEST] Email sending error: " . $e->getMessage());
            }
        }
        
        error_log("[TEST] Test completed for booking ID: {$TEST_BOOKING_ID}");
    } else {
        error_log("[TEST] No booking found with ID: {$TEST_BOOKING_ID}");
    }

} catch (Exception $e) {
    error_log("[TEST] Error: " . $e->getMessage());
} finally {
    if (isset($stmt) && $stmt instanceof mysqli_stmt) {
        $stmt->close();
    }
    if ($conn) {
        $conn->close();
        error_log("[TEST] Database connection closed");
    }
}

// Log script completion
error_log("[TEST] Test reminder script completed at " . date('Y-m-d H:i:s')); 