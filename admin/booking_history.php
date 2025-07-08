<?php
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'airgo');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Archive past completed, done, rejected, or cancelled bookings
$archiveSQL = "
    INSERT IGNORE INTO booking_history (
        user_id, full_name, email, service, location, appointment_date, appointment_time,
        phone_number, technician, status
    )
    SELECT 
        user_id, full_name, email, service, location, appointment_date, appointment_time,
        phone_number, 
        COALESCE(NULLIF(technician, ''), 'Unassigned'),
        status
    FROM bookings
    WHERE status IN ('completed', 'done', 'rejected', 'cancelled')
      AND appointment_date < CURDATE()
";

if (!$conn->query($archiveSQL)) {
    die("Archive Error: " . $conn->error);
}

// Delete old bookings after archiving
$deleteSQL = "
    DELETE FROM bookings
    WHERE status IN ('completed', 'done', 'rejected', 'cancelled')
      AND appointment_date < CURDATE()
";

if (!$conn->query($deleteSQL)) {
    die("Delete Error: " . $conn->error);
}

// Search logic
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT * FROM booking_history";

if ($search !== '') {
    $search = $conn->real_escape_string($search);
    $sql .= " WHERE full_name LIKE '%$search%'
              OR email LIKE '%$search%'
              OR service LIKE '%$search%'
              OR location LIKE '%$search%'
              OR phone_number LIKE '%$search%'
              OR technician LIKE '%$search%'
              OR status LIKE '%$search%'";
}

$sql .= " ORDER BY appointment_date DESC, appointment_time ASC";
$result = $conn->query($sql);

// Handle possible query error
if (!$result) {
    die("Query Error: " . $conn->error);
}

$history = $result->fetch_all(MYSQLI_ASSOC);

// Export to CSV if requested
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filefull_name=booking_history.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['full_name', 'Email', 'Service', 'Location', 'Appointment Date', 'Appointment Time', 'Phone', 'Technician', 'Status']);

    foreach ($history as $row) {
        $formattedTime = date("g:i A", strtotime($row['appointment_time']));
        fputcsv($output, [
            $row['full_name'], $row['email'], $row['service'], $row['location'],
            $row['appointment_date'], $formattedTime, $row['phone_number'],
            $row['technician'], $row['status']
        ]);
    }

    fclose($output);
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AirGo Admin - Booking History</title>
    <meta full_name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --bg: #4c7273;
            --main: #07353f;
            --accent: #CACBBB;
            --light: #ffffff;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: var(--bg);
            display: flex;
        }
        .sidebar {
            width: 250px; height: 100vh;
            background: var(--main);
            color: var(--light);
            padding: 30px 20px;
            position: fixed;
            display: flex;
            flex-direction: column;
        }
        .sidebar h2 {
            font-size: 24px; margin-bottom: 30px; text-align: center;
        }
        .sidebar a {
            color: var(--light); text-decoration: none;
            margin: 12px 0; padding: 10px 15px;
            border-radius: 10px;
            transition: background 0.3s ease;
        }
        .sidebar a:hover {
            background: var(--accent); color: #000;
        }
        .main {
            margin-left: 250px;
            padding: 40px;
            width: calc(100% - 250px);
        }
        .header {
            font-size: 2em;
            margin-bottom: 30px;
            color: white;
        }
        .search-form {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
        }
        .search-form input {
            width: 200px;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        .search-form button {
            background-color: var(--main);
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .search-form button:hover {
            background-color: #0b4c58;
        }
        .history-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            margin-bottom: 20px;
        }
        .date-header {
            background: var(--main);
            color: white;
            padding: 12px 20px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #ffffffc9;
            font-weight: bold;
            color: #07353f;
        }
        tr:hover {
            background-color: #e0f0f8;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            text-transform: capitalize;
            color: white;
        }
        .status-badge.completed,
        .status-badge.done {
            background-color: #28a745;
        }
        .status-badge.rejected {
            background-color: #dc3545;
        }
        .status-badge.cancelled {
            background-color: #e3342f;
        }
        .status-badge.pending {
            background-color: #ffc107;
            color: black;
        }
        .status-badge.in-progress {
            background-color: #007bff;
        }
        .no-data {
            background-color: #fffbe6;
            padding: 15px;
            border-left: 5px solid #ffcd39;
            margin-top: 20px;
            border-radius: 10px;
        }
        @media (max-width: 768px) {
            .main {
                margin-left: 0;
                padding: 20px;
                width: 100%;
            }
            .sidebar {
                display: none;
            }
            th, td {
                font-size: 14px;
                padding: 10px;
            }
            .search-form {
                justify-content: center;
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
    <div class="header">📖 Booking History</div>

    <form class="search-form" method="get">
        <input type="text" full_name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
        <?php if ($search): ?>
            <a href="booking_history.php"><button type="button">Clear</button></a>
        <?php endif; ?>
        <a href="booking_history.php?export=csv"><button type="button">Export CSV</button></a>
    </form>

    <?php if (empty($history)): ?>
        <div class="no-data">No booking history available.</div>
    <?php else: ?>
        <?php
        $lastDate = '';
        foreach ($history as $row):
            $date = $row['appointment_date'];
            if ($date !== $lastDate):
                if ($lastDate !== '') echo "</table></div>";
                echo "<div class='history-card'>";
                echo "<div class='date-header'>📅 " . date('F j, Y', strtotime($date)) . "</div>";
                echo "<table><tr><th>full_name</th><th>Email</th><th>Service</th><th>Location</th><th>Date & Time</th><th>Phone</th><th>Status</th></tr>";
                $lastDate = $date;
            endif;
            $status = strtolower($row['status']);
            $normalTime = date("g:i A", strtotime($row['appointment_time']));
            $dateTime = date('F j, Y', strtotime($row['appointment_date'])) . ' - ' . $normalTime;
            echo "<tr>
                    <td>{$row['full_name']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['service']}</td>
                    <td>{$row['location']}</td>
                    <td>{$dateTime}</td>
                    <td>{$row['phone_number']}</td>
                    <td><span class='status-badge $status'>{$row['status']}</span></td>
                  </tr>";
        endforeach;
        echo "</table></div>";
        ?>
    <?php endif; ?>
</div>
</body>
</html>

<?php $conn->close(); ?>
