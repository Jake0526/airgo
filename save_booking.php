<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = Database::getConnection();

// Check if user already has a booking for this date
$check_stmt = $conn->prepare("
    SELECT COUNT(*) as booking_count 
    FROM bookings 
    WHERE user_id = ? 
    AND appointment_date = ? 
    AND status != 'Cancelled'
");

$appointment_date = $_POST['appointment_date'];
$check_stmt->bind_param("is", $user_id, $appointment_date);
$check_stmt->execute();
$result = $check_stmt->get_result();
$row = $result->fetch_assoc();

if ($row['booking_count'] > 0) {
    echo json_encode([
        'success' => false, 
        'message' => 'You already have a booking scheduled for this date. Please select a different date.'
    ]);
    exit();
}

// Convert time from "2:40 PM" format to "HH:mm:ss"
$appointment_time = $_POST['appointment_time'];
try {
    $time_obj = DateTime::createFromFormat('g:i A', $appointment_time);
    if (!$time_obj) {
        throw new Exception('Invalid time format');
    }
    $appointment_time = $time_obj->format('H:i:s');
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid time format provided'
    ]);
    exit();
}

// If no existing booking, proceed with saving the new booking
$stmt = $conn->prepare("
    INSERT INTO bookings (
        user_id, service, appointment_date, appointment_time, 
        phone, location, price, note, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')
");

$service = $_POST['service'];
$phone = $_POST['phone'];
$location = $_POST['location'];
$price = $_POST['price'];
$note = $_POST['note'] ?? '';

$stmt->bind_param(
    "isssssds",
    $user_id, $service, $appointment_date, $appointment_time,
    $phone, $location, $price, $note
);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to save booking: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>