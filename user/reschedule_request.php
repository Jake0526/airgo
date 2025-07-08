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

$user_id = $_SESSION['user_id'];
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch booking info
$stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if (!$booking) {
    echo "Booking not found.";
    exit();
}

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_date = trim($_POST['new_date']);
    $new_time = trim($_POST['new_time']);

    // Check for duplicate reschedule
    $check = $conn->prepare("SELECT id FROM reschedule_requests WHERE booking_id = ?");
    $check->bind_param("i", $booking_id);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows > 0) {
        $message = "<div class='alert alert-warning'>You have already requested a reschedule for this booking.</div>";
    } else {
        // Insert reschedule request
        $stmt = $conn->prepare("INSERT INTO reschedule_requests (booking_id, requested_date, requested_time) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $booking_id, $new_date, $new_time);
        $stmt->execute();
        $stmt->close();

        // Update booking status
        $stmt = $conn->prepare("UPDATE bookings SET status = 'Reschedule Requested' WHERE id = ?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $stmt->close();

        header("Location: book-now.php?reschedule=success");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Reschedule</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background:#d0f0ff;
            font-family: 'Segoe UI', sans-serif;
        }
        .reschedule-box {
            background: #d0f0ff;
            padding: 25px;
            max-width: 600px;
            margin: 60px auto;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgb(248, 246, 246);
            border:1px solid  #07353f;
            margin-top: 100px;
        }
        .btn-primary {
            background-color: #0ea5e9;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0284c7;
        }
    </style>
</head>
<body>

<div class="reschedule-box">
    <h3 class="mb-4">Request to Reschedule</h3>
    <?= $message ?>
    <form method="POST">
        <div class="mb-3">
            <label for="new_date" class="form-label">New Appointment Date</label>
            <input type="date" class="form-control" name="new_date" required min="<?= date('Y-m-d') ?>">
        </div>
        <div class="mb-3">
            <label for="new_time" class="form-label">New Appointment Time</label>
            <input type="time" class="form-control" name="new_time" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit Request</button>
        <a href="book-now.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

</body>
</html>
