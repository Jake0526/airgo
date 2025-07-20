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

            // Send email to employee if assigned
            if ($employee_id) {
                // Get employee details
                $emp_sql = "SELECT name, email FROM employees WHERE id = ?";
                $emp_stmt = $conn->prepare($emp_sql);
                $emp_stmt->bind_param("i", $employee_id);
                $emp_stmt->execute();
                $emp_result = $emp_stmt->get_result();
                $employee = $emp_result->fetch_assoc();
                $emp_stmt->close();

                // Get booking details
                $booking_sql = "SELECT b.*, CONCAT(u.fname, ' ', u.lname) as customer_name 
                              FROM bookings b 
                              LEFT JOIN user u ON b.user_id = u.id 
                              WHERE b.id = ?";
                $booking_stmt = $conn->prepare($booking_sql);
                $booking_stmt->bind_param("i", $booking_id);
                $booking_stmt->execute();
                $booking_result = $booking_stmt->get_result();
                $booking = $booking_result->fetch_assoc();
                $booking_stmt->close();

                if ($employee && $booking) {
                    require_once dirname(dirname(__FILE__)) . '/config/mailer.php';
                    try {
                        $mailer = Mailer::getInstance();
                        
                        // Create email body
                        $emailBody = "
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                            <div style='background-color: #07353f; padding: 20px; text-align: center;'>
                                <h1 style='color: #ffffff; margin: 0;'>AirGo Service Assignment</h1>
                            </div>
                            <div style='padding: 20px; background-color: #ffffff; border: 1px solid #e0e0e0;'>
                                <h2 style='color: #07353f;'>New Service Assignment</h2>
                                <p>Dear {$employee['name']},</p>
                                <p>You have been assigned to a new service booking. Here are the details:</p>
                                <div style='background-color: #f5f5f5; padding: 15px; margin: 20px 0;'>
                                    <p><strong>Customer:</strong> {$booking['customer_name']}</p>
                                    <p><strong>Service:</strong> {$booking['service']}</p>
                                    <p><strong>Location:</strong> {$booking['location']}</p>
                                    <p><strong>Date:</strong> {$booking['appointment_date']}</p>
                                    <p><strong>Time:</strong> " . date('g:i A', strtotime($booking['appointment_time'])) . "</p>
                                    <p><strong>Contact:</strong> {$booking['phone']}</p>
                                    " . ($booking['note'] ? "<p><strong>Note:</strong> {$booking['note']}</p>" : "") . "
                                </div>
                                <p>Please ensure you arrive on time and provide excellent service to our customer.</p>
                                <p>Best regards,<br>The AirGo Team</p>
                            </div>
                            <div style='text-align: center; padding: 20px; color: #666666; font-size: 12px;'>
                                <p>This is an automated message from AirGo Aircon Services.</p>
                            </div>
                        </div>";

                        if (!$mailer->sendEmail($employee['email'], 'New Service Assignment - AirGo', $emailBody)) {
                            error_log("Failed to send email to employee {$employee['name']} ({$employee['email']})");
                        }
                    } catch (Exception $e) {
                        error_log("Error sending email: " . $e->getMessage());
                    }
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