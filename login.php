<?php  
// Include the database connection
include('db_connection.php');

// Start the session to track the user session
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the submitted data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the SQL query to find the user by email
    $sql = "SELECT * FROM user WHERE email = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("s", $email);

        // Execute the query
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Check if a user is found
        if ($result->num_rows > 0) {
            // Fetch user data
            $user = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Password is correct, start the session and redirect
                $_SESSION['user_id'] = $user['id']; // Store user ID in session
                $_SESSION['email'] = $email; // Store username in session
                header("Location: dashboard.php"); // Redirect to dashboard.php
                exit();
            } else {
                // Password is incorrect
                $error = "Invalid password.";
            }
        } else {
            // Username not found
            $error = "Invalid username.";
        }

        // Close the statement
        $stmt->close();
    } else {
        $error = "Error preparing statement: " . $conn->error;
    }

    // Close the connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Airgo</title>
    <style>
        /* Reset */
        * {
            margin: 0; 
            padding: 0; 
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color:  #d0f0ff;
            color: #07353f;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Header */
        header {
            background-color: #07353f;
            padding: 8px 20px;
            box-shadow: 0 2px 8px rgba(7, 53, 63, 0.8);
        }

        header nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo h1 {
            font-weight: 100;
            font-size: 1.5rem;
            color:  #d0f0ff;
            letter-spacing: 1px;
        }

        .nav-link a {
            background-color:  #d0f0ff;
            padding: 12px 65px;
            border-radius: 25px;
            font-weight: 300;
            color: #07353f;
            box-shadow: 0 4px 6px rgba(202, 203, 187, 0.6);
            transition: background-color 0.25s ease, color 0.25s ease, box-shadow 0.25s ease;
            display: inline-block;
        }

        .nav-link a:hover {
            background-color: #07353f;
            color:  #d0f0ff;
            box-shadow: 0 6px 12px rgba(7, 53, 63, 0.8);
        }

        /* Main Section */
        #login {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 0;
        }

        .login-wrapper {
            background:  #d0f0ff;
            padding: 10px 60px;
            border-radius: 18px;
            box-shadow: 0 12px 35px rgba(7, 53, 63, 0.2);
            width: 330px;
            text-align: center;
            border: 2.5px solid #07353f;
        }

        .login-wrapper h2 {
            font-size: 1.2rem;
            font-weight: 500;
            color: #07353f;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        label {
            font-weight: 300;
            color: #07353f;
            display: block;
            margin-bottom: 8px;
            text-align: left;
            font-size: 0.95rem;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 12px;
            border: 2px solid #07353f;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            background:  #d0f0ff;
            color: #07353f;
        }

        input[type="text"]:focus, input[type="password"]:focus {
            outline: none;
            border-color:  #d0f0ff;
            box-shadow: 0 0 8px  #d0f0ff;
            background: #fff;
        }

        button {
            width: 100%;
            background-color: #07353f;
            color:  #d0f0ff;
            border: none;
            padding: 14px 0;
            border-radius: 25px;
            font-size: 1.15rem;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
            box-shadow: 0 6px 12px rgba(7, 53, 63, 0.3);
        }

        button:hover {
            background-color: #d0f0ff;
            color: #07353f;
            box-shadow: 0 8px 18px rgba(202, 203, 187, 0.7);
        }

        .register-area {
            margin-top: 24px;
            font-weight: 500;
            font-size: 0.95rem;
            color: #07353f;
        }

        .register-area a {
            color: #07353f;
            font-weight: 500;
            text-decoration: underline;
            transition: color 0.2s ease;
        }

        .register-area a:hover {
            color: #CACBBB;
            background-color: #07353f;
            padding: 2px 6px;
            border-radius: 4px;
            text-decoration: none;
        }

        .error {
            margin-bottom: 15px;
            color: #cc0000;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-size: 0.9rem;
        }

        /* Footer */
        footer {
            background-color: #07353f;
            text-align: center;
            padding: 18px 10px;
            color: #CACBBB;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: inset 0 2px 6px rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>

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
        Airgo
      </h1>
    </div>
    <div class="Home-button">
      <a href="index.php" class="btn-Home" style="position: relative; color: #07353f; background-color:  #d0f0ff; padding: 10px 26px; border-radius: 25px; font-weight: 600; font-family: 'Poppins', sans-serif; text-transform: uppercase; letter-spacing: 1.2px; text-decoration: none; overflow: hidden; display: inline-block; transition: background-color 0.3s ease;">
        Home
        <span class="underline"></span>
      </a>
    </div>
  </nav>
</header>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@600&family=Playfair+Display:wght@900&display=swap');

  .btn-Home {
    cursor: pointer;
  }

  .btn-Home .underline {
    position: absolute;
    bottom: 8px;
    left: 20%;
    width: 60%;
    height: 3px;
    background-color: #07353f;
    border-radius: 2px;
    transform: scaleX(0);
    transform-origin: center;
    transition: transform 0.35s ease;
  }

  .btn-Home:hover {
    background-color: #d6cec6;
    color: #07353f;
  }

  .btn-Home:hover .underline {
    transform: scaleX(1);
  }
</style>


    <!-- Login Form Section -->
    <section id="login">
        <div class="login-wrapper">
            <h2>Login to Your Account</h2>

            <?php if (isset($error)): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form action="login.php" method="POST" autocomplete="off">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" required />

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required />

                <button type="submit">Login</button>
            </form>

            <div class="register-area">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </section>

</body>
</html>
