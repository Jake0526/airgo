<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get current page for active sidebar highlighting
$current_page = basename($_SERVER['PHP_SELF']);

$host = getenv('DB_HOST') ?: 'localhost';
$db = getenv('DB_NAME') ?: 'airgo';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

$calendar_events = [];
$date_counts = [];
$today = date('Y-m-d');
$all_result = $conn->query("SELECT appointment_date FROM bookings WHERE status != 'Cancelled'");
while ($row = $all_result->fetch_assoc()) {
    $date = $row['appointment_date'];
    if (!isset($date_counts[$date])) $date_counts[$date] = 0;
    $date_counts[$date]++;
}

$max_slots_per_day = 46;
$range_start = strtotime('-1 month');
$range_end = strtotime('+3 months');

for ($i = $range_start; $i <= $range_end; $i += 86400) {
    $date = date('Y-m-d', $i);
    $booked = $date_counts[$date] ?? 0;
    $remaining = $max_slots_per_day - $booked;

    if ($date < $today) {
        $color = "#ffffff";
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
        "color" => $color
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
        <a href="book-now.php" class="<?= $current_page === 'book-now.php' ? 'active' : '' ?>"><i class="fas fa-calendar-alt"></i> Booking</a>
        <a href="cancel_booking.php" class="<?= $current_page === 'cancel_booking.php' ? 'active' : '' ?>"><i class="fa-solid fa-clock-rotate-left"></i> History</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main-container">
    <div class="calendar-container">
        <div class="label-title">
            <i class="fas fa-calendar-check"></i>
            Select a Date to Book
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
                    <!-- Booking form will be dynamically inserted here -->
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
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

// Update handleBookingSubmit function
window.handleBookingSubmit = function(form, event) {
    event.preventDefault();
    
    // Get form data
    const formData = new FormData(form);
    
    // Add +639 prefix to contact number
    let contact = formData.get('contact');
    if (contact && !contact.startsWith('+639')) {
        formData.set('contact', '+639' + contact);
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
            <p><strong>Name:</strong> ${formData.get('name')}</p>
            <p><strong>Contact:</strong> ${formData.get('contact')}</p>
            <p><strong>Location:</strong> ${formData.get('location')}</p>
            <p><strong>Price:</strong> PHP ${parseFloat(formData.get('service_price')).toLocaleString()}</p>
            ${formData.get('note') ? `<p><strong>Note:</strong> ${formData.get('note')}</p>` : ''}
        </div>
    `;

    showConfirmation('Please confirm your booking details:', details, (calendarInstance) => {
        // Make AJAX request to save booking
        fetch('save_booking.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hide confirmation modal
                const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
                confirmModal.hide();

                // Show success confirmation
                showBookingConfirmation({
                    service: formData.get('service'),
                    date: formData.get('appointment_date'),
                    time: formData.get('appointment_time'),
                    price: formData.get('service_price'),
                    name: formData.get('name'),
                    email: formData.get('email'),
                    contact: formData.get('contact'),
                    location: formData.get('location'),
                    note: formData.get('note')
                });
                
                // Refresh calendar events using the passed calendar instance
                if (calendarInstance && typeof calendarInstance.refetchEvents === 'function') {
                    calendarInstance.refetchEvents();
                }
            } else {
                throw new Error(data.message || 'Failed to submit booking');
            }
        })
        .catch(error => {
            // Show error message
            alert('An error occurred while submitting the booking: ' + error.message);
            
            // Hide confirmation modal
            const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
            confirmModal.hide();
        });
    });
}

// Function to show booking confirmation
window.showBookingConfirmation = function(bookingDetails) {
    // Hide the time slots modal
    const timeSlotsModal = bootstrap.Modal.getInstance(document.getElementById('timeSlotsModal'));
    timeSlotsModal.hide();

    // Format the date and time
    const formattedDate = new Date(bookingDetails.date).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    // Format time without trying to create a Date object
    const formattedTime = bookingDetails.time;

    // Update confirmation details
    const detailsHtml = `
        <div class="text-start">
            <p><strong>Name:</strong> ${bookingDetails.name}</p>
            <p><strong>Email:</strong> ${bookingDetails.email}</p>
            <p><strong>Contact:</strong> ${bookingDetails.contact}</p>
            <p><strong>Location:</strong> ${bookingDetails.location}</p>
            <p><strong>Service:</strong> ${bookingDetails.service}</p>
            <p><strong>Price:</strong> PHP ${parseFloat(bookingDetails.price).toLocaleString()}</p>
            <p><strong>Date:</strong> ${formattedDate}</p>
            <p><strong>Time:</strong> ${formattedTime}</p>
            ${bookingDetails.note ? `<p><strong>Note:</strong> ${bookingDetails.note}</p>` : ''}
        </div>
    `;
    document.querySelector('.booking-details').innerHTML = detailsHtml;

    // Show the confirmation modal
    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    confirmationModal.show();
}

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const timeSlotsList = document.getElementById('timeSlotsList');
    const bookingForm = document.getElementById('bookingForm');
    let selectedDate = '';

    // Initialize the modal once
    const timeSlotsModal = new bootstrap.Modal(document.getElementById('timeSlotsModal'), {
        backdrop: 'static',
        keyboard: false
    });

    // Clean up modal backdrop on hide
    document.getElementById('timeSlotsModal').addEventListener('hidden.bs.modal', function () {
        const backdrops = document.getElementsByClassName('modal-backdrop');
        while (backdrops.length > 0) {
            backdrops[0].parentNode.removeChild(backdrops[0]);
        }
        document.body.classList.remove('modal-open');
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

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 650,
        selectable: true,
        events: <?= json_encode($calendar_events) ?>,
        dateClick: function (info) {
            selectedDate = info.dateStr;
            const today = new Date().toISOString().split('T')[0];
            if (selectedDate < today) {
                alert("You cannot select a past date.");
                return;
            }

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

    // Update handleBookingSubmit function
    window.handleBookingSubmit = function(form, event) {
        event.preventDefault();
        
        // Get form data
        const formData = new FormData(form);
        
        // Add +639 prefix to contact number
        let contact = formData.get('contact');
        if (contact && !contact.startsWith('+639')) {
            formData.set('contact', '+639' + contact);
        }

        // Get the correct price from the service prices object
        const selectedService = formData.get('service');
        const servicePrice = servicePrices[selectedService] || 0;
        formData.set('service_price', servicePrice);

        // Format booking details for confirmation
        const details = `
            <div class="booking-details">
                <p><strong>Service:</strong> ${selectedService}</p>
                <p><strong>Date:</strong> ${new Date(formData.get('appointment_date')).toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                })}</p>
                <p><strong>Time:</strong> ${formData.get('appointment_time')}</p>
                <p><strong>Name:</strong> ${formData.get('name')}</p>
                <p><strong>Contact:</strong> ${formData.get('contact')}</p>
                <p><strong>Location:</strong> ${formData.get('location')}</p>
                <p><strong>Price:</strong> PHP ${servicePrice.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })}</p>
                ${formData.get('note') ? `<p><strong>Note:</strong> ${formData.get('note')}</p>` : ''}
            </div>
        `;

        // Validate price before showing confirmation
        if (servicePrice === 0) {
            alert('Please select a valid service');
            return;
        }

        showConfirmation('Please confirm your booking details:', details, (calendarInstance) => {
            // Make AJAX request to save booking
            fetch('save_booking.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close all modals and clean up
                    const modals = document.querySelectorAll('.modal');
                    modals.forEach(modalEl => {
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) {
                            modal.hide();
                        }
                    });

                    // Remove any remaining backdrops and modal-open class
                    document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                    document.body.classList.remove('modal-open');

                    // Show success confirmation
                    showBookingConfirmation({
                        service: formData.get('service'),
                        date: formData.get('appointment_date'),
                        time: formData.get('appointment_time'),
                        price: servicePrice,
                        name: formData.get('name'),
                        email: formData.get('email'),
                        contact: formData.get('contact'),
                        location: formData.get('location'),
                        note: formData.get('note')
                    });
                    
                    // Refresh calendar events
                    if (calendarInstance && typeof calendarInstance.refetchEvents === 'function') {
                        calendarInstance.refetchEvents();
                    }
                } else {
                    throw new Error(data.message || 'Failed to submit booking');
                }
            })
            .catch(error => {
                // Show error message
                alert('An error occurred while submitting the booking: ' + error.message);
                
                // Close all modals and clean up
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modalEl => {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) {
                        modal.hide();
                    }
                });

                // Remove any remaining backdrops and modal-open class
                document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
            });
        });
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

// Function to show booking form
window.showBookingForm = function(date, time) {
    const timeSlotsList = document.getElementById('timeSlotsList');
    const bookingForm = document.getElementById('bookingForm');
    const modalTitle = document.getElementById('timeSlotsModalLabel');

    // Update modal title
    modalTitle.textContent = 'Book Appointment';

    // Show loading state
    bookingForm.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading booking form...</p>
        </div>`;

    // Hide time slots and show booking form
    timeSlotsList.style.display = 'none';
    bookingForm.style.display = 'block';

    // Fetch the booking form
    fetch('get_booking_form.php?date=' + date + '&time=' + time)
        .then(async response => {
            const text = await response.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error('Invalid response format from server');
            }
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.message || 'Server error occurred');
            }
            
            if (!data.html || !data.script) {
                throw new Error('Invalid response format: missing html or script');
            }
            
            // Insert HTML
            bookingForm.innerHTML = data.html;
            
            // Execute script
            const scriptElement = document.createElement('script');
            scriptElement.textContent = data.script;
            document.body.appendChild(scriptElement);
            
            // Initialize any form scripts
            if (typeof updatePrice === 'function') {
                updatePrice();
            }
            
            if (typeof enableAppointmentTime === 'function') {
                enableAppointmentTime();
            }
        })
        .catch(error => {
            bookingForm.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    <h5 class="alert-heading">Error Loading Form</h5>
                    <p>${error.message}</p>
                    <hr>
                    <p class="mb-0">Please try again or contact support if the problem persists.</p>
                </div>
                <div class="text-center mt-3">
                    <button type="button" class="btn btn-primary" onclick="showTimeSlots('${date}')">
                        <i class="fas fa-arrow-left me-2"></i>Back to Time Slots
                    </button>
                </div>`;
        });
}
</script>

<style>
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
}

#confirmationModal .booking-details {
    background: rgba(7, 53, 63, 0.05);
    padding: 1.5rem;
    border-radius: 12px;
    margin-top: 1rem;
}

#confirmationModal .booking-details p {
    margin-bottom: 0.5rem;
}

#confirmationModal .booking-details strong {
    color: var(--primary-color);
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
</body>
</html>
