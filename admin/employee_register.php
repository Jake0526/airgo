 <?php
// Start the session (ensure the user is logged in as admin)
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php"); // Redirect to login if not logged in as admin
    exit();
}

require_once '../config/database.php';
$conn = Database::getConnection();

$message = "";

// Handle employee form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_employee'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $position = mysqli_real_escape_string($conn, $_POST['position']);
    $hire_date = mysqli_real_escape_string($conn, $_POST['hire_date']);
    $status = mysqli_real_escape_string($conn, $_POST['status']); // Capture status
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate passwords match
    if ($password !== $confirm_password) {
        $message = "Error: Password and Confirm Password do not match.";
    } else {
        // Hash the password before storing
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insert employee including password
        $sql_insert = "INSERT INTO employees (name, email, position, hire_date, status, password) 
                       VALUES ('$name', '$email', '$position', '$hire_date', '$status', '$password_hash')";

        if ($conn->query($sql_insert) === TRUE) {
            $message = "Employee added successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

// Handle delete request via AJAX
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Query to delete employee
    $sql_delete = "DELETE FROM employees WHERE id = '$delete_id'";

    if ($conn->query($sql_delete) === TRUE) {
        echo "Employee deleted successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
    exit; // Exit after deletion to prevent further code execution
}

// Query to get all employees
$sql_employees = "SELECT * FROM employees";
$result_employees = $conn->query($sql_employees);

// Fetch employees data
$employees = [];
while ($row = $result_employees->fetch_assoc()) {
    $employees[] = $row;
}

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin - Employees</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet" />
    <style>
        /* General Styles */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #07353f;
            display: flex;
            height: 100vh;
        }
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: #07353f;
            color: white;
            padding-top: 20px;
            position: fixed;
            height: 100%;
            left: 0;
            top: 0;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 15px 20px;
            text-decoration: none;
            font-size: 1.1em;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease, padding-left 0.3s ease;
        }
        .sidebar a:hover {
            background-color: skyblue;
            padding-left: 25px;
        }
        .sidebar .active {
            background-color: skyblue;
        }
        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
            background-color: #CACBBB;
            height: 100%;
        }
        h1 {
            font-size: 2em;
            color: #333;
        }
        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            table-layout: fixed;
            border: 3px solid black;
        }
        th, td {
            text-align: left;
            padding: 12px;
            border: 1px solid #ddd;
            word-wrap: break-word;
        }
        th {
            background-color: #CACBBB;
            width: 16%;
        }
        /* Form Styles */
        .form-container {
            background-color: #CACBBB;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            margin-right: 10px;
            margin-bottom: 30px;
            border: 3px solid black;
        }
        .form-container input, .form-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        .form-row input, .form-row select {
            width: 48%;
        }
        .form-container button {
            padding: 10px 20px;
            background-color: #07353f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: skyblue;
        }
        /* Message */
        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        /* Button Styles */
        .action-button {
            padding: 5px 15px;
            color: white;
            background-color: #07353f;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            border: 3px solid black;
        }
        .action-button:hover {
            background-color: skyblue;
        }
        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            .main-content {
                margin-left: 200px;
            }
        }
        @media (max-width: 600px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="dashboard.php">Dashboard</a>
        <a href="admin_bookings.php">Bookings</a>
        <a href="admin_employees.php" class="active">Employees</a>
        <a href="admin_booking_history.php">Booking History</a>
        <a href="index.php">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Employees List</h1>

        <!-- Success/Error Message -->
        <?php if (!empty($message)): ?>
            <div class="message <?php echo (strpos($message, 'successfully') !== false) ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Add Employee Form -->
        <div class="form-container">
            <h2>Add New Employee</h2>
            <form method="POST" action="admin_employees.php">
                <div class="form-row">
                    <input type="text" name="name" placeholder="Employee Name" required />
                    <input type="email" name="email" placeholder="Employee Email" required />
                </div>
                <div class="form-row">
                    <input type="text" name="position" placeholder="Position" required />
                    <input type="date" name="hire_date" placeholder="Hire Date" required />
                </div>
                <div class="form-row">
                    <select name="status" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>

        <!-- Employees Table -->
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Position</th>
                    <th>Hire Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $employee): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($employee['name']); ?></td>
                        <td><?php echo htmlspecialchars($employee['email']); ?></td>
                        <td><?php echo htmlspecialchars($employee['position']); ?></td>
                        <td><?php echo htmlspecialchars($employee['hire_date']); ?></td>
                        <td><?php echo htmlspecialchars($employee['status']); ?></td>
                        <td>
                            <a href="edit_employee.php?id=<?php echo $employee['id']; ?>">
                                <butt on class="action-button">Edit</button>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>  