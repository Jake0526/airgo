<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get current page for active sidebar highlighting
$current_page = basename($_SERVER['PHP_SELF']);

// Include the database connection
require_once 'db_connection.php';
$conn = Database::getConnection();

// Define services and their prices
$services_prices = [
    'Aircon Check-up' => 500,
    'Aircon Relocation' => 3500,
    'Aircon Repair' => 1500,
    'Aircon cleaning (window type)' => 800,
    'Window type (inverter)' => 2500,
    'Window type (U shape)' => 2300,
    'Split type' => 2800,
    'Floormounted' => 3000,
    'Cassette' => 3200,
    'Capacitor Thermostat' => 1200
];

$user_id = $_SESSION['user_id'];

// Cancel Booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking_id'])) {
    $booking_id = intval($_POST['cancel_booking_id']);

    // Update the status to 'Cancelled' in bookings table
    $update = $conn->prepare("UPDATE bookings SET status = 'Cancelled' WHERE id = ? AND user_id = ?");
    $update->bind_param("ii", $booking_id, $user_id);
    $update->execute();
    $update->close();

    header("Location: book-now.php");
    exit();
}

// Fetch bookings by status
function fetch_bookings($conn, $user_id, $status) {
    $stmt = $conn->prepare("SELECT b.*, e.name AS employee_name, b.reschedule_attempt 
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
$rescheduled_result = fetch_bookings($conn, $user_id, 'Rescheduled');

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Bookings - AirGo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='20' fill='%2307353f'/%3E%3Cpath d='M30 50c0-11 9-20 20-20s20 9 20 20-9 20-20 20-20-9-20-20zm35 0c0-8.3-6.7-15-15-15s-15 6.7-15 15 6.7 15 15 15 15-6.7 15-15z' fill='%233cd5ed'/%3E%3Cpath d='M50 60c-5.5 0-10-4.5-10-10s4.5-10 10-10 10 4.5 10 10-4.5 10-10 10z' fill='white'/%3E%3C/svg%3E" />
    <link rel="icon" type="image/png" sizes="32x32" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAABKhJREFUWEe9V21sU2UUfu7H3a7dmDAgKgwQPxAkKAYnJsyPGCGK0ZAQFfEHJGpigkT8gYrxB5pI/GD+QBNjNCr+ICYaEzWBRDBRIQQIiCIIKLIvGIPtbrfb7b3XnHPvbVc2trs3bU/S7H7c877Pec95z3veK+AeHOIe4MN/B0CWZVkQhHui3LquX5YkSboXk+u6PqwoCud3HEAkEuH8qqrS0NBQYWxsLDY2NhaNRqNRVVVjmqYpuq4rpHwwGHQHAgG3y+VyezweN/1yu91uQRDcBIr+CoIgCILAf222GfRqgKZpGsdHYDRNm6Xr+llZlr1/CcDQ0BCNjZumxXVdVxVFiWmaFlVVNaooSjQejxPQEUVRYpqmxXRd5+vpuu5yOBwet9vtczgcPofD4SWQBMjpdLodDofL6XS6nE4nARFFUeR7QRAIJIMxAVJVVWPQODNBkiSvLMsDkiTJt23BwMAAjVEURY3H4zFFUaKqqkYJiKqqUU3T6G9UVdW4pmkxAqfrOgMWRdHjdDq9BNDhcPgcDofX5XL5XS6Xz+12+10ul9fpdHoJqMvlcjudTjcBJVOYQOm6rsXjcS0Wi8UjkUiU/kYikWgsFovF4/F4LBaLxePxeDQaZUDWFPT39xMAVVXVWCwWVRQlqihKhEAqihIxmUxRXdf52WQyEQCPKIo+URR9BNDpdPqtVqvfYrH4rFar3263++12u89ms3ltNpvXZrN5rFYrAXWRDgioYU7DnMSUoihsXkVRFPJEJBKJRKPR8O0a6O3tNQNgJlRVNayqaphAEgOSJIUlSQobDLgoK0RR9JEGLBaL32q1+m02m5/AEli73e6z2+0+q9Xqc7lcXrvd7rFarW6r1eq2WCwExk2ZQCDJ75IkhXVdD+u6HtZ1PazrehiYEtZ1XTYykCRJYdu27du3j8YbIAJGbIQJpKqqYUmSwgTSYMFNLJAGHA6H32Kx+G02m5/AElgCabfbfTabzWu1Wj0ul8tjs9k8VqvVbbFY3E6n0+10Ot0EkjQgSVKY7qmqGqbxkiSFVVUNU8FTVTVEz7IsywkNbN682QyAM0BV1RCZkEBKkhTSNC0kSVKIGHA4HD6n0+mzWCw+AksmtNlsXpvN5rXb7R6Xy+UhoFar1W21Wl0EkoAaZg1rmhYi89F4TdNCmqaFVFUNkXkTNZHNZvts27aNxhuNCIEI0T2ZkN4bLLhEUfQaueAjDdhsNi+BJD0QUKfT6SYdEFDKAEmSQoqihOjeAJG4/gQ7W7duzQXwP9cBnNexpjhBAAAAAElFTkSuQmCC" />
    <link rel="icon" type="image/png" sizes="16x16" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAlpJREFUOE+Nk0tIVFEYx//n3jtz544z4zg6OuqoqKVp9LDUMAqKFtGiWtSiWkQkEUEtahERRBQVLYJoUW2iRdCiKKJFD4JqE0REDxQfw1hq5DiOztz5mHvPPeeMjlr2wVl8fN/v+z++c87HsB8f24d9+H8A0un0IcbYUdd1PSLyiIg8z3MJyPU8zyEix/M8h4hcz/McInI9z7WJyLJt27Isy7IsS7YsS7YsS7YsW2aMWbZtW5ZlWaZpEsdxEgghDCGEwRjbBpBKpQ4yxo66rusRkedRwQQgEpHreZ5DRK7neQ4RuZ7nOZ7n2a7rWo7jWLZtW47jyI7jSLZty47jSI7jSJZlSYwxadOvCSGMbQDJZPIQY+yI67qe53ke/QsgItf1XIeIHNd1Hdd1Hc/zbNu2Ldu2ZMexJMexJNu2JcaYtAXAGDM2AWKx2GHG2BEiIiLPIyLPdd0CQK7ruq7ruq5t25Zt25LjOLLjOJLjOJLjOBJjTNoUlzFmbAJEo9EjjLHDRERE5BERua7rFgByXdd1Xde1bdu2bNuWHceWHceWHMeSHMeSbNuWGGPSFgBjzNgEiEQihxljh4iIiMgjInJd1y0A5Lqu67qu69i2bdm2LTmOLTmOLTmOJTmOJdm2LTHGpC1xGWPGJkA4HD7CGDtIRJ7neUREnut6BYBc13Ud27Yt27Ylx7Elx7Ekx7Ekx7Ek27YlxpgkSRJjjBkbAMFg8BBj7IDruh4ReUREnut6BYBc13Ud27Yt27Ylx7Elx7Ekx7Ek27YlxpgkSRJjjBl/AQgEAgcZY/uJyPsJ8AOvgZzXMm3oPQAAAABJRU5ErkJggg==" />
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@400;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
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

        .sidebar a.active {
            background: var(--secondary-color);
            color: var(--primary-color);
        }

        .sidebar a.active i {
            color: var(--primary-color);
        }

        .sidebar a.active:hover::before {
            transform: scaleX(0);
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
        <a href="dashboard.php" class="<?= $current_page === 'dashboard.php' ? 'active' : '' ?>"><i class="fa-solid fa-house"></i> Dashboard</a>
        <a href="bookings.php" class="<?= $current_page === 'bookings.php' ? 'active' : '' ?>"><i class="fa-solid fa-calendar-plus"></i> Booking</a>
        <a href="booking-history.php" class="<?= $current_page === 'booking-history.php' ? 'active' : '' ?>"><i class="fa-solid fa-clock-rotate-left"></i> History</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</div>

<div class="main">
    <h1>Your Bookings</h1>

    <ul class="nav nav-tabs" id="bookingTabs" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending">Pending</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#rescheduled">Rescheduled</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#approved">Approved</button></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="pending">
            <?php if ($pending_result): foreach ($pending_result as $b): ?>
            <div class="booking-card">
                <div class="action-icons">
                    <?php if ($b['status'] === 'Pending'): ?>
                        <a href="#" onclick="showEditModal(<?= $b['id'] ?>, '<?= htmlspecialchars($b['service']) ?>', '<?= htmlspecialchars($b['location']) ?>', '<?= $b['appointment_date'] ?>', '<?= $b['appointment_time'] ?>')" title="Edit booking" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fa-solid fa-pen-to-square"></i></a>
                    <?php endif; ?>
                </div>
                <h5><?= htmlspecialchars($b['service']) ?></h5>
                <p><strong>Date:</strong> <?= $b['appointment_date'] ?> - <strong>Time:</strong> <?= date("g:i A", strtotime($b['appointment_time'])) ?></p>
                <p><strong>Technician:</strong> <?= $b['employee_name'] ?: 'Pending' ?></p>
                <p><strong>Status:</strong> <?= $b['status'] ?></p>
                <?php if (isset($b['price'])): ?>
                <p><strong>Price:</strong> ₱<?= number_format($b['price'], 2) ?></p>
                <?php endif; ?>

                <?php if ($b['status'] === 'Pending'): ?>
                <form method="POST" class="cancel-form mt-2" onsubmit="return confirm('Cancel this booking?');">
                    <input type="hidden" name="cancel_booking_id" value="<?= $b['id'] ?>">
                    <button type="submit"><i class="fa-solid fa-ban"></i> Cancel</button>
                </form>
                <?php endif; ?>
            </div>
            <?php endforeach; else: ?>
            <p>No pending bookings.</p>
            <?php endif; ?>
        </div>
        <div class="tab-pane fade" id="rescheduled">
            <?php if ($rescheduled_result): foreach ($rescheduled_result as $b): ?>
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
            <p>No rescheduled bookings.</p>
            <?php endif; ?>
        </div>
        <div class="tab-pane fade" id="approved">
            <?php if ($approved_result): foreach ($approved_result as $b): ?>
            <div class="booking-card">
                <?php if ($b['reschedule_attempt'] == 0): ?>
                <div class="action-icons">
                    <a href="#" onclick="showRescheduleModal(<?= $b['id'] ?>, '<?= $b['appointment_date'] ?>')" title="Reschedule"><i class="fa-solid fa-calendar-days"></i></a>
                </div>
                <?php endif; ?>
                <h5><?= htmlspecialchars($b['service']) ?></h5>
                <p><strong>Date:</strong> <?= $b['appointment_date'] ?> - <strong>Time:</strong> <?= date("g:i A", strtotime($b['appointment_time'])) ?></p>
                <p><strong>Technician:</strong> <?= $b['employee_name'] ?: 'Pending' ?></p>
                <p><strong>Price:</strong> ₱<?= number_format($b['price'], 2) ?></p>
                <p><strong>Status:</strong> 
                    <?php if ($b['status'] === 'Approved' && $b['reschedule_attempt'] > 0): ?>
                        Approved Reschedule
                    <?php else: ?>
                        <?= $b['status'] ?>
                    <?php endif; ?>
                </p>
                <?php if ($b['reschedule_attempt'] > 0): ?>
                    <div class="alert alert-warning mt-3" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Cannot reschedule anymore:</strong> You have already used your one-time reschedule option for this booking.
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; else: ?>
            <p>No approved bookings.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Reschedule Modal -->
<div class="modal fade" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rescheduleModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Reschedule Booking
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="reschedule_booking_id">
                <input type="hidden" id="reschedule_current_date">
                <div class="calendar-container">
                    <div id="rescheduleCalendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Time Slots Modal -->
<div class="modal fade" id="timeSlotsModal" tabindex="-1" aria-labelledby="timeSlotsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="timeSlotsModalLabel">
                    <i class="fas fa-clock"></i>
                    Available Time Slots
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="timeSlotsList">
                    <!-- Time slots will be dynamically inserted here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">
                    <i class="fas fa-question-circle me-2"></i>
                    Confirm Action
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="confirmationMessage" class="mb-3"></p>
                <div id="bookingDetails" class="booking-details"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" id="confirmActionBtn">
                    <i class="fas fa-check me-2"></i>Confirm
                </button>
            </div>
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

<!-- Edit Booking Modal -->
<div class="modal fade" id="editBookingModal" tabindex="-1" aria-labelledby="editBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBookingModalLabel">
                    <i class="fas fa-edit me-2"></i>
                    Edit Booking
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editBookingForm" method="POST">
                    <input type="hidden" name="booking_id" id="edit_booking_id">
                    <div class="mb-3">
                        <label for="edit_service" class="form-label">Service</label>
                        <select class="form-control" name="service" id="edit_service" required onchange="updateEditPrice()">
                            <option value="">-- Select Service --</option>
                            <?php foreach ($services_prices as $service => $price): ?>
                                <option value="<?= htmlspecialchars($service) ?>"><?= htmlspecialchars($service) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_price" class="form-label">Price</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="text" class="form-control" id="edit_price" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_location" class="form-label">Location</label>
                        <input type="text" class="form-control" name="location" id="edit_location" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_appointment_date" class="form-label">Date</label>
                        <div class="input-group">
                            <input type="date" class="form-control" name="appointment_date" id="edit_appointment_date" readonly>
                            <div class="input-group-append">
                                <span class="input-group-text bg-light" data-bs-toggle="tooltip" data-bs-placement="top" title="To change date/time, use the reschedule option">
                                    <i class="fas fa-info-circle"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_appointment_time" class="form-label">Time</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="appointment_time" id="edit_appointment_time" readonly>
                            <div class="input-group-append">
                                <span class="input-group-text bg-light" data-bs-toggle="tooltip" data-bs-placement="top" title="To change date/time, use the reschedule option">
                                    <i class="fas fa-info-circle"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Modal Styles */
.modal-content {
    border-radius: 20px;
    border: none;
    box-shadow: 0 20px 40px var(--card-shadow);
}

.modal-header {
    background: var(--primary-color);
    color: white;
    border-radius: 20px 20px 0 0;
    padding: 1.5rem;
}

.modal-title {
    font-weight: 600;
    font-size: 1.25rem;
}

.modal-body {
    padding: 2rem;
}

.modal-footer {
    border-top: 1px solid rgba(0,0,0,0.1);
    padding: 1.5rem;
}

.form-control {
    border-radius: 8px;
    padding: 0.8rem 1rem;
    border: 1px solid rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: var(--secondary-color);
    box-shadow: 0 0 0 0.2rem rgba(60, 213, 237, 0.1);
}

.form-label {
    font-weight: 500;
    color: var(--text-color);
    margin-bottom: 0.5rem;
}

.btn {
    padding: 0.8rem 1.5rem;
    border-radius: 50px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background-color: var(--primary-color);
    border: none;
}

.btn-primary:hover {
    background-color: var(--secondary-color);
    transform: translateY(-2px);
}

.btn-secondary {
    background-color: #6c757d;
    border: none;
}

.btn-secondary:hover {
    background-color: #5a6268;
    transform: translateY(-2px);
}

.btn-close-white {
    filter: brightness(0) invert(1);
}

/* Confirmation Modal Styles */
#confirmationModal .modal-header {
    background: var(--primary-color);
    color: white;
}

#confirmationModal .modal-content {
    border-radius: 20px;
    border: none;
    box-shadow: 0 20px 40px var(--card-shadow);
}

#confirmationModal .modal-body {
    padding: 2rem;
    text-align: center;
}

