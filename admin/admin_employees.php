<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

// Include the database connection
include('../db_connection.php');

$message = "";

// ADD EMPLOYEE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_employee'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $position = mysqli_real_escape_string($conn, trim($_POST['position']));
    $hire_date = mysqli_real_escape_string($conn, trim($_POST['hire_date']));
    $status = mysqli_real_escape_string($conn, trim($_POST['status']));
    $password_input = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password_input !== $confirm_password) {
        $message = "❌ Passwords do not match.";
    } else {
        $hashed_password = password_hash($password_input, PASSWORD_DEFAULT);

        $sql_insert = "INSERT INTO employees (name, email, position, hire_date, status, password)
                       VALUES ('$name', '$email', '$position', '$hire_date', '$status', '$hashed_password')";

        if ($conn->query($sql_insert) === TRUE) {
            $message = "✅ Employee added successfully!";
        } else {
            $message = "❌ Error: " . $conn->error;
        }
    }
}

// DELETE EMPLOYEE
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $sql_delete = "DELETE FROM employees WHERE id = '$delete_id'";
    if ($conn->query($sql_delete) === TRUE) {
        $message = "✅ Employee deleted successfully!";
    } else {
        $message = "❌ Error deleting employee: " . $conn->error;
    }
}

// Initialize filter variables
$filter_name = isset($_GET['filter_name']) ? mysqli_real_escape_string($conn, $_GET['filter_name']) : '';
$filter_position = isset($_GET['filter_position']) ? mysqli_real_escape_string($conn, $_GET['filter_position']) : '';
$filter_status = isset($_GET['filter_status']) ? mysqli_real_escape_string($conn, $_GET['filter_status']) : '';

// Pagination setup
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page); // Ensure page is at least 1
$offset = ($page - 1) * $records_per_page;

// Get unique positions for dropdown
$sql_positions = "SELECT DISTINCT position FROM employees ORDER BY position";
$result_positions = $conn->query($sql_positions);
$positions = [];
while ($row = $result_positions->fetch_assoc()) {
    $positions[] = $row['position'];
}

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM employees WHERE 1=1";
if (!empty($filter_name)) {
    $count_sql .= " AND name LIKE '%$filter_name%'";
}
if (!empty($filter_position)) {
    $count_sql .= " AND position = '$filter_position'";
}
if (!empty($filter_status)) {
    $count_sql .= " AND status = '$filter_status'";
}
$count_result = $conn->query($count_sql);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// FETCH EMPLOYEES with filters and pagination
$sql_employees = "SELECT * FROM employees WHERE 1=1";
if (!empty($filter_name)) {
    $sql_employees .= " AND name LIKE '%$filter_name%'";
}
if (!empty($filter_position)) {
    $sql_employees .= " AND position = '$filter_position'";
}
if (!empty($filter_status)) {
    $sql_employees .= " AND status = '$filter_status'";
}
$sql_employees .= " ORDER BY id DESC LIMIT $records_per_page OFFSET $offset";

