<?php
session_start();
require_once 'includes/verify_check.php';

// Set timezone
date_default_timezone_set('Asia/Manila');

// Get current page for active sidebar highlighting
$current_page = basename($_SERVER['PHP_SELF']);

require_once 'config/database.php';
$conn = Database::getConnection();

$user_id = $_SESSION['user_id'];

// Debug information
error_log("User ID from session: " . print_r($user_id, true));

// First, let's check the table structure
$table_info = $conn->query("DESCRIBE users");
while($row = $table_info->fetch_assoc()) {
    error_log("Column: " . print_r($row['Field'], true));
}

// Fetch user's full name
$user_full_name = 'User'; // Default value
$user_contact = '';
$user_district = '';
$user_barangay = '';
$user_houseno = '';
$user_street = '';
$user_query = $conn->prepare("SELECT CONCAT(fname, ' ', lname) as full_name, contact, district, barangay, houseno, street FROM user WHERE id = ?");
if ($user_query) {
    $user_query->bind_param("i", $user_id);
    $user_query->execute();
    $user_result = $user_query->get_result();
    error_log("Query result rows: " . print_r($user_result->num_rows, true));
    if ($user_data = $user_result->fetch_assoc()) {
        $user_full_name = $user_data['full_name'];
        $user_contact = $user_data['contact'];
        $user_district = $user_data['district'];
        $user_barangay = $user_data['barangay'];
        $user_houseno = $user_data['houseno'];
        $user_street = $user_data['street'];
        error_log("Found user name: " . $user_full_name);
    } else {
        error_log("No user found for ID: " . $user_id);
        // Let's check what's in the users table
        $all_users = $conn->query("SELECT * FROM users WHERE id = " . $user_id);
        if($user_row = $all_users->fetch_assoc()) {
            error_log("User data found: " . print_r($user_row, true));
        }
    }
    $user_query->close();
} else {
    error_log("Failed to prepare user query");
}

$calendar_events = [];
$date_counts = [];
$today = date('Y-m-d');

// Get all bookings for slot availability
$all_result = $conn->query("SELECT appointment_date FROM bookings WHERE status != 'Cancelled'");
while ($row = $all_result->fetch_assoc()) {
    $date = $row['appointment_date'];
    if (!isset($date_counts[$date])) $date_counts[$date] = 0;
    $date_counts[$date]++;
}

