<?php
session_start();

if (!isset($_SESSION['employee_logged_in']) || !isset($_SESSION['employee_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

require_once '../config/database.php';
$conn = Database::getConnection();

$response = ['success' => false, 'message' => 'Unknown error occurred'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id']) && isset($_FILES['payment_proof'])) {
    $booking_id = (int)$_POST['booking_id'];
    $employee_id = $_SESSION['employee_id'];

    // Verify the booking belongs to this employee
    $stmt = $conn->prepare("SELECT id FROM bookings WHERE id = ? AND employee_id = ?");
    $stmt->bind_param("ii", $booking_id, $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $response = ['success' => false, 'message' => 'Invalid booking or unauthorized access'];
    } else {
        $file = $_FILES['payment_proof'];
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_error = $file['error'];

        // Check for upload errors
        if ($file_error !== UPLOAD_ERR_OK) {
            $response = ['success' => false, 'message' => 'Error uploading file'];
        } else {
            // Get file extension
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Allowed file types
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array($file_ext, $allowed)) {
                $response = ['success' => false, 'message' => 'Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.'];
            } else {
                // Create upload directory if it doesn't exist
                $upload_dir = '../uploads/payment_proofs/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                // Generate unique filename
                $new_filename = uniqid('payment_') . '.' . $file_ext;
                $upload_path = $upload_dir . $new_filename;
                $db_path = 'uploads/payment_proofs/' . $new_filename;

                if (move_uploaded_file($file_tmp, $upload_path)) {
                    // Update database with new file path
                    $update = $conn->prepare("UPDATE bookings SET payment_proof = ? WHERE id = ?");
                    $update->bind_param("si", $db_path, $booking_id);
                    
                    if ($update->execute()) {
                        $response = ['success' => true, 'message' => 'Payment proof uploaded successfully'];
                    } else {
                        $response = ['success' => false, 'message' => 'Error updating database'];
                    }
                    $update->close();
                } else {
                    $response = ['success' => false, 'message' => 'Error moving uploaded file'];
                }
            }
        }
    }
    $stmt->close();
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($response); 