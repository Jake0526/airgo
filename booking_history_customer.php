<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$host = 'localhost';
$db = 'airgo';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get booking_history_customer (only Done, Complete, Cancelled, or Rejected)
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT b.*, e.name AS technician
    FROM bookings b
    LEFT JOIN employees e ON b.employee_id = e.id
    WHERE b.user_id = ?
      AND b.status IN ('Done', 'Complete', 'Cancelled', 'Rejected')
    ORDER BY b.appointment_date DESC, b.appointment_time DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Save to booking_history_customer if not already saved
foreach ($bookings as $b) {
    $checkStmt = $conn->prepare("
        SELECT id FROM booking_history_customer
        WHERE user_id = ? AND booking_date = ? AND booking_time = ?
    ");
    $checkStmt->bind_param("iss", $user_id, $b['appointment_date'], $b['appointment_time']);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows === 0) {
        $insertStmt = $conn->prepare("
            INSERT INTO booking_history_customer 
            (user_id, service_type, booking_date, booking_time, technician_name, phone, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $insertStmt->bind_param(
            "issssss",
            $user_id,
            $booking['service'],
            $booking['appointment_date'],
            $booking['appointment_time'],
            $booking['technician'],
            $booking['phone_number'],
            $booking['status']
        );
        $insertStmt->execute();
        $insertStmt->close();
    }

    $checkStmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking History - AirGo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color:  #d0f0ff;
            font-family: 'Segoe UI', sans-serif;
        }

        .custom-container {
            padding: 20px;
            padding-left: 3in;
            padding-right: 3in;
        }

        h2 {
            color: #07353f;
            font-weight: bold;
            margin-bottom: 25px;
        }

        .btn-back {
            background-color: #07353f;
            color: white;
            border: none;
            margin-bottom: 20px;
            padding: 5px 10px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-back:hover {
            background-color: #055665;
            color: #fff;
        }

        table {
            width: 100%;
            background-color: white;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(7, 53, 63, 0.2);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }

        th {
            background-color: #07353f;
            color: white;
        }

        tr:hover {
            background-color: #e0f7ff;
        }

        .text-muted {
            color: #666 !important;
        }
    </style>
</head>
<body>
<div class="custom-container">
    <a href="book-now.php" class="btn-back">&larr; Back to Bookings</a>
    <h2>Booking History</h2>

    <?php if (!empty($bookings)): ?>
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Service</th>
                <th>Location</th>
                <th>Phone</th>
                <th>Appointment Date</th>
                <th>Appointment Time</th>
                <th>Technician</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($bookings as $b): ?>
                <tr>
                    <td><?= htmlspecialchars($b['name']) ?></td>
                    <td><?= htmlspecialchars($b['service']) ?></td>
                    <td><?= htmlspecialchars($b['location']) ?></td>
                    <td><?= htmlspecialchars($b['phone_number']) ?></td>
                    <td><?= htmlspecialchars($b['appointment_date']) ?></td>
                    <td><?= date("g:i A", strtotime($b['appointment_time'])) ?></td>
                    <td><?= htmlspecialchars($b['technician']) ?></td>
                    <td><?= htmlspecialchars($b['status']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-muted">No booking history found.</p>
    <?php endif; ?>
</div>
</body>
</html>