// Get user's bookings
$user_bookings = [];
$user_stmt = $conn->prepare("
    SELECT id, appointment_date, appointment_time, service, status, reschedule_attempt
    FROM bookings 
    WHERE user_id = ? 
    AND status != 'Cancelled'
    AND appointment_date >= CURDATE()
");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

// Add user's bookings first
while ($row = $user_result->fetch_assoc()) {
    // Create a DateTime object for the appointment date
    $appointmentDate = new DateTime($row['appointment_date']);
    $appointmentDate->setTime(0, 0, 0); // Set time to midnight

    // Determine the display title based on status and reschedule_attempt
    $displayTitle = $row['status'];
    if ($row['status'] === 'Approved' && intval($row['reschedule_attempt']) > 0) {
        $displayTitle = 'Approved Reschedule';
    }

    $calendar_events[] = [
        "title" => $displayTitle,
        "start" => $appointmentDate->format('Y-m-d'), // Use only the date part
        "display" => "block",
        "backgroundColor" => "#0ea5e9",
        "textColor" => "#ffffff",
        "classNames" => ["user-booking"],
        "allDay" => true,
        "extendedProps" => [
            "type" => "user_booking",
            "time" => $row['appointment_time'],
            "booking_id" => $row['id'],
            "status" => $row['status'],
            "service" => $row['service'],
            "reschedule_attempt" => $row['reschedule_attempt']
        ]
    ];
}
$user_stmt->close();

// Then add availability background events
$max_slots_per_day = 46;
$range_start = strtotime('-1 month');
$range_end = strtotime('+3 months');

for ($i = $range_start; $i <= $range_end; $i += 86400) {
    $date = date('Y-m-d', $i);
    $booked = $date_counts[$date] ?? 0;
    $remaining = $max_slots_per_day - $booked;

    if ($date <= $today) {
        $color = "#e5e7eb"; // Gray color for past dates and today
        $title = "";
    } elseif ($remaining <= 0) {
        $color = "#ff6b6b";
        $title = "";
    } else {
        $color = "#48c78e";
        $title = "";
    }

    $calendar_events[] = [
        "title" => $title,
        "start" => $date,
        "display" => "background",
        "color" => $color,
        "classNames" => ["availability-indicator"],
        "extendedProps" => [
            "type" => "availability"
        ]
    ];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>AirGo | Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='20' fill='%2307353f'/%3E%3Cpath d='M30 50c0-11 9-20 20-20s20 9 20 20-9 20-20 20-20-9-20-20zm35 0c0-8.3-6.7-15-15-15s-15 6.7-15 15 6.7 15 15 15 15-6.7 15-15z' fill='%233cd5ed'/%3E%3Cpath d='M50 60c-5.5 0-10-4.5-10-10s4.5-10 10-10 10 4.5 10 10-4.5 10-10 10z' fill='white'/%3E%3C/svg%3E" />
    <link rel="icon" type="image/png" sizes="32x32" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAABKhJREFUWEe9V21sU2UUfu7H3a7dmDAgKgwQPxAkKAYnJsyPGCGK0ZAQFfEHJGpigkT8gYrxB5pI/GD+QBNjNCr+ICYaEzWBRDBRIQQIiCIIKLIvGIPtbrfb7b3XnHPvbVc2trs3bU/S7H7c877Pec95z3veK+AeHOIe4MN/B0CWZVkQhHui3LquX5YkSboXk+u6PqwoCud3HEAkEuH8qqrS0NBQYWxsLDY2NhaNRqNRVVVjmqYpuq4rpHwwGHQHAgG3y+VyezweN/1yu91uQRDcBIr+CoIgCILAf222GfRqgKZpGsdHYDRNm6Xr+llZlr1/CcDQ0BCNjZumxXVdVxVFiWmaFlVVNaooSjQejxPQEUVRYpqmxXRd5+vpuu5yOBwet9vtczgcPofD4SWQBMjpdLodDofL6XS6nE4nARFFUeR7QRAIJIMxAVJVVWPQODNBkiSvLMsDkiTJt23BwMAAjVEURY3H4zFFUaKqqkYJiKqqUU3T6G9UVdW4pmkxAqfrOgMWRdHjdDq9BNDhcPgcDofX5XL5XS6Xz+12+10ul9fpdHoJqMvlcjudTjcBJVOYQOm6rsXjcS0Wi8UjkUiU/kYikWgsFovF4/F4LBaLxePxeDQaZUDWFPT39xMAVVXVWCwWVRQlqihKhEAqihIxmUxRXdf52WQyEQCPKIo+URR9BNDpdPqtVqvfYrH4rFar3263++12u89ms3ltNpvXZrN5rFYrAXWRDgioYU7DnMSUoihsXkVRFPJEJBKJRKPR8O0a6O3tNQNgJlRVNayqaphAEgOSJIUlSQobDLgoK0RR9JEGLBaL32q1+m02m5/AEli73e6z2+0+q9Xqc7lcXrvd7rFarW6r1eq2WCwExk2ZQCDJ75IkhXVdD+u6HtZ1PazrehiYEtZ1XTYykCRJYdu27du3j8YbIAJGbIQJpKqqYUmSwgTSYMFNLJAGHA6H32Kx+G02m5/AElgCabfbfTabzWu1Wj0ul8tjs9k8VqvVbbFY3E6n0+10Ot0EkjQgSVKY7qmqGqbxkiSFVVUNU8FTVTVEz7IsywkNbN682QyAM0BV1RCZkEBKkhTSNC0kSVKIGHA4HD6n0+mzWCw+AksmtNlsXpvN5rXb7R6Xy+UhoFar1W21Wl0EkoAaZg1rmhYi89F4TdNCmqaFVFUNkXkTNZHNZvts27aNxhuNCIEI0T2ZkN4bLLhEUfQaueAjDdhsNi+BJD0QUKfT6SYdEFDKAEmSQoqihOjeAJG4/gQ7W7duzQXwP9cBnNexpjhBAAAAAElFTkSuQmCC" />
    <link rel="icon" type="image/png" sizes="16x16" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAlpJREFUOE+Nk0tIVFEYx//n3jtz544z4zg6OuqoqKVp9LDUMAqKFtGiWtSiWkQkEUEtahERRBQVLYJoUW2iRdCiKKJFD4JqE0REDxQfw1hq5DiOztz5mHvPPeeMjlr2wVl8fN/v+z++c87HsB8f24d9+H8A0un0IcbYUdd1PSLyiIg8z3MJyPU8zyEix/M8h4hcz/McInI9z7WJyLJt27Isy7IsS7YsS7IsS7YsW2aMWbZtW5ZlWaZpEsdxEgghDCGEwRjbBpBKpQ4yxo66rusRkedRwQQgEpHreZ5DRK7neQ4RuZ7nOZ7n2a7rWo7jWLZtW47jyI7jSLZty47jSI7jSJZlSYwxadOvCSGMbQDJZPIQY+yI67qe53ke/QsgItf1XIeIHNd1Hdd1Hc/zbNu2Ldu2ZMexJMexJNu2JcaYtAXAGDM2AWKx2GHG2BEiIiLPIyLPdd0CQK7ruq7ruq5t25Zt25LjOLLjOJLjOJLjOBJjTNoUlzFmbAJEo9EjjLHDRERE5BERua7rFgByXdd1Xde1bdu2bNuWHceWHceWHMeSHMeSbNuWGGPSFgBjzNgEiEQihxljh4iIiMgjInJd1y0A5Lqu67qu69i2bdm2LTmOLTmOLTmOJTmOJdm2LTHGpC1xGWPGJkA4HD7CGDtIRJ7neUREnut6BYBc13Ud27Yt27Ylx7Elx7Ekx7Ekx7Ek27YlxpgkSRJjjBkbAMFg8BBj7IDruh4ReUREnut6BYBc13Ud27Yt27Ylx7Elx7Ekx7Ek27YlxpgkSRJjjBl/AQgEAgcZY/uJyPsJ8AOvgZzXMm3oPQAAAABJRU5ErkJggg==" />
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@400;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

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

        .main-container {
            margin-left: 250px;
            padding: clamp(1.5rem, 4vw, 3rem);
            min-height: 100vh;
        }

        .calendar-container {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px var(--card-shadow);
            transition: transform 0.3s ease;
        }

        .calendar-container:hover {
            transform: translateY(-5px);
        }

        .label-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .label-title i {
            color: var(--secondary-color);
        }

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

        .time-slot-btn {
            width: 100%;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .time-slot-btn:hover {
            transform: translateY(-2px);
        }

        .time-slot-btn.available {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }

        .time-slot-btn.unavailable {
            background-color: #f8f9fa;
            color: #6c757d;
            border: 1px solid #dee2e6;
            cursor: not-allowed;
        }

        /* Readonly input styling */
        .form-control[readonly] {
            background-color: #f8f9fa;
            cursor: not-allowed;
            opacity: 0.8;
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

            .nav-links a {
                margin: 0.5rem;
                padding: 10px 20px;
            }

            .main-container {
                margin-left: 0;
                padding: 1.5rem;
            }
        }

        @media (max-width: 575px) {
            .sidebar {
                padding: 0.5rem;
            }

            .nav-links a {
                padding: 8px 16px;
                font-size: 0.9rem;
                min-width: 120px;
                text-align: center;
                justify-content: center;
            }

            .main-container {
                padding: 1rem;
            }

            .calendar-container {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
<div class="loading"></div>

<div class="sidebar">
    <h2>Air<span>go</span></h2>
    <div class="nav-links">
        <a href="dashboard.php" class="<?= $current_page === 'dashboard.php' ? 'active' : '' ?>"><i class="fas fa-home"></i> Dashboard</a>
        <a href="bookings.php" class="<?= $current_page === 'bookings.php' ? 'active' : '' ?>"><i class="fas fa-calendar-alt"></i> Booking</a>
        <a href="booking-history.php" class="<?= $current_page === 'booking-history.php' ? 'active' : '' ?>"><i class="fa-solid fa-clock-rotate-left"></i> History</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main-container">
    <div class="calendar-container">
        <div class="calendar-header">
            <div class="calendar-title">
                <i class="fas fa-calendar-check"></i>
                Select a Date to Book
            </div>
        </div>
        <div class="calendar-legend">
            <div class="legend-item">
                <div class="legend-color legend-available"></div>
                <span>Available</span>
            </div>
            <div class="legend-item">
                <div class="legend-color legend-booked"></div>
                <span>Fully Booked</span>
            </div>
            <div class="legend-item">
                <div class="legend-color legend-your-booking"></div>
                <span>Your Booking</span>
            </div>
        </div>
        <div id="calendar"></div>
    </div>
    </div>

<!-- Time Slots Modal -->
<div class="modal fade" id="timeSlotsModal" tabindex="-1" aria-labelledby="timeSlotsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="timeSlotsModalLabel">Available Time Slots</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="timeSlotsList">
                    <!-- Time slots will be dynamically inserted here -->
                </div>
                <div id="bookingForm" style="display: none;">
                    <form id="appointmentForm" onsubmit="handleBookingSubmit(this, event)">
                        <input type="hidden" name="appointment_date" id="appointment_date">
                        <input type="hidden" name="appointment_time" id="appointment_time">
                        
                        <div class="mb-3">
                            <label for="service" class="form-label">Service Type</label>
                            <select class="form-select" id="service" name="service" onchange="updatePrice()" required>
                                <option value="">Select a service</option>
                                <option value="Aircon Check-up">Aircon Check-up</option>
                                <option value="Aircon Relocation">Aircon Relocation</option>
                                <option value="Aircon Repair">Aircon Repair</option>
                                <option value="Aircon cleaning (window type)">Aircon cleaning (window type)</option>
                                <option value="Window type (inverter)">Window type (inverter)</option>
                                <option value="Window type (U shape)">Window type (U shape)</option>
                                <option value="Split type">Split type</option>
                                <option value="Floormounted">Floormounted</option>
                                <option value="Cassette">Cassette</option>
                                <option value="Capacitor Thermostat">Capacitor Thermostat</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="service_price" class="form-label">Service Price (PHP)</label>
                            <input type="number" class="form-control" id="service_price" name="service_price" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($user_full_name) ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Contact Number</label>
                            <div class="input-group">
                                <span class="input-group-text">+639</span>
                                <input type="tel" class="form-control" id="phone" name="phone" pattern="[0-9]{9}" maxlength="9" placeholder="123456789" required readonly>
                                <button type="button" class="btn btn-outline-secondary" onclick="toggleEdit('phone')">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                            </div>
                            <small class="text-muted">Format: +639XXXXXXXXX</small>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Complete Address</label>
                            <input type="text" class="form-control" id="location" name="location" readonly>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="house_number" class="form-label">House Number</label>
                                <input type="text" class="form-control" id="house_number" name="house_number" required onchange="updateLocation()">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="street" class="form-label">Street</label>
                                <input type="text" class="form-control" id="street" name="street" required onchange="updateLocation()">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="district" class="form-label">District</label>
                                <div class="input-group">
                                    <select class="form-select" id="district" name="district" onchange="updateBarangays(); updateLocation()" required readonly>
                                        <option value="">Select District</option>
                                        <option value="Poblacion">Poblacion</option>
                                        <option value="Talomo">Talomo</option>
                                        <option value="Agdao">Agdao</option>
                                        <option value="Buhangin">Buhangin</option>
                                        <option value="Bunawan">Bunawan</option>
                                        <option value="Paquibato">Paquibato</option>
                                        <option value="Baguio">Baguio</option>
                                        <option value="Calinan">Calinan</option>
                                        <option value="Marilog">Marilog</option>
                                        <option value="Toril">Toril</option>
                                        <option value="Tugbok">Tugbok</option>
                                    </select>
                                    <button type="button" class="btn btn-outline-secondary" onclick="toggleEdit('district')">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="barangay" class="form-label">Barangay</label>
                                <div class="input-group">
                                    <select class="form-select" id="barangay" name="barangay" required readonly>
                                        <option value="">Select Barangay</option>
                                    </select>
                                    <button type="button" class="btn btn-outline-secondary" onclick="toggleEdit('barangay')">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="note" class="form-label">Additional Notes (Optional)</label>
                            <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                        </div>

                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-secondary me-2" onclick="backToTimeSlots()">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-calendar-check me-2"></i>Book Appointment
                            </button>
                        </div>
                    </form>
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
                    Confirm Booking
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="confirmationMessage"></p>
                <div id="bookingDetails" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" id="confirmActionBtn">
                    <i class="fas fa-check me-2"></i>Confirm Booking
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="successModalLabel">
                    <i class="fas fa-check-circle me-2"></i>
                    Booking Successful
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="window.location.reload();"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-4">
                    <i class="fas fa-calendar-check text-success" style="font-size: 4rem;"></i>
                </div>
                <h4 class="mb-3">Thank you for your booking!</h4>
                <p class="mb-0">Your appointment has been successfully scheduled.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-success px-4" onclick="window.location.reload();">
                    <i class="fas fa-check me-2"></i>Done
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Notification Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">
                    <i class="fas fa-info-circle me-2"></i>
                    <span id="notificationTitle">Notification</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-4">
                    <i id="notificationIcon" class="fas fa-calendar-times text-warning" style="font-size: 4rem;"></i>
                </div>
                <p id="notificationMessage" class="mb-0" style="font-size: 1.1rem;"></p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-check me-2"></i>Okay
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reschedule Option Modal -->
<div class="modal fade" id="rescheduleOptionModal" tabindex="-1" aria-labelledby="rescheduleOptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rescheduleOptionModalLabel">
                    <i class="fas fa-calendar-check me-2"></i>
                    Existing Booking Found
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-4">
                    <i class="fas fa-calendar-alt text-primary" style="font-size: 4rem;"></i>
                </div>
                <h4 class="mb-3">You have an approved booking on this date</h4>
                <p id="rescheduleOptionMessage" class="mb-4"></p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary btn-lg" id="rescheduleBookingBtn">
                        <i class="fas fa-calendar-days me-2"></i>Reschedule This Booking
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                </div>
            </div>
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

<!-- Reschedule Time Slots Modal -->
<div class="modal fade" id="rescheduleTimeSlotsModal" tabindex="-1" aria-labelledby="rescheduleTimeSlotsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rescheduleTimeSlotsModalLabel">
                    <i class="fas fa-clock"></i>
                    Available Time Slots
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="rescheduleTimeSlotsList">
                    <!-- Time slots will be dynamically inserted here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Define global functions first
window.backToTimeSlots = function() {
    const timeSlotsList = document.getElementById('timeSlotsList');
    const bookingForm = document.getElementById('bookingForm');
    const modalTitle = document.getElementById('timeSlotsModalLabel');
    const date = document.getElementById('appointment_date').value;

    // Update modal title and show time slots
    showTimeSlots(date);
    
    // Show time slots and hide booking form
    timeSlotsList.style.display = 'block';
    bookingForm.style.display = 'none';
};

// Function to show time slots
window.showTimeSlots = function(date) {
    // Update modal title
    document.getElementById('timeSlotsModalLabel').textContent = 
        `Available Time Slots for ${new Date(date).toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        })}`;

    // Show loading state
    const timeSlotsList = document.getElementById('timeSlotsList');
    const bookingForm = document.getElementById('bookingForm');
    timeSlotsList.style.display = 'block';
    bookingForm.style.display = 'none';
    timeSlotsList.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading available time slots...</p>
        </div>`;

    fetch('get_slots.php?date=' + date)
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            if (data.error) throw new Error(data.error);
            
            const slots = data.slots || [];
            if (slots.length === 0) {
                timeSlotsList.innerHTML = `
                    <div class="alert alert-info" role="alert">
                        No time slots available for this date.
                    </div>`;
                return;
            }

            let html = '<div class="row g-3">';
            slots.forEach(slot => {
                const btnClass = slot.available ? 'available' : 'unavailable';
                const disabled = !slot.available;
                const remainingText = slot.available ? 
                    `${slot.remaining} slots remaining` : 
                    'Fully booked';
                
                html += `
                    <div class="col-md-4">
                        <button class="time-slot-btn ${btnClass}" 
                                onclick="showBookingForm('${date}', '${slot.time}')"
                                ${disabled ? 'disabled' : ''}>
                            <div class="fw-bold">${slot.time}</div>
                            <small>${remainingText}</small>
                        </button>
                    </div>`;
            });
            html += '</div>';
            timeSlotsList.innerHTML = html;
        })
        .catch(error => {
            timeSlotsList.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    Error: ${error.message}
                </div>`;
        });
}

// Function to show confirmation modal
function showConfirmation(message, details, callback) {
    const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    document.getElementById('confirmationMessage').textContent = message;
    document.getElementById('bookingDetails').innerHTML = details;
    
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
        
        callback(calendar);  // Pass calendar instance to callback
    });
    
    modal.show();
}

// Function to show notification modal
function showNotification(title, message, icon = 'calendar-times', type = 'warning') {
    const modalElement = document.getElementById('notificationModal');
    if (!modalElement) return;

    const modal = new bootstrap.Modal(modalElement);
    
    // Update title if element exists
    const titleElement = document.getElementById('notificationTitle');
    if (titleElement) {
        titleElement.textContent = title;
    }
    
    // Update message if element exists
    const messageElement = document.getElementById('notificationMessage');
    if (messageElement) {
        messageElement.innerHTML = message; // Changed to innerHTML to support HTML content
    }
    
    // Update icon if element exists
    const iconElement = document.getElementById('notificationIcon');
    if (iconElement) {
        iconElement.className = `fas fa-${icon} text-${type}`;
    }
    
    // Update header if element exists
    const headerElement = modalElement.querySelector('.modal-header');
    if (headerElement) {
        headerElement.className = `modal-header bg-${type} text-white`;
    }
    
    // Update button if element exists
    const buttonElement = modalElement.querySelector('.modal-footer .btn');
    if (buttonElement) {
        buttonElement.className = `btn btn-${type} px-4`;
    }
    
    modal.show();
}

// Function to show booking details notification for all booking statuses
function showBookingDetailsNotification(bookingTitle, formattedDate, bookingData) {
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

    const service = bookingData.service || 'Service information not available';
    const time = bookingData.time || 'Time not available';
    const price = servicePrices[service] || 'Price not available';
    const status = bookingData.status || bookingTitle;
    
    // Format time for display
    let displayTime = time;
    if (time !== 'Time not available') {
        try {
            displayTime = new Date(`2000-01-01 ${time}`).toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: 'numeric',
                hour12: true
            });
        } catch (e) {
            displayTime = time;
        }
    }

    // Determine message based on booking status
    let statusMessage = '';
    let modalIcon = 'calendar-check';
    let modalType = 'info';
    
    const statusLower = status.toLowerCase();
    
    if (statusLower.includes('pending')) {
        statusMessage = `Your ${status} booking is awaiting confirmation.`;
        modalIcon = 'clock';
        modalType = 'warning';
    } else if (statusLower.includes('approved')) {
        statusMessage = `Your booking has been approved and confirmed.`;
        modalIcon = 'check-circle';
        modalType = 'success';
    } else if (statusLower.includes('rescheduled') || statusLower.includes('reschedule')) {
        statusMessage = `Your booking has been rescheduled and is awaiting approval.`;
        modalIcon = 'calendar-alt';
        modalType = 'info';
    } else if (statusLower.includes('completed') || statusLower.includes('done')) {
        statusMessage = `Your service has been completed successfully.`;
        modalIcon = 'check-double';
        modalType = 'success';
    } else if (statusLower.includes('cancelled')) {
        statusMessage = `Your booking has been cancelled.`;
        modalIcon = 'times-circle';
        modalType = 'danger';
    } else if (statusLower.includes('rejected')) {
        statusMessage = `Your booking request has been rejected.`;
        modalIcon = 'exclamation-triangle';
        modalType = 'danger';
    } else {
        statusMessage = `You have a ${status} booking on this date.`;
        modalIcon = 'calendar-check';
        modalType = 'info';
    }

    const message = `
        <div class="mb-3">
            <strong>${statusMessage}</strong>
        </div>
        <div class="booking-details-card">
            <div class="booking-detail-row">
                <div class="detail-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="detail-content">
                    <strong>Status:</strong><br>
                    <span class="detail-value status-${status.toLowerCase().replace(/\s+/g, '-')}">${status}</span>
                </div>
            </div>
            <div class="booking-detail-row">
                <div class="detail-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="detail-content">
                    <strong>Service:</strong><br>
                    <span class="detail-value">${service}</span>
                </div>
            </div>
            <div class="booking-detail-row">
                <div class="detail-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="detail-content">
                    <strong>Date:</strong><br>
                    <span class="detail-value">${formattedDate}</span>
                </div>
            </div>
            <div class="booking-detail-row">
                <div class="detail-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="detail-content">
                    <strong>Time:</strong><br>
                    <span class="detail-value">${displayTime}</span>
                </div>
            </div>
            <div class="booking-detail-row">
                <div class="detail-icon">
                    <i class="fas fa-peso-sign"></i>
                </div>
                <div class="detail-content">
                    <strong>Price:</strong><br>
                    <span class="detail-value price">₱${typeof price === 'number' ? price.toLocaleString() : price}</span>
                </div>
            </div>
        </div>
        <div class="mt-3 text-muted">
            <small>Please select a different date to make a new booking.</small>
        </div>
    `;

    showNotification(
        'Booking Details',
        message,
        modalIcon,
        modalType
    );
}