#confirmationModal .modal-body i {
    font-size: 3rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

#confirmationModal .modal-footer {
    justify-content: center;
    padding: 1.5rem;
    border-top: 1px solid rgba(0,0,0,0.1);
}

#confirmationModal .btn {
    padding: 0.8rem 1.5rem;
    border-radius: 50px;
    font-weight: 500;
    min-width: 120px;
    transition: all 0.3s ease;
}

#confirmationModal .btn-primary {
    background-color: var(--primary-color);
    border: none;
}

#confirmationModal .btn-primary:hover {
    background-color: var(--secondary-color);
    transform: translateY(-2px);
}

#confirmationModal .btn-secondary {
    background-color: #6c757d;
    border: none;
}

#confirmationModal .btn-secondary:hover {
    background-color: #5a6268;
    transform: translateY(-2px);
}

/* Update the modal backdrop styles */
.modal-backdrop {
    --bs-backdrop-opacity: 0.5;
    --bs-backdrop-bg: rgba(0, 0, 0, 0.5);
    background-color: var(--bs-backdrop-bg);
    z-index: 1040;
    transition: all 0.2s ease-in-out;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

.modal-backdrop.show {
    opacity: var(--bs-backdrop-opacity) !important;
}

.modal-backdrop.fade {
    opacity: 0;
    backdrop-filter: blur(0px);
    -webkit-backdrop-filter: blur(0px);
}

/* Ensure content behind modal is blurred */
body.modal-open .main {
    filter: blur(4px);
    transition: filter 0.2s ease-in-out;
}

/* Modal z-index stacking order */
#rescheduleModal {
    z-index: 1045;
}

