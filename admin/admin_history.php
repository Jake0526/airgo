<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

include 'db_connection.php';

// Move completed bookings to booking_history
$moveQuery = "INSERT INTO booking_history (user_id, service, booking_date, status)
              SELECT user_id, service, booking_date, status
              FROM bookings
              WHERE status = 'completed' AND booking_date < CURDATE()";

$deleteQuery = "DELETE FROM bookings
                WHERE status = 'completed' AND booking_date < CURDATE()";

mysqli_query($conn, $moveQuery);
mysqli_query($conn, $deleteQuery);

// Fetch booking history joined with users and employees info
$sql = "SELECT bh.*, u.username, e.name AS employee_name
        FROM booking_history bh
        LEFT JOIN users u ON bh.user_id = u.id
        LEFT JOIN employees e ON bh.employee_id = e.id
        ORDER BY bh.booking_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - Booking History</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #fafafa; }
        table { border-collapse: collapse; width: 100%; background: #fff; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #07353f; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        h2 { color: #07353f; }
    </style>
</head>
<body>

<h2>Booking History</h2>

<?php if ($result->num_rows > 0): ?>
<table>
    <thead>
        <tr>
            <th>User</th>
            <th>Service</th>
            <th>Booking Date</th>
            <th>Status</th>
            <th>Employee</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['username'] ?? 'Unknown') ?></td>
            <td><?= htmlspecialchars($row['service']) ?></td>
            <td><?= htmlspecialchars($row['booking_date']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td><?= htmlspecialchars($row['employee_name'] ?? 'Unassigned') ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
<p>No booking history found.</p>
<?php endif; ?>

</body>
</html>
