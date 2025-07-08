<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "airgo";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$total_bookings = $conn->query("SELECT COUNT(*) AS count FROM bookings")->fetch_assoc()['count'];
$pending_approvals = $conn->query("SELECT COUNT(*) AS count FROM bookings WHERE status = 'Pending'")->fetch_assoc()['count'];
$total_employees = $conn->query("SELECT COUNT(*) AS count FROM employees")->fetch_assoc()['count'];

$recent = $conn->query("
    SELECT b.*, e.name AS employee_name, u.username AS user_name
    FROM bookings b
    LEFT JOIN employees e ON b.employee_id = e.id
    LEFT JOIN `user` u ON b.user_id = u.id
    ORDER BY b.created_at DESC
    LIMIT 5
");

if (!$recent) {
    die("Query failed: " . $conn->error);
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AirGo Admin Dashboard</title>
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

        .main {
            margin-left: 250px;
            padding: 40px;
            width: calc(100% - 250px);
        }

        .main h1 {
            margin-bottom: 30px;
            color: var(--main);
            font-size: 25px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .card-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .card {
            background: var(--light);
            padding: 15px;
            border-radius: 15px;
            box-shadow: 3px 3px 6px var(--shadow), -3px -3px 6px #ffffff;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            cursor: pointer;
        }

        .card h3 {
            font-size: 15px;
            color: #777;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 20px;
            font-weight: bold;
            color: var(--main);
        }

        table {
            width: 100%;
            background: var(--light);
            border-radius: 15px;
            box-shadow: 3px 3px 6px var(--shadow), -3px -3px 6px #ffffff;
            border-collapse: collapse;
            overflow: hidden;
        }

        th, td {
            padding: 15px 20px;
            text-align: left;
        }

        th {
            background: var(--main);
            color: var(--light);
            font-weight: 600;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        tr:hover {
            background: #e0f7f7;
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
	<a href="admin_register.php">Administrator</a>
    <a href="login.php">Logout</a>
</div>

<div class="main">
    <h1>Welcome👋</h1>

    <div class="grid">
        <a href="admin_bookings.php" class="card-link">
            <div class="card">
                <h3>Total Bookings</h3>
                <p><?php echo $total_bookings; ?></p>
            </div>
        </a>
        <a href="admin_bookings.php?tab=pending" class="card-link">
            <div class="card">
                <h3>Total Pending</h3>
                <p><?php echo $pending_approvals; ?></p>
            </div>
        </a>
        <a href="admin_employees.php" class="card-link">
            <div class="card">
                <h3>Total Employees</h3>
                <p><?php echo $total_employees; ?></p>
            </div>
        </a>
    </div>

    <div class="card" style="padding: 0;">
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Assigned Employee</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                
                <?php while ($row = $recent->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['user_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['service']); ?></td>
                        <td><?php echo $row['employee_name'] ?: 'Unassigned'; ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
