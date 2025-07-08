<?php
session_start();
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "airgo";
$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$error_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = trim($_POST['employee_id']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($employee_id) || empty($email) || empty($password)) {
        $error_message = "All fields are required.";
    } else {
        $sql = "SELECT * FROM employees WHERE id = ? AND email = ? AND status = 'Active'";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("is", $employee_id, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                $_SESSION['employee_logged_in'] = true;
                $_SESSION['employee_id'] = $user['id'];
                $_SESSION['employee_name'] = $user['name'];
                $_SESSION['employee_email'] = $user['email'];
                $_SESSION['employee_position'] = $user['position'];
                header("Location: employee_dashboard.php");
                exit();
            } else {
                $error_message = "Invalid credentials.";
            }
        } else {
            $error_message = "Invalid credentials or inactive account.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AirGo - Employee Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #4c7273, #07353f);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(15px);
            padding: 30px 20px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 40%;
            max-width: 300px;
            animation: fadeIn 0.6s ease-in-out;
            font-family: 'Poppins', sans-serif;
            color: #07353f; /* All text default to this color */
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
            color: #07353f; /* Heading color */
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #07353f; /* Label color */
            font-size: 14px;
        }

        input[type="number"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            margin-bottom: 20px;
            background-color: rgba(255,255,255,0.7);
            font-size: 14px;
            transition: box-shadow 0.3s;
        }

        input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.4);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #07353f;
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #4c7273;
        }

        .error {
            text-align: center;
            background-color: rgba(255, 0, 0, 0.1);
            color: #ff0000;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Employee Login</h2>
        <?php if (!empty($error_message)): ?>
            <div class="error"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        <form method="POST">
            <label for="employee_id">Employee ID</label>
            <input type="number" name="employee_id" id="employee_id" required>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