$result_employees = $conn->query($sql_employees);
$employees = [];
while ($row = $result_employees->fetch_assoc()) {
    $employees[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirGo Admin - Employees</title>
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

        .form-container {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 20px var(--card-shadow);
            margin-bottom: 2rem;
        }

        .form-container h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-family: 'Playfair Display', serif;
        }

        .form-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid var(--background-color);
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 4px rgba(60, 213, 237, 0.1);
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-color);
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .toggle-password:hover {
            opacity: 1;
        }

        button[type="submit"] {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        button[type="submit"]:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px var(--card-shadow);
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

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .action-button {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .edit-btn {
            background: var(--primary-color);
            color: white;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
        }

        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px var(--card-shadow);
        }

        .message {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .success {
            background: #d1e7dd;
            color: #0f5132;
        }

        .error {
            background: #f8d7da;
            color: #842029;
        }

        /* Pagination Styles */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
            padding: 1rem;
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 2px 8px var(--card-shadow);
        }

        .pagination-info {
            font-size: 0.9rem;
            color: var(--text-color);
        }

        .pagination-links {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .pagination-link {
            padding: 0.5rem 0.75rem;
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
            font-size: 0.85rem;
        }

        .pagination-link:hover,
        .pagination-link.active {
            background: var(--secondary-color);
            color: var(--primary-color);
            transform: translateY(-2px);
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
            color: white;
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

        @media (max-width: 1024px) {
            .sidebar {
                width: 240px;
            }
            .main {
                margin-left: 240px;
                width: calc(100% - 240px);
            }
            .form-row {
                flex-direction: column;
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
                margin-left: 0;
                width: 100%;
                padding: 1.5rem;
            }
            .form-row {
                flex-direction: column;
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

            /* Name header */
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

            /* Actions buttons */
            td:last-child {
                padding: 0.75rem 1rem;
                display: flex;
                gap: 0.5rem;
                flex-wrap: wrap;
                justify-content: flex-start;
                background: #f8f9fa;
                border-radius: 0 0 12px 12px;
            }

            td:last-child .action-button {
                flex: 1;
                min-width: 120px;
                justify-content: center;
                font-size: 0.85rem;
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

            td:not(:first-child):not(:last-child):before {
                content: attr(data-label);
                font-weight: 500;
                color: var(--primary-color);
                min-width: 100px;
            }

            /* Status styling */
            td[data-label="Status"] {
                padding: 0.75rem 1rem;
            }

            td[data-label="Status"]:before {
                content: attr(data-label);
                font-weight: 500;
                color: var(--primary-color);
                min-width: 100px;
            }

            td[data-label="Status"] {
                font-weight: 500;
            }

            td[data-status="Active"] {
                color: #198754;
            }

            td[data-status="Inactive"] {
                color: #dc3545;
            }

            .pagination-container {
                flex-direction: column;
                gap: 1rem;
                padding: 0.75rem;
                margin-top: 1rem;
            }

            .pagination-info {
                text-align: center;
                font-size: 0.85rem;
            }

            .pagination-links {
                justify-content: center;
                flex-wrap: wrap;
            }

            .pagination-link {
                padding: 0.4rem 0.6rem;
                min-width: 32px;
                height: 32px;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 575px) {
            .sidebar {
                padding: 0.5rem;
            }

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
                padding: 1rem;
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
        <h1><i class="fas fa-users"></i> Manage Employees</h1>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo (strpos($message, '✅') !== false) ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <h2>Add New Employee</h2>
            <form method="POST" onsubmit="return confirmAddEmployee(this);">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Employee Name</label>
                        <input type="text" id="name" name="name" required />
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required />
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="position">Position</label>
                        <input type="text" id="position" name="position" required />
                    </div>
                    <div class="form-group">
                        <label for="hire_date">Hire Date</label>
                        <input type="date" id="hire_date" name="hire_date" required />
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-container">
                            <input type="password" id="password" name="password" required />
                            <span class="toggle-password" onclick="togglePassword('password', this)">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <div class="password-container">
                            <input type="password" id="confirm_password" name="confirm_password" required />
                            <span class="toggle-password" onclick="togglePassword('confirm_password', this)">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="add_employee">
                    <i class="fas fa-plus"></i> Add Employee
                </button>
            </form>
        </div>

        <div class="form-container filter-container">
            <h2>Filter Employees</h2>
            <form method="GET" class="filter-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="filter_name">Name</label>
                        <input type="text" id="filter_name" name="filter_name" value="<?= htmlspecialchars($filter_name) ?>" placeholder="Search by name..."/>
                    </div>
                    <div class="form-group">
                        <label for="filter_position">Position</label>
                        <select id="filter_position" name="filter_position">
                            <option value="">All Positions</option>
                            <?php foreach ($positions as $position): ?>
                                <option value="<?= htmlspecialchars($position) ?>" <?= $filter_position === $position ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($position) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filter_status">Status</label>
                        <select id="filter_status" name="filter_status">
                            <option value="">All Status</option>
                            <option value="Active" <?= $filter_status === 'Active' ? 'selected' : '' ?>>Active</option>
                            <option value="Inactive" <?= $filter_status === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="filter-btn">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
                <a href="admin_employees.php" class="reset-btn">
                    <i class="fas fa-undo"></i> Reset Filters
                </a>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Position</th>
                    <th>Hire Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($employees) > 0): ?>
                    <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td data-label="ID"><?= htmlspecialchars($employee['id'] ?? '') ?></td>
                            <td data-label="Name"><?= htmlspecialchars($employee['name'] ?? '') ?></td>
                            <td data-label="Email"><?= htmlspecialchars($employee['email'] ?? '') ?></td>
                            <td data-label="Position"><?= htmlspecialchars($employee['position'] ?? '') ?></td>
                            <td data-label="Hire Date"><?= htmlspecialchars($employee['hire_date'] ?? '') ?></td>
                            <td data-label="Status" data-status="<?= htmlspecialchars($employee['status'] ?? '') ?>"><?= htmlspecialchars($employee['status'] ?? '') ?></td>
                            <td data-label="Actions" class="action-buttons">
                                <a href="employee_bookings.php?employee_id=<?= $employee['id'] ?? '' ?>" class="action-button view-btn">
                                    <i class="fas fa-calendar-check"></i> Bookings
                                </a>
                                <button class="action-button edit-btn" onclick='openEditModal(<?= json_encode($employee) ?>)'>
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <a href="admin_employees.php?delete_id=<?= $employee['id'] ?? '' ?>" 
                                   class="action-button delete-btn" 
                                   onclick="return confirmDelete('admin_employees.php?delete_id=<?= $employee['id'] ?? '' ?>');">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">No employees found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($total_pages > 1): ?>
            <div class="pagination-container">
                <div class="pagination-info">
                    Showing <?= min($offset + 1, $total_records) ?> to <?= min($offset + $records_per_page, $total_records) ?> of <?= $total_records ?> employees
                </div>
                <div class="pagination-links">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&filter_name=<?= urlencode($filter_name) ?>&filter_position=<?= urlencode($filter_position) ?>&filter_status=<?= urlencode($filter_status) ?>" class="pagination-link">Previous</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?= $i ?>&filter_name=<?= urlencode($filter_name) ?>&filter_position=<?= urlencode($filter_position) ?>&filter_status=<?= urlencode($filter_status) ?>" class="pagination-link <?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page + 1 ?>&filter_name=<?= urlencode($filter_name) ?>&filter_position=<?= urlencode($filter_position) ?>&filter_status=<?= urlencode($filter_status) ?>" class="pagination-link">Next</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Edit Employee Modal -->
    <div class="modal" id="editEmployeeModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Edit Employee</h2>
                <button class="close-btn" onclick="closeEditModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="editEmployeeForm" onsubmit="updateEmployee(event)">
                    <input type="hidden" id="employee_id" name="employee_id">
                    <div class="form-group">
                        <label for="edit_name">Employee Name</label>
                        <input type="text" id="edit_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_email">Email Address</label>
                        <input type="email" id="edit_email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_position">Position</label>
                        <input type="text" id="edit_position" name="position" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_hire_date">Hire Date</label>
                        <input type="date" id="edit_hire_date" name="hire_date" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_status">Status</label>
                        <select id="edit_status" name="status" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
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

    <script>
        function openEditModal(employee) {
            document.getElementById('employee_id').value = employee.id;
            document.getElementById('edit_name').value = employee.name;
            document.getElementById('edit_email').value = employee.email;
            document.getElementById('edit_position').value = employee.position;
            document.getElementById('edit_hire_date').value = employee.hire_date;
            document.getElementById('edit_status').value = employee.status;

            const modal = document.getElementById('editEmployeeModal');
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('active'), 10);
        }

        function closeEditModal() {
            const modal = document.getElementById('editEmployeeModal');
            modal.classList.remove('active');
            setTimeout(() => modal.style.display = 'none', 300);
        }

        async function updateEmployee(event) {
            event.preventDefault();
            
            showConfirmationModal('Are you sure you want to update this employee?', async () => {
                const form = event.target;
                const formData = new FormData(form);

                try {
                    const response = await fetch('update_employee.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();
                    if (result.success) {
                        closeEditModal();
                        showSuccessMessage('Employee updated successfully!');
                    } else {
                        showErrorMessage(result.message || 'Error updating employee');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showErrorMessage('An error occurred while updating the employee');
                }
            });
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

        function confirmDelete(deleteUrl) {
            showConfirmationModal('Are you sure you want to delete this employee?', () => {
                window.location.href = deleteUrl;
            });
            return false;
        }

        function confirmAddEmployee(form) {
            showConfirmationModal('Are you sure you want to add this employee?', () => {
                form.submit();
            });
            return false;
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const editModal = document.getElementById('editEmployeeModal');
            const confirmationModal = document.getElementById('confirmationModal');
            if (event.target === editModal) {
                closeEditModal();
            }
            if (event.target === confirmationModal) {
                closeConfirmationModal();
            }
        }

        function togglePassword(fieldId, icon) {
            const input = document.getElementById(fieldId);
            const i = icon.querySelector('i');
            if (input.type === "password") {
                input.type = "text";
                i.classList.remove('fa-eye');
                i.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                i.classList.remove('fa-eye-slash');
                i.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
