<?php
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

// Include the database connection
include('../db_connection.php');

// Archive past completed, done, rejected, or cancelled bookings
$archiveSQL = "
    INSERT IGNORE INTO booking_history (
        user_id, name, email, service, location, appointment_date, appointment_time,
        phone_number, technician, status
    )
    SELECT 
        b.user_id,
        CONCAT(u.fname, ' ', u.lname) as name,
        u.email,
        b.service,
        b.location,
        b.appointment_date,
        b.appointment_time,
        b.phone, 
        COALESCE(e.name, 'Unassigned') as technician,
        b.status
    FROM bookings b
    LEFT JOIN user u ON b.user_id = u.id
    LEFT JOIN employees e ON b.employee_id = e.id
    WHERE b.status IN ('Completed', 'Done', 'Rejected', 'Cancelled')
      AND b.appointment_date < CURDATE()
";

if (!$conn->query($archiveSQL)) {
    die("Archive Error: " . $conn->error);
}

// Delete old bookings after archiving
$deleteSQL = "
    DELETE FROM bookings
    WHERE status IN ('Completed', 'Done', 'Rejected', 'Cancelled')
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
    $sql .= " WHERE name LIKE '%$search%'
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
    header('Content-Disposition: attachment;filename=booking_history.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Name', 'Email', 'Service', 'Location', 'Appointment Date', 'Appointment Time', 'Phone', 'Technician', 'Status']);

    foreach ($history as $row) {
        $formattedTime = date("g:i A", strtotime($row['appointment_time']));
        fputcsv($output, [
            $row['name'], $row['email'], $row['service'], $row['location'],
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirGo Admin - Booking History</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@400;700;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #07353f;
            --secondary-color: #3cd5ed;
            --background-color: #d0f0ff;
            --text-color: #344047;
            --card-bg: #ffffff;
            --card-shadow: rgba(7, 53, 63, 0.1);
            --spacing-unit: clamp(0.5rem, 2vw, 1rem);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--background-color), #fff);
            color: var(--text-color);
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-color), #052830);
            padding: 2rem 1.5rem;
            color: white;
            box-shadow: 4px 0 20px var(--card-shadow);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 900;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            letter-spacing: 1px;
        }

        .sidebar h2 span {
            color: var(--secondary-color);
            font-style: italic;
        }

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 12px 16px;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .sidebar a::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--secondary-color);
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.3s ease;
            z-index: -1;
            border-radius: 12px;
        }

        .sidebar a:hover {
            color: var(--primary-color);
            transform: translateX(5px);
        }

        .sidebar a:hover::before {
            transform: scaleX(1);
            transform-origin: left;
        }

        .sidebar a i {
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .sidebar a:hover i {
            transform: scale(1.1);
        }

        .sidebar a.active {
            background: var(--secondary-color);
            color: var(--primary-color);
        }

        .sidebar a.active i {
            color: var(--primary-color);
        }

        .sidebar a.active:hover::before {
            transform: scaleX(0);
        }

        .main {
            margin-left: 250px;
            padding: 2.5rem;
            width: calc(100% - 250px);
        }

        .main h1 {
            margin-bottom: 2rem;
            color: var(--primary-color);
            font-size: 2rem;
            font-family: 'Playfair Display', serif;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .search-form {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 20px var(--card-shadow);
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-form input {
            flex: 1;
            min-width: 200px;
            padding: 0.8rem 1rem;
            border: 2px solid var(--background-color);
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }

        .search-form input:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 4px rgba(60, 213, 237, 0.1);
        }

        .button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .button:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px var(--card-shadow);
        }

        .history-card {
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 10px 20px var(--card-shadow);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .date-header {
            background: var(--primary-color);
            color: white;
            padding: 1rem 1.5rem;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        th, td {
            padding: 1.2rem 1.5rem;
            text-align: left;
        }

        th {
            background: var(--primary-color);
            color: var(--card-bg);
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:nth-child(even) {
            background: var(--background-color);
        }

        tbody tr {
            transition: all 0.3s ease;
        }

        tbody tr:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            color: var(--primary-color);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: capitalize;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .status-badge.completed,
        .status-badge.done {
            background: #28a745;
            color: white;
        }

        .status-badge.rejected {
            background: #dc3545;
            color: white;
        }

        .status-badge.cancelled {
            background: #e3342f;
            color: white;
        }

        .status-badge.pending {
            background: #ffc107;
            color: var(--primary-color);
        }

        .status-badge.in-progress {
            background: #007bff;
            color: white;
        }

        .no-data {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 20px;
            text-align: center;
            color: var(--text-color);
            font-size: 1.1rem;
            box-shadow: 0 10px 20px var(--card-shadow);
        }

        @media (max-width: 1024px) {
            .sidebar {
                width: 240px;
            }
            .main {
                margin-left: 240px;
                width: calc(100% - 240px);
            }
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                padding: 1rem;
            }
            .main {
                margin-left: 0;
                width: 100%;
                padding: 1.5rem;
            }
            .search-form {
                flex-direction: column;
            }
            th, td {
                padding: 0.8rem;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Air<span>go</span></h2>
        <div class="nav-links">
            <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="admin_bookings.php" class="<?= basename($_SERVER['PHP_SELF']) === 'admin_bookings.php' ? 'active' : '' ?>"><i class="fas fa-calendar-alt"></i> Bookings</a>
            <a href="admin_employees.php" class="<?= basename($_SERVER['PHP_SELF']) === 'admin_employees.php' ? 'active' : '' ?>"><i class="fas fa-users"></i> Employees</a>
            <a href="booking_history.php" class="<?= basename($_SERVER['PHP_SELF']) === 'booking_history.php' ? 'active' : '' ?>"><i class="fas fa-history"></i> Booking History</a>
            <a href="admin_register.php" class="<?= basename($_SERVER['PHP_SELF']) === 'admin_register.php' ? 'active' : '' ?>"><i class="fas fa-user-shield"></i> Administrator</a>
            <a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="main">
        <h1><i class="fas fa-history"></i> Booking History</h1>

        <form class="search-form" method="get">
            <input type="text" name="search" placeholder="Search bookings..." value="<?= htmlspecialchars($search ?? '') ?>">
            <button type="submit" class="button">
                <i class="fas fa-search"></i> Search
            </button>
            <?php if ($search): ?>
                <a href="booking_history.php" class="button">
                    <i class="fas fa-times"></i> Clear
                </a>
            <?php endif; ?>
            <a href="booking_history.php?export=csv" class="button">
                <i class="fas fa-download"></i> Export CSV
            </a>
        </form>

        <?php if (empty($history)): ?>
            <div class="no-data">
                <i class="fas fa-info-circle"></i> No booking history available.
            </div>
        <?php else: ?>
            <?php
            $lastDate = '';
            foreach ($history as $row):
                $date = $row['appointment_date'];
                if ($date !== $lastDate):
                    if ($lastDate !== '') echo "</table></div>";
                    echo "<div class='history-card'>";
                    echo "<div class='date-header'><i class='fas fa-calendar-day'></i> " . date('F j, Y', strtotime($date)) . "</div>";
                    echo "<table><tr><th>Name</th><th>Email</th><th>Service</th><th>Location</th><th>Time</th><th>Phone</th><th>Status</th></tr>";
                    $lastDate = $date;
                endif;
                $status = strtolower($row['status']);
                $normalTime = date("g:i A", strtotime($row['appointment_time']));
                echo "<tr>
                        <td>" . htmlspecialchars($row['name'] ?? '') . "</td>
                        <td>" . htmlspecialchars($row['email'] ?? '') . "</td>
                        <td>" . htmlspecialchars($row['service'] ?? '') . "</td>
                        <td>" . htmlspecialchars($row['location'] ?? '') . "</td>
                        <td>" . htmlspecialchars($normalTime) . "</td>
                        <td>" . htmlspecialchars($row['phone_number'] ?? '') . "</td>
                        <td><span class='status-badge $status'>" . htmlspecialchars($row['status'] ?? '') . "</span></td>
                      </tr>";
            endforeach;
            echo "</table></div>";
            ?>
        <?php endif; ?>
    </div>
</body>
</html>
