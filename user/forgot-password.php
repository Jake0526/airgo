<?php
require_once 'db_connection'; // Include the database connection
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// required files
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    if (!empty($email)) {
        try {
            // Check if the email exists
            $stmt = $conn->prepare("SELECT id FROM user WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // Generate a unique token
                $token = bin2hex(random_bytes(50));

                // Save the token in the database with an expiration time (e.g., 1 hour)
                $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (:user_id, :token, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
                $stmt->bindParam(':user_id', $user['id'], PDO::PARAM_INT);
                $stmt->bindParam(':token', $token, PDO::PARAM_STR);
                $stmt->execute();

                // Send the email
                $resetLink = "http://localhost/login-system-with-forgot-password/reset-password.php?token=$token";

                $mail = new PHPMailer(true);

                // Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'your_gmail_here@gmail.com'; // Your Email Here
                $mail->Password   = 'your_password_here'; // Your App Password Here
                $mail->SMTPSecure = 'ssl';
                $mail->Port       = 465;

                // Recipients
                $mail->setFrom('your_gmail_here@gmail.com', 'Reset Your Password');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $mail->Body    = "Hi,<br><br>Click on the link below to reset your password:<br><br><a href='$resetLink'>$resetLink</a><br><br>The link will expire in 1 hour.";
                
                if ($mail->send()) {
                    echo "
                    <script>
                        alert('Reset password link sent to your email.');
                        window.location.href = 'http://localhost:81/user/';
                    </script>
                    ";
                } else {
                    echo "Failed to send the email.";
                }
            } else {
                echo "
                <script>
                    alert('No account found with this email.');
                    window.location.href = 'http://localhost:81/user/';
                </script>
                ";
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "
        <script>
            alert('Please enter your email.');
            window.location.href = 'http://localhost:81/user/';
        </script>
        ";
    }
}
?>
