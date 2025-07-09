<?php  
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

// Include the database connection
include('../db_connection.php');

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
$sql = "SELECT b.id, u.username, b.service, b.status, b.created_at, b.location, b.phone, 
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>AirGo Admin - Bookings</title>
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
            min-height: 100vh;
            display: flex;
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

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 1rem 0;
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

        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .tab-link {
            background: var(--card-bg);
            color: var(--text-color);
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px var(--card-shadow);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .tab-link.active, .tab-link:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px var(--card-shadow);
        }

        table {
            width: 100%;
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 10px 20px var(--card-shadow);
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
            margin-top: 1rem;
        }

        th, td {
            padding: 1.2rem 1.5rem;
            text-align: left;
            font-size: 0.95rem;
        }

        th {
            background: var(--primary-color);
            color: var(--card-bg);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
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

        .button {
            background: var(--primary-color);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .button:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px var(--card-shadow);
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
            .tabs {
                justify-content: center;
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
        <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="admin_bookings.php"><i class="fas fa-calendar-alt"></i> Bookings</a>
        <a href="admin_employees.php"><i class="fas fa-users"></i> Employees</a>
        <a href="booking_history.php"><i class="fas fa-history"></i> Booking History</a>
        <a href="admin_register.php"><i class="fas fa-user-shield"></i> Administrator</a>
        <a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main">
        <h1><i class="fas fa-calendar-check"></i> Manage Bookings</h1>

        <div class="tabs">
            <a href="?tab=all" class="tab-link <?= ($tab == 'all') ? 'active' : '' ?>">
                <i class="fas fa-list"></i> All Bookings
            </a>
            <a href="?tab=pending" class="tab-link <?= ($tab == 'pending') ? 'active' : '' ?>">
                <i class="fas fa-clock"></i> Pending
            </a>
            <a href="?tab=reschedule" class="tab-link <?= ($tab == 'reschedule') ? 'active' : '' ?>">
                <i class="fas fa-sync"></i> Reschedule Requests
            </a>
            <a href="?tab=approved" class="tab-link <?= ($tab == 'approved') ? 'active' : '' ?>">
                <i class="fas fa-check-circle"></i> Approved
            </a>
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
                        <td><?= htmlspecialchars($row['username'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['service'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['location'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['phone'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['appointment_date'] ?? '') ?></td>
                        <td><?= htmlspecialchars(date("g:i A", strtotime($row['appointment_time'] ?? ''))) ?></td>
                        <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
                        <td>
                            <?php
                            if (in_array(strtolower($row['status'] ?? ''), ['rejected', 'cancelled'])) {
                                echo 'N/A';
                            } else {
                                echo $row['employee_name'] ? htmlspecialchars($row['employee_name']) : 'To be assigned';
                            }
                            ?>
                        </td>
                        <td>
                            <a href="edit_booking.php?booking_id=<?= $row['id'] ?? '' ?>" class="button">
                                <i class="fas fa-edit"></i> Update
                            </a>
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
