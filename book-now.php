<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get current page for active sidebar highlighting
$current_page = basename($_SERVER['PHP_SELF']);

// Include the database connection
include('db_connection.php');

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
        <a href="book-now.php" class="<?= $current_page === 'book-now.php' ? 'active' : '' ?>"><i class="fa-solid fa-calendar-plus"></i> Booking</a>
        <a href="cancel_booking.php" class="<?= $current_page === 'cancel_booking.php' ? 'active' : '' ?>"><i class="fa-solid fa-clock-rotate-left"></i> History</a>
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
                    <a href="#" onclick="showEditModal(<?= $b['id'] ?>, '<?= htmlspecialchars($b['service']) ?>', '<?= htmlspecialchars($b['location']) ?>', '<?= $b['appointment_date'] ?>', '<?= $b['appointment_time'] ?>')" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
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
                <p id="confirmationMessage"></p>
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
                        <label for="edit_location" class="form-label">Location</label>
                        <input type="text" class="form-control" name="location" id="edit_location" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_appointment_date" class="form-label">Date</label>
                        <input type="date" class="form-control" name="appointment_date" id="edit_appointment_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_appointment_time" class="form-label">Time</label>
                        <select class="form-control" name="appointment_time" id="edit_appointment_time" required>
                            <option value="">Select a time</option>
                            <?php
                            $start = strtotime("08:00");
                            $end = strtotime("16:20");
                            $interval = 100 * 60; // 1 hour 40 minutes
                            while ($start <= $end) {
                                $time = date("H:i", $start);
                                echo "<option value=\"$time\">" . date("g:i A", $start) . "</option>";
                                $start += $interval;
                            }
                            ?>
                        </select>
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
</style>

<script>
// Function to populate and show edit modal
function showEditModal(bookingId, service, location, date, time) {
    document.getElementById('edit_booking_id').value = bookingId;
    document.getElementById('edit_service').value = service;
    document.getElementById('edit_location').value = location;
    document.getElementById('edit_appointment_date').value = date;
    document.getElementById('edit_appointment_time').value = time;
    
    const editModal = new bootstrap.Modal(document.getElementById('editBookingModal'));
    editModal.show();
}

// Handle edit form submission
document.getElementById('editBookingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.innerHTML;
    
    // Show loading state
    submitButton.disabled = true;
    submitButton.innerHTML = `
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        Updating...
    `;
    
    fetch('edit_booking.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const editModal = bootstrap.Modal.getInstance(document.getElementById('editBookingModal'));
            editModal.hide();
            window.location.reload(); // Refresh to show updated data
        } else {
            throw new Error(data.message || 'Failed to update booking');
        }
    })
    .catch(error => {
        // Show error message
        alert('An error occurred while updating the booking: ' + error.message);
        
        // Reset button
        submitButton.disabled = false;
        submitButton.innerHTML = originalButtonText;
    });
});

// Function to update price in edit modal
function updateEditPrice() {
    const serviceSelect = document.getElementById('edit_service');
    const selectedService = serviceSelect.value;
    const servicePrices = <?php echo json_encode($services_prices); ?>;
    
    if (servicePrices[selectedService]) {
        // If we need to update price somewhere in the modal
        console.log('Selected service price:', servicePrices[selectedService]);
    }
}

// Function to show confirmation modal
function showConfirmation(message, callback) {
    const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    document.getElementById('confirmationMessage').textContent = message;
    
    const confirmBtn = document.getElementById('confirmActionBtn');
    const originalBtnHtml = confirmBtn.innerHTML;
    
    // Remove any existing click handlers
    confirmBtn.replaceWith(confirmBtn.cloneNode(true));
    
    // Add new click handler
    document.getElementById('confirmActionBtn').addEventListener('click', function() {
        // Show loading state
        this.disabled = true;
        this.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Processing...
        `;
        
        callback();
    });
    
    modal.show();
}

// Update cancel booking form submission
document.querySelectorAll('.cancel-form').forEach(form => {
    form.onsubmit = function(e) {
        e.preventDefault();
        showConfirmation('Are you sure you want to cancel this booking?', () => {
            form.submit();
        });
    };
});

// Update edit booking form submission
document.getElementById('editBookingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    showConfirmation('Are you sure you want to update this booking?', () => {
        const submitButton = this.querySelector('button[type="submit"]');
        
        fetch('edit_booking.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const editModal = bootstrap.Modal.getInstance(document.getElementById('editBookingModal'));
                editModal.hide();
                window.location.reload();
            } else {
                throw new Error(data.message || 'Failed to update booking');
            }
        })
        .catch(error => {
            alert('An error occurred while updating the booking: ' + error.message);
            const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
            confirmModal.hide();
        });
    });
});
</script>

</body>
</html>
