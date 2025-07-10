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

// Get unique positions for dropdown
$sql_positions = "SELECT DISTINCT position FROM employees ORDER BY position";
$result_positions = $conn->query($sql_positions);
$positions = [];
while ($row = $result_positions->fetch_assoc()) {
    $positions[] = $row['position'];
}

// FETCH EMPLOYEES with filters
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
$sql_employees .= " ORDER BY id DESC";

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
            .main {
                margin-left: 0;
                width: 100%;
                padding: 1.5rem;
            }
            .form-row {
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
        <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="admin_bookings.php"><i class="fas fa-calendar-alt"></i> Bookings</a>
        <a href="admin_employees.php"><i class="fas fa-users"></i> Employees</a>
        <a href="booking_history.php"><i class="fas fa-history"></i> Booking History</a>
        <a href="admin_register.php"><i class="fas fa-user-shield"></i> Administrator</a>
        <a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
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
            <form method="POST">
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
                            <td><?= htmlspecialchars($employee['id'] ?? '') ?></td>
                            <td><?= htmlspecialchars($employee['name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($employee['email'] ?? '') ?></td>
                            <td><?= htmlspecialchars($employee['position'] ?? '') ?></td>
                            <td><?= htmlspecialchars($employee['hire_date'] ?? '') ?></td>
                            <td><?= htmlspecialchars($employee['status'] ?? '') ?></td>
                            <td class="action-buttons">
                                <a href="employee_bookings.php?employee_id=<?= $employee['id'] ?? '' ?>" class="action-button view-btn">
                                    <i class="fas fa-calendar-check"></i> Bookings
                                </a>
                                <a href="edit_employee.php?id=<?= $employee['id'] ?? '' ?>" class="action-button edit-btn">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="admin_employees.php?delete_id=<?= $employee['id'] ?? '' ?>" 
                                   class="action-button delete-btn" 
                                   onclick="return confirm('Are you sure you want to delete this employee?');">
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
    </div>

    <script>
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
