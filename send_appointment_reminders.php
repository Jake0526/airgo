<?php
// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/reminder_log');

// Get the base path
define('BASE_PATH', dirname(__FILE__));

// Include required files
require BASE_PATH . '/vendor/autoload.php';
require BASE_PATH . '/config/database.php';
require BASE_PATH . '/config/mailer.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Create database connection
try {
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8mb4
    $conn->set_charset("utf8mb4");
    error_log("Database connection established successfully");
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    exit();
}

// Get current date and time in Philippines timezone
date_default_timezone_set('Asia/Manila');
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');
$tomorrowDate = date('Y-m-d', strtotime('+1 day'));

try {
    if (!$conn) {
        throw new Exception("Database connection not available");
    }

    error_log("Starting reminder run at " . date('Y-m-d H:i:s'));

    // Query for appointments tomorrow
    $tomorrowQuery = "SELECT b.*, u.email, u.fname, u.lname 
                     FROM bookings b 
                     JOIN user u ON b.user_id = u.id 
                     WHERE DATE(b.appointment_date) = ? 
                     AND b.status = 'confirmed'";

    $stmt = $conn->prepare($tomorrowQuery);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $tomorrowDate);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $tomorrowResults = $stmt->get_result();
    error_log("Found " . $tomorrowResults->num_rows . " appointments for tomorrow");

    // Send reminders for tomorrow's appointments
    while ($booking = $tomorrowResults->fetch_assoc()) {
        $appointmentTime = date('h:i A', strtotime($booking['appointment_time']));
        $subject = "Your AirGo Appointment Tomorrow";
        $message = "
            <html>
            <body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background-color: #07353f; padding: 20px; text-align: center;'>
                    <h1 style='color: #ffffff; margin: 0;'>AirGo Appointment Reminder</h1>
                </div>
                <div style='padding: 20px; background-color: #ffffff; border: 1px solid #e0e0e0;'>
                    <h2 style='color: #07353f;'>Appointment Reminder</h2>
                    <p>Dear {$booking['fname']} {$booking['lname']},</p>
                    <p>This is a friendly reminder that you have an appointment scheduled for tomorrow at {$appointmentTime}.</p>
                    <div style='background-color: #f5f5f5; padding: 15px; margin: 20px 0;'>
                        <p><strong>Service:</strong> {$booking['service']}</p>
                        <p><strong>Location:</strong> {$booking['location']}</p>
                        <p><strong>Contact:</strong> {$booking['phone']}</p>
                    </div>
                    <p>If you need to make any changes, please contact us as soon as possible.</p>
                    <p>Thank you for choosing Airgo Aircon Cleaning!</p>
                </div>
                <div style='text-align: center; padding: 20px; color: #666666; font-size: 12px;'>
                    <p>This is an automated message, please do not reply to this email.</p>
                </div>
            </body>
            </html>
        ";
        
        error_log("Attempting to send tomorrow's reminder to: {$booking['email']}");
        try {
            $mailer = Mailer::getInstance();
            if ($mailer->sendEmail($booking['email'], $subject, $message)) {
                error_log("Successfully sent tomorrow's reminder to: {$booking['email']}");
            } else {
                error_log("Failed to send tomorrow's reminder to: {$booking['email']}");
            }
        } catch (Exception $e) {
            error_log("Email sending error: " . $e->getMessage());
        }
    }

    // Query for today's appointments (if current time is 5 AM)
    if (date('H') == '05' && date('i') < 15) { // Run between 5:00 AM and 5:15 AM
        $todayQuery = "SELECT b.*, u.email, u.fname, u.lname 
                       FROM bookings b 
                       JOIN user u ON b.user_id = u.id 
                       WHERE DATE(b.appointment_date) = ? 
                       AND b.status = 'confirmed'";

        $stmt = $conn->prepare($todayQuery);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $currentDate);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $todayResults = $stmt->get_result();
        error_log("Found " . $todayResults->num_rows . " appointments for today");

        // Send reminders for today's appointments
        while ($booking = $todayResults->fetch_assoc()) {
            $appointmentTime = date('h:i A', strtotime($booking['appointment_time']));
            $subject = "Your AirGo Appointment Today";
            $message = "
                <html>
                <body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background-color: #07353f; padding: 20px; text-align: center;'>
                        <h1 style='color: #ffffff; margin: 0;'>AirGo Appointment Reminder</h1>
                    </div>
                    <div style='padding: 20px; background-color: #ffffff; border: 1px solid #e0e0e0;'>
                        <h2 style='color: #07353f;'>Today's Appointment Reminder</h2>
                        <p>Dear {$booking['fname']} {$booking['lname']},</p>
                        <p>This is a reminder that you have an appointment scheduled for today at {$appointmentTime}.</p>
                        <div style='background-color: #f5f5f5; padding: 15px; margin: 20px 0;'>
                            <p><strong>Service:</strong> {$booking['service']}</p>
                            <p><strong>Location:</strong> {$booking['location']}</p>
                            <p><strong>Contact:</strong> {$booking['phone']}</p>
                        </div>
                        <p>Please ensure you are available at the scheduled time.</p>
                        <p>Thank you for choosing Airgo Aircon Cleaning!</p>
                    </div>
                    <div style='text-align: center; padding: 20px; color: #666666; font-size: 12px;'>
                        <p>This is an automated message, please do not reply to this email.</p>
                    </div>
                </body>
                </html>
            ";
            
            error_log("Attempting to send today's reminder to: {$booking['email']}");
            try {
                $mailer = Mailer::getInstance();
                if ($mailer->sendEmail($booking['email'], $subject, $message)) {
                    error_log("Successfully sent today's reminder to: {$booking['email']}");
                } else {
                    error_log("Failed to send today's reminder to: {$booking['email']}");
                }
            } catch (Exception $e) {
                error_log("Email sending error: " . $e->getMessage());
            }
        }
    }

} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
} finally {
    if (isset($stmt) && $stmt instanceof mysqli_stmt) {
        $stmt->close();
    }
    if ($conn) {
        $conn->close();
        error_log("Database connection closed");
    }
}

// Log script completion
error_log("Reminder script completed at " . date('Y-m-d H:i:s')); 