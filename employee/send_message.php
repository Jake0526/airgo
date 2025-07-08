<?php
session_start();
if (!isset($_POST['message']) || !isset($_SESSION['employee_id'])) {
    header("Location: chatbox.php");
    exit();
}

$sender_id = $_SESSION['employee_id'];
$sender_name = $_SESSION['employee_name'];
$message = $_POST['message'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "airgo";
$conn = new mysqli($servername, $username, $password, $dbname);

// For now, broadcast to all customers (or choose specific one later)
$sql = "INSERT INTO messages (sender_id, sender_name, message, receiver_role, timestamp) VALUES (?, ?, ?, 'customer', NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $sender_id, $sender_name, $message);
$stmt->execute();

header("Location: chatbox.php");
exit();
?>
