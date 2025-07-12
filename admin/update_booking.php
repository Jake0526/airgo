<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

include('../db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];

    try {
        $booking_id = $_POST['booking_id'];
        $service = $_POST['service'];
        $location = $_POST['location'];
        $phone = $_POST['phone'];
        $appointment_date = $_POST['appointment_date'];
        $appointment_time = $_POST['appointment_time'];
        $status = $_POST['status'];
        $employee_id = !empty($_POST['employee_id']) ? $_POST['employee_id'] : NULL;

        $sql = "UPDATE bookings SET 
                service = ?, 
                location = ?, 
                phone = ?, 
                appointment_date = ?, 
                appointment_time = ?, 
                status = ?, 
                employee_id = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ssssssii", 
            $service, 
            $location, 
            $phone, 
            $appointment_date, 
            $appointment_time, 
            $status, 
            $employee_id, 
            $booking_id
        );

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Booking updated successfully';
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $stmt->close();
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    $conn->close();
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Invalid request method']);
exit(); 