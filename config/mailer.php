<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once dirname(__DIR__) . '/vendor/autoload.php';

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
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function sendVerificationEmail($email, $name, $otp) {
        try {
            // Clear all addresses and attachments
            $this->mailer->clearAllRecipients();
            $this->mailer->clearAttachments();
            
            // Add recipient
            $this->mailer->addAddress($email, $name);
            $this->mailer->Subject = 'Verify Your AirGo Account';
            
            // Email body
            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background-color: #07353f; padding: 20px; text-align: center;'>
                    <h1 style='color: #ffffff; margin: 0;'>AirGo</h1>
                </div>
                <div style='padding: 20px; background-color: #ffffff; border: 1px solid #e0e0e0;'>
                    <h2 style='color: #07353f;'>Welcome to AirGo!</h2>
                    <p>Dear $name,</p>
                    <p>Thank you for registering with AirGo. To complete your registration, please use the following verification code:</p>
                    <div style='background-color: #f5f5f5; padding: 15px; text-align: center; margin: 20px 0;'>
                        <h2 style='color: #07353f; letter-spacing: 5px; margin: 0;'>$otp</h2>
                    </div>
                    <p>This code will expire in 15 minutes.</p>
                    <p>If you didn't create an account with AirGo, please ignore this email.</p>
                    <p>Best regards,<br>The AirGo Team</p>
                </div>
                <div style='text-align: center; padding: 20px; color: #666666; font-size: 12px;'>
                    <p>This is an automated message, please do not reply to this email.</p>
                </div>
            </div>";
            
            $this->mailer->Body = $body;
            $this->mailer->AltBody = "Your verification code is: $otp";
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Failed to send verification email: " . $e->getMessage());
            return false;
        }
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
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Failed to send email: " . $e->getMessage());
            return false;
        }
    }
} 