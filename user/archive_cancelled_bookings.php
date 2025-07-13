<?php
require_once '../config/database.php';
$conn = Database::getConnection();

$sql = "SELECT * FROM bookings WHERE booking_date < CURDATE() AND status = 'Cancelled'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($booking = $result->fetch_assoc()) {
        $employee_id = $booking['employee_id'];

        // Fetch technician name
        $tech_stmt = $conn->prepare("SELECT name FROM employees WHERE id = ?");
        $tech_stmt->bind_param("i", $employee_id);
        $tech_stmt->execute();
        $tech_stmt->bind_result($technician_name);
        $tech_stmt->fetch();
        $tech_stmt->close();

        // Insert into history
        $insert = $conn->prepare("INSERT INTO booking_history_customer 
            (user_id, service_type, booking_date, booking_time, phone, technician_name, status, moved_at)
            VALUES (?, ?, ?, ?, ?, ?, 'Cancelled', NOW())");

        $insert->bind_param("isssss",
            $booking['user_id'],
            $booking['service'],
            $booking['appointment_date'],
            $booking['appointment_time'],
            $booking['phone'],
            $technician_name
        );
        $insert->execute();
        $insert->close();

        // Delete after archiving
        $delete = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        $delete->bind_param("i", $booking['id']);
        $delete->execute();
        $delete->close();
    }
}
$conn->close();
?>