// Function to show reschedule not available modal with booking details
window.showRescheduleNotAvailableModal = function(formattedDate, bookingData) {
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

    const service = bookingData.service || 'Service information not available';
    const time = bookingData.time || 'Time not available';
    const price = servicePrices[service] || 'Price not available';
    const status = bookingData.status || 'Approved';
    
    // Format time for display
    let displayTime = time;
    if (time !== 'Time not available') {
        try {
            displayTime = new Date(`2000-01-01 ${time}`).toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: 'numeric',
                hour12: true
            });
        } catch (e) {
            displayTime = time;
        }
    }

    const message = `
        <div class="mb-3">
            <strong>Reschedule is no longer available for this booking.</strong>
        </div>
        <div class="alert alert-warning mb-3" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 8px; padding: 1rem;">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Note:</strong> You have already used your one-time reschedule option for this booking.
        </div>
        <div class="booking-details-card">
            <div class="booking-detail-row">
                <div class="detail-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="detail-content">
                    <strong>Status:</strong><br>
                    <span class="detail-value status-${status.toLowerCase().replace(/\s+/g, '-')}">${status}</span>
                </div>
            </div>
            <div class="booking-detail-row">
                <div class="detail-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="detail-content">
                    <strong>Service:</strong><br>
                    <span class="detail-value">${service}</span>
                </div>
            </div>
            <div class="booking-detail-row">
                <div class="detail-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="detail-content">
                    <strong>Date:</strong><br>
                    <span class="detail-value">${formattedDate}</span>
                </div>
            </div>
            <div class="booking-detail-row">
                <div class="detail-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="detail-content">
                    <strong>Time:</strong><br>
                    <span class="detail-value">${displayTime}</span>
                </div>
            </div>
            <div class="booking-detail-row">
                <div class="detail-icon">
                    <i class="fas fa-peso-sign"></i>
                </div>
                <div class="detail-content">
                    <strong>Price:</strong><br>
                    <span class="detail-value price">₱${typeof price === 'number' ? price.toLocaleString() : price}</span>
                </div>
            </div>
        </div>
        <div class="mt-3 text-muted">
            <small>If you need to make changes to this booking, please contact our support team.</small>
        </div>
    `;

    showNotification(
        'Reschedule Not Available',
        message,
        'exclamation-triangle',
        'warning'
    );
}

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const timeSlotsList = document.getElementById('timeSlotsList');
    const bookingForm = document.getElementById('bookingForm');
    let selectedDate = ''; // Moved to higher scope

    // Initialize the modal once
    const timeSlotsModal = new bootstrap.Modal(document.getElementById('timeSlotsModal'), {
        backdrop: 'static',
        keyboard: false
    });

    // Clean up modal backdrop on hide
    document.getElementById('timeSlotsModal').addEventListener('hidden.bs.modal', function () {
        // Close confirmation modal if it's open
        const confirmationModal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
        if (confirmationModal) {
            confirmationModal.hide();
        }

        // Clean up all modal backdrops and restore body state
        const backdrops = document.getElementsByClassName('modal-backdrop');
        while (backdrops.length > 0) {
            backdrops[0].parentNode.removeChild(backdrops[0]);
        }
        document.body.classList.remove('modal-open');
        
        // Update modal blur effects
        updateModalBlurEffects();
    });

    // Also handle confirmation modal cleanup
    document.getElementById('confirmationModal').addEventListener('hidden.bs.modal', function () {
        const backdrops = document.getElementsByClassName('modal-backdrop');
        while (backdrops.length > 0) {
            backdrops[0].parentNode.removeChild(backdrops[0]);
        }
        document.body.classList.remove('modal-open');
        
        // Update modal blur effects
        updateModalBlurEffects();
    });

    // Function to update modal blur effects based on stacking order
    function updateModalBlurEffects() {
        // Use a timeout to ensure DOM has updated
        setTimeout(() => {
            const openModals = document.querySelectorAll('.modal.show');
            
            // Reset all modal dialogs first
            openModals.forEach(modal => {
                const modalDialog = modal.querySelector('.modal-dialog');
                if (modalDialog) {
                    modalDialog.style.transition = 'filter 0.3s ease, opacity 0.3s ease';
                }
            });
            
            // Apply blur effects based on z-index and position
            openModals.forEach((modal, index) => {
                const modalDialog = modal.querySelector('.modal-dialog');
                if (modalDialog) {
                    const isTopmost = (index === openModals.length - 1);
                    
                    if (isTopmost) {
                        // This is the topmost modal - crystal clear
                        modalDialog.style.filter = 'none';
                        modalDialog.style.opacity = '1';
                        modalDialog.style.pointerEvents = 'auto';
                    } else {
                        // This is a background modal - apply progressive blur
                        const position = openModals.length - 1 - index;
                        const blurAmount = Math.min(position * 3, 12); // Max 12px blur
                        const opacity = Math.max(0.7, 1 - (position * 0.15)); // Min 70% opacity
                        
                        modalDialog.style.filter = `blur(${blurAmount}px)`;
                        modalDialog.style.opacity = opacity.toString();
                        modalDialog.style.pointerEvents = 'none';
                    }
                }
            });
        }, 50);
    }

    // Add event listeners to all modals for blur effect management
    const allModals = document.querySelectorAll('.modal');
    allModals.forEach(modal => {
        modal.addEventListener('shown.bs.modal', updateModalBlurEffects);
        modal.addEventListener('hidden.bs.modal', updateModalBlurEffects);
    });

    // Service prices for the booking form
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

    // Function to update service price
    window.updatePrice = function() {
        const serviceSelect = document.getElementById('service');
        const priceInput = document.getElementById('service_price');
        if (serviceSelect && priceInput) {
            const selectedService = serviceSelect.value;
            
            if (selectedService && servicePrices[selectedService]) {
                priceInput.value = servicePrices[selectedService];
            } else {
                priceInput.value = '';
            }
        }
    }

    // Update handleBookingSubmit function
    window.handleBookingSubmit = function(form, event, formData) {
        event.preventDefault();
        
        // If formData is not provided, create it from the form
        if (!formData) {
            formData = new FormData(form);
        }
        
        // Format phone number
        let phone = formData.get('phone');
        if (phone && !phone.startsWith('+639')) {
            formData.set('phone', '+639' + phone);
        }

        // Get the service and price and validate
        const service = formData.get('service');
        const servicePrice = formData.get('service_price');
        
        if (!service) {
            alert('Please select a service');
            return;
        }
        
        if (!servicePrice || parseFloat(servicePrice) === 0) {
            // Try to update the price one more time
            updatePrice();
            const updatedPrice = document.getElementById('service_price').value;
            if (!updatedPrice || parseFloat(updatedPrice) === 0) {
                alert('Please select a valid service');
                return;
            }
            formData.set('service_price', updatedPrice);
        }

        // Update price field name
        const finalPrice = formData.get('service_price');
        formData.delete('service_price');
        formData.append('price', finalPrice);

        // Validate all required fields
        const requiredFields = {
            'service': 'Service',
            'appointment_date': 'Appointment Date',
            'appointment_time': 'Appointment Time',
            'full_name': 'Full Name',
            'phone': 'Contact Number',
            'location': 'Location',
            'house_number': 'House Number',
            'street': 'Street',
            'district': 'District',
            'barangay': 'Barangay'
        };

        let isValid = true;
        let errorMessage = '';
        for (const field in requiredFields) {
            const value = formData.get(field);
            if (!value) {
                errorMessage += `${requiredFields[field]} is required.\n`;
                isValid = false;
            }
        }

        if (!isValid) {
            alert(errorMessage);
            return;
        }

        // Format booking details for confirmation
        const details = `
            <div class="booking-details">
                <p><strong>Service:</strong> ${formData.get('service')}</p>
                <p><strong>Date:</strong> ${new Date(formData.get('appointment_date')).toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                })}</p>
                <p><strong>Time:</strong> ${formData.get('appointment_time')}</p>
                <p><strong>Name:</strong> ${formData.get('full_name')}</p>
                <p><strong>Contact:</strong> ${formData.get('phone')}</p>
                <p><strong>Location:</strong> ${formData.get('location')}</p>
                <p><strong>Price:</strong> PHP ${parseFloat(finalPrice).toLocaleString()}</p>
                ${formData.get('note') ? `<p><strong>Note:</strong> ${formData.get('note')}</p>` : ''}
            </div>
        `;

        // Show confirmation modal
        const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        document.getElementById('confirmationMessage').textContent = 'Please confirm your booking details:';
        document.getElementById('bookingDetails').innerHTML = details;
        
        // Remove any existing click handlers from confirm button
        const confirmBtn = document.getElementById('confirmActionBtn');
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
        
        // Add new click handler
        newConfirmBtn.addEventListener('click', function() {
            // Show loading state
            this.disabled = true;
            this.innerHTML = `
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Processing...
            `;
            
            // Make AJAX request to save booking
            fetch('save_booking.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close all modals except success modal
                    const modals = document.querySelectorAll('.modal');
                    modals.forEach(modalEl => {
                        if (modalEl.id !== 'successModal') {
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) {
                                modal.hide();
                            }
                        }
                    });

                    // Remove any remaining backdrops and modal-open class
                    document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                    document.body.classList.remove('modal-open');

                    // Show success modal
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();

                    // Refresh calendar data
                    window.location.reload(); // This will refresh the entire page to get fresh calendar data
                } else {
                    throw new Error(data.message || 'Failed to submit booking');
                }
            })
            .catch(error => {
                // Show error message
                alert('An error occurred while submitting the booking: ' + error.message);
                
                // Re-enable the confirm button and restore original text
                this.disabled = false;
                this.innerHTML = `<i class="fas fa-check me-2"></i>Confirm Booking`;
            });
        });

        // Add event listener to update blur effects when confirmation modal is shown
        document.getElementById('confirmationModal').addEventListener('shown.bs.modal', function() {
            updateModalBlurEffects();
        });

        confirmationModal.show();
        
        // Immediately update blur effects after showing modal
        setTimeout(() => updateModalBlurEffects(), 100);
    };

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 650,
        selectable: true,
        events: <?= json_encode($calendar_events) ?>,
        eventDidMount: function(info) {
            // Add tooltip for user's bookings
            if (info.event.display === 'block') {
                info.el.title = info.event.title;
            }
        },
        dateClick: function (info) {
            selectedDate = info.dateStr;
            
            // Get today's date in Asia/Manila timezone
            const now = new Date();
            const today = new Date(now.toLocaleString('en-US', { timeZone: 'Asia/Manila' })).toISOString().split('T')[0];
            
            // Check if selected date is in the past
            if (selectedDate < today) {
                showNotification(
                    'Invalid Date',
                    'You cannot select a past date.',
                    'calendar-times',
                    'danger'
                );
                return;
            }

            // Check if trying to book for today
            if (selectedDate === today) {
                showNotification(
                    'Same Day Booking',
                    'Same day booking is not allowed. Please select a future date.',
                    'clock',
                    'warning'
                );
                return;
            }

            // Find user bookings for the selected date
            const userBookings = calendar.getEvents().filter(event => {
                // Only look at user booking events
                if (!event.extendedProps || event.extendedProps.type !== 'user_booking') {
                    return false;
                }
                
                // Get event date without time component
                const eventStart = new Date(event.start);
                const eventDate = new Date(
                    eventStart.getFullYear(),
                    eventStart.getMonth(),
                    eventStart.getDate()
                ).toISOString().split('T')[0];

                // Get selected date without time component
                const selectedDateObj = new Date(selectedDate);
                const compareDate = new Date(
                    selectedDateObj.getFullYear(),
                    selectedDateObj.getMonth(),
                    selectedDateObj.getDate()
                ).toISOString().split('T')[0];

                // Compare the dates
                return eventDate === compareDate;
            });

            if (userBookings.length > 0) {
                const booking = userBookings[0];
                const bookingData = booking.extendedProps;
                const formattedDate = new Date(selectedDate).toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                
                // Check if the booking is approved and can be rescheduled
                const bookingTitle = booking.title;
                const isApproved = bookingTitle.includes('Approved');
                
                if (isApproved) {
                    // Show option to reschedule the approved booking with details
                    showRescheduleOption(selectedDate, booking);
                } else {
                    // Show booking details for all other statuses (Pending, Rescheduled, etc.)
                    showBookingDetailsNotification(bookingTitle, formattedDate, bookingData);
                }
                return;
            }

            // If we get here, it's a valid date with no existing booking
            showTimeSlots(selectedDate);
            timeSlotsModal.show();
        }
    });

    calendar.render();

    // Function to show confirmation modal
    function showConfirmation(message, details, callback) {
        const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        document.getElementById('confirmationMessage').textContent = message;
        document.getElementById('bookingDetails').innerHTML = details;
        
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
            
            callback(calendar);  // Pass calendar instance to callback
        });
        
        modal.show();
    }

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

