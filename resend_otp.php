<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and needs verification
if (!isset($_SESSION['user_id']) || !isset($_SESSION['needs_verification'])) {
    header("Location: login.php");
    exit();
}

try {
    // Generate new OTP
    $otp = sprintf("%06d", mt_rand(0, 999999));
    $user_id = $_SESSION['user_id'];
    
    // Update OTP in database
    $conn = Database::getConnection();
    $stmt = $conn->prepare("UPDATE user SET otp_code = ? WHERE id = ?");
    $stmt->bind_param("si", $otp, $user_id);
    
    if ($stmt->execute()) {
        // Send new verification email
        require_once 'config/mailer.php';
        $mailer = Mailer::getInstance();
        $name = $_SESSION['fname'] . ' ' . $_SESSION['lname'];
        $email = $_SESSION['email'];
        
        if ($mailer->sendVerificationEmail($email, $name, $otp)) {
            $_SESSION['success_message'] = "New verification code sent! Please check your email.";
        } else {
            $_SESSION['error_message'] = "Failed to send verification code. Please try again.";
        }
    } else {
        $_SESSION['error_message'] = "Failed to generate new verification code. Please try again.";
    }
} catch (Exception $e) {
    error_log("Resend OTP error: " . $e->getMessage());
    $_SESSION['error_message'] = "System error while resending verification code. Please try again later.";
}

// Redirect back to verify page
header("Location: verify.php");
exit(); 