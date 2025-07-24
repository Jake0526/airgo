<?php
session_start();

// Check if user has a reset email in session
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

// Include the database connection
require_once 'config/database.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $otp = trim($_POST['otp']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['reset_email'];
    
    // Validate password strength
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>])[A-Za-z\d!@#$%^&*(),.?":{}|<>]{12,}$/', $new_password)) {
        $error = "Password must be minimum 12 characters and include uppercase letters, lowercase letters, numbers, and special characters.";
    }
    // Check if passwords match
    elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    }
    else {
        // Get database connection
        $conn = Database::getConnection();
        
        if ($conn) {
            // Verify OTP and check if it's not expired
            $sql = "SELECT id FROM user WHERE email = ? AND reset_otp = ? AND reset_otp_expiry > NOW()";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ss", $email, $otp);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    
                    // Hash the new password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    
                    // Update password and clear reset OTP
                    $update_sql = "UPDATE user SET password = ?, reset_otp = NULL, reset_otp_expiry = NULL WHERE id = ?";
                    if ($update_stmt = $conn->prepare($update_sql)) {
                        $update_stmt->bind_param("si", $hashed_password, $user['id']);
                        
                        if ($update_stmt->execute()) {
                            // Clear session and redirect to login with success message
                            unset($_SESSION['reset_email']);
                            $_SESSION['success_message'] = "Password has been reset successfully. Please login with your new password.";
                            header("Location: login.php");
                            exit();
                        } else {
                            $error = "Error updating password. Please try again.";
                        }
                        $update_stmt->close();
                    }
                } else {
                    $error = "Invalid or expired reset code.";
                }
                $stmt->close();
            }
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Reset Password - Airgo</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='20' fill='%2307353f'/%3E%3Cpath d='M30 50c0-11 9-20 20-20s20 9 20 20-9 20-20 20-20-9-20-20zm35 0c0-8.3-6.7-15-15-15s-15 6.7-15 15 6.7 15 15 15 15-6.7 15-15z' fill='%233cd5ed'/%3E%3Cpath d='M50 60c-5.5 0-10-4.5-10-10s4.5-10 10-10 10 4.5 10 10-4.5 10-10 10z' fill='white'/%3E%3C/svg%3E" />
    <link rel="icon" type="image/png" sizes="32x32" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAABKhJREFUWEe9V21sU2UUfu7H3a7dmDAgKgwQPxAkKAYnJsyPGCGK0ZAQFfEHJGpigkT8gYrxB5pI/GD+QBNjNCr+ICYaEzWBRDBRIQQIiCIIKLIvGIPtbrfb7b3XnHPvbVc2trs3bU/S7H7c877Pec95z3veK+AeHOIe4MN/B0CWZVkQhHui3LquX5YkSboXk+u6PqwoCud3HEAkEuH8qqrS0NBQYWxsLDY2NhaNRqNRVVVjmqYpuq4rpHwwGHQHAgG3y+VyezweN/1yu91uQRDcBIr+CoIgCILAf222GfRqgKZpGsdHYDRNm6Xr+llZlr1/CcDQ0BCNjZumxXVdVxVFiWmaFlVVNaooSjQejxPQEUVRYpqmxXRd5+vpuu5yOBwet9vtczgcPofD4SWQBMjpdLodDofL6XS6nE4nARFFUeR7QRAIJIMxAVJVVWPQODNBkiSvLMsDkiTJt23BwMAAjVEURY3H4zFFUaKqqkYJiKqqUU3T6G9UVdW4pmkxAqfrOgMWRdHjdDq9BNDhcPgcDofX5XL5XS6Xz+12+10ul9fpdHoJqMvlcjudTjcBJVOYQOm6rsXjcS0Wi8UjkUiU/kYikWgsFovF4/F4LBaLxePxeDQaZUDWFPT39xMAVVVVWCwWVRQlqihKhEAqihIxmUxRXdf52WQyEQCPKIo+URR9BNDpdPqtVqvfYrH4rFar3263++12u89ms3ltNpvXZrN5rFYrAXWRDgioYU7DnMSUoihsXkVRFPJEJBKJRKPR8O0a6O3tNQNgJlRVNayqaphAEgOSJIUlSQobDLgoK0RR9JEGLBaL32q1+m02m5/AEli73e6z2+0+q9Xqc7lcXrvd7rFarW6r1eq2WCwExk2ZQCDJ75IkhXVdD+u6HtZ1PazrehiYEtZ1XTYykCRJYdu27du3j8YbIAJGbIQJpKqqYUmSwgTSYMFNLJAGHA6H32Kx+G02m5/AElgCabfbfTabzWu1Wj0ul8tjs9k8VqvVbbFY3E6n0+10Ot0EkjQgSVKY7qmqGqbxkiSFVVUNU8FTVTVEz7IsywkNbN682QyAM0BV1RCZkEBKkhTSNC0kSVKIGHA4HD6n0+mzWCw+AksmtNlsXpvN5rXb7R6Xy+UhoFar1W21Wl0EkoAaZg1rmhYi89F4TdNCmqaFVFUNkXkTNZHNZvts27aNxhuNCIEI0T2ZkN4bLLhEUfQaueAjDdhsNi+BJD0QUKfT6SYdEFDKAEmSQoqihOjeAJG4/gQ7W7duzQXwP9cBnNexpjhBAAAAAElFTkSuQmCC" />
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@400;700;900&display=swap" rel="stylesheet">
    <!-- Header styles -->
    <link rel="stylesheet" href="styles/header.css">
    <style>
        :root {
            --primary-color: #07353f;
            --secondary-color: #3cd5ed;
            --background-color: #d0f0ff;
            --text-color: #344047;
            --card-bg: #ffffff;
            --card-shadow: rgba(7, 53, 63, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--background-color), #ffffff);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #reset-verify {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .reset-verify-form {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px var(--card-shadow);
            width: min(90%, 400px);
            text-align: center;
        }

        .reset-verify-form h2 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .description {
            color: var(--text-color);
            margin-bottom: 2rem;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            padding: 4px;
            display: flex;
            align-items: center;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .password-toggle svg {
            width: 20px;
            height: 20px;
        }

        .password-field-wrapper {
            position: relative;
        }

        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 0.8rem 1rem;
            padding-right: 40px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(60, 213, 237, 0.1);
        }

        button[type="submit"] {
            width: 100%;
            padding: 1rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        button[type="submit"]:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        .error {
            background: #fee;
            color: #e44;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            text-align: left;
        }

        .password-requirements {
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: #666;
            text-align: left;
        }

        .requirement {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.3rem;
        }

        .requirement::before {
            content: '';
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 1px solid #ccc;
            display: inline-block;
        }

        .requirement.met::before {
            background: #0a0;
            border-color: #0a0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3E%3Cpath d='M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z'/%3E%3C/svg%3E");
            background-size: 10px;
            background-position: center;
            background-repeat: no-repeat;
        }

        .requirement.unmet::before {
            background: #e44;
            border-color: #e44;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3E%3Cpath d='M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z'/%3E%3C/svg%3E");
            background-size: 10px;
            background-position: center;
            background-repeat: no-repeat;
        }

        @media (max-width: 480px) {
            .reset-verify-form {
                padding: 1.5rem;
            }

            .reset-verify-form h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="container">
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

    <section id="reset-verify">
        <div class="reset-verify-form">
            <h2>Reset Password</h2>
            <p class="description">Enter the verification code sent to your email and your new password.</p>

            <?php if (isset($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="reset_verify.php" method="POST">
                <div class="form-group">
                    <label for="otp">Verification Code</label>
                    <input type="text" id="otp" name="otp" required placeholder="Enter 6-digit code">
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="password-field-wrapper">
                        <input type="password" id="new_password" name="new_password" required placeholder="Enter new password" oninput="validatePassword(this)">
                        <button type="button" class="password-toggle" onclick="togglePassword('new_password', this)" aria-label="Toggle password visibility">
                            <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg class="eye-off-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                    <div class="password-requirements">
                        <div class="requirement" id="req-length">At least 12 characters</div>
                        <div class="requirement" id="req-uppercase">Contains uppercase letter</div>
                        <div class="requirement" id="req-lowercase">Contains lowercase letter</div>
                        <div class="requirement" id="req-number">Contains numbers</div>
                        <div class="requirement" id="req-special">Contains special character (!@#$%^&*(),.?":{}|<>)</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <div class="password-field-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm new password" oninput="validateConfirmPassword(this)">
                        <button type="button" class="password-toggle" onclick="togglePassword('confirm_password', this)" aria-label="Toggle password visibility">
                            <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg class="eye-off-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit">
                    Reset Password
                    <span class="btn-icon">→</span>
                </button>
            </form>
        </div>
    </section>

    <script>
        function validatePassword(input) {
            const value = input.value;
            const requirements = {
                length: value.length >= 12,
                uppercase: /[A-Z]/.test(value),
                lowercase: /[a-z]/.test(value),
                number: /\d/.test(value),
                special: /[!@#$%^&*(),.?":{}|<>]/.test(value)
            };

            // Update requirement indicators
            document.getElementById('req-length').className = 
                `requirement ${requirements.length ? 'met' : 'unmet'}`;
            document.getElementById('req-uppercase').className = 
                `requirement ${requirements.uppercase ? 'met' : 'unmet'}`;
            document.getElementById('req-lowercase').className = 
                `requirement ${requirements.lowercase ? 'met' : 'unmet'}`;
            document.getElementById('req-number').className = 
                `requirement ${requirements.number ? 'met' : 'unmet'}`;
            document.getElementById('req-special').className = 
                `requirement ${requirements.special ? 'met' : 'unmet'}`;

            // Validate confirm password if it has a value
            const confirmInput = document.getElementById('confirm_password');
            if (confirmInput.value) {
                validateConfirmPassword(confirmInput);
            }
        }

        function validateConfirmPassword(input) {
            const password = document.getElementById('new_password').value;
            input.classList.remove('valid', 'invalid');
            
            if (input.value === password) {
                input.classList.add('valid');
            } else {
                input.classList.add('invalid');
            }
        }

        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const eyeIcon = button.querySelector('.eye-icon');
            const eyeOffIcon = button.querySelector('.eye-off-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = 'block';
            } else {
                input.type = 'password';
                eyeIcon.style.display = 'block';
                eyeOffIcon.style.display = 'none';
            }
        }
    </script>
</body>
</html> 