const barangaysByDistrict = {
    "Poblacion": ["1-A", "2-A", "3-A", "4-A", "5-A", "6-A", "7-A", "8-A", "9-A", "10-A",
        "11-B", "12-B", "13-B", "14-B", "15-B", "16-B", "17-B", "18-B", "19-B", "20-B",
        "21-C", "22-C", "23-C", "24-C", "25-C", "26-C", "27-C", "28-C", "29-C", "30-C",
        "31-D", "32-D", "33-D", "34-D", "35-D", "36-D", "37-D", "38-D", "39-D", "40-D"],
    "Talomo": ["Bago Aplaya", "Bago Gallera", "Baliok", "Bucana", "Catalunan Grande",
        "Catalunan Pequeño", "Dumoy", "Langub", "Ma-a", "Magtuod",
        "Matina Aplaya", "Matina Crossing", "Matina Pangi", "Talomo Proper"],
    "Agdao": ["Agdao Proper", "Centro (San Juan)", "Lapu-Lapu", "Leon Garcia",
        "Ubalde", "Wilfredo Aquino"],
    "Buhangin": ["Acacia", "Buhangin Proper", "Cabantian", "Communal",
        "Indangan", "Mandug", "Pampanga", "Sasa", "Tigatto", "Vicente Hizon Sr."],
    "Bunawan": ["Alejandra Navarro (Lasang)", "Bunawan Proper", "Gatungan",
        "Ilang", "Mahayag", "Mudiang", "Panacan", "San Isidro (Licanan)",
        "Tibungco"],
    "Paquibato": ["Colosas", "Fatima (Benowang)", "Lumiad", "Mabuhay",
        "Malabog", "Mapula", "Panalum", "Pandaitan", "Paquibato Proper",
        "Paradise Embak", "Salapawan", "Sumimao", "Tapak"],
    "Baguio": ["Baguio Proper", "Cadalian", "Carmen", "Gumalang",
        "Malagos", "Tawan-Tawan", "Tambubong"],
    "Calinan": ["Biao Escuela", "Calinan Proper", "Dacudao", "Dalagdag",
        "Dominga", "Lacson", "Lamanan", "Megkawayan", "Pangyan",
        "Riverside", "Sirib", "Subasta", "Talomo River"],
    "Marilog": ["Baganihan", "Buda", "Dalag", "Datu Salumay", "Gumitan",
        "Magsaysay", "Malamba", "Marilog Proper", "Salaysay",
        "Suawan (Tuli)", "Tamugan"],
    "Toril": ["Alambre", "Bangkas Heights", "Baracatan", "Bayabas",
        "Bato", "Binugao", "Camansi", "Catigan", "Crossing Bayabas",
        "Daliao", "Daliao Plantation", "Eden", "Kilate", "Lizada",
        "Lubogan", "Marapangi", "Mulig", "Santo Niño", "Sirawan",
        "Tagluno", "Tagurano", "Tibuloy", "Toril Proper"],
    "Tugbok": ["Angalan", "Bago Oshiro", "Balengaeng", "Biao Guianga",
        "Los Amigos", "Manambulan", "Manuel Guianga", "Matina Biao",
        "Mintal", "New Carmen", "New Valencia", "Santo Niño",
        "Tacunan", "Tagakpan", "Talandang", "Tugbok Proper",
        "Ula"]
};

