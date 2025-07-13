<?php
session_start();

// Ensure the user is logged in as admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';
$conn = Database::getConnection();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = ''; 
$success_message = ''; 

// Get current admin details
$admin_username = $_SESSION['admin_username'];
$sql = "SELECT * FROM admin WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admin_username);
$stmt->execute();
$result = $stmt->get_result();
$admin_data = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $name = $_POST['name'];

    // Check old password
    if (password_verify($old_password, $admin_data['password'])) {
        if (!empty($new_password) && $new_password === $confirm_password) {
            // Update password
            $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);
            $update_sql = "UPDATE admin SET name = ?, password = ? WHERE username = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("sss", $name, $new_password_hashed, $admin_username);

            if ($update_stmt->execute()) {
                $success_message = "Settings updated successfully.";
            } else {
                $error_message = "Error updating settings. Please try again.";
            }
        } else {
            $error_message = "New password and confirm password must match.";
        }
    } else {
        $error_message = "Incorrect old password.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        /* General Styles */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
            display: flex;
            height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
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
            background-color: #3498db;
            padding-left: 25px;
        }

        .sidebar .active {
            background-color: #3498db;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
            background-color: #fff;
            height: 100%;
            overflow-y: auto;
        }

        h1 {
            font-size: 2.2em;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Form Styling */
        .form-container {
            max-width: 650px;
            margin: 0 auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            border: 1px solid #eee;
        }

        .form-container h2 {
            font-size: 1.8em;
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }

        .form-container input {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            font-size: 1em;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            background-color: #f9f9f9;
            transition: border 0.3s ease;
        }

        .form-container input:focus {
            border-color: #3498db;
            outline: none;
        }

        .form-container button {
            width: 100%;
            padding: 15px;
            background-color: #3498db;
            color: white;
            font-size: 1.2em;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-container button:hover {
            background-color: #2980b9;
        }

        .error-message {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.1em;
        }

        .success-message {
            color: #2ecc71;
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.1em;
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .main-content {
                margin-left: 200px;
            }

            .form-container {
                padding: 25px;
                max-width: 90%;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="admin_bookings.php">Bookings</a>
        <a href="admin_employees.php">Employees</a>
        <a href="admin_settings.php" class="active">Settings</a>
        <a href="index.php">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Update Admin Settings</h1>

        <!-- Form Container -->
        <div class="form-container">
            <?php
            if (!empty($error_message)) {
                echo "<p class='error-message'>$error_message</p>";
            }
            if (!empty($success_message)) {
                echo "<p class='success-message'>$success_message</p>";
            }
            ?>

            <form action="admin_settings.php" method="POST">
                <h2>Update Name and Password</h2>

                <input type="text" name="name" value="<?php echo htmlspecialchars($admin_data['name']); ?>" placeholder="New Name" required>
                <input type="password" name="old_password" placeholder="Old Password" required>
                <input type="password" name="new_password" placeholder="New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                <button type="submit">Update Settings</button>
            </form>
        </div>
    </div>
</body>
</html>