#timeSlotsModal {
    z-index: 1046;
}

#confirmationModal {
    z-index: 1047;
}

/* Ensure multiple backdrops stack properly */
.modal-backdrop:nth-child(2) {
    z-index: 1041;
}

.modal-backdrop:nth-child(3) {
    z-index: 1042;
}

/* Edit Modal */
#editBookingModal {
    z-index: 1045;
}

/* Confirmation Modal */
#confirmationModal {
    z-index: 1050;
}

.booking-details {
    background: rgba(7, 53, 63, 0.05);
    border-radius: 12px;
    padding: 1.5rem;
    margin: 1rem 0;
}

.detail-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
    padding: 0.5rem;
}

.detail-item:last-child {
    margin-bottom: 0;
}

.detail-item i {
    color: var(--primary-color);
    font-size: 1.2rem;
    width: 24px;
    text-align: center;
    flex-shrink: 0;
    margin-top: 3px;
}

.detail-item span {
    flex: 1;
    line-height: 1.5;
}

.detail-item strong {
    color: var(--primary-color);
    margin-right: 0.5rem;
    display: block;
    margin-bottom: 0.25rem;
}

#confirmationModal .modal-body {
    text-align: left;
    padding: 1.5rem;
}

#confirmationMessage {
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
    color: var(--text-color);
}

