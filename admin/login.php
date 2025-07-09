<?php    
// Include the database connection
include('../db_connection.php');

// Start the session
session_start();

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error_message = "Both fields are required.";
    } else {
        $sql = "SELECT * FROM admin WHERE username = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if ($password === $user['password']) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $user['username'];
                header("Location: dashboard.php");
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Login - Airgo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #07353f;
            --secondary-color: #3cd5ed;
            --background-color: #d0f0ff;
            --text-color: #344047;
            --card-bg: #e9f0f1;
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: var(--primary-color);
            padding: var(--spacing-unit);
            box-shadow: 0 2px 10px var(--card-shadow);
        }

        header h1 {
            color: white;
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.5rem, 3vw, 2rem);
            text-align: center;
        }

        header h1 span {
            color: var(--secondary-color);
            font-style: italic;
        }

        #login {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: clamp(2rem, 5vw, 4rem) 0;
        }

        .login-form {
            background: rgba(255, 255, 255, 0.9);
            padding: clamp(2rem, 5vw, 3rem);
            border-radius: 20px;
            box-shadow: 0 20px 40px var(--card-shadow);
            width: min(90%, 400px);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .login-form h2 {
            font-size: clamp(1.5rem, 3vw, 1.8rem);
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            text-align: center;
            font-family: 'Playfair Display', serif;
            position: relative;
        }

        .login-form h2::after {
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
        }

        label {
            font-weight: 500;
            color: var(--text-color);
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        input[type="text"], 
        input[type="password"] {
            width: 100%;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            border: 2px solid var(--card-bg);
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
            color: var(--text-color);
            font-family: 'Poppins', sans-serif;
        }

        input[type="text"]:focus, 
        input[type="password"]:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 4px rgba(60, 213, 237, 0.1);
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
        }

        button:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px var(--card-shadow);
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
    </style>
</head>
<body>
    <header>
        <h1>Air<span>go</span> Admin</h1>
    </header>

    <section id="login">
        <div class="login-form">
            <h2>Admin Login</h2>

            <?php if (!empty($error_message)) : ?>
                <div class="error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit">Login</button>
            </form>
        </div>
    </section>
</body>
</html>
