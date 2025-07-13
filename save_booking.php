<?php
// Prevent any output before our JSON response
ob_start();

session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

try {
    // Get database connection
    $conn = Database::getConnection();

    // Validate required fields
    $required_fields = [
        'service',
        'appointment_date',
        'appointment_time',
        'phone',
        'location',
        'price'
    ];

    $missing_fields = [];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }

    if (!empty($missing_fields)) {
        throw new Exception('Missing required fields: ' . implode(', ', $missing_fields));
    }

    // Get form data
    $user_id = $_SESSION['user_id'];
    $service = $_POST['service'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    
    // Convert time from "11:20 AM" to "HH:mm:ss" format
    $time_obj = DateTime::createFromFormat('g:i A', $appointment_time);
    if (!$time_obj) {
        throw new Exception('Invalid time format');
    }
    $appointment_time = $time_obj->format('H:i:s');
    
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $note = $_POST['note'] ?? '';
    $status = 'Pending';
    $created_at = date('Y-m-d H:i:s');

    // Check if the time slot is still available
    $check_sql = "SELECT COUNT(*) as count FROM bookings WHERE appointment_date = ? AND appointment_time = ? AND status != 'Cancelled'";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $appointment_date, $appointment_time);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        throw new Exception('This time slot is no longer available. Please select another time.');
    }

    // Insert the booking
    $sql = "INSERT INTO bookings (user_id, service, appointment_date, appointment_time, phone, location, price, note, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssdsss", 
        $user_id,
        $service,
        $appointment_date,
        $appointment_time,
        $phone, // This will be stored in the 'phone' column
        $location,
        $price,
        $note,
        $status,
        $created_at
    );

    if ($stmt->execute()) {
        // Clear any buffered output
        ob_clean();
        
        // Send success response
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Booking saved successfully']);
    } else {
        throw new Exception('Failed to save booking: ' . $stmt->error);
    }

} catch (Exception $e) {
    // Clear any buffered output
    ob_clean();
    
    // Send error response
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Close the database connection
if (isset($conn)) {
    $conn->close();
}