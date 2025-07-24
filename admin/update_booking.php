<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once '../config/database.php';

// Prevent any output before headers
ob_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];

    try {
        // Get database connection
        $conn = Database::getConnection();

        $booking_id = $_POST['booking_id'];
        $service = $_POST['service'];
        $location = $_POST['location'];
        $phone = $_POST['phone'];
        $appointment_date = $_POST['appointment_date'];
        $appointment_time = $_POST['appointment_time'];
        $status = $_POST['status'];
        $employee_id = !empty($_POST['employee_id']) ? $_POST['employee_id'] : NULL;
        
        // Handle file upload for completed status
        $payment_proof_path = null;
        if ($status === 'Completed' && isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['payment_proof'];
            $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array($file_extension, $allowed_extensions)) {
                throw new Exception('Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.');
            }
            
            // Create upload directory if it doesn't exist
            $upload_dir = '../uploads/payment_proofs/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Generate unique filename
            $new_filename = uniqid('payment_') . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                // Store the relative path in the database
                $payment_proof_path = 'uploads/payment_proofs/' . $new_filename;
                
                // Add debug information to the response
                $response['debug'] = [
                    'upload_dir' => $upload_dir,
                    'new_filename' => $new_filename,
                    'upload_path' => $upload_path,
                    'payment_proof_path' => $payment_proof_path
                ];
            } else {
                throw new Exception('Failed to upload file.');
            }
        }

        // Get current booking status and payment proof
        $check_stmt = $conn->prepare("SELECT status, payment_proof FROM bookings WHERE id = ?");
        $check_stmt->bind_param("i", $booking_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $current_booking = $result->fetch_assoc();
        $check_stmt->close();

        // Keep existing payment proof if no new file is uploaded
        if (!$payment_proof_path && $status === 'Completed') {
            $payment_proof_path = $current_booking['payment_proof'];
        }

        // Update booking without affecting reschedule_attempt
        $update_sql = "
            UPDATE bookings 
            SET service = ?, 
                location = ?, 
                phone = ?, 
                appointment_date = ?, 
                appointment_time = ?, 
                status = ?, 
                employee_id = ?,
                payment_proof = ?
            WHERE id = ?
        ";

        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssssssssi", 
            $service, 
            $location, 
            $phone, 
            $appointment_date, 
            $appointment_time, 
            $status, 
            $employee_id,
            $payment_proof_path,
            $booking_id
        );

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Booking updated successfully';
            if ($payment_proof_path) {
                $response['payment_proof'] = $payment_proof_path;
            }
        } else {
            throw new Exception('Failed to update booking: ' . $stmt->error);
        }

    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    // Clear output buffer and send JSON response
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Clear any output buffers before sending response
while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Invalid request method']);
exit(); 