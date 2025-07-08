<?php  
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "airgo";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Move completed/cancelled/rejected/done bookings to history
$statusesToMove = ['Completed', 'Cancelled'];

$placeholders = implode(',', array_fill(0, count($statusesToMove), '?'));
$sql = "DELETE FROM bookings WHERE status IN ($placeholders)";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

// Bind all strings
$types = str_repeat('s', count($statusesToMove));
$stmt->bind_param($types, ...$statusesToMove);
$stmt->execute();

$stmt->close();

// Determine selected tab
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'all';

// Build WHERE clause
$where = "";
switch ($tab) {
    case 'pending':
        $where = "WHERE LOWER(b.status) = 'pending'";
        break;
    case 'reschedule':
        $where = "WHERE LOWER(b.status) = 'reschedule requested'";
        break;
    case 'approved':
        $where = "WHERE LOWER(b.status) = 'approved'";
        break;
    case 'all':
    default:
        $where = "";
        break;
}

// Fetch bookings
$sql = "SELECT b.id, u.username, b.service, b.status, b.created_at, b.location, b.contact, 
               b.appointment_date, b.appointment_time, b.employee_id, u.username, e.name AS employee_name
        FROM bookings b
        LEFT JOIN user u ON b.user_id = u.id
        LEFT JOIN employees e ON b.employee_id = e.id
        $where
        ORDER BY b.appointment_date, b.appointment_time";
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>AirGo Admin Dashboard</title>
<style>
    :root {
        --bg: #4c7273;
        --main: #07353f;
        --accent: #CACBBB;
        --light: #ffffff;
        --shadow: rgba(0, 0, 0, 0.1);
    }
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body {
        font-family: 'Segoe UI', sans-serif;
        background: var(--bg);
        display: flex;
    }
    .sidebar {
        width: 250px;
        height: 100vh;
        background: var(--main);
        color: var(--light);
        padding: 30px 20px;
        position: fixed;
        display: flex;
        flex-direction: column;
    }
    .sidebar h2 {
        font-size: 24px;
        margin-bottom: 30px;
        text-align: center;
    }
    .sidebar a {
        color: var(--light);
        text-decoration: none;
        margin: 12px 0;
        padding: 10px 15px;
        border-radius: 10px;
        transition: background 0.3s ease;
    }
    .sidebar a:hover {
        background: var(--accent);
        color: #000;
    }
    .main {
        margin-left: 250px;
        padding: 20px;
        width: calc(100% - 250px);
        color: black;
    }
    .main h1 {
        margin-bottom: 20px;
        color: white;
        font-size: 20px;
    }
    .tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .tab-link {
        background: var(--light);
        color: var(--main);
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
        transition: background 0.3s ease;
    }
    .tab-link.active, .tab-link:hover {
        background: var(--accent);
        color: #000;
    }
    table {
        width: 100%;
        background: var(--light);
        border-radius: 10px;
        box-shadow: 6px 6px 12px var(--shadow), -6px -6px 12px #ffffff;
        border-collapse: collapse;
        overflow: hidden;
    }
    th, td {
        padding: 14px 20px;
        text-align: right;
        font-size: 13px;
    }
    th {
        background: var(--main);
        color: var(--light);
    }
    tr:nth-child(even) {
        background: #f9f9f9;
    }
    tbody tr:hover {
        transform: translateY(-3px);
        background-color: #e0f7f7;
    }
    .button {
        background: var(--main);
        color: var(--light);
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        transition: background 0.3s ease;
    }
    .button:hover {
        background: #05525c;
    }
    @media (max-width: 768px) {
        .main {
            margin-left: 0;
            width: 100%;
        }
        .sidebar {
            display: none;
        }
    }
</style>
</head>
<body>
<div class="sidebar">
    <h2>AirGo Admin</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="admin_bookings.php">Bookings</a>
    <a href="admin_employees.php">Employees</a>
    <a href="booking_history.php">Booking History</a>
    <a href="login.php">Logout</a>
</div>

<div class="main">
    <h1>Bookings</h1>

    <div class="tabs">
        <a href="?tab=all" class="tab-link <?= ($tab == 'all') ? 'active' : '' ?>">All Bookings</a>
        <a href="?tab=pending" class="tab-link <?= ($tab == 'pending') ? 'active' : '' ?>">Pending</a>
        <a href="?tab=reschedule" class="tab-link <?= ($tab == 'reschedule') ? 'active' : '' ?>">Reschedule Requests</a>
        <a href="?tab=approved" class="tab-link <?= ($tab == 'approved') ? 'active' : '' ?>">Approved</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Service</th>
                <th>Location</th>
                <th>Contact</th>
                <th>Appointment Date</th>
                <th>Appointment Time</th>
                <th>Status</th>
                <th>Technician</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['service']) ?></td>
                    <td><?= htmlspecialchars($row['location']) ?></td>
                    <td><?= htmlspecialchars($row['contact']) ?></td>
                    <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                    <td><?= htmlspecialchars(date("g:i A", strtotime($row['appointment_time']))) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td>
                        <?php
                        if (in_array(strtolower($row['status']), ['rejected', 'cancelled'])) {
                            echo 'N/A';
                        } else {
                            echo $row['employee_name'] ? htmlspecialchars($row['employee_name']) : 'To be assigned';
                        }
                        ?>
                    </td>
                    <td>
                        <a href="edit_booking.php?booking_id=<?= $row['id'] ?>" class="button">Update</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="9" style="text-align:center;">No bookings found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