// Function to update barangay options based on selected district
function updateBarangays() {
    const district = document.getElementById('district').value;
    const barangaySelect = document.getElementById('barangay');
    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
    
    if (district && barangaysByDistrict[district]) {
        barangaysByDistrict[district].forEach(barangay => {
            const option = document.createElement('option');
            option.value = barangay;
            option.textContent = barangay;
            barangaySelect.appendChild(option);
        });
    }
}

// Function to update the location field
function updateLocation() {
    const houseNumber = document.getElementById('house_number').value.trim();
    const street = document.getElementById('street').value.trim();
    const district = document.getElementById('district').value;
    const barangay = document.getElementById('barangay').value;
    const locationField = document.getElementById('location');

    if (houseNumber && street && district && barangay) {
        locationField.value = `${houseNumber} ${street}, Brgy. ${barangay}, ${district} District, Davao City`;
    } else {
        locationField.value = '';
    }
}

// Function to show booking form
window.showBookingForm = function(date, time) {
    const timeSlotsList = document.getElementById('timeSlotsList');
    const bookingForm = document.getElementById('bookingForm');
    const modalTitle = document.getElementById('timeSlotsModalLabel');

    // Update modal title
    modalTitle.textContent = 'Book Appointment';

    // Set the date and time in hidden fields
    document.getElementById('appointment_date').value = date;
    document.getElementById('appointment_time').value = time;

    // Set user's information
    document.getElementById('full_name').value = <?= json_encode($user_full_name) ?>;
    
    // Set contact number (remove +639 prefix if present)
    let contact = <?= json_encode($user_contact) ?>;
    contact = contact.replace(/^\+639/, '');
    document.getElementById('phone').value = contact;
    
    // Set house number and street
    document.getElementById('house_number').value = <?= json_encode($user_houseno) ?>;
    document.getElementById('street').value = <?= json_encode($user_street) ?>;
    
    // Set district and trigger barangay update
    const districtSelect = document.getElementById('district');
    const userDistrict = <?= json_encode($user_district) ?>;
    if (userDistrict) {
        districtSelect.value = userDistrict;
        updateBarangays();
        
        // Set barangay after barangays are loaded
        setTimeout(() => {
            const barangaySelect = document.getElementById('barangay');
            const userBarangay = <?= json_encode($user_barangay) ?>;
            if (userBarangay) {
                barangaySelect.value = userBarangay;
            }
            updateLocation();
        }, 100);
    }

    // Hide time slots and show booking form
    timeSlotsList.style.display = 'none';
    bookingForm.style.display = 'block';

    // Initialize form
    if (typeof updatePrice === 'function') {
        updatePrice();
    }
}

