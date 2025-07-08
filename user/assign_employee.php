<?php
session_start();

// Check admin login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = intval($_POST['booking_id'] ?? 0);
    $employee_id = intval($_POST['employee_id'] ?? 0);

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "airgo";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Update booking with employee_id
    $stmt = $conn->prepare("UPDATE bookings SET employee_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $employee_id, $booking_id);
    $stmt->execute();
    $stmt->close();

    $conn->close();

    // Redirect back to admin bookings page
    header("Location: admin_bookings.php");
    exit();
} else {
    header("Location: admin_bookings.php");
    exit();
}
?>
