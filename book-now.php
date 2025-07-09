<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection
include('db_connection.php');

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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@400;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        :root {
            --primary-color: #07353f;
            --secondary-color: #3cd5ed;
            --background-color: #d0f0ff;
            --text-color: #344047;
            --card-bg: #ffffff;
            --card-shadow: rgba(7, 53, 63, 0.1);
            --spacing-unit: clamp(0.5rem, 2vw, 1rem);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--background-color), #ffffff);
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
            min-height: 100vh;
            font-size: 16px;
            line-height: 1.6;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-color), #052830);
            padding: 2rem 1.5rem;
            color: white;
            box-shadow: 4px 0 20px var(--card-shadow);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .logo {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 900;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            letter-spacing: 1px;
        }

        .sidebar h2 span {
            color: var(--secondary-color);
            font-style: italic;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 1rem 0;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 12px 16px;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .sidebar a::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--secondary-color);
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.3s ease;
            z-index: -1;
            border-radius: 12px;
        }

        .sidebar a:hover {
            color: var(--primary-color);
            transform: translateX(5px);
        }

        .sidebar a:hover::before {
            transform: scaleX(1);
            transform-origin: left;
        }

        .sidebar a i {
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .sidebar a:hover i {
            transform: scale(1.1);
        }

        .main {
            margin-left: 250px;
            padding: clamp(1.5rem, 4vw, 3rem);
            min-height: 100vh;
        }

        .main h1 {
            color: var(--primary-color);
            font-size: clamp(1.8rem, 3vw, 2.5rem);
            margin-bottom: 2rem;
            font-weight: 600;
        }

        .nav-tabs {
            border: none;
            margin-bottom: 2rem;
            gap: 1rem;
        }

        .nav-tabs .nav-link {
            border: none;
            background: var(--card-bg);
            color: var(--text-color);
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px var(--card-shadow);
        }

        .nav-tabs .nav-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px var(--card-shadow);
            color: var(--primary-color);
        }

        .nav-tabs .nav-link.active {
            background: var(--primary-color) !important;
            color: white !important;
            border: none;
        }

        .booking-card {
            background: var(--card-bg);
            border: none;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px var(--card-shadow);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            border-left: 5px solid var(--secondary-color);
        }

        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px var(--card-shadow);
        }

        .booking-card h5 {
            color: var(--primary-color);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .booking-card p {
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }

        .booking-card strong {
            color: var(--primary-color);
        }

        .action-icons {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            display: flex;
            gap: 1rem;
        }

        .action-icons a {
            color: var(--primary-color);
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .action-icons a:hover {
            color: var(--secondary-color);
            transform: scale(1.1);
        }

        .cancel-form button {
            background: #dc3545;
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .cancel-form button:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }

        @media (max-width: 991px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                padding: 1rem;
            }

            .sidebar h2 {
                display: none;
            }

            .nav-links {
                display: flex;
                justify-content: center;
                gap: 1rem;
                flex-wrap: wrap;
            }

            .sidebar a {
                margin: 0.5rem;
                padding: 10px 20px;
            }

            .main {
                margin-left: 0;
                padding: 1.5rem;
            }
        }

        @media (max-width: 575px) {
            .sidebar {
                padding: 0.5rem;
            }

            .sidebar a {
                padding: 8px 16px;
                font-size: 0.9rem;
            }

            .main {
                padding: 1rem;
            }

            .nav-tabs .nav-link {
                padding: 0.8rem 1.5rem;
                font-size: 0.9rem;
            }

            .booking-card {
                padding: 1.5rem;
            }
        }

        /* Simple loading indicator */
        .loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--secondary-color);
            z-index: 9999;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .loading.active {
            transform: scaleX(1);
        }
    </style>
</head>
<body>
<div class="loading"></div>

<div class="sidebar">
    <h2>Air<span>go</span></h2>
    <div class="nav-links">
        <a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
        <a href="book-now.php"><i class="fa-solid fa-calendar-plus"></i> Booking</a>
        <a href="cancel_booking.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</div>

<div class="main">
    <h1>Your Bookings</h1>

    <ul class="nav nav-tabs" id="bookingTabs" role="tablist">
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Simple page transition
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', function(e) {
            if (!this.href.includes('logout.php')) {
                e.preventDefault();
                const href = this.href;
                
                // Show loading indicator
                document.querySelector('.loading').classList.add('active');
                
                // Navigate after delay
                setTimeout(() => {
                    window.location.href = href;
                }, 300);
            }
        });
    });
});
</script>
</body>
</html>