/* Update the confirmation modal buttons */
#confirmationModal .modal-footer {
    justify-content: space-between;
    padding: 1rem 1.5rem;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
}

#confirmationModal .btn {
    min-width: 140px;
    padding: 0.75rem 1.5rem;
}

/* Time Slots Modal Styles */
.time-slot-btn {
    width: 100%;
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 12px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
}

.time-slot-btn:hover {
    transform: translateY(-2px);
}

.time-slot-btn.available {
    background-color: var(--primary-color);
    color: white;
    border: none;
}

.time-slot-btn.available:hover {
    background-color: var(--secondary-color);
    box-shadow: 0 4px 12px rgba(60, 213, 237, 0.2);
}

.time-slot-btn.unavailable {
    background-color: #f8f9fa;
    color: #6c757d;
    border: 1px solid #dee2e6;
    cursor: not-allowed;
}

/* Loading Animation */
.loading-spinner {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.loading-spinner .spinner-border {
    width: 3rem;
    height: 3rem;
    color: var(--secondary-color);
}

.loading-spinner p {
    margin-top: 1rem;
    color: var(--text-color);
    font-weight: 500;
}

/* Alert Styles */
.alert {
    border-radius: 12px;
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
    border: none;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.alert i {
    font-size: 1.5rem;
}

.alert-info {
    background-color: rgba(14, 165, 233, 0.1);
    color: #0284c7;
}

.alert-danger {
    background-color: rgba(239, 68, 68, 0.1);
    color: #dc2626;
}

/* Time Slots Grid */
.time-slots-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
    padding: 0.5rem;
}

@media (max-width: 768px) {
    .time-slots-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    }
}

/* Transition Animations */
.modal.fade .modal-dialog {
    transform: scale(0.95);
    transition: transform 0.2s ease-out;
}

.modal.show .modal-dialog {
    transform: scale(1);
}

