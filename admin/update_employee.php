<?php
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['admin_logged_in'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Include the database connection
include('../db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];

    // Get and sanitize input
    $id = intval($_POST['employee_id']);
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $position = $conn->real_escape_string(trim($_POST['position']));
    $hire_date = $conn->real_escape_string(trim($_POST['hire_date']));
    $status = $conn->real_escape_string(trim($_POST['status']));

    // Validate input
    if (empty($name) || empty($email) || empty($position) || empty($hire_date) || empty($status)) {
        $response['message'] = 'All fields are required';
    } else {
        // Update the employee
        $sql = "UPDATE employees SET 
                name = ?, 
                email = ?, 
                position = ?, 
                hire_date = ?, 
                status = ? 
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssssi", $name, $email, $position, $hire_date, $status, $id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Employee updated successfully';
            } else {
                $response['message'] = 'Error updating employee: ' . $stmt->error;
            }
            
            $stmt->close();
        } else {
            $response['message'] = 'Error preparing statement: ' . $conn->error;
        }
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// If not POST request
header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Invalid request method']);
exit(); 