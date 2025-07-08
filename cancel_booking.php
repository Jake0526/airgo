<?php  
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$host = 'localhost';
$db = 'airgo';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Fetch from booking_history_customer
$stmt1 = $conn->prepare("SELECT *, booking_date AS moved_at, 'History' AS source FROM booking_history_customer WHERE user_id = ?");
$stmt1->bind_param("i", $user_id);
$stmt1->execute();
$result1 = $stmt1->get_result();
$history = $result1->fetch_all(MYSQLI_ASSOC);
$stmt1->close();

// Fetch from cancel_booking
$stmt2 = $conn->prepare("SELECT *, booking_date AS moved_at, 'Cancelled' AS source FROM cancel_booking WHERE user_id = ?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$cancelled = $result2->fetch_all(MYSQLI_ASSOC);
$stmt2->close();

$conn->close();

// Combine arrays
$combined = array_merge($history, $cancelled);

// Sort by date+time descending
usort($combined, function($a, $b) {
    $datetimeA = strtotime($a['moved_at'].' '.$a['booking_time']);
    $datetimeB = strtotime($b['moved_at'].' '.$b['booking_time']);
    return $datetimeB <=> $datetimeA;
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking History - AirGo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            background: linear-gradient(to right, #d0f0ff, #e9f9ff);
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
        }
        .sidebar a:hover {
            background-color: #0ea5e9;
        }
        .main {
            margin-left: calc(230px + 1.5in);
            margin-right: 1.5in;
            padding: 30px;
        }
        .history-card {
            background: #fff;
            border-left: 5px solid #053b50;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            position: relative;
            margin-bottom: 20px;
            transition: transform 0.2s;
        }
        .history-card:hover {
            transform: scale(1.01);
        }
        .delete-link {
            position: absolute;
            top: 12px;
            right: 12px;
            text-decoration: none;
        }
        .delete-icon {
            color: #dc3545;
            font-size: 20px;
            cursor: pointer;
            transition: 0.2s;
        }
        .delete-icon:hover {
            color: #a71d2a;
            transform: scale(1.2);
        }
        .badge {
            font-size: 14px;
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
    <h1 class="mb-4">Booking History</h1>

    <?php if (!empty($combined)): ?>
        <?php foreach ($combined as $b): ?>
            <div class="history-card">
                <a class="delete-link"
                   href="delete_booking_history.php?id=<?= urlencode($b['id']) ?>&source=<?= urlencode(strtolower($b['source'])) ?>"
                   onclick="return confirm('Are you sure you want to delete this record?')">
                    <i class="fas fa-trash delete-icon"></i>
                </a>
                <div>
                    <h5><?= htmlspecialchars($b['service_type'] ?? 'Service') ?></h5>
                    <p><strong>Date:</strong> <?= htmlspecialchars($b['moved_at']) ?></p>
                    <p><strong>Time:</strong> <?= htmlspecialchars(date("g:i A", strtotime($b['booking_time']))) ?></p>
                    <p><strong>Technician:</strong> <?= htmlspecialchars($b['technician_name'] ?? 'N/A') ?></p>
                    <p><strong>Price:</strong> ₱<?= htmlspecialchars(number_format($b['price'] ?? 0, 2)) ?></p>
                    <p><strong>Record Type:</strong> <?= htmlspecialchars($b['source']) ?></p>
                    <?php
                        $status = isset($b['status']) ? strtolower($b['status']) : 'unknown';
                        $badgeClass = 'secondary';
                        if ($status === 'cancelled') {
                            $badgeClass = 'danger';
                        } elseif ($status === 'done') {
                            $badgeClass = 'success';
                        }
                    ?>
                    <p><strong>Status:</strong>
                        <span class="badge bg-<?= $badgeClass ?>">
                            <?= htmlspecialchars(ucfirst($status)) ?>
                        </span>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-muted">No booking history available.</p>
    <?php endif; ?>
</div>

</body>
</html>
