<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Define services and prices
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

// Get user details
$stmt = $conn->prepare("SELECT CONCAT(fname, ' ', lname) as fullname, email, contact, CONCAT(barangay, ', ', district, ', ', city) as location FROM user WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$name = $user['fullname'] ?? '';
$email = $user['email'] ?? '';
$contact = $user['contact'] ?? '';
$location = $user['location'] ?? '';

// Close the statement
$stmt->close();

$appointment_date = $_GET['date'] ?? '';
$appointment_time = $_GET['time'] ?? '';
?>

<style>
.form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    flex: 1;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #344047;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 14px;
    line-height: 1.5;
}

select.form-control {
    height: 38px;
    background-color: white;
}

.form-control:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.contact-input-wrapper {
    display: flex;
    align-items: center;
    border: 1px solid #ced4da;
    border-radius: 4px;
    overflow: hidden;
}

.contact-input-wrapper .prefix {
    padding: 8px 12px;
    background: #f8f9fa;
    border-right: 1px solid #ced4da;
    color: #495057;
    font-size: 14px;
}

.contact-input-wrapper input {
    border: none;
    flex: 1;
    padding: 8px 12px;
    width: 100%;
}

.contact-input-wrapper input:focus {
    outline: none;
}

.contact-input-wrapper:focus-within {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.contact-hint {
    font-size: 12px;
    color: #6c757d;
    margin-top: 4px;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}

.btn {
    padding: 10px 20px;
    border-radius: 4px;
    font-weight: 500;
    cursor: pointer;
    border: none;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-primary {
    background-color: #07353f;
    color: white;
}

.btn:hover {
    opacity: 0.9;
}
</style>

<form method="POST">
    <div class="form-row">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($name) ?>" readonly>
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($email) ?>" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($location) ?>" readonly>
        </div>
        <div class="form-group">
            <label for="contact">Contact Number</label>
            <div class="contact-input-wrapper">
                <span class="prefix">+639</span>
                <input type="text" 
                       class="form-control" 
                       name="contact" 
                       value="<?= substr(htmlspecialchars($contact), 4) ?>" 
                       maxlength="9"
                       pattern="[0-9]{9}"
                       required>
            </div>
            <small class="form-text text-muted">Enter 9 digits after +639</small>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="appointment_date">Appointment Date</label>
            <input type="date" class="form-control" name="appointment_date" value="<?= htmlspecialchars($appointment_date) ?>" readonly>
        </div>
        <div class="form-group">
            <label for="appointment_time">Appointment Time</label>
            <input type="text" class="form-control" name="appointment_time" value="<?= htmlspecialchars($appointment_time) ?>" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="service">Select Service</label>
            <select class="form-control" name="service" id="service" required onchange="updatePrice()">
                <option value="">-- Select Service --</option>
                <?php foreach ($services_prices as $service => $price): ?>
                    <option value="<?= htmlspecialchars($service) ?>"><?= htmlspecialchars($service) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="service_price">Service Price (PHP)</label>
            <input type="text" class="form-control" id="service_price" name="service_price" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="note">Additional Notes</label>
            <textarea class="form-control" id="note" name="note" rows="3" placeholder="Add any special instructions or additional information here"></textarea>
        </div>
    </div>

    <div class="modal-actions">
        <button type="button" class="btn btn-secondary" onclick="showTimeSlots('<?= htmlspecialchars($appointment_date) ?>')">
            <i class="fas fa-arrow-left me-2"></i>Back to Time Slots
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-calendar-check me-2"></i>Confirm Booking
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the form
    const form = document.querySelector('form');
    form.onsubmit = function(event) {
        handleBookingSubmit(this, event);
    };
    
    // Initialize service price
    if (typeof updatePrice === 'function') {
        updatePrice();
    }
});
</script> 