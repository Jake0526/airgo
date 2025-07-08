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

// Move Cancelled/Done/Rejected bookings to history
$move_query = "SELECT * FROM bookings WHERE user_id = ? AND status IN ('Cancelled', 'Done', 'Rejected')";
$move_stmt = $conn->prepare($move_query);
$move_stmt->bind_param("i", $user_id);
$move_stmt->execute();
$move_result = $move_stmt->get_result();

while ($booking = $move_result->fetch_assoc()) {
    $employee_id = $booking['employee_id'];
    $technician_name = 'N/A';

    if ($employee_id) {
        $tech_stmt = $conn->prepare("SELECT name FROM employees WHERE id = ?");
        $tech_stmt->bind_param("i", $employee_id);
        $tech_stmt->execute();
        $tech_stmt->bind_result($technician_name);
        $tech_stmt->fetch();
        $tech_stmt->close();
    }

    $check = $conn->prepare("SELECT id FROM booking_history_customer WHERE user_id = ? AND booking_date = ? AND booking_time = ?");
    $check->bind_param("iss", $booking['user_id'], $booking['appointment_date'], $booking['appointment_time']);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO booking_history_customer 
            (user_id, service_type, booking_date, booking_time, phone, technician_name, status, moved_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $insert->bind_param("issssss",
            $booking['user_id'],
            $booking['service'],
            $booking['appointment_date'],
            $booking['appointment_time'],
            $booking['phone'],
            $technician_name,
            $booking['status']
        );
        $insert->execute();
        $insert->close();
    }

    $check->close();
    $delete = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $delete->bind_param("i", $booking['id']);
    $delete->execute();
    $delete->close();
}
$move_stmt->close();

// Cancel Booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking_id'])) {
    $booking_id = intval($_POST['cancel_booking_id']);

    $fetch = $conn->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ? AND status = 'Pending'");
    $fetch->bind_param("ii", $booking_id, $user_id);
    $fetch->execute();
    $result = $fetch->get_result();

    if ($booking = $result->fetch_assoc()) {
        $technician_name = 'N/A';
        if ($booking['employee_id']) {
            $tech_stmt = $conn->prepare("SELECT name FROM employees WHERE id = ?");
            $tech_stmt->bind_param("i", $booking['employee_id']);
            $tech_stmt->execute();
            $tech_stmt->bind_result($technician_name);
            $tech_stmt->fetch();
            $tech_stmt->close();
        }

        $update = $conn->prepare("UPDATE bookings SET status = 'Cancelled' WHERE id = ?");
        $update->bind_param("i", $booking_id);
        $update->execute();
        $update->close();

        $insert = $conn->prepare("INSERT INTO booking_history_customer 
            (user_id, service_type, booking_date, booking_time, phone, technician_name, status, moved_at)
            VALUES (?, ?, ?, ?, ?, ?, 'Cancelled', NOW())");
        $insert->bind_param("isssss",
            $booking['user_id'],
            $booking['service'],
            $booking['appointment_date'],
            $booking['appointment_time'],
            $booking['phone'],
            $technician_name
        );
        $insert->execute();
        $insert->close();

        $delete = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        $delete->bind_param("i", $booking_id);
        $delete->execute();
        $delete->close();
    }
    $fetch->close();
    header("Location: book-now.php");
    exit();
}

