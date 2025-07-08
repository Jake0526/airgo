<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database credentials
$host = 'localhost';
$db = 'airgo';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$service = $_POST['service'] ?? '';
$appointment_date = $_POST['appointment_date'] ?? '';
$appointment_time = $_POST['appointment_time'] ?? '';
$location = $_POST['location'] ?? '';
$phone_number = $_POST['phone_number'] ?? '';
$service_price = $_POST['service_price'] ?? 0.00;

$user_id = $_SESSION['user_id'];

// 2. Prepare and execute the INSERT query
$stmt = $conn->prepare("INSERT INTO bookings 
    (user_id, service, appointment_date, appointment_time, phone, location, price) 
    VALUES (?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    die("SQL error: " . $conn->error);
}

$stmt->bind_param("isssssd", 
    $user_id, 
    $service, 
    $appointment_date, 
    $appointment_time, 
    $phone_number, 
    $location, 
    $service_price
);

$stmt->execute();
$stmt->close();

// 3. Optionally redirect
header("Location: book-now.php");
exit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmed</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="4;url=dashboard.php">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&family=Playfair+Display:wght@900&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color:  #d0f0ff;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeIn 1s ease-in;
        }

        header {
            background: #07353f;
            padding: 10px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            width: 100%;
        }

        nav.container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .logo h1 {
            color: #CACBBB;
            font-family: 'Playfair Display', serif;
            font-weight: 500;
            letter-spacing: 3px;
            font-size: 1.8rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
            text-transform: uppercase;
        }

        .btn-Back {
            position: relative;
            color: #07353f;
            background-color: #CACBBB;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 300;
            font-family: 'Poppins', sans-serif;
            letter-spacing: 1.2px;
            text-decoration: none;
            overflow: hidden;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .btn-Back:hover {
            background-color: #d6cec6;
            color: #07353f;
        }

        .btn-Back .underline {
            position: absolute;
            bottom: 8px;
            left: 20%;
            width: 50%;
            height: 1px;
            background-color: #07353f;
            border-radius: 2px;
            transform: scaleX(0);
            transform-origin: center;
            transition: transform 0.35s ease;
        }

        .btn-Back:hover .underline {
            transform: scaleX(1);
        }

        .card {
            background-color:  #d0f0ff;
            padding: 60px 40px;
            border-radius: 20px;
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 600px;
            text-align: center;
            margin-top: 60px;
            animation: fadeInUp 1s ease-in-out;
        }

        h1 {
            color: #07353f;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        p {
            color: #333;
            font-size: 1.2em;
            margin-top: 10px;
        }

        .loader {
            margin-top: 20px;
            border: 6px solid #f3f3f3;
            border-top: 6px solid #07353f;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            display: inline-block;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <script>
        setTimeout(function () {
            window.location.href = "dashboard.php";
        }, 1000); // 4000ms = 1 seconds
    </script>
</head>
<body>

<!-- Header -->
<header>
    <nav class="container">
        <div class="logo">
            <h1>Airgo</h1>
        </div>
    </nav>
</header>

<!-- Confirmation Card -->
<div class="card">
    <h1>Booking Successful</h1>
    <p><strong><?php echo $confirmation_message; ?></strong></p>
    <div class="loader"></div>
</div>

</body>
</html>
