<?php  
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

// Include the database connection
require_once '../config/database.php';

// Get database connection
$conn = Database::getConnection();

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

// Pagination setup
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page); // Ensure page is at least 1
$offset = ($page - 1) * $records_per_page;

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

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM bookings b
              LEFT JOIN user u ON b.user_id = u.id
              LEFT JOIN employees e ON b.employee_id = e.id
              $where";
$count_result = $conn->query($count_sql);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Fetch bookings with pagination
$sql = "SELECT b.id, u.username, b.service, b.status, b.created_at, b.location, b.phone, 
               b.appointment_date, b.appointment_time, b.employee_id, u.username, e.name AS employee_name,
               b.note
        FROM bookings b
        LEFT JOIN user u ON b.user_id = u.id
        LEFT JOIN employees e ON b.employee_id = e.id
        $where
        ORDER BY b.appointment_date, b.appointment_time
        LIMIT $records_per_page OFFSET $offset";
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

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
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
            font-size: 1.4rem;
            min-width: 32px;
            text-align: center;
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
            padding: 2rem;
            width: calc(100% - 250px);
        }

        .main h1 {
            margin-bottom: 2rem;
            color: var(--primary-color);
            font-size: 2.2rem;
            font-family: 'Playfair Display', serif;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0 1rem;
        }

        .main h1 i {
            color: var(--secondary-color);
        }

        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            padding: 0 1rem;
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
            font-size: 0.9rem;
        }

        .tab-link i {
            font-size: 1rem;
            color: var(--primary-color);
        }

        .tab-link.active, .tab-link:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px var(--card-shadow);
        }

        .tab-link.active i, .tab-link:hover i {
            color: var(--secondary-color);
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
            table-layout: fixed;
        }

        th, td {
            padding: 0.6rem;
            text-align: left;
            font-size: 0.8rem;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
            min-width: 0;
        }

        /* Column widths */
        th:nth-child(1), td:nth-child(1) { width: 10%; } /* Name */
        th:nth-child(2), td:nth-child(2) { width: 12%; } /* Service */
        th:nth-child(3), td:nth-child(3) { width: 15%; } /* Location */
        th:nth-child(4), td:nth-child(4) { width: 8%; }  /* Contact */
        th:nth-child(5), td:nth-child(5) { width: 8%; }  /* Date */
        th:nth-child(6), td:nth-child(6) { width: 8%; }  /* Time */
        th:nth-child(7), td:nth-child(7) { width: 15%; } /* Note */
        th:nth-child(8), td:nth-child(8) { width: 8%; }  /* Status */
        th:nth-child(9), td:nth-child(9) { width: 8%; }  /* Technician */
        th:nth-child(10), td:nth-child(10) { width: 8%; } /* Actions */

        td {
            line-height: 1.2;
        }

        th {
            background: var(--primary-color);
            color: var(--card-bg);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-size: 0.75rem;
            position: sticky;
            top: 0;
            z-index: 10;
            padding: 0.5rem;
        }

        tr:nth-child(even) {
            background: rgba(208, 240, 255, 0.2);
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
            padding: 0.3rem 0.6rem;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .button i {
            font-size: 0.7rem;
        }

        .button:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px var(--card-shadow);
            color: var(--primary-color);
        }

        /* Pagination Styles */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
            padding: 0 1rem;
        }

        .pagination-info {
            font-size: 0.9rem;
            color: var(--text-color);
        }

        .pagination {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .pagination-btn {
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 30px;
            height: 30px;
        }

        .pagination-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px var(--card-shadow);
            color: var(--primary-color);
        }

        .pagination-btn.active {
            background: var(--secondary-color);
            color: var(--primary-color);
            font-weight: 600;
        }

        .pagination-btn i {
            font-size: 0.8rem;
        }

        @media (max-width: 1200px) {
            th, td {
                padding: 0.4rem;
                font-size: 0.75rem;
            }
            .button {
                padding: 0.3rem 0.5rem;
                font-size: 0.7rem;
            }
            .main h1 {
                font-size: 2rem;
            }
            .tab-link {
                padding: 0.7rem 1.2rem;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 992px) {
            table {
                font-size: 0.75rem;
            }
            th, td {
                padding: 0.4rem;
            }
            .main h1 {
                font-size: 1.8rem;
            }
            .tab-link {
                padding: 0.6rem 1rem;
                font-size: 0.8rem;
            }
            /* Adjust column widths for medium screens */
            th:nth-child(1), td:nth-child(1) { width: 12%; }
            th:nth-child(2), td:nth-child(2) { width: 15%; }
            th:nth-child(3), td:nth-child(3) { width: 18%; }
            th:nth-child(7), td:nth-child(7) { width: 12%; }
        }

        @media (max-width: 991px) {
            body {
                background: #f5f9fc;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                padding: 1rem;
                background: var(--primary-color);
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .sidebar h2 {
                display: none;
            }

            .nav-links {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 0.75rem;
                padding: 0;
            }

            .nav-links a {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                color: rgba(255, 255, 255, 0.9);
                text-decoration: none;
                padding: 0.75rem;
                border-radius: 12px;
                font-size: 0.9rem;
                transition: all 0.3s ease;
                text-align: center;
                background: rgba(255, 255, 255, 0.1);
            }

            .nav-links a i {
                font-size: 1.1rem;
            }

            .nav-links a.active {
                background: var(--secondary-color);
                color: var(--primary-color);
                font-weight: 500;
            }

            .main {
                margin: 0;
                width: 100%;
                padding: 1rem;
                background: #f5f9fc;
                min-height: calc(100vh - 80px);
                flex: 1;
            }

            .main h1 {
                font-size: 1.5rem;
                margin-bottom: 1.25rem;
                color: var(--primary-color);
                font-weight: 600;
                padding: 0;
            }

            .tabs {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
                margin-bottom: 1.25rem;
                padding: 0;
            }

            .tab-link {
                padding: 0.75rem 1.25rem;
                border-radius: 50px;
                font-size: 0.9rem;
                background: white;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                color: var(--text-color);
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .tab-link.active {
                background: var(--primary-color);
                color: white;
            }

            /* Card style for table rows */
            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead {
                display: none;
            }

            tr {
                margin-bottom: 1rem;
                background: white;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                overflow: hidden;
                position: relative;
            }

            /* Service title header */
            td:first-child {
                background: var(--primary-color);
                color: white;
                font-size: 1rem;
                font-weight: 500;
                padding: 1rem;
                margin: -1px;
                border-radius: 12px 12px 0 0;
                border-bottom: none;
                display: flex;
                justify-content: space-between;
                align-items: center;
                position: relative;
                width: calc(100% + 2px);
            }

            /* Left border accent */
            tr::before {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                width: 4px;
                background: var(--secondary-color);
                border-radius: 12px 0 0 12px;
            }

            /* Actions button */
            td:last-child {
                position: absolute;
                top: 0;
                right: 0;
                padding: 0;
                border: none;
                background: none;
                z-index: 1;
                height: 52px;
                display: flex;
                align-items: center;
                padding-right: 1rem;
            }

            td:last-child .button {
                background: transparent;
                color: rgba(255, 255, 255, 0.75);
                width: 32px;
                height: 32px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: color 0.2s ease;
                border: none;
                padding: 0;
            }

            td:last-child .button:hover,
            td:last-child .button:active {
                color: white;
                background: transparent;
                transform: none;
                box-shadow: none;
            }

            td:last-child .button i {
                font-size: 1rem;
            }

            /* Content rows */
            td:not(:first-child):not(:last-child) {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            }

            td:not(:first-child):not(:last-child):last-of-type {
                border-bottom: none;
            }

            td:not(:first-child):not(:last-child):before {
                content: attr(data-label);
                font-weight: 500;
                color: var(--primary-color);
                min-width: 100px;
            }

            /* Status badges */
            td[data-label="Status"] {
                padding: 0.75rem 1rem;
            }

            td[data-label="Status"]:before {
                content: attr(data-label);
                font-weight: 500;
                color: var(--primary-color);
                min-width: 100px;
            }

            td[data-label="Status"] span {
                padding: 0.3rem 0.8rem;
                border-radius: 20px;
                font-size: 0.8rem;
                font-weight: 500;
                text-transform: uppercase;
            }

            td[data-status="Pending"] span {
                background: #fff3cd;
                color: #856404;
            }

            td[data-status="Approved"] span {
                background: #d4edda;
                color: #155724;
            }

            td[data-status="Reschedule Requested"] span {
                background: #cce5ff;
                color: #004085;
            }

            /* Pagination styles */
            .pagination-container {
                margin-top: 1.5rem;
                padding: 1rem;
                background: white;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .pagination-info {
                text-align: center;
                font-size: 0.85rem;
                color: var(--text-color);
                margin-bottom: 1rem;
            }

            .pagination {
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .pagination-btn {
                padding: 0.5rem 0.75rem;
                font-size: 0.85rem;
                min-width: 35px;
                height: 35px;
                border-radius: 8px;
                background: var(--primary-color);
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                transition: all 0.3s ease;
            }

            .pagination-btn:hover,
            .pagination-btn.active {
                background: var(--secondary-color);
                color: var(--primary-color);
            }
        }

        @media (max-width: 575px) {
            .nav-links {
                grid-template-columns: repeat(2, 1fr);
            }

            .nav-links a {
                padding: 0.6rem;
                font-size: 0.85rem;
            }

            .nav-links a i {
                font-size: 1rem;
            }

            .main {
                padding: 0.75rem;
            }

            .main h1 {
                font-size: 1.25rem;
                margin-bottom: 1rem;
            }

            .tab-link {
                padding: 0.6rem 1rem;
                font-size: 0.85rem;
            }

            tr {
                margin-bottom: 0.75rem;
            }

            td:first-child {
                font-size: 0.95rem;
                padding: 0.75rem;
            }

            td:not(:first-child):not(:last-child) {
                padding: 0.6rem 0.75rem;
                font-size: 0.85rem;
            }

            td:not(:first-child):not(:last-child):before {
                min-width: 90px;
                font-size: 0.8rem;
            }

            .pagination-container {
                padding: 0.75rem;
                margin-top: 1rem;
            }

            .pagination-btn {
                padding: 0.4rem 0.6rem;
                min-width: 32px;
                height: 32px;
                font-size: 0.8rem;
            }

            td:last-child {
                padding-right: 0.5rem;
            }

            td:last-child .button {
                width: 28px;
                height: 28px;
            }

            td:last-child .button i {
                font-size: 0.9rem;
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
            <!-- <a href="admin_register.php" class="<?= basename($_SERVER['PHP_SELF']) === 'admin_register.php' ? 'active' : '' ?>"><i class="fas fa-user-shield"></i> Administrator</a> -->
            <a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
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

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Service</th>
                        <th>Location</th>
                        <th>Contact</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Note</th>
                        <th>Status</th>
                        <th>Technician</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td data-label="Name"><?= htmlspecialchars($row['username'] ?? '') ?></td>
                            <td data-label="Service"><?= htmlspecialchars($row['service'] ?? '') ?></td>
                            <td data-label="Location"><?= htmlspecialchars($row['location'] ?? '') ?></td>
                            <td data-label="Contact"><?= htmlspecialchars($row['phone'] ?? '') ?></td>
                            <td data-label="Date"><?= htmlspecialchars($row['appointment_date'] ?? '') ?></td>
                            <td data-label="Time"><?= htmlspecialchars(date('g:i A', strtotime($row['appointment_time'] ?? ''))) ?></td>
                            <td data-label="Note"><?= htmlspecialchars($row['note'] ?? 'No note') ?></td>
                            <td data-label="Status" data-status="<?= htmlspecialchars($row['status'] ?? '') ?>">
                                <span><?= htmlspecialchars($row['status'] ?? '') ?></span>
                            </td>
                            <td data-label="Technician"><?php
                                if (in_array(strtolower($row['status'] ?? ''), ['rejected', 'cancelled'])) {
                                    echo 'N/A';
                                } else {
                                    echo $row['employee_name'] ? htmlspecialchars($row['employee_name']) : 'To be assigned';
                                }
                            ?></td>
                            <td data-label="Actions">
                                <button class="button edit-btn" onclick="openEditModal(<?= htmlspecialchars(json_encode($row)) ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="10" style="text-align:center;">No bookings found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination-container">
            <div class="pagination-info">
                <span>Showing <?= $offset + 1 ?> to <?= min($offset + $records_per_page, $total_records) ?> of <?= $total_records ?> entries</span>
            </div>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?tab=<?= $tab ?>&page=1" class="pagination-btn">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                    <a href="?tab=<?= $tab ?>&page=<?= $page - 1 ?>" class="pagination-btn">
                        <i class="fas fa-angle-left"></i>
                    </a>
                <?php endif; ?>

                <?php
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++):
                ?>
                    <a href="?tab=<?= $tab ?>&page=<?= $i ?>" class="pagination-btn <?= $i == $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?tab=<?= $tab ?>&page=<?= $page + 1 ?>" class="pagination-btn">
                        <i class="fas fa-angle-right"></i>
                    </a>
                    <a href="?tab=<?= $tab ?>&page=<?= $total_pages ?>" class="pagination-btn">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Edit Booking Modal -->
        <div class="modal" id="editBookingModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2><i class="fas fa-edit"></i> Edit Booking</h2>
                    <button class="close-btn" onclick="closeEditModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editBookingForm" onsubmit="updateBooking(event)">
                        <input type="hidden" id="booking_id" name="booking_id">
                        <div class="form-group">
                            <label for="username">Customer Name</label>
                            <input type="text" id="username" name="username" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="service">Service</label>
                            <input type="text" id="service" name="service" required>
                        </div>
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" id="location" name="location" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Contact</label>
                            <input type="text" id="phone" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="appointment_date">Appointment Date</label>
                            <input type="date" id="appointment_date" name="appointment_date" required>
                        </div>
                        <div class="form-group">
                            <label for="appointment_time">Appointment Time</label>
                            <input type="time" id="appointment_time" name="appointment_time" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                <option value="Pending">Pending</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                                <option value="Cancelled">Cancelled</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="employee_id">Assign Technician</label>
                            <select id="employee_id" name="employee_id">
                                <option value="">Select Technician</option>
                                <?php
                                $employees_sql = "SELECT id, name FROM employees WHERE status != 'Inactive'";
                                $employees_result = $conn->query($employees_sql);
                                while ($employee = $employees_result->fetch_assoc()):
                                ?>
                                <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="save-btn">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <button type="button" class="cancel-btn" onclick="closeEditModal()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="confirmation-modal" id="confirmationModal">
        <div class="confirmation-content">
            <div class="confirmation-header">
                <h3><i class="fas fa-question-circle"></i> Confirm Action</h3>
                <button class="close-btn" onclick="closeConfirmationModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="confirmation-body">
                <p id="confirmationMessage"></p>
            </div>
            <div class="confirmation-actions">
                <button class="confirm-btn" id="confirmActionBtn">
                    <i class="fas fa-check"></i> Confirm
                </button>
                <button class="cancel-btn" onclick="closeConfirmationModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </div>
    </div>

    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow-y: auto;
        }

        .modal-content {
            background: white;
            width: 90%;
            max-width: 600px;
            margin: 20px auto;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: relative;
            transform: translateY(-20px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .modal.active .modal-content {
            transform: translateY(0);
            opacity: 1;
        }

        .modal-header {
            background: var(--primary-color);
            color: white;
            padding: 1.5rem;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            transition: all 0.3s ease;
        }

        .close-btn:hover {
            color: var(--secondary-color);
            transform: scale(1.1);
        }

        .modal-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 2px rgba(60, 213, 237, 0.2);
            outline: none;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
        }

        .save-btn,
        .cancel-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .save-btn {
            background: var(--primary-color);
            color: white;
        }

        .save-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        .cancel-btn {
            background: #f8f9fa;
            color: #495057;
        }

        .cancel-btn:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                margin: 10px auto;
            }

            .modal-body {
                padding: 1.5rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .save-btn,
            .cancel-btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Confirmation Modal Styles */
        .confirmation-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .confirmation-modal.active {
            opacity: 1;
        }

        .confirmation-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -60%);
            background: white;
            padding: 0;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            opacity: 0;
            transform: translate(-50%, -60%) scale(0.95);
            transition: all 0.3s ease;
        }

        .confirmation-modal.active .confirmation-content {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }

        .confirmation-header {
            background: var(--primary-color);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .confirmation-header h3 {
            margin: 0;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .confirmation-body {
            padding: 1.5rem;
            text-align: center;
            color: var(--text-color);
        }

        .confirmation-actions {
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
            background: #f8f9fa;
            border-radius: 0 0 12px 12px;
        }

        .confirm-btn,
        .confirmation-actions .cancel-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .confirm-btn {
            background: var(--primary-color);
            color: white;
        }

        .confirm-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        .confirmation-actions .cancel-btn {
            background: #e9ecef;
            color: #495057;
        }

        .confirmation-actions .cancel-btn:hover {
            background: #dee2e6;
            transform: translateY(-2px);
        }

        @media (max-width: 576px) {
            .confirmation-content {
                width: 95%;
            }

            .confirmation-actions {
                flex-direction: column;
            }

            .confirm-btn,
            .confirmation-actions .cancel-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <script>
        function openEditModal(booking) {
            document.getElementById('booking_id').value = booking.id;
            document.getElementById('username').value = booking.username;
            document.getElementById('service').value = booking.service;
            document.getElementById('location').value = booking.location;
            document.getElementById('phone').value = booking.phone;
            document.getElementById('appointment_date').value = booking.appointment_date;
            document.getElementById('appointment_time').value = booking.appointment_time;
            document.getElementById('status').value = booking.status;
            document.getElementById('employee_id').value = booking.employee_id || '';

            const modal = document.getElementById('editBookingModal');
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('active'), 10);
        }

        function closeEditModal() {
            const modal = document.getElementById('editBookingModal');
            modal.classList.remove('active');
            setTimeout(() => modal.style.display = 'none', 300);
        }

        function showConfirmationModal(message, onConfirm) {
            const modal = document.getElementById('confirmationModal');
            const messageEl = document.getElementById('confirmationMessage');
            const confirmBtn = document.getElementById('confirmActionBtn');
            
            messageEl.textContent = message;
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('active'), 10);

            // Remove any existing click handler
            confirmBtn.replaceWith(confirmBtn.cloneNode(true));
            
            // Add new click handler
            document.getElementById('confirmActionBtn').addEventListener('click', () => {
                closeConfirmationModal();
                onConfirm();
            });
        }

        function closeConfirmationModal() {
            const modal = document.getElementById('confirmationModal');
            modal.classList.remove('active');
            setTimeout(() => modal.style.display = 'none', 300);
        }

        function showSuccessMessage(message) {
            showConfirmationModal(message);
            setTimeout(() => {
                closeConfirmationModal();
                window.location.reload();
            }, 1500);
        }

        function showErrorMessage(message) {
            showConfirmationModal('Error: ' + message);
        }

        async function updateBooking(event) {
            event.preventDefault();
            
            showConfirmationModal('Are you sure you want to update this booking?', async () => {
                const form = event.target;
                const formData = new FormData(form);

                try {
                    const response = await fetch('update_booking.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();
                    if (result.success) {
                        closeEditModal();
                        showSuccessMessage('Booking updated successfully!');
                    } else {
                        showErrorMessage(result.message || 'Error updating booking');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showErrorMessage('An error occurred while updating the booking');
                }
            });
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const editModal = document.getElementById('editBookingModal');
            const confirmationModal = document.getElementById('confirmationModal');
            if (event.target === editModal) {
                closeEditModal();
            }
            if (event.target === confirmationModal) {
                closeConfirmationModal();
            }
        }
    </script>
</body>
</html>