.time-slot-btn {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Calendar styles for reschedule calendar */
/* Gray styling for both past dates and today in reschedule calendar */
.fc-daygrid-day.fc-day-today,
.fc-daygrid-day.fc-day-past {
    background-color: rgba(128, 128, 128, 0.05) !important;
    color: #9ca3af !important;
    opacity: 0.6;
    pointer-events: none;
    cursor: not-allowed;
}

/* Make date numbers gray for past dates and today */
.fc-daygrid-day.fc-day-past .fc-daygrid-day-number,
.fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
    color: #9ca3af !important;
}

/* Make all text content gray in past dates and today */
.fc-daygrid-day.fc-day-past *,
.fc-daygrid-day.fc-day-today * {
    color: #9ca3af !important;
}

/* Style for day numbers - alternative selectors */
.fc-daygrid-day.fc-day-past .fc-daygrid-day-top,
.fc-daygrid-day.fc-day-past .fc-day-number,
.fc-daygrid-day.fc-day-today .fc-daygrid-day-top,
.fc-daygrid-day.fc-day-today .fc-day-number {
    color: #9ca3af !important;
}

/* Ensure past date and today events are also grayed out */
.fc-daygrid-day.fc-day-past .fc-event,
.fc-daygrid-day.fc-day-today .fc-event {
    opacity: 0.5;
    filter: grayscale(100%);
}

/* Calendar event styles for reschedule calendar */
.fc-event {
    border-radius: 4px;
    padding: 2px 4px;
    margin-bottom: 2px;
    font-size: 0.85em;
}

/* User booking styles */
.user-booking {
    border: none !important;
    margin: 2px 4px !important;
    padding: 4px 8px !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 2 !important;
}

.user-booking:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.2s ease;
}

/* Availability indicator styles */
.availability-indicator {
    z-index: 1 !important;
    opacity: 0.3;
}

.fc-daygrid-day-events {
    min-height: 2em;
    pointer-events: none;
}

.fc-daygrid-day-events > * {
    pointer-events: auto;
}

/* Calendar navigation buttons */
.fc-button-primary {
    background-color: #07353f !important;
    border-color: #07353f !important;
}

.fc-button-primary:hover {
    background-color: #0ea5e9 !important;
    border-color: #0ea5e9 !important;
}

.fc-button-primary:disabled {
    background-color: #64748b !important;
    border-color: #64748b !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Handle edit form submission
document.getElementById('editBookingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);

    // Get the service and format details for confirmation
    const details = {
        service: document.getElementById('edit_service').value,
        location: document.getElementById('edit_location').value,
        price: document.getElementById('edit_price').value
    };

    showConfirmation(
        'Confirm Update',
        'Are you sure you want to update this booking?',
        `
        <div class="booking-details">
            <div class="detail-item">
                <i class="fas fa-tools"></i>
                <div>
                    <strong>Service</strong>
                    <span>${details.service}</span>
                </div>
            </div>
            <div class="detail-item">
                <i class="fas fa-map-marker-alt"></i>
                <div>
                    <strong>Location</strong>
                    <span>${details.location}</span>
                </div>
            </div>
            <div class="detail-item">
                <i class="fas fa-tag"></i>
                <div>
                    <strong>Price</strong>
                    <span>₱${details.price}</span>
                </div>
            </div>
        </div>
        `,
        function() {
            // Show loading state
            this.disabled = true;
            this.innerHTML = `
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Processing...
            `;

            // Get the time value and ensure it's in 12-hour format
            const timeInput = document.getElementById('edit_appointment_time');
            const timeValue = timeInput.value;
            
            // Create a new FormData with the formatted time
            const submitFormData = new FormData(form);
            submitFormData.set('appointment_time', timeValue);

            fetch('edit_booking.php', {
                method: 'POST',
                body: submitFormData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close both modals
                    const editModal = bootstrap.Modal.getInstance(document.getElementById('editBookingModal'));
                    const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
                    if (editModal) editModal.hide();
                    if (confirmModal) confirmModal.hide();
                    
                    // Show success notification
                    showNotification(
                        'Booking Updated',
                        'Your booking has been successfully updated.',
                        'check-circle',
                        'success'
                    );
                    
                    // Reload the page after a short delay
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    throw new Error(data.message || 'Failed to update booking');
                }
            })
            .catch(error => {
                showNotification(
                    'Error',
                    error.message || 'An error occurred while updating the booking.',
                    'exclamation-circle',
                    'danger'
                );
                
                // Close confirmation modal
                const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
                if (confirmModal) confirmModal.hide();

                // Reset confirm button
                this.disabled = false;
                this.innerHTML = `<i class="fas fa-check me-2"></i>Confirm Update`;
            });
        }
    );
});

// Function to show edit modal
function showEditModal(bookingId, service, location, date, time) {
    document.getElementById('edit_booking_id').value = bookingId;
    document.getElementById('edit_service').value = service;
    document.getElementById('edit_location').value = location;
    document.getElementById('edit_appointment_date').value = date;
    
    // Format time for display
    const timeDate = new Date(`2000-01-01 ${time}`);
    const formattedTime = timeDate.toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: 'numeric',
        hour12: true
    });
    document.getElementById('edit_appointment_time').value = formattedTime;
    
    // Update the price display
    updateEditPrice();
    
    const editModal = new bootstrap.Modal(document.getElementById('editBookingModal'));
    editModal.show();
}

