<?php 
session_start();

require_once '../config/database.php';
$conn = Database::getConnection();

// Fetch all done/cancelled/completed/rejected bookings, no matter how old
$sql = "SELECT b.id, b.name, b.service, b.created_at, b.location, b.phone_number, 
            b.appointment_date, b.appointment_time, u.username, e.name AS employee_name, b.status
        FROM bookings b
        LEFT JOIN user u ON b.user_id = u.id
        LEFT JOIN employees e ON b.employee_id = e.id
        WHERE b.status IN ('done', 'cancelled', 'completed', 'rejected')
        ORDER BY b.appointment_date DESC";


$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AirGo Employee Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }

        .main-content h1 {
            margin-bottom: 20px;
            color: white;
            font-size: 22px;
        }

        table {
            width: 90%;
            background: var(--light);
            border-collapse: collapse;
            border-radius: 15px;
            box-shadow: 6px 6px 12px var(--shadow), -6px -6px 12px #ffffff;
            overflow: hidden;
            margin-bottom: 20px;
            margin-top: 30px;
            margin-left: 35px;
        }

        th, td {
            padding: 15px 25px;
            text-align: left;
            font-size: 13px;
        }

        th {
            background: var(--main);
            color: var(--light);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            background-color: #fff;
            color: #333;
            border-bottom: 1px solid #eee;
        }

        tr:hover td {
            background: #e6f7ff;
            transition: 0.3s ease;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
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
    <h2>AirGo Employee</h2>
    <a href="employee_dashboard.php">Dashboard</a>
    <a href="booking_history.php">History</a>
    <a href="employees_login.php">Logout</a>
</div>

<div class="main-content">
    <h1>Booking History</h1>
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
            
             <<tbody>
<?php
if ($result && $result->num_rows > 0) :
    while ($row = $result->fetch_assoc()) :
?>
    <tr>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['service']) ?></td>
        <td><?= htmlspecialchars($row['location']) ?></td>
        <td><?= htmlspecialchars($row['phone_number']) ?></td>
        <td><?= htmlspecialchars($row['appointment_date']) ?></td>
        <td><?= htmlspecialchars($row['appointment_time']) ?></td>
        <td><?= $row['employee_name'] ? htmlspecialchars($row['employee_name']) : 'Unassigned' ?></td>
        <td><?= htmlspecialchars($row['status']) ?></td>
    </tr>
<?php
    endwhile;
else :
?>
    <tr>
        <td colspan="8">No booking history found.</td>
    </tr>
<?php endif; ?>
</tbody>

    </table>
</div>

</body>
</html>
