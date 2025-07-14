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

        // Handle file upload
        $payment_proof_path = null;
        if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['payment_proof'];
            $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            // Validate file type
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($file_extension, $allowed_types)) {
                throw new Exception('Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.');
            }
            
            // Validate file size (max 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                throw new Exception('File is too large. Maximum size is 5MB.');
            }
            
            // Generate unique filename
            $new_filename = uniqid('payment_') . '.' . $file_extension;
            
            // Get the absolute path to the upload directory
            $base_dir = dirname(dirname(__FILE__));
            $upload_dir = $base_dir . '/uploads/payment_proofs/';
            $target_path = $upload_dir . $new_filename;
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                if (!mkdir($upload_dir, 0775, true)) {
                    throw new Exception('Failed to create upload directory.');
                }
                chmod($upload_dir, 0775);
            }
            
            // Debug information
            error_log("Upload path: " . $target_path);
            error_log("Directory exists: " . (file_exists($upload_dir) ? 'yes' : 'no'));
            error_log("Directory writable: " . (is_writable($upload_dir) ? 'yes' : 'no'));
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                chmod($target_path, 0664); // Set proper file permissions
                // Store the path relative to the project root
                $payment_proof_path = 'uploads/payment_proofs/' . $new_filename;
            } else {
                $upload_error = error_get_last();
                throw new Exception('Failed to upload file. Error: ' . ($upload_error['message'] ?? 'Unknown error'));
            }
        }

        // First, check if there's an existing payment proof
        $check_sql = "SELECT payment_proof FROM bookings WHERE id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $booking_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $existing_proof = $check_result->fetch_assoc();
        $check_stmt->close();

        // Prepare SQL based on whether we have a new payment proof
        if ($payment_proof_path !== null) {
            $sql = "UPDATE bookings SET 
                    service = ?, 
                    location = ?, 
                    phone = ?, 
                    appointment_date = ?, 
                    appointment_time = ?, 
                    status = ?, 
                    employee_id = ?,
                    payment_proof = ?
                    WHERE id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssiss", 
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
        } else {
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
        }

        if ($stmt->execute()) {
            // If upload successful and there was an existing file, delete the old one
            if ($payment_proof_path !== null && !empty($existing_proof['payment_proof'])) {
                $old_file = dirname(dirname(__FILE__)) . '/' . $existing_proof['payment_proof'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
            
            $response['success'] = true;
            $response['message'] = 'Booking updated successfully';
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $stmt->close();
    } catch (Exception $e) {
        error_log("Error in update_booking.php: " . $e->getMessage());
        $response['message'] = $e->getMessage();
    }
    
    // Clear any output buffers before sending response
    while (ob_get_level()) {
        ob_end_clean();
    }
    
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