// Function to update price in edit modal
function updateEditPrice() {
    const serviceSelect = document.getElementById('edit_service');
    const selectedService = serviceSelect.value;
    const servicePrices = {
        'Aircon Check-up': 500,
        'Aircon Relocation': 3500,
        'Aircon Repair': 1500,
        'Aircon cleaning (window type)': 800,
        'Window type (inverter)': 2500,
        'Window type (U shape)': 2300,
        'Split type': 2800,
        'Floormounted': 3000,
        'Cassette': 3200,
        'Capacitor Thermostat': 1200
    };
    const priceInput = document.getElementById('edit_price');
    
    if (servicePrices[selectedService]) {
        const price = servicePrices[selectedService];
        priceInput.value = price.toLocaleString('en-PH');
    } else {
        priceInput.value = '';
    }
}

// Function to show confirmation modal
function showConfirmation(title, message, details, callback) {
    const confirmationModal = document.getElementById('confirmationModal');
    const modal = new bootstrap.Modal(confirmationModal);

    // Update modal content
    confirmationModal.querySelector('.modal-title').innerHTML = `
        <i class="fas fa-question-circle me-2"></i>
        ${title}
    `;
    document.getElementById('confirmationMessage').textContent = message;
    document.getElementById('bookingDetails').innerHTML = details;

    // Set up confirmation button
    const confirmBtn = document.getElementById('confirmActionBtn');
    if (callback) {
        confirmBtn.style.display = 'block';
        confirmBtn.innerHTML = `
            <i class="fas fa-check me-2"></i>
            Confirm
        `;

        // Remove any existing click handlers and create new button
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

        // Add new click handler
        newConfirmBtn.addEventListener('click', callback);
    } else {
        confirmBtn.style.display = 'none';
    }

    // Add warning class if it's a warning message
    const modalContent = confirmationModal.querySelector('.modal-content');
    if (title.includes('Not Available')) {
        modalContent.classList.add('warning-modal');
    } else {
        modalContent.classList.remove('warning-modal');
    }

    modal.show();
}

// Function to show reschedule modal with calendar
function showRescheduleModal(bookingId, currentDate) {
    document.getElementById('reschedule_booking_id').value = bookingId;
    document.getElementById('reschedule_current_date').value = currentDate;
    
    const calendarEl = document.getElementById('rescheduleCalendar');
    let calendar = null; // Initialize calendar variable

    // Initialize the modal first
    const modal = new bootstrap.Modal(document.getElementById('rescheduleModal'));
    
    // Remove any existing event listeners
    document.getElementById('rescheduleModal').removeEventListener('shown.bs.modal', initializeCalendar);
    
    // Create a function to initialize the calendar
    function initializeCalendar() {
        if (!calendarEl.classList.contains('calendar-initialized')) {
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 650,
                selectable: true,
                timeZone: 'Asia/Manila',
                events: function(info, successCallback, failureCallback) {
                    // Fetch events for the calendar
                    fetch('get_calendar_events.php')
                        .then(response => response.json())
                        .then(data => {
                            successCallback(data);
                        })
                        .catch(error => {
                            console.error('Error fetching calendar events:', error);
                            failureCallback(error);
                        });
                },
                dateClick: function(info) {
                    const selectedDate = info.dateStr;
                    const currentDate = document.getElementById('reschedule_current_date').value;
                    
                    // Get today's date in Asia/Manila timezone
                    const now = new Date();
                    const manilaDate = new Date(now.toLocaleString('en-US', { timeZone: 'Asia/Manila' }));
                    const today = manilaDate.toISOString().split('T')[0];
                    
                    // Check if selected date is in the past
                    if (selectedDate < today) {
                        showConfirmation(
                            'Date Not Available',
                            'You cannot select a past date.',
                            `
                            <div class="booking-details">
                                <div class="detail-item">
                                    <i class="fas fa-calendar-times"></i>
                                    <div>
                                        <strong>Selected Date</strong>
                                        <span>${new Date(selectedDate).toLocaleDateString('en-US', { 
                                            weekday: 'long', 
                                            year: 'numeric', 
                                            month: 'long', 
                                            day: 'numeric' 
                                        })}</span>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <div>
                                        <strong>Reason</strong>
                                        <span>Cannot book appointments for past dates.</span>
                                    </div>
                                </div>
                            </div>
                            `,
                            null
                        );
                        return;
                    }

                    // Check if trying to book for today
                    if (selectedDate === today) {
                        showConfirmation(
                            'Date Not Available',
                            'Same day booking is not allowed.',
                            `
                            <div class="booking-details">
                                <div class="detail-item">
                                    <i class="fas fa-calendar-day"></i>
                                    <div>
                                        <strong>Selected Date</strong>
                                        <span>${new Date(selectedDate).toLocaleDateString('en-US', { 
                                            weekday: 'long', 
                                            year: 'numeric', 
                                            month: 'long', 
                                            day: 'numeric' 
                                        })}</span>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-info-circle"></i>
                                    <div>
                                        <strong>Reason</strong>
                                        <span>Please select a future date for your appointment.</span>
                                    </div>
                                </div>
                            </div>
                            `,
                            null
                        );
                        return;
                    }

                    // Get all events for the selected date
                    const dateEvents = calendar.getEvents().filter(event => {
                        const eventDate = event.start.toISOString().split('T')[0];
                        return eventDate === selectedDate;
                    });

                    // Check if the selected date has any bookings
                    const hasBookings = dateEvents.some(event => {
                        // Skip background events (availability indicators)
                        if (event.display === 'background') {
                            return false;
                        }
                        // Allow clicking if it's the same date as the current booking
                        if (selectedDate === currentDate) {
                            return false;
                        }
                        // Block if there are any other bookings
                        return true;
                    });

                    if (hasBookings) {
                        showConfirmation(
                            'Date Not Available',
                            'This date is already taken, please select a different date.',
                            `
                            <div class="booking-details">
                                <div class="detail-item">
                                    <i class="fas fa-calendar"></i>
                                    <div>
                                        <strong>Selected Date</strong>
                                        <span>${new Date(selectedDate).toLocaleDateString('en-US', { 
                                            weekday: 'long', 
                                            year: 'numeric', 
                                            month: 'long', 
                                            day: 'numeric' 
                                        })}</span>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <div>
                                        <strong>Status</strong>
                                        <span>This time slot is not available for booking.</span>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-info-circle"></i>
                                    <div>
                                        <strong>What to do</strong>
                                        <span>Please choose another date from the calendar.</span>
                                    </div>
                                </div>
                            </div>
                            `,
                            null
                        );
                        return;
                    }

                    // Show time slots for the selected date
                    showTimeSlots(selectedDate);
                }
            });

            calendar.render();
            calendarEl.classList.add('calendar-initialized');
        }
        
        // Make sure to update the calendar size when modal is shown
        if (calendar) {
            calendar.updateSize();
        }
    }

    // Add the event listener for modal shown
    document.getElementById('rescheduleModal').addEventListener('shown.bs.modal', initializeCalendar);
    
    // Show the modal
    modal.show();
}

