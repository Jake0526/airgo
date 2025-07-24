<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['date'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Date parameter is required']);
    exit();
}

$date = $_GET['date'];
$user_id = $_SESSION['user_id'];

try {
    $conn = Database::getConnection();
    
    // Define time slots with 1 hour 40 minute intervals
    $slots = [];
    $start = strtotime("08:00");
    $end = strtotime("16:20");
    $interval = 100 * 60; // 1 hour 40 minutes
    
    // Get all bookings for the selected date
    $stmt = $conn->prepare("
        SELECT appointment_time, COUNT(*) as count 
        FROM bookings 
        WHERE appointment_date = ? 
        AND status != 'Cancelled'
        GROUP BY appointment_time
    ");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Create a map of booked slots
    $booked_slots = [];
    while ($row = $result->fetch_assoc()) {
        $booked_slots[$row['appointment_time']] = $row['count'];
    }
    
    // Maximum bookings per time slot
    $max_bookings_per_slot = 2;
    
    // Generate time slots
    while ($start <= $end) {
        $time = date("H:i:s", $start);
        $display_time = date("g:i A", $start);
        $booked = isset($booked_slots[$time]) ? $booked_slots[$time] : 0;
        $remaining = $max_bookings_per_slot - $booked;
        
        $slots[] = [
            'time' => $display_time,
            'available' => $remaining > 0,
            'remaining' => $remaining
        ];
        
        $start += $interval;
    }
    
    echo json_encode(['slots' => $slots]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
} 