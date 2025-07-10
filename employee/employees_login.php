<?php
// Include the database connection
include('../db_connection.php');

// Start the session to track the user session
session_start();

$error_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = trim($_POST['employee_id']);
    $password = $_POST['password'];

    if (empty($employee_id) || empty($password)) {
        $error_message = "All fields are required.";
    } else {
        $sql = "SELECT * FROM employees WHERE id = ? AND status = 'Active'";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("i", $employee_id);
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AirGo - Employee Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/header.css">
    <style>
        /* Root variables are now in header.css */
        :root {
            --primary-color: #07353f;
            --secondary-color: #3cd5ed;
            --background-color: #d0f0ff;
            --text-color: #344047;
            --card-bg: #e9f0f1;
            --card-shadow: rgba(7, 53, 63, 0.1);
            --spacing-unit: clamp(0.5rem, 2vw, 1rem);
        }

        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--background-color), #fff);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        /* Main Section */
        #login {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
            min-height: calc(100vh - 80px);
            width: 100%;
            margin: 0 auto;
        }

        .login-wrapper {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px var(--card-shadow);
            width: min(90%, 400px);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin: 0 auto;
            box-sizing: border-box;
            position: relative;
            top: -40px;
        }

        .login-wrapper h2 {
            font-size: clamp(1.5rem, 3vw, 1.8rem);
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 2rem;
            text-align: center;
            font-family: 'Playfair Display', serif;
            position: relative;
        }

        .login-wrapper h2::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--secondary-color);
            border-radius: 2px;
        }

        .form-group {
            margin-bottom: 1.5rem;
            width: 100%;
        }

        label {
            font-weight: 500;
            color: var(--text-color);
            display: block;
            margin-bottom: 0.75rem;
            font-size: 1rem;
        }

        input[type="number"],
        input[type="password"] {
            width: 100%;
            padding: 0.875rem 1rem;
            border-radius: 12px;
            border: 2px solid var(--card-bg);
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
            color: var(--text-color);
            font-family: 'Poppins', sans-serif;
            -webkit-appearance: none;
            appearance: none;
            box-sizing: border-box;
            max-width: 100%;
        }

        input[type="number"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 4px rgba(60, 213, 237, 0.1);
        }

        input::placeholder {
            color: #a0aec0;
            opacity: 1;
        }

        button {
            width: 100%;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-family: 'Poppins', sans-serif;
            -webkit-appearance: none;
            appearance: none;
            margin-top: 1rem;
        }

        button:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px var(--card-shadow);
        }

        button:active {
            transform: translateY(0);
            box-shadow: 0 5px 10px var(--card-shadow);
        }

        .error {
            background: #fee;
            color: #e44;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .error::before {
            content: '⚠️';
        }

        @media (max-width: 480px) {
            #login {
                padding: 0;
                align-items: center;
            }

            .login-wrapper {
                width: 90%;
                padding: 1.5rem;
                border-radius: 16px;
                margin: 0 auto;
                top: -20px;
            }

            input[type="number"],
            input[type="password"] {
                padding: 0.875rem;
                font-size: 16px;
                width: 100%;
            }

            .form-group {
                margin-bottom: 1.25rem;
                width: 100%;
            }

            button {
                padding: 0.875rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="container flex flex-center">
            <div class="logo">
                <h1>Air<span>go</span></h1>
            </div>
            <div class="header-button">
                <a href="../index.php">
                    Home
                    <span class="btn-icon">→</span>
                </a>
            </div>
        </nav>
    </header>

    <!-- Login Form Section -->
    <section id="login">
        <div class="login-wrapper">
            <h2>Employee Login</h2>

            <?php if (!empty($error_message)): ?>
                <div class="error"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <form method="POST" autocomplete="off">
                <div class="form-group">
                    <label for="employee_id">Employee ID</label>
                    <input type="number" id="employee_id" name="employee_id" required placeholder="Enter your employee ID">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>

                <button type="submit">
                    Login
                    <span class="btn-icon">→</span>
                </button>
            </form>
        </div>
    </section>
</body>
</html>
