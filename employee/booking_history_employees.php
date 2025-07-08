<?php
session_start();

if (!isset($_SESSION['employee_logged_in'])) {
    header("Location: login.php");
    exit();
}

$employee_id = $_SESSION['employee_id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "airgo";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT 
            b.id,
            user_id,
            b.service,
            b.created_at,
            b.location,
            b.phone,
            b.appointment_date,
            b.appointment_time,
            e.name AS employee_name,
            b.status,
            b.employee_id
        FROM bookings b
        LEFT JOIN users u ON b.user_id = u.id
        LEFT JOIN employees e ON b.employee_id = e.id
        WHERE b.status IN ('done', 'cancelled', 'completed', 'rejected')
          AND b.employee_id = ?
        ORDER BY b.appointment_date DESC, b.created_at DESC";
     
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>AirGo Employee Booking History</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
:root {
    --bg: #4c7273;
    --main: #07353f;
    --accent: #CACBBB;
    --light: #ffffff;
    --shadow: rgba(0,0,0,0.1);
}
* {margin:0; padding:0; box-sizing:border-box;}
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
    position: relative;
}
.main-content h1 {
    margin-bottom: 20px;
    color: white;
    font-size: 22px;
}
.export-btn {
    display: inline-block;
    padding: 8px 14px;
    background: #07353f;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    font-size: 14px;
    position: absolute;
    top: 20px;
    right: 20px;
    transition: background 0.3s ease;
}
.export-btn:hover {
    background: #CACBBB;
}
.date-heading {
    color: #fff;
    margin-top: 40px;
    font-size: 18px;
    text-decoration: underline;
}
table {
    width: 90%;
    background: var(--light);
    border-collapse: collapse;
    border-radius: 15px;
    box-shadow: 6px 6px 12px var(--shadow), -6px -6px 12px #ffffff;
    overflow: hidden;
    margin: 20px auto;
}
th, td {
    padding: 15px 20px;
    text-align: left;
    font-size: 13px;
}
th {
    background: var(--main);
    color: var(--light);
}
td {
    background: #fff;
    border-bottom: 1px solid #eee;
}
tr:hover td {
    background: #e6f7ff;
    transition: 0.3s ease;
}
.delete-btn {
    background: #c0392b;
    color: #fff;
    padding: 5px 10px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}
.delete-btn:hover {
    background: #e74c3c;
}
@media(max-width:768px){
    .main-content {margin-left:0; width:100%;}
    .sidebar {display:none;}
    .export-btn {
        position: static;
        margin-bottom: 10px;
        display: block;
    }
}
</style>
</head>
<body>
<div class="sidebar">
    <h2>AirGo Employee</h2>
    <a href="employee_dashboard.php">Dashboard</a>
    <a href="booking_history_employees.php">History</a>
    <a href="employees_login.php">Logout</a>
</div>

<div class="main-content">
    <h1>Booking History</h1>
    <a href="export_booking_history_employees.php" class="export-btn">Export CSV</a>
     
    <?php
    if (empty($bookings)) {
        echo "<p style='color:white;'>No booking history available.</p>";
    } else {
        $current_date = null;
        foreach ($bookings as $row):
            if ($current_date !== $row['appointment_date']):
                if ($current_date !== null) echo "</tbody></table>";
                $current_date = $row['appointment_date'];
    ?>
    <h2 class="date-heading"><?= htmlspecialchars(date("F j, Y", strtotime($current_date))) ?></h2>
    <table>
        <thead>
            <tr>
                <th>User_id</th>
                <th>Service</th>
                <th>Location</th>
                <th>Phone</th>
                <th>Appointment Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
    <?php
            endif;
    ?>
        <tr>
            <td><?= htmlspecialchars($row['user_id']) ?></td>
            <td><?= htmlspecialchars($row['service']) ?></td>
            <td><?= htmlspecialchars($row['location']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td>
                <?php
                $time = htmlspecialchars($row['appointment_time']);
                echo $time ? date("g:i A", strtotime($time)) : "-";
                ?>
            </td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td>
                <form method="POST" action="delete_booking.php" onsubmit="return confirm('Are you sure you want to delete this booking? This cannot be undone.');">
                    <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
                    <button type="submit" class="delete-btn">Delete</button>
                </form>
            </td>
        </tr>
    <?php
        endforeach;
        echo "</tbody></table>";
    }
    ?>
</div>
</body>
</html>
