<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/database.php';
$conn = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['source'])) {
    $id = intval($_POST['id']);
    $source = $_POST['source'];

    // Validate source table
    if (!in_array($source, ['History', 'Cancelled'])) {
        exit('Invalid source.');
    }

    $table = $source === 'History' ? 'booking_history_customer' : 'cancel_booking';

    // Delete record
    $stmt = $conn->prepare("DELETE FROM $table WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: cancel_booking.php");
    exit();
}
?>
