<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection
require_once '../config/database.php';

// Get database connection
$conn = Database::getConnection();

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>AirGo Admin - Dashboard</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='20' fill='%2307353f'/%3E%3Cpath d='M30 50c0-11 9-20 20-20s20 9 20 20-9 20-20 20-20-9-20-20zm35 0c0-8.3-6.7-15-15-15s-15 6.7-15 15 6.7 15 15 15 15-6.7 15-15z' fill='%233cd5ed'/%3E%3Cpath d='M50 60c-5.5 0-10-4.5-10-10s4.5-10 10-10 10 4.5 10 10-4.5 10-10 10z' fill='white'/%3E%3C/svg%3E" />
    <link rel="icon" type="image/png" sizes="32x32" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAABKhJREFUWEe9V21sU2UUfu7H3a7dmDAgKgwQPxAkKAYnJsyPGCGK0ZAQFfEHJGpigkT8gYrxB5pI/GD+QBNjNCr+ICYaEzWBRDBRIQQIiCIIKLIvGIPtbrfb7b3XnHPvbVc2trs3bU/S7H7c877Pec95z3veK+AeHOIe4MN/B0CWZVkQhHui3LquX5YkSboXk+u6PqwoCud3HEAkEuH8qqrS0NBQYWxsLDY2NhaNRqNRVVVjmqYpuq4rpHwwGHQHAgG3y+VyezweN/1yu91uQRDcBIr+CoIgCILAf222GfRqgKZpGsdHYDRNm6Xr+llZlr1/CcDQ0BCNjZumxXVdVxVFiWmaFlVVNaooSjQejxPQEUVRYpqmxXRd5+vpuu5yOBwet9vtczgcPofD4SWQBMjpdLodDofL6XS6nE4nARFFUeR7QRAIJIMxAVJVVWPQODNBkiSvLMsDkiTJt23BwMAAjVEURY3H4zFFUaKqqkYJiKqqUU3T6G9UVdW4pmkxAqfrOgMWRdHjdDq9BNDhcPgcDofX5XL5XS6Xz+12+10ul9fpdHoJqMvlcjudTjcBJVOYQOm6rsXjcS0Wi8UjkUiU/kYikWgsFovF4/F4LBaLxePxeDQaZUDWFPT39xMAVVVVWCwWVRQlqihKhEAqihIxmUxRXdf52WQyEQCPKIo+URR9BNDpdPqtVqvfYrH4rFar3263++12u89ms3ltNpvXZrN5rFYrAXWRDgioYU7DnMSUoihsXkVRFPJEJBKJRKPR8O0a6O3tNQNgJlRVNayqaphAEgOSJIUlSQobDLgoK0RR9JEGLBaL32q1+m02m5/AEli73e6z2+0+q9Xqc7lcXrvd7rFarW6r1eq2WCwExk2ZQCDJ75IkhXVdD+u6HtZ1PazrehiYEtZ1XTYykCRJYdu27du3j8YbIAJGbIQJpKqqYUmSwgTSYMFNLJAGHA6H32Kx+G02m5/AElgCabfbfTabzWu1Wj0ul8tjs9k8VqvVbbFY3E6n0+10Ot0EkjQgSVKY7qmqGqbxkiSFVVUNU8FTVTVEz7IsywkNbN682QyAM0BV1RCZkEBKkhTSNC0kSVKIGHA4HD6n0+mzWCw+AksmtNlsXpvN5rXb7R6Xy+UhoFar1W21Wl0EkoAaZg1rmhYi89F4TdNCmqaFVFUNkXkTNZHNZvts27aNxhuNCIEI0T2ZkN4bLLhEUfQaueAjDdhsNi+BJD0QUKfT6SYdEFDKAEmSQoqihOjeAJG4/gQ7W7duzQXwP9cBnNexpjhBAAAAAElFTkSuQmCC" />
    <link rel="icon" type="image/png" sizes="16x16" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAlpJREFUOE+Nk0tIVFEYx//n3jtz544z4zg6OuqoqKVp9LDUMAqKFtGiWtSiWkQkEUEtahERRBQVLYJoUW2iRdCiKKJFD4JqE0REDxQfw1hq5DiOztz5mHvPPeeMjlr2wVl8fN/v+z++c87HsB8f24d9+H8A0un0IcbYUdd1PSLyiIg8z3MJyPU8zyEix/M8h4hcz/McInI9z7WJyLJt27Isy7IsS7YsS7IsS7YsW2aMWbZtW5ZlWaZpEsdxEgghDCGEwRjbBpBKpQ4yxo66rusRkedRwQQgEpHreZ5DRK7neQ4RuZ7nOZ7n2a7rWo7jWLZtW47jyI7jSLZty47jSI7jSJZlSYwxadOvCSGMbQDJZPIQY+yI67qe53ke/QsgItf1XIeIHNd1Hdd1Hc/zbNu2Ldu2ZMexJMexJNu2JcaYtAXAGDM2AWKx2GHG2BEiIiLPIyLPdd0CQK7ruq7ruq5t25Zt25LjOLLjOJLjOJLjOJLjOBJjTNoUlzFmbAJEo9EjjLHDRERE5BERua7rFgByXdd1Xde1bdu2bNuWHceWHceWHMeSHMeSbNuWGGPSFgBjzNgEiEQihxljh4iIiMgjInJd1y0A5Lqu67qu69i2bdm2LTmOLTmOLTmOJTmOJdm2LTHGpC1xGWPGJkA4HD7CGDtIRJ7neUREnut6BYBc13Ud27Yt27Ylx7Elx7Ekx7Ekx7Ek27YlxpgkSRJjjBkbAMFg8BBj7IDruh4ReUREnut6BYBc13Ud27Yt27Ylx7Elx7Ekx7Ek27YlxpgkSRJjjBl/AQgEAgcZY/uJyPsJ8AOvgZzXMm3oPQAAAABJRU5ErkJggg==" />
    
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
            --accent-light: #f0f9ff;
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
            margin-left: 280px;
            padding: 2.5rem;
            width: calc(100% - 280px);
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

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .card-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .card {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 20px var(--card-shadow);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px var(--card-shadow);
        }

        .card h3 {
            font-size: 1rem;
            color: var(--text-color);
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .card p {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
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
            background: var(--accent-light);
        }

        tr:hover {
            background: var(--background-color);
        }

        td {
            font-size: 0.95rem;
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
            .grid {
                grid-template-columns: 1fr;
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

            /* Customer name header */
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

            /* Content rows */
            td:not(:first-child) {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            }

            td:not(:first-child):before {
                content: attr(data-label);
                font-weight: 500;
                color: var(--primary-color);
                min-width: 100px;
            }

            td:last-child {
                border-bottom: none;
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

            td[data-status="Pending"] {
                color: #856404;
            }

            td[data-status="Approved"] {
                color: #155724;
            }

            td[data-status="Completed"] {
                color: #28a745;
            }

            td[data-status="Cancelled"] {
                color: #dc3545;
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

        .modal-backdrop {
            --bs-backdrop-opacity: 0.8;
            --bs-backdrop-bg: #000;
            background-color: var(--bs-backdrop-bg);
            z-index: 1040;
            transition: opacity 0.2s ease-in-out;
        }

        .modal-backdrop.show {
            opacity: var(--bs-backdrop-opacity) !important;
        }

        .modal-backdrop.fade {
            opacity: 0;
        }

        .modal {
            z-index: 1045;
        }

        /* Add transition to modal itself for smoother appearance */
        .modal.fade .modal-dialog {
            transition: transform 0.2s ease-out;
            transform: translateY(-20px);
        }

        .modal.show .modal-dialog {
            transform: translateY(0);
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
        <!-- <a href="admin_register.php" class="<?= basename($_SERVER['PHP_SELF']) === 'admin_register.php' ? 'active' : '' ?>"><i class="fas fa-user-shield"></i> Administrator</a> -->
        <a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main">
    <h1>Welcome Back 👋</h1>

    <div class="grid">
        <a href="admin_bookings.php" class="card-link">
            <div class="card">
                <h3>Total Bookings</h3>
                <p><?php echo $total_bookings; ?></p>
            </div>
        </a>
        <a href="admin_bookings.php?tab=pending" class="card-link">
            <div class="card">
                <h3>Pending Approvals</h3>
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

    <div class="card">
        <h3>Recent Bookings</h3>
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
                        <td data-label="Customer"><?php echo htmlspecialchars($row['user_name'] ?? 'N/A'); ?></td>
                        <td data-label="Service"><?php echo htmlspecialchars($row['service']); ?></td>
                        <td data-label="Assigned Employee"><?php echo $row['employee_name'] ?: 'Unassigned'; ?></td>
                        <td data-label="Status" data-status="<?php echo htmlspecialchars($row['status']); ?>"><?php echo htmlspecialchars($row['status']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
