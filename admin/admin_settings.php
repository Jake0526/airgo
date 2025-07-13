<?php
session_start();

require_once '../config/database.php';
$conn = Database::getConnection();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = ''; // Initialize error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and validate
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error_message = "Both fields are required.";
    } else {
        // Query to fetch the admin user from the database
        $sql = "SELECT * FROM admin WHERE username = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error); // Handle query preparation error
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // User exists, fetch user data
            $user = $result->fetch_assoc();
            
            // Check if the password matches directly without hashing
            if ($password === $user['password']) { 
                // Password is correct, start session and log in
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $user['username'];
                header("Location: admin_dashboard.php"); // Redirect to the admin dashboard
                exit();
            } else {
                // Password is incorrect
                $error_message = "Invalid username or password.";
            }
        } else {
            // Username not found
            $error_message = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>

    <!-- Embedded CSS -->
    <style>
        /* Reset default browser styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body and general page styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Main login container */
        .login-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        /* Heading style */
        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        /* Form inputs */
        .login-container form div {
            margin-bottom: 20px;
        }

        /* Label styles */
        .login-container label {
            font-size: 16px;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }

        /* Input field styles */
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #fafafa;
        }

        /* Submit button styles */
        .login-container button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .login-container button:hover {
            background-color: #45a049;
        }

        /* Error message styles */
        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>

</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        
        <?php
        // Display error message if login failed
        if (!empty($error_message)) {
            echo "<p class='error-message'>$error_message</p>";
        }
        ?>

        <form action="admin.php" method="POST">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div>
                <button type="submit">Login</button>
            </div>
        </form>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>