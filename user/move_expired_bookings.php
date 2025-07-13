<?php
require_once '../config/database.php';
$conn = Database::getConnection();

// Get all bookings with past date/time
$query = "SELECT * FROM bookings WHERE CONCAT(appointment_date, ' ', appointment_time) < NOW()";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    // Insert into booking_history
    $stmt = $conn->prepare("INSERT INTO booking_history (name, email, appointment_date, appointment_time, service, location, phone_number, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $status = 'Expired';
    $stmt->bind_param("ssssssss", $row['name'], $row['email'], $row['appointment_date'], $row['appointment_time'], $row['service'], $row['location'], $row['phone_number'], $status);
    $stmt->execute();
    $stmt->close();

    // Delete from bookings
    $delete = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $delete->bind_param("i", $row['id']);
    $delete->execute();
    $delete->close();
}

$conn->close();

echo "Expired bookings moved to history.";
?>