// Function to toggle edit mode for a field
window.toggleEdit = function(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;

    if (field.hasAttribute('readonly')) {
        // Enable editing
        field.removeAttribute('readonly');
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-primary');
        field.focus();
    } else {
        // Disable editing
        field.setAttribute('readonly', '');
        button.innerHTML = '<i class="fas fa-pencil-alt"></i>';
        button.classList.remove('btn-primary');
        button.classList.add('btn-outline-secondary');
        
        // If this is the district field, update barangays
        if (fieldId === 'district') {
            updateBarangays();
        }
        
        // Update location after any field is edited
        updateLocation();
    }
}

// Function to show reschedule option when clicking on existing booking date
window.showRescheduleOption = function(selectedDate, booking) {
    const bookingData = booking.extendedProps;
    const formattedDate = new Date(selectedDate).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    const formattedTime = new Date(`2000-01-01 ${bookingData.time}`).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: 'numeric',
        hour12: true
    });

    // Check if reschedule is allowed
    if (bookingData.reschedule_attempt > 0) {
        showRescheduleNotAvailableModal(formattedDate, bookingData);
        return;
    }

    // Get service price
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
    
    const price = servicePrices[bookingData.service] || 'Price not available';
    
    // Enhanced message with booking details and reschedule option
    const message = `
        <div class="booking-details-card">
            <div class="booking-detail-row">
                <div class="detail-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="detail-content">
                    <strong>Service:</strong><br>
                    <span class="detail-value">${bookingData.service}</span>
                </div>
            </div>
            <div class="booking-detail-row">
                <div class="detail-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="detail-content">
                    <strong>Date:</strong><br>
                    <span class="detail-value">${formattedDate}</span>
                </div>
            </div>
            <div class="booking-detail-row">
                <div class="detail-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="detail-content">
                    <strong>Time:</strong><br>
                    <span class="detail-value">${formattedTime}</span>
                </div>
            </div>
            <div class="booking-detail-row">
                <div class="detail-icon">
                    <i class="fas fa-peso-sign"></i>
                </div>
                <div class="detail-content">
                    <strong>Price:</strong><br>
                    <span class="detail-value price">₱${typeof price === 'number' ? price.toLocaleString() : price}</span>
                </div>
            </div>
        </div>
        <div class="mt-3 text-center">
            <p><strong>Would you like to reschedule this booking to a different date and time?</strong></p>
        </div>
    `;
    
    document.getElementById('rescheduleOptionMessage').innerHTML = message;
    
    const modal = new bootstrap.Modal(document.getElementById('rescheduleOptionModal'));
    
    // Set up the reschedule button click handler
    const rescheduleBtn = document.getElementById('rescheduleBookingBtn');
    rescheduleBtn.onclick = function() {
        modal.hide();
        showRescheduleModal(bookingData.booking_id, selectedDate);
    };
    
    modal.show();
}

// Function to show reschedule modal with calendar
window.showRescheduleModal = function(bookingId, currentDate) {
    document.getElementById('reschedule_booking_id').value = bookingId;
    document.getElementById('reschedule_current_date').value = currentDate;
    
    const calendarEl = document.getElementById('rescheduleCalendar');
    let rescheduleCalendar = null;

    // Initialize the modal
    const modal = new bootstrap.Modal(document.getElementById('rescheduleModal'));
    
    // Function to initialize the calendar
    function initializeRescheduleCalendar() {
        if (!calendarEl.classList.contains('calendar-initialized')) {
            rescheduleCalendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 650,
                selectable: true,
                timeZone: 'Asia/Manila',
                events: <?= json_encode($calendar_events) ?>,
                eventDidMount: function(info) {
                    if (info.event.display === 'block') {
                        info.el.title = info.event.title;
                    }
                },
                dateClick: function(info) {
                    const selectedDate = info.dateStr;
                    const currentDate = document.getElementById('reschedule_current_date').value;
                    
                    // Get today's date
                    const now = new Date();
                    const manilaDate = new Date(now.toLocaleString('en-US', { timeZone: 'Asia/Manila' }));
                    const today = manilaDate.toISOString().split('T')[0];
                    
                    // Check if selected date is in the past
                    if (selectedDate < today) {
                        showNotification(
                            'Invalid Date',
                            'You cannot select a past date.',
                            'calendar-times',
                            'danger'
                        );
                        return;
                    }

                    // Check if trying to book for today
                    if (selectedDate === today) {
                        showNotification(
                            'Same Day Booking',
                            'Same day booking is not allowed. Please select a future date.',
                            'clock',
                            'warning'
                        );
                        return;
                    }

                    // Get all events for the selected date
                    const dateEvents = rescheduleCalendar.getEvents().filter(event => {
                        const eventDate = event.start.toISOString().split('T')[0];
                        return eventDate === selectedDate;
                    });

                    // Check if the selected date has any bookings (excluding current booking and background events)
                    const hasConflictingBookings = dateEvents.some(event => {
                        // Skip background events
                        if (event.display === 'background') {
                            return false;
                        }
                        // Skip the current booking being rescheduled
                        if (selectedDate === currentDate) {
                            return false;
                        }
                        // Check for other bookings
                        return true;
                    });

                    if (hasConflictingBookings) {
                        showNotification(
                            'Date Not Available',
                            'This date already has a booking. Please select a different date.',
                            'calendar-times',
                            'warning'
                        );
                        return;
                    }

                    // Show time slots for the selected date
                    showRescheduleTimeSlots(selectedDate);
                }
            });

            rescheduleCalendar.render();
            calendarEl.classList.add('calendar-initialized');
        }
        
        if (rescheduleCalendar) {
            rescheduleCalendar.updateSize();
        }
    }

    // Add event listener for modal shown
    document.getElementById('rescheduleModal').addEventListener('shown.bs.modal', initializeRescheduleCalendar, { once: true });
    
    modal.show();
}