// Fetch bookings by status
function fetch_bookings($conn, $user_id, $status) {
    $stmt = $conn->prepare("SELECT b.*, e.name AS employee_name 
        FROM bookings b 
        LEFT JOIN employees e ON b.employee_id = e.id 
        WHERE b.user_id = ? AND b.status = ?
        ORDER BY b.appointment_date DESC, b.id DESC");
    $stmt->bind_param("is", $user_id, $status);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


$pending_result = fetch_bookings($conn, $user_id, 'Pending');
$approved_result = fetch_bookings($conn, $user_id, 'Approved');
$reschedule_result = fetch_bookings($conn, $user_id, 'Reschedule Requested');

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Bookings - AirGo</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<style>
body {
    background: linear-gradient(to right, #d0f0ff);
    font-family: 'Segoe UI', sans-serif;
}
.sidebar {
    width: 230px;
    height: 100vh;
    background: #07353f;
    color: white;
    position: fixed;
    padding: 30px 20px;
}
.sidebar h2 {
    font-size: 28px;
    font-weight: bold;
}
.sidebar a {
    display: block;
    margin: 15px 0;
    color: white;
    text-decoration: none;
    padding: 10px;
    border-radius: 8px;
    transition: background 0.3s, transform 0.2s;
}
.sidebar a:hover {
    background-color: #d0f0ff;
    transform: translateX(5px);
    color: #07353f !important;
}
.main {
    margin-left: 250px;
    padding: 30px;
}
.booking-card {
    background: #fff;
    border-left: 5px solid #053b50;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    position: relative;
    margin-bottom: 25px;
}
.action-icons {
    position: absolute;
    top: 15px;
    right: 15px;
}
.action-icons a {
    color: #07353f;
    margin-left: 12px;
    font-size: 18px;
}
.cancel-form button {
    background: #dc3545;
    color: #fff;
    border: none;
    padding: 8px 15px;
    border-radius: 6px;
}
.cancel-form button:hover {
    background-color: #c82333;
}
.nav-tabs .nav-link {
    transition: all 0.3s ease;
    border-radius: 8px;
}
.nav-tabs .nav-link:hover {
    background-color: #e0f7ff;
    transform: translateY(-2px);
    color: #07353f !important;
}
.nav-tabs .nav-link.active {
    background-color: #07353f !important;
    color: white !important;
    border-color: transparent;
}
@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
    }
    .main {
        margin-left: 0;
        padding: 15px;
    }
}
</style>
</head>
<body>

<div class="sidebar">
    <h2><i class="fa-solid fa-wind"></i> AirGo</h2>
    <a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
    <a href="book-now.php"><i class="fa-solid fa-calendar-plus"></i> Booking</a>
    <a href="cancel_booking.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a>
    <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<div class="main">
    <h1 class="mb-4">Your Bookings</h1>

    <ul class="nav nav-tabs mb-4" id="bookingTabs" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending">Pending</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#reschedule">Reschedule</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#approved">Approved</button></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="pending">
            <?php if ($pending_result): foreach ($pending_result as $b): ?>
            <div class="booking-card">
                <div class="action-icons">
                    <a href="edit_booking.php?id=<?= $b['id'] ?>" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                </div>
                <h5><?= htmlspecialchars($b['service']) ?></h5>
                <p><strong>Date:</strong> <?= $b['appointment_date'] ?> - <strong>Time:</strong> <?= date("g:i A", strtotime($b['appointment_time'])) ?></p>
                <p><strong>Technician:</strong> <?= $b['employee_name'] ?: 'Pending' ?></p>
                <p><strong>Status:</strong> <?= $b['status'] ?></p>
                <?php if (isset($b['price'])): ?>
                <p><strong>Price:</strong> ₱<?= number_format($b['price'], 2) ?></p>
                <?php endif; ?>

                <form method="POST" class="cancel-form mt-2" onsubmit="return confirm('Cancel this booking?');">
                    <input type="hidden" name="cancel_booking_id" value="<?= $b['id'] ?>">
                    <button type="submit"><i class="fa-solid fa-ban"></i> Cancel</button>
                </form>
            </div>
            <?php endforeach; else: ?>
            <p>No pending bookings.</p>
            <?php endif; ?>
        </div>
        <div class="tab-pane fade" id="reschedule">
            <?php if ($reschedule_result): foreach ($reschedule_result as $b): ?>
            <div class="booking-card">
                <h5><?= htmlspecialchars($b['service']) ?></h5>
                <p><strong>Date:</strong> <?= $b['appointment_date'] ?> - <strong>Time:</strong> <?= date("g:i A", strtotime($b['appointment_time'])) ?></p>
                <p><strong>Technician:</strong> <?= $b['employee_name'] ?: 'Pending' ?></p>
                <p><strong>Status:</strong> <?= $b['status'] ?></p>
                <?php if (isset($b['price'])): ?>
                <p><strong>Price:</strong> ₱<?= number_format($b['price'], 2) ?></p>
            <?php endif; ?>

            </div>
            <?php endforeach; else: ?>
            <p>No reschedule requests.</p>
            <?php endif; ?>
        </div>
        <div class="tab-pane fade" id="approved">
            <?php if ($approved_result): foreach ($approved_result as $b): ?>
            <div class="booking-card">
                <div class="action-icons">
                    <a href="reschedule_request.php?id=<?= $b['id'] ?>" title="Reschedule"><i class="fa-solid fa-calendar-days"></i></a>
                </div>
                <h5><?= htmlspecialchars($b['service']) ?></h5>
                <p><strong>Date:</strong> <?= $b['appointment_date'] ?> - <strong>Time:</strong> <?= date("g:i A", strtotime($b['appointment_time'])) ?></p>
                <p><strong>Technician:</strong> <?= $b['employee_name'] ?: 'Pending' ?></p>
                <p><strong>Price:</strong> ₱<?= number_format($b['price'], 2) ?></p>
                <p><strong>Status:</strong> <?= $b['status'] ?></p>
            </div>
            <?php endforeach; else: ?>
            <p>No approved bookings.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
