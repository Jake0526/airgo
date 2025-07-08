<?php
// Start session and DB connection
session_start();
require 'db_conn.php'; // include your DB connection
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$booking_id = $_POST['booking_id'] ?? $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $new_date = $_POST['new_date'];
    $new_time = $_POST['new_time'];
    $reason = $_POST['reason'];

    // Insert request to reschedule_requests table (create this table if not exists)
    $stmt = $conn->prepare("INSERT INTO reschedule_requests (booking_id, user_id, new_date, new_time, reason, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("iisss", $booking_id, $_SESSION['user_id'], $new_date, $new_time, $reason);
    $stmt->execute();

    // Send email to admin/technician
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com'; // change this
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@example.com';
        $mail->Password = 'your_email_password';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('your_email@example.com', 'AirGo Notification');
        $mail->addAddress('admin@example.com'); // admin or technician

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Reschedule Request for Booking ID ' . $booking_id;
        $mail->Body = "
            <h3>New Reschedule Request</h3>
            <p><strong>Booking ID:</strong> $booking_id</p>
            <p><strong>New Date:</strong> $new_date</p>
            <p><strong>New Time:</strong> $new_time</p>
            <p><strong>Reason:</strong><br>" . nl2br(htmlspecialchars($reason)) . "</p>
        ";

        $mail->send();
        $msg = "Reschedule request sent successfully!";
    } catch (Exception $e) {
        $msg = "Request saved, but email could not be sent. Mailer Error: " . $mail->ErrorInfo;
    }

    header("Location: my_bookings.php?reschedule=success");
    exit();
}

// Fetch original booking details
if ($booking_id) {
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE booking_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();

    if (!$booking) {
        die("Invalid booking ID.");
    }
} else {
    die("Booking ID missing.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Request Reschedule - AirGo</title>
    <style>
        body {
            font-family: Arial;
            background: #f0f8ff;
            padding: 20px;
        }
        form {
            background: #fff;
            max-width: 500px;
            margin: auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }
        input, textarea, button {
            width: 100%;
            margin-bottom: 12px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background: #007bff;
            color: #fff;
            border: none;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>

<h2>Request to Reschedule</h2>

<form method="POST">
    <input type="hidden" name="booking_id" value="<?= $booking_id ?>">
    <label><strong>New Date:</strong></label>
    <input type="date" name="new_date" min="<?= date('Y-m-d') ?>" required>

    <label><strong>New Time:</strong></label>
    <input type="time" name="new_time" required>

    <label><strong>Reason for Reschedule:</strong></label>
    <textarea name="reason" rows="4" placeholder="Explain why you want to reschedule..." required></textarea>

    <button type="submit" name="submit">Submit Request</button>
</form>

</body>
</html>
