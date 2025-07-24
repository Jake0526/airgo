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

    // Verify the booking belongs to the user and check reschedule attempts
    $check_stmt = $conn->prepare("SELECT id, reschedule_attempt FROM bookings WHERE id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $booking_id, $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $booking = $result->fetch_assoc();

    if (!$booking) {
        throw new Exception('Booking not found or unauthorized');
    }

    // Debug the current reschedule_attempt value
    error_log("Current reschedule_attempt value: " . var_export($booking['reschedule_attempt'], true));

    // Check if user has already used their reschedule attempt (treat NULL as 0)
    $current_attempts = intval($booking['reschedule_attempt'] ?? 0);
    if ($current_attempts > 0) {
        throw new Exception('You have already used your reschedule attempt for this booking');
    }

    // First, let's check if the reschedule_attempt column exists
    $check_column = $conn->query("SHOW COLUMNS FROM bookings LIKE 'reschedule_attempt'");
    if ($check_column->num_rows === 0) {
        // Add the column if it doesn't exist
        $conn->query("ALTER TABLE bookings ADD COLUMN reschedule_attempt INT DEFAULT 0 NOT NULL");
        error_log("Added reschedule_attempt column to bookings table");
    }
    
    // Update any NULL values to 0 for consistency
    $conn->query("UPDATE bookings SET reschedule_attempt = 0 WHERE reschedule_attempt IS NULL");
    error_log("Updated NULL reschedule_attempt values to 0");

    // Update the booking with new date, time, status and increment reschedule attempt
    $update_stmt = $conn->prepare("
        UPDATE bookings 
        SET status = 'Rescheduled',
            appointment_date = ?,
            appointment_time = ?,
            reschedule_attempt = COALESCE(reschedule_attempt, 0) + 1
        WHERE id = ? AND user_id = ?
    ");
    $update_stmt->bind_param("ssii", $new_date, $new_time, $booking_id, $user_id);
    
    if (!$update_stmt->execute()) {
        error_log("Reschedule update failed: " . $update_stmt->error);
        throw new Exception('Failed to update booking: ' . $update_stmt->error);
    }

    // Verify the update worked
    $verify_stmt = $conn->prepare("SELECT reschedule_attempt FROM bookings WHERE id = ?");
    $verify_stmt->bind_param("i", $booking_id);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    $updated_booking = $verify_result->fetch_assoc();
    
    error_log("Reschedule attempt after update: " . $updated_booking['reschedule_attempt']);

    echo json_encode(['success' => true, 'message' => 'Booking rescheduled successfully']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