// Function to show time slots and get selection
function showTimeSlots(date) {
    // Show loading state
    const timeSlotsList = document.getElementById('timeSlotsList');
    const timeSlotsModal = new bootstrap.Modal(document.getElementById('timeSlotsModal'));
    
    timeSlotsList.innerHTML = `
        <div class="loading-spinner">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p>Loading available time slots...</p>
        </div>`;

    // Update modal title
    document.getElementById('timeSlotsModalLabel').innerHTML = `
        <i class="fas fa-clock"></i>
        Available Times for ${new Date(date).toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        })}`;

    // Fetch available time slots
    fetch('get_time_slots.php?date=' + date)
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            if (data.error) throw new Error(data.error);
            
            const slots = data.slots || [];
            if (slots.length === 0) {
                timeSlotsList.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <div>No time slots available for this date.</div>
                    </div>`;
                return;
            }

            let html = '<div class="time-slots-grid">';
            slots.forEach(slot => {
                const btnClass = slot.available ? 'available' : 'unavailable';
                const disabled = !slot.available;
                const remainingText = slot.available ? 
                    `${slot.remaining} slots remaining` : 
                    'Fully booked';
                
                html += `
                    <button class="time-slot-btn ${btnClass}" 
                            onclick="handleTimeSlotSelection('${date}', '${slot.time}')"
                            ${disabled ? 'disabled' : ''}>
                        <div class="fw-bold">${slot.time}</div>
                        <small>${remainingText}</small>
                    </button>`;
            });
            html += '</div>';
            timeSlotsList.innerHTML = html;
        })
        .catch(error => {
            timeSlotsList.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>Error: ${error.message}</div>
                </div>`;
        });

    timeSlotsModal.show();
}

