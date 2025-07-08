<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$services = ['Aircon Check-up', 'Aircon Relocation', 'Aircon Repair', 'Aircon cleaning (window type)', 'Window type (inverter)', 'Window type (U shape)', 'Split type', 'Floormounted', 'Cassette' , 'Capacitor Thermostat'];
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


$name = $email = $contact = $location = '';
$stmt = $conn->prepare("SELECT username, email, contact, city FROM user WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $contact, $location);
$stmt->fetch();
$stmt->close();

// Read from GET (from calendar click)
$appointment_date = $_GET['date'] ?? ($_GET['appointment_date'] ?? '');
$appointment_time = $_GET['time'] ?? ($_GET['appointment_time'] ?? '');
$service = $_GET['service'] ?? '';
$today = date('Y-m-d');
$current_time = date("H:i");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>AirGo Booking</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet" />
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@600&family=Playfair+Display:wght@900&display=swap');
    body { margin: 0; padding: 0; background-color:  #d0f0ff; font-family: 'Roboto', sans-serif; }
    header { background: #07353f; padding: 10px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
    nav.container { display: flex; justify-content: space-between; align-items: center; max-width: 1100px; margin: 0 auto; padding: 0 24px; }
    .logo h1 { color: #CACBBB; font-family: 'Playfair Display'; font-size: 1.8rem; margin: 0; text-shadow: 1px 1px 2px rgba(0,0,0,0.3); text-transform: uppercase; }
    .btn-Back { color: #07353f; background-color:  #d0f0ff; padding: 10px 20px; border-radius: 25px; font-family: 'Poppins', sans-serif; text-decoration: none; }
    .btn-Back:hover { background-color: #d6cec6; color: #07353f; }
    .airgo-booking-form { margin: 5vh auto; max-width: 500px; background-color:  #d0f0ff; padding: 30px; border-radius: 10px; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3); animation: fadeIn 0.8s ease-in-out; }
    @keyframes fadeIn { 0% { opacity: 0; transform: translateY(40px); } 100% { opacity: 1; transform: translateY(0); } }
    .form-row { display: flex; flex-wrap: wrap; justify-content: space-between; margin-bottom: 20px; gap: 20px; }
    .form-group { flex: 1; min-width: 240px; display: flex; flex-direction: column; }
    label { font-weight: 300; }
    input[type="text"], input[type="email"], input[type="date"], input[type="tel"], select {
      padding: 12px; font-size: 1em; border: 1px solid #ccc; border-radius: 6px; margin-top: 6px;
    }
    button {
      width: 50%; padding: 10px; font-size: 1.1em; color: white; background-color: #07353f;
      border: none; border-radius: 6px; cursor: pointer; transition: background 0.3s ease;
    }
    button:hover { background-color: #0c4f5a; }
  </style>
</head>
<body>

<header>
  <nav class="container">
    <div class="logo"><h1>Airgo</h1></div>
    <div class="Back-button"><a href="dashboard.php" class="btn-Back">Back</a></div>
  </nav>
</header>

<div class="airgo-booking-form">
  <h2 style="text-align:center; margin-bottom: 20px;">Book an Appointment</h2>

  <form action="booking_confirmation.php" method="POST">
    <div class="form-row">
      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($name); ?>" required>
      </div>
      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" name="email" value="<?= htmlspecialchars($email); ?>" required>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="location">Location</label>
        <input type="text" name="location" value="<?= htmlspecialchars($location); ?>" required>
      </div>
      <div class="form-group">
		<label for="contact">Cellphone Number</label>
        <input type="text" value="+63" name="contact" value="<?= htmlspecialchars($contact); ?>" required style="margin-bottom:0;">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="service">Select Service</label>
        <select name="service" id="service" required onchange="updatePrice()">
        <option value="">-- Select Service --</option>
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
          <div class="form-group">
  <label for="service_price">Service Price (PHP)</label>
  <input type="text" id="service_price" name="service_price" readonly>
</div>    
      </div>

    <div class="form-row">
      <div class="form-group">
        <label for="appointment_date">Appointment Date</label>
        <input type="date" id="appointment_date" name="appointment_date" value="<?= htmlspecialchars($appointment_date) ?>" required min="<?= $today ?>" onchange="enableAppointmentTime()">
      </div>
      <div class="form-group">
        <label for="appointment_time">Appointment Time</label>
        <select id="appointment_time" name="appointment_time" required>
          <option value="">Select a time</option>
          <?php
          $start = strtotime("08:00");
          $end = strtotime("16:20");
          $interval = 100 * 60; // 1 hour 40 minutes
          while ($start <= $end) {
              $value = date("H:i", $start);
              $label = date("g:i A", $start);
              $is_today = ($appointment_date == $today);
              $is_past = ($is_today && $start < strtotime($current_time)) ? 'disabled' : '';
              $selected = ($appointment_time == $value || $appointment_time == $label) ? 'selected' : '';
              echo "<option value=\"$value\" $selected $is_past>$label</option>";
              $start += $interval;
          }
          ?>
        </select>
      </div>
    </div>

    <div style="text-align: center; margin-top: 20px;">
      <button type="submit">Confirm Booking</button>
    </div>
  </form>
  <script>
    function updatePrice() {
        const service = document.getElementById('service').value;
        const prices = {
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
        document.getElementById('service_price').value = prices[service] || '';
    }
</script>
</div>

<script>
  function enableAppointmentTime() {
    const dateField = document.getElementById("appointment_date");
    const timeField = document.getElementById("appointment_time");
    timeField.disabled = !dateField.value;
  }
  window.onload = enableAppointmentTime;
</script>
<script>
  // Map services to their prices
  const servicePrices = <?php echo json_encode($services_prices); ?>;

  const serviceSelect = document.querySelector('select[name="service"]');
  const priceField = document.getElementById('service_price');

  // Function to update the price field
  function updatePrice() {
    const selectedService = serviceSelect.value;
    if (servicePrices[selectedService]) {
      priceField.value = servicePrices[selectedService];
    } else {
      priceField.value = '';
    }
  }

  // Event listener
  serviceSelect.addEventListener('change', updatePrice);

  // Initialize on load
  updatePrice();
</script>

</body>
</html>
