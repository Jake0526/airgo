<?php 
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

require_once('config/database.php');
$conn = Database::getConnection();

$booking_id = intval($_POST['booking_id']);
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service = $_POST['service'];
    $location = $_POST['location'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];

    // Verify the booking belongs to the user
    $check = $conn->prepare("SELECT id FROM bookings WHERE id = ? AND user_id = ? AND status = 'Pending'");
    $check->bind_param("ii", $booking_id, $user_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Booking not found or cannot be edited']);
        exit();
    }
    $check->close();

    // Update the booking
    $stmt = $conn->prepare("UPDATE bookings SET service = ?, location = ?, appointment_date = ?, appointment_time = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssssii", $service, $location, $appointment_date, $appointment_time, $booking_id, $user_id);
    
    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Booking updated successfully']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to update booking']);
    }
    $stmt->close();
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

// No need to manually close the connection as the Database class handles it
?>
