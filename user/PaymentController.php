<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

// Load database connection
$host = 'localhost';
$user = 'root';
$pass = ''; // update if needed
$dbname = 'airgo_db'; // your actual DB name

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize form inputs
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $appointment_date = htmlspecialchars($_POST['appointment_date']);
    $appointment_time = htmlspecialchars($_POST['appointment_time']);
    $service = htmlspecialchars($_POST['service']);
    $location = htmlspecialchars($_POST['location']);
    $phone_number = htmlspecialchars($_POST['phone_number']);

    // Save to MySQL database
    $stmt = $conn->prepare("INSERT INTO bookings (name, email, appointment_date, appointment_time, service, location, phone_number) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $email, $appointment_date, $appointment_time, $service, $location, $phone_number);
    $stmt->execute();
    $stmt->close();

    // Send confirmation email
    $mail = new PHPMailer(true);
    try {
        // Email config
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'youremail@gmail.com';         // your Gmail
        $mail->Password   = 'your-app-password';           // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('youremail@gmail.com', 'AirGo Aircon Cleaning');
        $mail->addAddress($email, $name);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'AirGo Booking Confirmation';
        $mail->Body    = "
            <h2>Booking Confirmed</h2>
            <p>Dear $name,</p>
            <p>Your aircon cleaning appointment is scheduled.</p>
            <p><strong>Service:</strong> $service</p>
            <p><strong>Date:</strong> $appointment_date</p>
            <p><strong>Time:</strong> $appointment_time</p>
            <p><strong>Location:</strong> $location</p>
            <p><strong>Contact:</strong> $phone_number</p>
            <br><p>Thank you,<br>AirGo Aircon Cleaning</p>
        ";
        $mail->send();
    } catch (Exception $e) {
        // Optionally log error: $mail->ErrorInfo
    }

    $conn->close();

    // Redirect to PayMongo payment link
    header("Location: https://pm.link/airgoairconcleaning/test/XRpcag8");
    exit();
} else {
    // Redirect if not POST
    header("Location: booking.php");
    exit();
}
?>