// Function to show time slots for reschedule
window.showRescheduleTimeSlots = function(date) {
    const timeSlotsList = document.getElementById('rescheduleTimeSlotsList');
    const timeSlotsModal = new bootstrap.Modal(document.getElementById('rescheduleTimeSlotsModal'));
    
    timeSlotsList.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading available time slots...</p>
        </div>`;

    // Update modal title
    document.getElementById('rescheduleTimeSlotsModalLabel').innerHTML = `
        <i class="fas fa-clock"></i>
        Available Times for ${new Date(date).toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        })}`;

    // Fetch available time slots
    fetch('get_slots.php?date=' + date)
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            if (data.error) throw new Error(data.error);
            
            const slots = data.slots || [];
            if (slots.length === 0) {
                timeSlotsList.innerHTML = `
                    <div class="alert alert-info" role="alert">
                        No time slots available for this date.
                    </div>`;
                return;
            }

            let html = '<div class="row g-3">';
            slots.forEach(slot => {
                const btnClass = slot.available ? 'available' : 'unavailable';
                const disabled = !slot.available;
                const remainingText = slot.available ? 
                    `${slot.remaining} slots remaining` : 
                    'Fully booked';
                
                html += `
                    <div class="col-md-4">
                        <button class="time-slot-btn ${btnClass}" 
                                onclick="handleRescheduleTimeSlotSelection('${date}', '${slot.time}')"
                                ${disabled ? 'disabled' : ''}>
                            <div class="fw-bold">${slot.time}</div>
                            <small>${remainingText}</small>
                        </button>
                    </div>`;
            });
            html += '</div>';
            timeSlotsList.innerHTML = html;
        })
        .catch(error => {
            timeSlotsList.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    Error: ${error.message}
                </div>`;
        });

    timeSlotsModal.show();
}

