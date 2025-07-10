<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

// Include the database connection
include('../db_connection.php');

if (!isset($_GET['employee_id'])) {
    header("Location: admin_employees.php");
    exit();
}

$employee_id = mysqli_real_escape_string($conn, $_GET['employee_id']);

// Get employee details
$sql_employee = "SELECT * FROM employees WHERE id = '$employee_id'";
$result_employee = $conn->query($sql_employee);
$employee = $result_employee->fetch_assoc();

if (!$employee) {
    header("Location: admin_employees.php");
    exit();
}

// Initialize filter variables
$filter_status = isset($_GET['filter_status']) ? mysqli_real_escape_string($conn, $_GET['filter_status']) : '';
$filter_date_from = isset($_GET['filter_date_from']) ? mysqli_real_escape_string($conn, $_GET['filter_date_from']) : '';
$filter_date_to = isset($_GET['filter_date_to']) ? mysqli_real_escape_string($conn, $_GET['filter_date_to']) : '';

// Fetch bookings for the employee
$sql_bookings = "SELECT * FROM bookings WHERE employee_id = '$employee_id'";

if (!empty($filter_status)) {
    $sql_bookings .= " AND status = '$filter_status'";
}
if (!empty($filter_date_from)) {
    $sql_bookings .= " AND booking_date >= '$filter_date_from'";
}
if (!empty($filter_date_to)) {
    $sql_bookings .= " AND booking_date <= '$filter_date_to'";
}

$sql_bookings .= " ORDER BY booking_date DESC";
$result_bookings = $conn->query($sql_bookings);
$bookings = [];
while ($row = $result_bookings->fetch_assoc()) {
    $bookings[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirGo Admin - Employee Bookings</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@400;700;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Copy all the existing styles from admin_employees.php */
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

        .main {
            margin-left: 250px;
            padding: 2.5rem;
            width: calc(100% - 250px);
        }

        .employee-header {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 20px var(--card-shadow);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .employee-info h2 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            font-family: 'Playfair Display', serif;
        }

        .employee-info p {
            color: var(--text-color);
            opacity: 0.8;
        }

        .back-btn {
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        .filter-container {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 20px var(--card-shadow);
            margin-bottom: 2rem;
        }

        .filter-form .form-row {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .form-group {
            flex: 1;
            min-width: 200px;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid var(--background-color);
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
        }

        .filter-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-right: 1rem;
        }

        .reset-btn {
            background: #6c757d;
            color: white;
            text-decoration: none;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        table {
            width: 100%;
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 10px 20px var(--card-shadow);
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
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
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
        }

        .status-pending {
            background: #ffd700;
            color: #856404;
        }

        .status-confirmed {
            background: #28a745;
            color: white;
        }

        .status-cancelled {
            background: #dc3545;
            color: white;
        }

        .status-completed {
            background: #17a2b8;
            color: white;
        }

        @media (max-width: 1024px) {
            .form-row {
                flex-direction: column;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main {
                margin-left: 0;
                width: 100%;
            }
            .employee-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
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
        <div class="employee-header">
            <div class="employee-info">
                <h2><?= htmlspecialchars($employee['name']) ?>'s Bookings</h2>
                <p>
                    <strong>Position:</strong> <?= htmlspecialchars($employee['position']) ?> |
                    <strong>Status:</strong> <?= htmlspecialchars($employee['status']) ?>
                </p>
            </div>
            <a href="admin_employees.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Employees
            </a>
        </div>

        <div class="filter-container">
            <h2>Filter Bookings</h2>
            <form method="GET" class="filter-form">
                <input type="hidden" name="employee_id" value="<?= htmlspecialchars($employee_id) ?>">
                <div class="form-row">
                    <div class="form-group">
                        <label for="filter_status">Status</label>
                        <select id="filter_status" name="filter_status">
                            <option value="">All Status</option>
                            <option value="Pending" <?= $filter_status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Confirmed" <?= $filter_status === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                            <option value="Cancelled" <?= $filter_status === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            <option value="Completed" <?= $filter_status === 'Completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filter_date_from">Date From</label>
                        <input type="date" id="filter_date_from" name="filter_date_from" value="<?= htmlspecialchars($filter_date_from) ?>">
                    </div>
                    <div class="form-group">
                        <label for="filter_date_to">Date To</label>
                        <input type="date" id="filter_date_to" name="filter_date_to" value="<?= htmlspecialchars($filter_date_to) ?>">
                    </div>
                </div>
                <button type="submit" class="filter-btn">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
                <a href="employee_bookings.php?employee_id=<?= htmlspecialchars($employee_id) ?>" class="reset-btn">
                    <i class="fas fa-undo"></i> Reset Filters
                </a>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Customer Name</th>
                    <th>Service</th>
                    <th>Booking Date</th>
                    <th>Status</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($bookings) > 0): ?>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?= htmlspecialchars($booking['id']) ?></td>
                            <td><?= htmlspecialchars($booking['customer_name']) ?></td>
                            <td><?= htmlspecialchars($booking['service']) ?></td>
                            <td><?= htmlspecialchars($booking['booking_date']) ?></td>
                            <td>
                                <span class="status-badge status-<?= strtolower($booking['status']) ?>">
                                    <?= htmlspecialchars($booking['status']) ?>
                                </span>
                            </td>
                            <td>$<?= number_format($booking['total_amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No bookings found for this employee.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 