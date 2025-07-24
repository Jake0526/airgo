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

// Check if all required fields are present
$required_fields = ['booking_id', 'service', 'location'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit();
    }
}

$booking_id = intval($_POST['booking_id']);
$service = $_POST['service'];
$location = $_POST['location'];

try {
    // First verify that this booking belongs to the user
    $check_stmt = $conn->prepare("SELECT id FROM bookings WHERE id = ? AND user_id = ? AND status = 'Pending'");
    $check_stmt->bind_param("ii", $booking_id, $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Booking not found or not authorized to edit');
    }
    
    // Get the service price
    $services_prices = [
        'Aircon Check-up' => 500,
        'Aircon Relocation' => 3500,
        'Aircon Repair' => 1500,
        'Aircon cleaning (window type)' => 800,
        'Window type (inverter)' => 2500,
        'Window type (U shape)' => 2300,
        'Split type' => 2800,
        'Floormounted' => 3000,
        'Cassette' => 3200,
        'Capacitor Thermostat' => 1200
    ];
    
    $price = $services_prices[$service] ?? null;
    if ($price === null) {
        throw new Exception('Invalid service selected');
    }

    // Update the booking
    $update_stmt = $conn->prepare("
        UPDATE bookings 
        SET service = ?, 
            location = ?, 
            price = ?
        WHERE id = ? AND user_id = ? AND status = 'Pending'
    ");
    
    $update_stmt->bind_param("ssdii", $service, $location, $price, $booking_id, $user_id);
    
    if (!$update_stmt->execute()) {
        throw new Exception('Failed to update booking');
    }
    
    if ($update_stmt->affected_rows === 0) {
        throw new Exception('No changes were made to the booking');
    }
    
    echo json_encode(['success' => true, 'message' => 'Booking updated successfully']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
