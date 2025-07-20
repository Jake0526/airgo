<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check if required parameters are provided
if (!isset($_POST['booking_id']) || !isset($_POST['new_date']) || !isset($_POST['new_time'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

$booking_id = intval($_POST['booking_id']);
$new_date = $_POST['new_date'];
$new_time = $_POST['new_time'];
$user_id = $_SESSION['user_id'];

try {
    $conn = Database::getConnection();

    // Verify the booking belongs to the user
    $check_stmt = $conn->prepare("SELECT id FROM bookings WHERE id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $booking_id, $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Booking not found or unauthorized');
    }

    // Update the booking with new date, time and status
    $update_stmt = $conn->prepare("
        UPDATE bookings 
        SET status = 'Rescheduled',
            appointment_date = ?,
            appointment_time = ?
        WHERE id = ? AND user_id = ?
    ");
    $update_stmt->bind_param("ssii", $new_date, $new_time, $booking_id, $user_id);
    
    if (!$update_stmt->execute()) {
        throw new Exception('Failed to update booking');
    }

    echo json_encode(['success' => true, 'message' => 'Booking rescheduled successfully']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
