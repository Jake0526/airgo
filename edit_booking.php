<?php 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$db = 'airgo';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$booking_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service = $_POST['service'];
    $location = $_POST['location'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];

    $stmt = $conn->prepare("UPDATE bookings SET service = ?, location = ?, appointment_date = ?, appointment_time = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssssii", $service, $location, $appointment_date, $appointment_time, $booking_id, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: book-now.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$booking) {
    echo "Booking not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(to right, #d0f0ff); 
        }
        .form-container {
            background-color:  #d0f0ff;
            padding: 30px;
            border-radius: 15px;
            max-width: 300px;
            margin: auto;
            box-shadow: 0 6px 20px rgb(248, 246, 246);
            border:3px solid  #07353f;
        }
        h2 {
            color: #07353f;
        }
        label {
            font-weight: bold;
        }
        .btn-success {
            background-color: #198754;
            border-color: #198754;
        }
        .btn-secondary {
            margin-left: 10px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="form-container">
        <h2 class="mb-4">Edit Booking</h2>
        <form method="POST">
            <div class="mb-3">
                <label>Service</label>
                <input type="text" name="service" class="form-control" value="<?= htmlspecialchars($booking['service']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Location</label>
                <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($booking['location']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Date</label>
                <input type="date" name="appointment_date" class="form-control" value="<?= $booking['appointment_date'] ?>" required>
            </div>
            <div class="mb-3">
                <label>Time</label>
                <input type="time" name="appointment_time" class="form-control" value="<?= $booking['appointment_time'] ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Update Booking</button>
            <a href="book-now.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
</body>
</html>