// Function to handle time slot selection
function handleTimeSlotSelection(date, time) {
    const timeSlotsModal = bootstrap.Modal.getInstance(document.getElementById('timeSlotsModal'));
    timeSlotsModal.hide();

    // Format date for display
    const formattedDate = new Date(date).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    // Convert time to 24-hour format for database
    let databaseTime = time;
    if (time.includes('AM') || time.includes('PM')) {
        // If time is in 12-hour format, convert to 24-hour
        const [timePart, meridiem] = time.split(' ');
        let [hours, minutes] = timePart.split(':');
        hours = parseInt(hours);
        
        if (meridiem === 'PM' && hours !== 12) {
            hours += 12;
        } else if (meridiem === 'AM' && hours === 12) {
            hours = 0;
        }
        
        databaseTime = `${hours.toString().padStart(2, '0')}:${minutes}:00`;
    } else {
        // If time is already in 24-hour format, just ensure it has seconds
        databaseTime = time.includes(':') ? (time + ':00').substring(0, 8) : time + ':00';
    }

    // Format display time
    const displayTime = new Date(`2000-01-01 ${databaseTime}`).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: 'numeric',
        hour12: true
    });

    // Show confirmation modal with booking details
    const bookingDetails = `
        <div class="booking-details">
            <div class="detail-item">
                <i class="fas fa-calendar-day"></i>
                <div>
                    <strong>New Date</strong>
                    <span>${formattedDate}</span>
                </div>
            </div>
            <div class="detail-item">
                <i class="fas fa-clock"></i>
                <div>
                    <strong>New Time</strong>
                    <span>${displayTime}</span>
                </div>
            </div>
        </div>
    `;

    showConfirmation(
        'Confirm Reschedule',
        'Are you sure you want to reschedule this booking?',
        bookingDetails,
        () => {
            const bookingId = document.getElementById('reschedule_booking_id').value;
            const formData = new FormData();
            formData.append('booking_id', bookingId);
            formData.append('new_date', date);
            formData.append('new_time', databaseTime);

            // Show loading state in confirmation modal
            const confirmBtn = document.getElementById('confirmActionBtn');
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Processing...
            `;

            fetch('request_reschedule.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close all modals
                    const modals = document.querySelectorAll('.modal');
                    modals.forEach(modalEl => {
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                    });

                    // Show success notification and reload
                    showNotification(
                        'Booking Rescheduled',
                        'Your booking has been successfully rescheduled.',
                        'check-circle',
                        'success'
                    );
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    // If the error is about reschedule attempt, show a specific message
                    if (data.message.includes('reschedule attempt')) {
                        showNotification(
                            'Reschedule Not Allowed',
                            'You have already used your one-time reschedule for this booking.',
                            'exclamation-circle',
                            'warning'
                        );
                    } else {
                        throw new Error(data.message || 'Failed to reschedule booking');
                    }
                    
                    // Close confirmation modal
                    const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
                    if (confirmModal) confirmModal.hide();
                }
            })
            .catch(error => {
                showNotification(
                    'Error',
                    error.message || 'An error occurred while rescheduling the booking.',
                    'exclamation-circle',
                    'danger'
                );
                
                // Close confirmation modal
                const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
                if (confirmModal) confirmModal.hide();
            });
        }
    );
}

// Add these styles for the booking details in confirmation modal
document.head.insertAdjacentHTML('beforeend', `
<style>
.booking-details {
    background: rgba(7, 53, 63, 0.05);
    border-radius: 12px;
    padding: 1.5rem;
    margin: 1rem 0;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.detail-item:last-child {
    margin-bottom: 0;
}

.detail-item i {
    color: var(--primary-color);
    font-size: 1.2rem;
    width: 24px;
    text-align: center;
}

.detail-item strong {
    color: var(--primary-color);
    margin-right: 0.5rem;
}

#confirmationModal .modal-body {
    text-align: left;
}

#confirmationMessage {
    font-size: 1.1rem;
    margin-bottom: 1rem;
}

/* Add these styles for the info icon */
.input-group-text.bg-light {
    background-color: #f8f9fa;
    border-left: none;
    cursor: help;
}

.input-group-text.bg-light i {
    color: #6c757d;
}

.input-group input[readonly] {
    background-color: #e9ecef;
    cursor: not-allowed;
}

/* Make the input group look connected */
.input-group input:not(:last-child) {
    border-right: none;
}

.input-group-append {
    margin-left: -1px;
}

.input-group-append .input-group-text {
    border-radius: 0 4px 4px 0;
}
</style>
`);

// Create notification container if it doesn't exist
if (!document.getElementById('notification-container')) {
    const container = document.createElement('div');
    container.id = 'notification-container';
    document.body.appendChild(container);
}

// Add showNotification function
function showNotification(title, message, icon, type) {
    // Create notification container if it doesn't exist
    let container = document.getElementById('notification-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notification-container';
        container.style.position = 'fixed';
        container.style.top = '20px';
        container.style.right = '20px';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${icon}"></i>
        <div class="notification-content">
            <div class="notification-title">${title}</div>
            <div class="notification-message">${message}</div>
        </div>
    `;
    
    // Add to container
    container.appendChild(notification);
    
    // Trigger animation
    setTimeout(() => notification.classList.add('show'), 100);
    
    // Remove after delay
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Add CSS for notifications
const style = document.createElement('style');
style.textContent = `
    .notification {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: flex-start;
        padding: 16px;
        margin-bottom: 10px;
        transform: translateX(120%);
        transition: transform 0.3s ease;
        max-width: 400px;
    }
    
    .notification.show {
        transform: translateX(0);
    }
    
    .notification i {
        margin-right: 12px;
        font-size: 20px;
    }
    
    .notification.success i { color: #48c78e; }
    .notification.warning i { color: #ffe08a; }
    .notification.danger i { color: #f14668; }
    
    .notification-content {
        flex: 1;
    }
    
    .notification-title {
        font-weight: 600;
        margin-bottom: 4px;
    }
    
    .notification-message {
        color: #666;
        font-size: 0.9em;
    }
`;
document.head.appendChild(style);

// Add these styles for the warning modal
const warningModalStyles = document.createElement('style');
warningModalStyles.textContent = `
    .warning-modal {
        background-color: #fff3cd !important;
        border: 1px solid #ffeeba !important;
    }
    
    .warning-modal .modal-header {
        background-color: #ffc107 !important;
        color: #856404 !important;
        border-bottom: 1px solid #ffeeba !important;
    }
    
    .warning-modal .modal-title i {
        color: #856404 !important;
    }
    
    .warning-modal .booking-details {
        background: rgba(255, 193, 7, 0.1) !important;
    }
    
    .warning-modal .detail-item i {
        color: #856404 !important;
    }
    
    .warning-modal .detail-item strong {
        color: #856404 !important;
    }
    
    .warning-modal .btn-secondary {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: white !important;
    }
`;
document.head.appendChild(warningModalStyles);
</script>
</body>
</html>