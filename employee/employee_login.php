<?php 
session_start();

// DB Connection
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "airgo";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$error_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error_message = "Both fields are required.";
    } else {
        $sql = "SELECT * FROM employee WHERE username = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) die("Error preparing statement: " . $conn->error);

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if ($password === $user['password']) {
                $_SESSION['employee_logged_in'] = true;
                $_SESSION['employee_username'] = $user['username'];
                header("Location: employee_dashboard.php");
                exit();
            } else {
                $error_message = "Invalid username or password.";
            }
        } else {
            $error_message = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AirGo | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0; padding: 0; box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #CACBBB, #CACBBB);
            height: 90vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: #07353f;
            padding: 20px 2in;
            color: #CACBBB;
            font-size: 1.8rem;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .login-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            background: #CACBBB;
            padding: 30px 35px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 300px;
            animation: fadeIn 0.6s ease-in-out;
        }

        .login-box h2 {
            text-align: center;
            color: #07353f;
            margin-bottom: 15px;
        }

        .error-message {
            background-color: #ffe6e6;
            color: red;
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
            border-radius: 6px;
            font-weight: bold;
        }

        .login-box label {
            display: block;
            margin: 12px 0 5px;
            color: #333;
        }

        .login-box input {
            width: 90%;
            padding: 8px 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
            transition: 0.3s ease;
        }

        .login-box input:focus {
            outline: none;
            border-color: #07353f;
            box-shadow: 0 0 0 3px rgba(7, 53, 63, 0.15);
        }

        .login-box button {
            width: 50%;
            padding: 6px;
            margin-top: 20px;
            background-color: #07353f;
            color: white;
            font-size: 14px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .login-box button:hover {
            background-color: skyblue;
            color: #07353f;
        }

        .forgot-password {
            margin-top: 10px;
            text-align: center;
        }

        .forgot-password a {
            text-decoration: none;
            font-size: 0.9rem;
            color: #07353f;
            transition: color 0.3s;
        }

        .forgot-password a:hover {
            color: skyblue;
        }

        footer {
            background-color: #07353f;
            color: skyblue;
            text-align: center;
            padding: 20px;
            font-size: 0.9rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 500px) {
            header {
                padding: 20px;
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <header>AirGo Employee</header>

    <div class="login-wrapper">
        <div class="login-box">
            <h2>Login</h2>

            <?php if (!empty($error_message)) : ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" action="employee_login.php">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <div style="text-align: center;">
                    <button type="submit">Login</button>
                </div>
            </form>


</body>
</html>

<?php $conn->close(); ?>
