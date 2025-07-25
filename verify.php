<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and not verified
if (!isset($_SESSION['user_id']) || !isset($_SESSION['needs_verification'])) {
    header("Location: login.php");
    exit();
}

$error_message = "";
$success_message = "";
$otp_expiry_time = null;

// Get OTP expiry time for countdown
$conn = Database::getConnection();
$user_id = $_SESSION['user_id'];
$expiry_stmt = $conn->prepare("SELECT otp_expiry FROM user WHERE id = ?");
$expiry_stmt->bind_param("i", $user_id);
$expiry_stmt->execute();
$expiry_result = $expiry_stmt->get_result();
if ($expiry_row = $expiry_result->fetch_assoc()) {
    $otp_expiry_time = $expiry_row['otp_expiry'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $otp = trim($_POST['otp']);
    $user_id = $_SESSION['user_id'];
    
    $conn = Database::getConnection();
    
    // Debug: Check user_id
    error_log("Verifying user_id: " . $user_id);
    
    // First check if user exists and get OTP details
    $check_stmt = $conn->prepare("SELECT id, is_verified, otp_code, otp_expiry FROM user WHERE id = ?");
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_row = $check_result->fetch_assoc()) {
        error_log("User found - is_verified: " . $check_row['is_verified'] . ", otp_code: " . ($check_row['otp_code'] ? 'exists' : 'null'));
        
        // Now proceed with verification
        if ($check_row['is_verified'] == 0) {
            // Check if OTP has expired (only if expiry time exists)
            if ($check_row['otp_expiry'] && strtotime($check_row['otp_expiry']) < time()) {
                $error_message = "Verification code has expired. Please request a new code.";
            } elseif ($check_row['otp_code'] === $otp) {
                // Update user as verified and clear OTP data
                $update_stmt = $conn->prepare("UPDATE user SET is_verified = 1, otp_code = NULL, otp_expiry = NULL WHERE id = ?");
                $update_stmt->bind_param("i", $user_id);
                
                if ($update_stmt->execute()) {
                    // Get full user data for session
                    $user_stmt = $conn->prepare("SELECT username, fname, lname FROM user WHERE id = ?");
                    $user_stmt->bind_param("i", $user_id);
                    $user_stmt->execute();
                    $user_result = $user_stmt->get_result();
                    $user_data = $user_result->fetch_assoc();
                    
                    // Set full session data
                    $_SESSION['username'] = $user_data['username'];
                    $_SESSION['fname'] = $user_data['fname'];
                    $_SESSION['lname'] = $user_data['lname'];
                    
                    // Remove verification flag
                    unset($_SESSION['needs_verification']);
                    
                    $_SESSION['success_message'] = "Email verified successfully! You can now use your account.";
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error_message = "An error occurred. Please try again.";
                }
            } else {
                $error_message = "Invalid verification code. Please try again.";
            }
        } else {
            $error_message = "User is already verified.";
        }
    } else {
        $error_message = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - AirGo</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='20' fill='%2307353f'/%3E%3Cpath d='M30 50c0-11 9-20 20-20s20 9 20 20-9 20-20 20-20-9-20-20zm35 0c0-8.3-6.7-15-15-15s-15 6.7-15 15 6.7 15 15 15 15-6.7 15-15z' fill='%233cd5ed'/%3E%3Cpath d='M50 60c-5.5 0-10-4.5-10-10s4.5-10 10-10 10 4.5 10 10-4.5 10-10 10z' fill='white'/%3E%3C/svg%3E" />
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #07353f;
            --secondary-color: #3cd5ed;
            --background-color: #d0f0ff;
            --text-color: #344047;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--background-color), #ffffff);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .verify-container {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(7, 53, 63, 0.1);
            width: min(90%, 400px);
            text-align: center;
        }

        .logo {
            color: var(--primary-color);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .logo span {
            color: var(--secondary-color);
        }

        h1 {
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        p {
            color: var(--text-color);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .otp-input {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .otp-input input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 1.5rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: white;
            transition: all 0.3s ease;
        }

        .otp-input input:focus {
            border-color: var(--secondary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(60, 213, 237, 0.1);
        }

        button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        button:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        .error-message {
            background: #fee;
            color: #e44;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .success-message {
            background: #e6ffe6;
            color: #0a0;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .resend-link {
            display: block;
            margin-top: 1rem;
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .resend-link:hover {
            color: var(--secondary-color);
        }

        .countdown-container {
            margin: 1rem 0;
            text-align: center;
        }

        .countdown-container p {
            margin: 0;
            font-size: 0.9rem;
            color: var(--text-color);
        }

        .expired-message {
            background: #fff3cd;
            color: #856404;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .form-disabled {
            opacity: 0.6;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="logo">Air<span>go</span></div>
        <h1>Email Verification</h1>
        <p>We've sent a verification code to <?php echo htmlspecialchars($_SESSION['email']); ?>. Please enter the code below to verify your account.</p>
        
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (!$otp_expiry_time): ?>
            <div class="error-message">
                ⚠️ Database needs update! <a href="run_migration.php" style="color: #07353f; text-decoration: underline;">Click here to run migration</a>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="verify.php" id="verifyForm">
            <div class="otp-input">
                <input type="text" name="otp" maxlength="6" pattern="[0-9]{6}" required
                       placeholder="Enter OTP" style="width: 200px;" id="otpInput">
            </div>
            <div class="countdown-container" id="countdownContainer">
                <p>Code expires in: <span id="countdown" style="color: var(--secondary-color); font-weight: 600;"></span></p>
            </div>
            <button type="submit" id="verifyButton">Verify Email</button>
        </form>
        
        <a href="resend_otp.php" class="resend-link" id="resendLink">Didn't receive the code? Resend</a>
    </div>

    <script>
        // Get OTP expiry time from PHP
        const otpExpiryTime = <?php echo $otp_expiry_time ? "'" . $otp_expiry_time . "'" : 'null'; ?>;
        let countdownInterval;
        
        // Debug logging
        console.log('OTP Expiry Time from PHP:', otpExpiryTime);
        console.log('Type of otpExpiryTime:', typeof otpExpiryTime);
        <?php if ($otp_expiry_time): ?>
        console.log('PHP says expiry time exists:', '<?php echo $otp_expiry_time; ?>');
        <?php else: ?>
        console.log('PHP says expiry time is NULL - database field might not exist!');
        <?php endif; ?>

        function startCountdown() {
            if (!otpExpiryTime) {
                console.log('No expiry time available - hiding countdown');
                document.getElementById('countdown').textContent = 'Unknown';
                document.getElementById('countdownContainer').innerHTML = '<p style="color: orange;">⚠️ Timer unavailable - database needs update</p>';
                return;
            }

            // Parse the datetime string properly for timezone
            const expiryDate = new Date(otpExpiryTime.replace(' ', 'T') + '+08:00').getTime();
            const countdownElement = document.getElementById('countdown');
            const form = document.getElementById('verifyForm');
            const otpInput = document.getElementById('otpInput');
            const verifyButton = document.getElementById('verifyButton');
            const resendLink = document.getElementById('resendLink');
            
            console.log('Parsed expiry date:', new Date(expiryDate));

            countdownInterval = setInterval(function() {
                const now = new Date().getTime();
                const timeLeft = expiryDate - now;

                if (timeLeft > 0) {
                    const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
                    
                    countdownElement.textContent = 
                        String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
                } else {
                    // Time's up
                    clearInterval(countdownInterval);
                    countdownElement.textContent = '00:00';
                    
                    // Disable form
                    form.classList.add('form-disabled');
                    otpInput.disabled = true;
                    verifyButton.disabled = true;
                    
                    // Show expiry message
                    const expiredDiv = document.createElement('div');
                    expiredDiv.className = 'expired-message';
                    expiredDiv.textContent = 'Verification code has expired. Please request a new code.';
                    form.parentNode.insertBefore(expiredDiv, form);
                    
                    // Update resend link text
                    resendLink.textContent = 'Get New Code';
                    resendLink.style.fontWeight = '600';
                }
            }, 1000);
        }

        // Auto-submit form when all digits are entered
        document.getElementById('otpInput').addEventListener('input', function(e) {
            if (this.value.length === 6 && !this.disabled) {
                this.form.submit();
            }
        });

        // Start countdown when page loads
        document.addEventListener('DOMContentLoaded', function() {
            startCountdown();
        });
    </script>
</body>
</html> 