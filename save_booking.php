<?php
session_start();
include('db_connection.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$service = $_POST['service'] ?? '';
$appointment_date = $_POST['appointment_date'] ?? '';
$appointment_time = $_POST['appointment_time'] ?? '';
$contact = $_POST['contact'] ?? '';
$location = $_POST['location'] ?? '';
$price = $_POST['service_price'] ?? '';
$note = $_POST['note'] ?? '';

// Validate required fields
if (empty($service) || empty($appointment_date) || empty($appointment_time) || 
    empty($contact) || empty($location) || empty($price)) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
    exit();
}

try {
    // Insert into bookings table
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, service, appointment_date, appointment_time, phone, location, price, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssds", $user_id, $service, $appointment_date, $appointment_time, $contact, $location, $price, $note);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Booking saved successfully'
        ]);
    } else {
        throw new Exception("Failed to save booking");
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error saving booking: ' . $e->getMessage()
    ]);
}

$stmt->close();
$conn->close(); 