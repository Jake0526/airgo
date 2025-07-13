<?php
// Start the session to track the user session
session_start();

// Include the database connection
require_once 'db_connection.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the submitted data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Get database connection
    $conn = Database::getConnection();

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
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='20' fill='%2307353f'/%3E%3Cpath d='M30 50c0-11 9-20 20-20s20 9 20 20-9 20-20 20-20-9-20-20zm35 0c0-8.3-6.7-15-15-15s-15 6.7-15 15 6.7 15 15 15 15-6.7 15-15z' fill='%233cd5ed'/%3E%3Cpath d='M50 60c-5.5 0-10-4.5-10-10s4.5-10 10-10 10 4.5 10 10-4.5 10-10 10z' fill='white'/%3E%3C/svg%3E" />
    <link rel="icon" type="image/png" sizes="32x32" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAABKhJREFUWEe9V21sU2UUfu7H3a7dmDAgKgwQPxAkKAYnJsyPGCGK0ZAQFfEHJGpigkT8gYrxB5pI/GD+QBNjNCr+ICYaEzWBRDBRIQQIiCIIKLIvGIPtbrfb7b3XnHPvbVc2trs3bU/S7H7c877Pec95z3veK+AeHOIe4MN/B0CWZVkQhHui3LquX5YkSboXk+u6PqwoCud3HEAkEuH8qqrS0NBQYWxsLDY2NhaNRqNRVVVjmqYpuq4rpHwwGHQHAgG3y+VyezweN/1yu91uQRDcBIr+CoIgCILAf222GfRqgKZpGsdHYDRNm6Xr+llZlr1/CcDQ0BCNjZumxXVdVxVFiWmaFlVVNaooSjQejxPQEUVRYpqmxXRd5+vpuu5yOBwet9vtczgcPofD4SWQBMjpdLodDofL6XS6nE4nARFFUeR7QRAIJIMxAVJVVWPQODNBkiSvLMsDkiTJt23BwMAAjVEURY3H4zFFUaKqqkYJiKqqUU3T6G9UVdW4pmkxAqfrOgMWRdHjdDq9BNDhcPgcDofX5XL5XS6Xz+12+10ul9fpdHoJqMvlcjudTjcBJVOYQOm6rsXjcS0Wi8UjkUiU/kYikWgsFovF4/F4LBaLxePxeDQaZUDWFPT39xMAVVXVWCwWVRQlqihKhEAqihIxmUxRXdf52WQyEQCPKIo+URR9BNDpdPqtVqvfYrH4rFar3263++12u89ms3ltNpvXZrN5rFYrAXWRDgioYU7DnMSUoihsXkVRFPJEJBKJRKPR8O0a6O3tNQNgJlRVNayqaphAEgOSJIUlSQobDLgoK0RR9JEGLBaL32q1+m02m5/AEli73e6z2+0+q9Xqc7lcXrvd7rFarW6r1eq2WCwExk2ZQCDJ75IkhXVdD+u6HtZ1PazrehiYEtZ1XTYykCRJYdu27du3j8YbIAJGbIQJpKqqYUmSwgTSYMFNLJAGHA6H32Kx+G02m5/AElgCabfbfTabzWu1Wj0ul8tjs9k8VqvVbbFY3E6n0+10Ot0EkjQgSVKY7qmqGqbxkiSFVVUNU8FTVTVEz7IsywkNbN682QyAM0BV1RCZkEBKkhTSNC0kSVKIGHA4HD6n0+mzWCw+AksmtNlsXpvN5rXb7R6Xy+UhoFar1W21Wl0EkoAaZg1rmhYi89F4TdNCmqaFVFUNkXkTNZHNZvts27aNxhuNCIEI0T2ZkN4bLLhEUfQaueAjDdhsNi+BJD0QUKfT6SYdEFDKAEmSQoqihOjeAJG4/gQ7W7duzQXwP9cBnNexpjhBAAAAAElFTkSuQmCC" />
    <link rel="icon" type="image/png" sizes="16x16" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAlpJREFUOE+Nk0tIVFEYx//n3jtz544z4zg6OuqoqKVp9LDUMAqKFtGiWtSiWkQkEUEtahERRBQVLYJoUW2iRdCiKKJFD4JqE0REDxQfw1hq5DiOztz5mHvPPeeMjlr2wVl8fN/v+z++c87HsB8f24d9+H8A0un0IcbYUdd1PSLyiIg8z3MJyPU8zyEix/M8h4hcz/McInI9z7WJyLJt27Isy7IsS7YsS7IsS7YsW2aMWbZtW5ZlWaZpEsdxEgghDCGEwRjbBpBKpQ4yxo66rusRkedRwQQgEpHreZ5DRK7neQ4RuZ7nOZ7n2a7rWo7jWLZtW47jyI7jSLZty47jSI7jSJZlSYwxadOvCSGMbQDJZPIQY+yI67qe53ke/QsgItf1XIeIHNd1Hdd1Hc/zbNu2Ldu2ZMexJMexJNu2JcaYtAXAGDM2AWKx2GHG2BEiIiLPIyLPdd0CQK7ruq7ruq5t25Zt25LjOLLjOJLjOJLjOJLjOBJjTNoUlzFmbAJEo9EjjLHDRERE5BERua7rFgByXdd1Xde1bdu2bNuWHceWHceWHMeSHMeSbNuWGGPSFgBjzNgEiEQihxljh4iIiMgjInJd1y0A5Lqu67qu69i2bdm2LTmOLTmOLTmOJTmOJdm2LTHGpC1xGWPGJkA4HD7CGDtIRJ7neUREnut6BYBc13Ud27Yt27Ylx7Elx7Ekx7Ekx7Ek27YlxpgkSRJjjBkbAMFg8BBj7IDruh4ReUREnut6BYBc13Ud27Yt27Ylx7Elx7Ekx7Ek27YlxpgkSRJjjBl/AQgEAgcZY/uJyPsJ8AOvgZzXMm3oPQAAAABJRU5ErkJggg==" />
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@400;700;900&display=swap" rel="stylesheet">
    <!-- Header styles -->
    <link rel="stylesheet" href="styles/header.css">
    <style>
        /* Root variables are now in header.css */
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

        input[type="text"], 
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 0.875rem 1rem;
            border-radius: 12px;
            border: 2px solid var(--card-bg);
            font-size: 16px; /* Prevents iOS zoom */
            transition: all 0.3s ease;
            background: white;
            color: var(--text-color);
            font-family: 'Poppins', sans-serif;
            -webkit-appearance: none; /* Removes iOS default styling */
            appearance: none;
            box-sizing: border-box;
            max-width: 100%;
        }

        input[type="text"]:focus, 
        input[type="password"]:focus,
        input[type="email"]:focus {
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
            -webkit-appearance: none; /* Removes iOS default styling */
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

        .register-area {
            margin-top: 2rem;
            text-align: center;
            font-weight: 500;
            font-size: 0.95rem;
            color: var(--text-color);
        }

        .register-area a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
            padding-bottom: 2px;
        }

        .register-area a:hover {
            color: var(--secondary-color);
            border-bottom-color: var(--secondary-color);
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

            input[type="text"], 
            input[type="password"],
            input[type="email"] {
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
                <a href="index.php">
        Home
                    <span class="btn-icon">→</span>
      </a>
    </div>
  </nav>
</header>

    <!-- Login Form Section -->
    <section id="login">
        <div class="login-wrapper">
            <h2>Welcome Back</h2>

            <?php if (isset($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST" autocomplete="off">
                <div class="form-group">
                <label for="email">Email</label>
                    <input type="text" id="email" name="email" required placeholder="Enter your email" />
                </div>

                <div class="form-group">
                <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password" />
                </div>

                <button type="submit">
                    Login
                    <span class="btn-icon">→</span>
                </button>
            </form>

            <div class="register-area">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </section>

</body>
</html>
