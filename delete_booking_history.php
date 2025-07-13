<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['source'])) {
    die("Invalid request.");
}

$id = intval($_GET['id']);
$source = strtolower($_GET['source']);

if ($source === 'cancelled') {
    $table = 'cancel_booking';
} elseif ($source === 'history') {
    $table = 'booking_history_customer';
} else {
    die("Invalid source.");
}

require_once 'config/database.php';
$conn = Database::getConnection();

$stmt = $conn->prepare("DELETE FROM $table WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $_SESSION['user_id']);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: cancel_booking.php");
exit();
?>
