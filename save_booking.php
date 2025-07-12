<?php
// Start output buffering
ob_start();

session_start();
include('db_connection.php');

// Clear any previous output and set headers
ob_end_clean();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$service = $_POST['service'] ?? '';
$appointment_date = $_POST['appointment_date'] ?? '';
// Convert 12-hour time format to 24-hour format
$appointment_time = $_POST['appointment_time'];
if (preg_match('/(\d{1,2}):(\d{2})\s*(AM|PM)/i', $appointment_time, $matches)) {
    $hour = intval($matches[1]);
    $minute = $matches[2];
    $meridiem = strtoupper($matches[3]);
    
    if ($meridiem === 'PM' && $hour < 12) {
        $hour += 12;
    } elseif ($meridiem === 'AM' && $hour === 12) {
        $hour = 0;
    }
    
    $appointment_time = sprintf('%02d:%02d:00', $hour, $minute);
}
$contact = $_POST['contact'] ?? '';
$location = $_POST['location'] ?? '';
$price = $_POST['price'] ?? '';
$note = $_POST['note'] ?? '';

// Validate required fields
if (empty($service) || empty($appointment_date) || empty($appointment_time) || 
    empty($contact) || empty($location) || empty($price)) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
    exit();
}

// Validate location format
$location_parts = explode(',', $location);
if (count($location_parts) < 4) {
    echo json_encode(['success' => false, 'message' => 'Invalid address format. Please provide complete address details.']);
    exit();
}

try {
    // Prepare the SQL statement
    $sql = "INSERT INTO bookings (user_id, service, price, appointment_date, appointment_time, location, note, status, phone) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsssss", $user_id, $service, $price, $appointment_date, $appointment_time, $location, $note, $contact);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        echo json_encode([
            'success' => true,
            'message' => 'Booking saved successfully'
        ]);
        exit();
    } else {
        throw new Exception("Failed to save booking");
    }
} catch (Exception $e) {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
    echo json_encode([
        'success' => false, 
        'message' => 'Error saving booking: ' . $e->getMessage()
    ]);
    exit();
}