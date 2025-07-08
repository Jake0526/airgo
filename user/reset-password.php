<?php
require_once 'db_connection';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $newPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!empty($token) && !empty($newPassword) && !empty($confirmPassword)) {
        if ($newPassword === $confirmPassword) {
            try {
                // Validate token
                $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = :token AND expires_at > NOW()");
                $stmt->bindParam(':token', $token, PDO::PARAM_STR);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $userId = $row['user_id'];

                    // Update password without hashing
                    $stmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :user_id");
                    $stmt->bindParam(':password', $newPassword, PDO::PARAM_STR);
                    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        // Delete the reset token
                        $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = :token");
                        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
                        $stmt->execute();

                        echo "
                        <script>
                            alert('Password reset successfully! Login now!');
                            window.location.href = 'http://localhost/login-system-with-forgot-password/';
                        </script>
                        ";
                    } else {
                        echo "
                        <script>
                            alert('Failed to update the password');
                            window.location.href = 'http://localhost/login-system-with-forgot-password/';
                        </script>
                        ";
                    }
                } else {
                    echo "
                    <script>
                        alert('Invalid or expired token.');
                        window.location.href = 'http://localhost/login-system-with-forgot-password/';
                    </script>
                    ";
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            echo "Passwords do not match.";
        }
    } else {
        echo "Please fill out all fields.";
    }
} elseif (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    echo "Invalid request.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>

    <!-- Style CSS -->
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700|Poppins:400,500&display=swap');

        * {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            padding: 0;
            box-sizing: border-box;
        }
        .bg-img {
            position: relative;
            background: url('bg.jpg') no-repeat center;
            background-size: cover;
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .bg-img::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1;
        }

        .content {
            position: relative;
            z-index: 2;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 8px;
            padding: 50px 40px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            max-width: 400px;
            width: 100%;
        }

        .content header {
            color: #fff;
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 25px;
            font-family: 'Montserrat', sans-serif;
        }

        .field {
            position: relative;
            margin-top: 20px;
        }

        .field input {
            width: 100%;
            padding: 12px 45px 12px 15px;
            font-size: 16px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 6px;
            outline: none;
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
        }

        .field span {
            position: absolute;
            top: 12px;
            right: 15px;
            color: #bbb;
            font-size: 14px;
            cursor: pointer;
        }

        .field input[type="submit"] {
            background: #3498db;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            padding: 12px;
        }

        .field input[type="submit"]:hover {
            background: #2980b9;
        }

        .pass {
            text-align: left;
            margin: 10px 0;
        }

        .pass a {
            color: #fff;
            font-size: 14px;
            text-decoration: none;
        }

        .pass a:hover {
            text-decoration: underline;
        }

        .signup {
            color: #fff;
            font-size: 14px;
            margin-top: 20px;
        }

        .signup a {
            color: #3498db;
            text-decoration: none;
        }

        .signup a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="bg-img">
        <div class="content">
            <div class="login-form">
                <header>Your New Password</header>
                <form method="POST" action="reset-password.php">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    <div class="field">
                        <input type="password" class="pass-key" name="password" required placeholder="Password">
                        <span class="show">SHOW</span>
                    </div>
                    <div class="field">
                        <input type="password" class="pass-key" name="confirm_password" required placeholder="Password">
                        <span class="show">SHOW</span>
                    </div>
                    <div class="field">
                        <input type="submit" value="RESET PASSWORD">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Password visibility toggle
        const showBtns = document.querySelectorAll('.show');
        
        showBtns.forEach(showBtn => {
            showBtn.addEventListener('click', function () {
                const passField = this.previousElementSibling; // Target the associated input field
                if (passField.type === "password") {
                    passField.type = "text";
                    this.textContent = "HIDE";
                } else {
                    passField.type = "password";
                    this.textContent = "SHOW";
                }
            });
        });

    </script>

</body>
</html>
