<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once '../config/database.php';

try {
    $conn = Database::getConnection();
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    if (!isset($_GET['employee_id'])) {
        throw new Exception("Employee ID is required");
    }

    $employee_id = mysqli_real_escape_string($conn, $_GET['employee_id']);
    
    // Build the query
    $sql = "SELECT b.*, CONCAT(u.fname, ' ', u.lname) as customer_name 
            FROM bookings b 
            LEFT JOIN user u ON b.user_id = u.id 
            WHERE b.employee_id = ?";
    
    $params = [$employee_id];
    $types = "i";

    // Add filters if provided
    if (isset($_GET['status']) && $_GET['status'] !== '') {
        $sql .= " AND b.status = ?";
        $params[] = $_GET['status'];
        $types .= "s";
    }

    if (isset($_GET['date_from']) && $_GET['date_from'] !== '') {
        $sql .= " AND DATE(b.appointment_date) >= ?";
        $params[] = $_GET['date_from'];
        $types .= "s";
    }

    if (isset($_GET['date_to']) && $_GET['date_to'] !== '') {
        $sql .= " AND DATE(b.appointment_date) <= ?";
        $params[] = $_GET['date_to'];
        $types .= "s";
    }

    $sql .= " ORDER BY b.appointment_date DESC, b.appointment_time DESC";

    // Prepare and execute the statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        throw new Exception("Failed to execute query: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $bookings = [];

    while ($row = $result->fetch_assoc()) {
        // Format the time to 12-hour format
        $row['appointment_time'] = date('g:i A', strtotime($row['appointment_time']));
        $bookings[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'bookings' => $bookings]);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
} 