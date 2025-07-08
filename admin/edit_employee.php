<?php
// Start the session (ensure the user is logged in as admin)
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "airgo");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the employee's data
$employee = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM employees WHERE id = $id");

    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
    } else {
        echo "Employee not found!";
        exit();
    }
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_employee'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $position = $conn->real_escape_string($_POST['position']);
    $hire_date = $conn->real_escape_string($_POST['hire_date']);
    $status = $conn->real_escape_string($_POST['status']);

    $sql_update = "UPDATE employees SET name = '$name', email = '$email', position = '$position', hire_date = '$hire_date', status = '$status' WHERE id = $id";
    $message = ($conn->query($sql_update) === TRUE) ? "Employee updated successfully!" : "Error: " . $conn->error;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Employee</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #4c7273;
        }

        .form-container {
            background-color: #CACBBB;
            padding: 20px;
            margin: 100px auto;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 30px 30px white;
            border: 3px solid  #07353f;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            flex-direction: row;
            gap: 10px;
            margin-bottom: 15px;
        }

        .form-row input,
        .form-row select {
            flex: 1;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        button {
            padding: 10px 20px;
            background-color: #07353f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }

        button:hover {
            background-color: #CACBBB;
            color: #07353f;
        }

        .message {
            text-align: center;
            margin-bottom: 20px;
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

        .button-group {
            text-align: center;
            margin-top: 20px;
        }

        @media (max-width: 600px) {
            .form-row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Edit Employee Information</h2>

        <?php if (isset($message)): ?>
            <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="edit_employee.php?id=<?php echo $employee['id']; ?>">
            <div class="form-row">
                <input type="text" name="name" placeholder="Employee Name" value="<?php echo $employee['name']; ?>" required>
                <input type="email" name="email" placeholder="Employee Email" value="<?php echo $employee['email']; ?>" required>
            </div>
            <div class="form-row">
                <input type="text" name="position" placeholder="Position" value="<?php echo $employee['position']; ?>" required>
                <input type="text" name="hire_date" placeholder="Hire Date (YYYY-MM-DD)" value="<?php echo $employee['hire_date']; ?>" required>
            </div>
            <div class="form-row">
                <select name="status" required>
                    <option value="Active" <?php echo $employee['status'] == 'Active' ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?php echo $employee['status'] == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="button-group">
                <button type="submit" name="update_employee">Update</button>
                <a href="admin_employees.php"><button type="button">Go Back</button></a>
            </div>
        </form>
    </div>
</body>
</html>
