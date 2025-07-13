<?php    
session_start();

require_once '../config/database.php';
$conn = Database::getConnection();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error_message = "Both fields are required.";
    } else {
        $sql = "SELECT * FROM user WHERE username = ?";
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
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_username'] = $user['username'];
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirGo User Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color:  #4c7273;
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            color: #333;
        }

        header {
            background-color: #07353f;
            padding: 10px 0;
            text-align: left ;
            color: #CACBBB;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
            padding-left: 2in;
        }

        header h1 {
            margin: 0;
            font-size: 2em;
            font-weight: 600;
        }

        #login {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px 5px;
        }

        .login-form {
    background: #CACBBB;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2),
                0 0 15px rgba(255, 255, 255, 0.8); /* Added white shadow */
    width: 90%;
    max-width: 250px;
    text-align: center;
    animation: fadeIn 0.8s ease-in-out;
}

        }

        .login-form h2 {
            margin-bottom: 10px;
            color: #07353f;
            font-weight: 600;
        }

        .error {
            color: red;
            background-color: #ffe6e6;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 8px;
            font-weight: bold;
        }

        .login-form label {
            display: block;
            text-align: left;
            margin-top: 10px;
            font-weight: 400;
        }

        .login-form input {
            width: 90%;
            padding: 5px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1em;
        }

        .login-form button {
            width: 70%;
            padding: 10px;
            margin-top: 25px;
            background-color: #07353f;
            border: none;
            border-radius: 8px;
            font-size: 1.1em;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .login-form button:hover {
            background-color: skyblue;
            color: #07353f;
        }

        .login-form a {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: #07353f;
            font-size: 0.9em;
            transition: 0.3s;
        }

        .login-form a:hover {
            text-decoration: underline;
            color:  #CACBBB;
        }

        footer {
            background-color: #07353f;
            color: skyblue;
            text-align: center;
            padding: 15px 10px;
            font-size: 0.9em;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <header>
        <h1>AirGo</h1>
		<div style="display: flex; justify-content: flex-end;">
		<a href="index.php" class="btn-Home" style="color: #07353f; background-color: #d0f0ff; padding: 2px 8px; border-radius: 12px; font-weight: 600; font-family: 'Poppins', sans-serif; text-transform: uppercase; letter-spacing: 1.2px; text-decoration: none; overflow: hidden; display: inline-block; transition: background-color 0.3s ease;">
        Home
		</a>
		</div>
    </header>
	
  <!-- Header -->
<header style="background: #07353f; padding: 10px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
  <nav class="container" style="display: flex; justify-content: space-between; align-items: center; max-width: 1100px; margin: 0 auto; padding: 0 24px;">
    <div class="logo">
      <h1 style="
        color: #d0f0ff; 
        font-family: 'Playfair Display', Georgia, serif; 
        font-weight: 100; 
        font-style: normal; 
        letter-spacing: 3px; 
        font-size: 1.9rem; 
        margin: 0; 
        cursor: default;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        text-transform: uppercase;
        ">
    </header>
    </div>
   </nav>
</header>
    <section id="login">
        <div class="login-form">
            <h2>Login</h2>

            <!-- Show error message directly under the title -->
            <?php if (!empty($error_message)) : ?>
                <p class="error"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <label for="username">Username</label>
                <input type="text" id="username" name="username">

                <label for="password">Password</label>
                <input type="password" id="password" name="password">
				<div class="password-area">
                <p>Reset Your Password? <a href="reset-password.php">Password reset here</a></p>
            </div>

                <button type="submit">Login</button>
			<div class="register-area">
                <p>Already have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
		
    </section>
</body>
</html>

<?php
$conn->close();
?>