// Function to handle reschedule time slot selection
window.handleRescheduleTimeSlotSelection = function(date, time) {
    const timeSlotsModal = bootstrap.Modal.getInstance(document.getElementById('rescheduleTimeSlotsModal'));
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
        databaseTime = time.includes(':') ? (time + ':00').substring(0, 8) : time + ':00';
    }

    // Format display time
    const displayTime = new Date(`2000-01-01 ${databaseTime}`).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: 'numeric',
        hour12: true
    });

    // Show confirmation modal
    const bookingDetails = `
        <div style="background: rgba(7, 53, 63, 0.05); border-radius: 12px; padding: 1.5rem; margin: 1rem 0;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                <i class="fas fa-calendar-day" style="color: var(--primary-color); font-size: 1.2rem; width: 24px; text-align: center;"></i>
                <div>
                    <strong style="color: var(--primary-color);">New Date</strong><br>
                    <span>${formattedDate}</span>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <i class="fas fa-clock" style="color: var(--primary-color); font-size: 1.2rem; width: 24px; text-align: center;"></i>
                <div>
                    <strong style="color: var(--primary-color);">New Time</strong><br>
                    <span>${displayTime}</span>
                </div>
            </div>
        </div>
    `;

    // Set the modal title manually
    document.getElementById('confirmationModalLabel').innerHTML = `
        <i class="fas fa-question-circle me-2"></i>
        Confirm Reschedule
    `;

    showConfirmation(
        'Are you sure you want to reschedule this booking?',
        bookingDetails,
        () => {
            const bookingId = document.getElementById('reschedule_booking_id').value;
            const formData = new FormData();
            formData.append('booking_id', bookingId);
            formData.append('new_date', date);
            formData.append('new_time', databaseTime);

            // Show loading state
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

                    // Show success modal
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();

                    // Reload page to refresh calendar
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    if (data.message.includes('reschedule attempt')) {
                        showNotification(
                            'Reschedule Not Allowed',
                            'You have already used your one-time reschedule for this booking.',
                            'exclamation-triangle',
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
</script>

<style>
    /* Update modal backdrop styles */
    .modal-backdrop {
        --bs-backdrop-opacity: 0.5;
        --bs-backdrop-bg: rgba(0, 0, 0, 0.5);
        background-color: var(--bs-backdrop-bg);
        z-index: 1040;
        transition: all 0.3s ease-in-out;
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

    /* Enhanced backdrop blur for stacked modals */
    .modal-backdrop:nth-of-type(2) {
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    .modal-backdrop:nth-of-type(3) {
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }

    .modal-backdrop:nth-of-type(4) {
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
    }

    .modal-backdrop:nth-of-type(n+5) {
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
    }

    .modal {
        z-index: 1045;
    }

    /* Ensure content behind modal is blurred */
    body.modal-open .main-container {
        filter: blur(4px);
        transition: filter 0.2s ease-in-out;
    }

    /* Add transition to modal itself for smoother appearance */
    .modal.fade .modal-dialog {
        transition: transform 0.2s ease-out;
        transform: translateY(-20px);
    }

    .modal.show .modal-dialog {
        transform: translateY(0);
    }

    /* Notification Modal Enhancements */
    #notificationModal .modal-content {
        transform: scale(0.95);
        opacity: 0;
        transition: transform 0.2s ease-out, opacity 0.2s ease-out;
    }

    #notificationModal.show .modal-content {
        transform: scale(1);
        opacity: 1;
    }

    /* Success Modal Enhancements */
    #successModal .modal-content {
        transform: scale(0.95);
        opacity: 0;
        transition: transform 0.2s ease-out, opacity 0.2s ease-out;
    }

    #successModal.show .modal-content {
        transform: scale(1);
        opacity: 1;
    }

    /* Confirmation Modal Enhancements */
    #confirmationModal .modal-content {
        transform: scale(0.95);
        opacity: 0;
        transition: transform 0.2s ease-out, opacity 0.2s ease-out;
    }

    #confirmationModal.show .modal-content {
        transform: scale(1);
        opacity: 1;
    }

    /* Calendar container styles */
    .calendar-container {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }

    .calendar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
    }

    .calendar-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #07353f;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .calendar-title i {
        color: #0ea5e9;
    }

    .calendar-legend {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        padding: 0.5rem;
        background: rgba(0, 0, 0, 0.02);
        border-radius: 8px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #64748b;
    }

    .legend-color {
        width: 1rem;
        height: 1rem;
        border-radius: 4px;
    }

    .legend-available {
        background-color: #48c78e;
    }

    .legend-booked {
        background-color: #ff6b6b;
    }

    .legend-your-booking {
        background-color: #0ea5e9;
    }

    /* Existing calendar event styles */
    .fc-event {
        border-radius: 4px;
        padding: 2px 4px;
        margin-bottom: 2px;
        font-size: 0.85em;
    }

    .fc-event[style*="background-color: rgb(14, 165, 233)"] {
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .fc-event[style*="background-color: rgb(14, 165, 233)"]:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }

    /* Calendar event styles */
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
        z-index: 2 !important; /* Ensure user bookings appear above background colors */
    }

    .user-booking:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }

    /* Availability indicator styles */
    .availability-indicator {
        z-index: 1 !important; /* Keep background colors behind user bookings */
        opacity: 0.3; /* Make background colors more subtle */
    }

    .fc-daygrid-day-events {
        min-height: 2em;
        pointer-events: none; /* This ensures clicks go to the date cell */
    }

    .fc-daygrid-day-events > * {
        pointer-events: auto; /* Re-enable pointer events for actual events */
    }

    /* Gray styling for both past dates and today */
    .fc-daygrid-day.fc-day-today,
    .fc-daygrid-day.fc-day-past {
        background-color: rgba(128, 128, 128, 0.1) !important;
        color: #6b7280 !important;
        opacity: 0.85;
        pointer-events: none;
        cursor: not-allowed;
        position: relative;
    }

    /* Add diagonal lines pattern for better visual indication */
    .fc-daygrid-day.fc-day-today::before,
    .fc-daygrid-day.fc-day-past::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: repeating-linear-gradient(
            45deg,
            transparent,
            transparent 3px,
            rgba(156, 163, 175, 0.1) 3px,
            rgba(156, 163, 175, 0.1) 6px
        );
        pointer-events: none;
        z-index: 1;
    }

    /* Make date numbers gray for past dates and today */
    .fc-daygrid-day.fc-day-past .fc-daygrid-day-number,
    .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
        color: #6b7280 !important;
        font-weight: 500;
        position: relative;
        z-index: 2;
    }

    /* Make all text content gray in past dates and today */
    .fc-daygrid-day.fc-day-past *,
    .fc-daygrid-day.fc-day-today * {
        color: #6b7280 !important;
    }

    /* Style for day numbers - alternative selectors */
    .fc-daygrid-day.fc-day-past .fc-daygrid-day-top,
    .fc-daygrid-day.fc-day-past .fc-day-number,
    .fc-daygrid-day.fc-day-today .fc-daygrid-day-top,
    .fc-daygrid-day.fc-day-today .fc-day-number {
        color: #6b7280 !important;
        font-weight: 500;
        position: relative;
        z-index: 2;
    }

    /* Ensure past date and today events are visible and readable */
    .fc-daygrid-day.fc-day-past .fc-event,
    .fc-daygrid-day.fc-day-today .fc-event {
        opacity: 0.9;
        filter: grayscale(30%);
        position: relative;
        z-index: 2;
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2) !important;
    }

    /* Make user booking events on past dates more prominent */
    .fc-daygrid-day.fc-day-past .fc-event.user-booking,
    .fc-daygrid-day.fc-day-today .fc-event.user-booking {
        opacity: 1;
        filter: grayscale(20%);
        border: 2px solid rgba(255, 255, 255, 0.9) !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;
        font-weight: 600;
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

    /* Booking info styles */
    .booking-info {
        background: rgba(14, 165, 233, 0.1);
        border: 1px solid rgba(14, 165, 233, 0.2);
        border-radius: 8px;
        padding: 1rem;
        margin: 1rem 0;
    }

    .booking-info p {
        margin-bottom: 0.5rem;
        color: #07353f;
    }

    .booking-info strong {
        color: #0ea5e9;
        font-size: 1.1em;
    }

    /* Modal notification enhancements */
    #notificationModal .modal-body {
        padding: 2rem;
    }

    #notificationModal .text-info {
        color: #0ea5e9 !important;
    }

    #notificationModal .btn-info {
        background-color: #0ea5e9;
        border-color: #0ea5e9;
        color: white;
    }

    #notificationModal .btn-info:hover {
        background-color: #0284c7;
        border-color: #0284c7;
        transform: translateY(-1px);
    }

    #notificationModal .text-muted {
        color: #64748b !important;
    }

    /* Reschedule Modal Styles */
    #rescheduleOptionModal .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 40px var(--card-shadow);
    }

    #rescheduleOptionModal .modal-header {
        background: var(--primary-color);
        color: white;
        border-radius: 20px 20px 0 0;
        padding: 1.5rem;
    }

    #rescheduleOptionModal .modal-body {
        padding: 2rem;
    }

    #rescheduleOptionModal .btn-lg {
        padding: 1rem 2rem;
        font-size: 1.1rem;
        border-radius: 12px;
        font-weight: 600;
    }

    #rescheduleModal .modal-content,
    #rescheduleTimeSlotsModal .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 40px var(--card-shadow);
    }

    #rescheduleModal .modal-header,
    #rescheduleTimeSlotsModal .modal-header {
        background: var(--primary-color);
        color: white;
        border-radius: 20px 20px 0 0;
        padding: 1.5rem;
    }

    #rescheduleModal .modal-body,
    #rescheduleTimeSlotsModal .modal-body {
        padding: 2rem;
    }

    /* Reschedule Calendar Styles */
    #rescheduleCalendar {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    /* Modal z-index layering */
    #timeSlotsModal {
        z-index: 1045;
    }

    #rescheduleOptionModal {
        z-index: 1046;
    }

    #rescheduleModal {
        z-index: 1047;
    }

    #rescheduleTimeSlotsModal {
        z-index: 1048;
    }

    #notificationModal {
        z-index: 1049;
    }

    /* Confirmation modal should be highest for all flows */
    #confirmationModal {
        z-index: 1050 !important;
    }

    #successModal {
        z-index: 1051 !important;
    }

    /* Ensure proper stacking of modal backdrops */
    .modal-backdrop {
        z-index: auto;
    }

    #timeSlotsModal + .modal-backdrop {
        z-index: 1044;
    }

    #confirmationModal + .modal-backdrop {
        z-index: 1049;
    }

    /* Ensure modals stack properly */
    .modal-backdrop:nth-of-type(2) {
        z-index: 1041;
    }

    .modal-backdrop:nth-of-type(3) {
        z-index: 1042;
    }

    .modal-backdrop:nth-of-type(4) {
        z-index: 1043;
    }

    .modal-backdrop:nth-of-type(5) {
        z-index: 1044;
    }

    /* Blur effect for background modals when overlapping */
    .modal .modal-dialog {
        transition: filter 0.3s ease, opacity 0.3s ease, transform 0.3s ease;
    }

    /* Ensure smooth transitions for all modal dialogs */
    .modal.fade .modal-dialog {
        transition: filter 0.3s ease, opacity 0.3s ease, transform 0.3s ease;
    }

    /* Override for better stacking visual hierarchy */
    .modal:not(:last-of-type) .modal-dialog {
        pointer-events: none; /* Prevent interaction with background modals */
    }

    .modal:last-of-type .modal-dialog {
        pointer-events: auto; /* Allow interaction with top modal */
    }

    /* Specific blur effects for confirmation modal over booking form */
    #confirmationModal.show ~ #timeSlotsModal .modal-dialog {
        filter: blur(4px) !important;
        opacity: 0.8 !important;
        transition: filter 0.3s ease, opacity 0.3s ease !important;
    }

    /* Enhanced blur for deeply stacked modals */
    body.modal-open .modal.show:not(:last-child) .modal-dialog {
        filter: blur(3px);
        opacity: 0.85;
        transform: scale(0.98);
        transition: all 0.3s ease;
    }

    /* Ensure confirmation modal is always crystal clear */
    #confirmationModal.show .modal-dialog {
        filter: none !important;
        opacity: 1 !important;
        transform: scale(1) !important;
        transition: all 0.3s ease !important;
    }

    /* Booking Details Card Styles */
    .booking-details-card {
        background: rgba(7, 53, 63, 0.03);
        border: 1px solid rgba(7, 53, 63, 0.1);
        border-radius: 12px;
        padding: 1.25rem;
        margin: 1rem 0;
    }

    .booking-detail-row {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
        padding: 0.75rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }

    .booking-detail-row:last-child {
        margin-bottom: 0;
    }

    .booking-detail-row:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .detail-icon {
        width: 40px;
        height: 40px;
        background: var(--primary-color);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .detail-content {
        flex: 1;
        min-width: 0;
    }

    .detail-content strong {
        color: var(--primary-color);
        font-size: 0.875rem;
        font-weight: 600;
        display: block;
        margin-bottom: 0.25rem;
    }

    .detail-value {
        color: var(--text-color);
        font-size: 1rem;
        font-weight: 500;
        line-height: 1.4;
    }

    .detail-value.price {
        color: var(--secondary-color);
        font-weight: 700;
        font-size: 1.1rem;
    }

    /* Notification Modal Enhancements for Booking Details */
    #notificationModal .modal-body {
        padding: 2rem 2rem 1.5rem 2rem;
    }

    #notificationModal #notificationMessage {
        text-align: left;
        font-size: 1rem;
        line-height: 1.5;
    }

    /* Booking Status Styles */
    .detail-value.status-pending {
        color: #f59e0b;
        font-weight: 600;
        background: rgba(245, 158, 11, 0.1);
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        display: inline-block;
    }

    .detail-value.status-approved {
        color: #10b981;
        font-weight: 600;
        background: rgba(16, 185, 129, 0.1);
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        display: inline-block;
    }

    .detail-value.status-approved-reschedule {
        color: #3b82f6;
        font-weight: 600;
        background: rgba(59, 130, 246, 0.1);
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        display: inline-block;
    }

    .detail-value.status-rescheduled {
        color: #8b5cf6;
        font-weight: 600;
        background: rgba(139, 92, 246, 0.1);
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        display: inline-block;
    }

    .detail-value.status-cancelled {
        color: #ef4444;
        font-weight: 600;
        background: rgba(239, 68, 68, 0.1);
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        display: inline-block;
    }

    .detail-value.status-completed,
    .detail-value.status-done {
        color: #059669;
        font-weight: 600;
        background: rgba(5, 150, 105, 0.1);
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        display: inline-block;
    }

    .detail-value.status-rejected {
        color: #dc2626;
        font-weight: 600;
        background: rgba(220, 38, 38, 0.1);
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        display: inline-block;
    }

    .detail-value.status-reschedule-requested {
        color: #7c3aed;
        font-weight: 600;
        background: rgba(124, 58, 237, 0.1);
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        display: inline-block;
    }

    /* Responsive adjustments for booking details */
    @media (max-width: 768px) {
        .booking-details-card {
            padding: 1rem;
        }
        
        .booking-detail-row {
            gap: 0.75rem;
            padding: 0.5rem;
        }
        
        .detail-icon {
            width: 35px;
            height: 35px;
            font-size: 0.9rem;
        }
        
        .detail-content strong {
            font-size: 0.8rem;
        }
        
        .detail-value {
            font-size: 0.9rem;
        }
        
        .detail-value.price {
            font-size: 1rem;
        }
        
        .detail-value[class*="status-"] {
            font-size: 0.8rem;
            padding: 0.2rem 0.4rem;
        }
    }
</style>
</body>
</html>
