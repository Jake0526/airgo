<?php
// Prevent any output before our JSON response
ob_start();

session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Create an array to hold both HTML and script
$response = [
    'html' => '',
    'script' => ''
];

try {
$user_id = $_SESSION['user_id'];

    // Define districts and barangays
    $districts = [
        "Poblacion" => [
            "1-A", "2-A", "3-A", "4-A", "5-A", "6-A", "7-A", "8-A", "9-A", "10-A",
            "11-B", "12-B", "13-B", "14-B", "15-B", "16-B", "17-B", "18-B", "19-B", "20-B",
            "21-C", "22-C", "23-C", "24-C", "25-C", "26-C", "27-C", "28-C", "29-C", "30-C",
            "31-D", "32-D", "33-D", "34-D", "35-D", "36-D", "37-D", "38-D", "39-D", "40-D"
        ],
        "Talomo" => [
            "Bago Aplaya", "Bago Gallera", "Baliok", "Bucana", "Catalunan Grande", "Catalunan Pequeño",
            "Dumoy", "Langub", "Ma-a", "Magtuod", "Matina Aplaya", "Matina Crossing", "Matina Pangi", "Talomo Proper"
        ],
        "Agdao" => [
            "Agdao Proper", "Centro (San Juan)", "Gov. Paciano Bangoy", "Gov. Vicente Duterte", "Kap. Tomas Monteverde, Sr.",
            "Lapu-Lapu", "Leon Garcia", "Rafael Castillo", "San Antonio", "Ubalde", "Wilfredo Aquino"
        ],
        "Buhangin" => [
            "Acacia", "Alfonso Angliongto Sr.", "Buhangin Proper", "Cabantian", "Callawa",
            "Communal", "Indangan", "Mandug", "Pampanga", "Sasa", "Tigatto", "Vicente Hizon Sr.", "Waan"
        ],
        "Bunawan" => [
            "Alejandra Navarro (Lasang)", "Bunawan Proper", "Gatungan", "Ilang", "Mahayag",
            "Mudiang", "Panacan", "San Isidro (Licanan)", "Tibungco"
        ],
        "Paquibato" => [
            "Colosas", "Fatima (Benowang)", "Lumiad", "Mabuhay", "Malabog", "Mapula", "Panalum",
            "Pandaitan", "Paquibato Proper", "Paradise Embak", "Salapawan", "Sumimao", "Tapak"
        ],
        "Baguio" => [
            "Baguio Proper", "Cadalian", "Carmen", "Gumalang", "Malagos", "Tambobong", "Tawan-Tawan", "Wines"
        ],
        "Calinan" => [
            "Biao Joaquin", "Calinan Proper", "Cawayan", "Dacudao", "Dalagdag", "Dominga", "Inayangan",
            "Lacson", "Lamanan", "Lampianao", "Megkawayan", "Pangyan", "Riverside", "Saloy",
            "Sirib", "Subasta", "Talomo River", "Tamayong", "Wangan"
        ],
        "Marilog" => [
            "Baganihan", "Bantol", "Buda", "Dalag", "Datu Salumay", "Gumitan", "Magsaysay",
            "Malamba", "Marilog Proper", "Salaysay", "Suawan (Tuli)", "Tamugan"
        ],
        "Toril" => [
            "Alambre", "Atan-Awe", "Bangkas Heights", "Baracatan", "Bato", "Bayabas", "Binugao",
            "Camansi", "Catigan", "Crossing Bayabas", "Daliao", "Daliaon Plantation", "Eden",
            "Kilate", "Lizada", "Lubogan", "Marapangi", "Mulig", "Sibulan", "Sirawan",
            "Tagluno", "Tagurano", "Quiboloy", "Toril Proper", "Tungkalan"
        ],
        "Tugbok" => [
            "Angalan", "Bago Oshiro", "Balenggaeng", "Biao Escuela", "Biao Guinga", "Los Amigos",
            "Manambulan", "Manuel Guianga", "Matina Biao", "Mintal", "New Carmen", "New Valencia",
            "Santo Niño", "Tacunan", "Tagakpan", "Talandang", "Tugbok Proper", "Ulas"
        ]
    ];

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
    $stmt = $conn->prepare("SELECT CONCAT(fname, ' ', lname) as fullname, email, contact FROM user WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$name = $user['fullname'] ?? '';
$email = $user['email'] ?? '';
$contact = $user['contact'] ?? '';

// Close the statement
$stmt->close();

$appointment_date = $_GET['date'] ?? '';
$appointment_time = $_GET['time'] ?? '';

    // Start capturing HTML
    ob_start();
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
.address-fields {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.address-fields select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 14px;
    line-height: 1.5;
    background-color: white;
}

.address-fields select:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
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
            <label for="location">Complete Address</label>
            <div class="address-fields" style="display: flex; flex-direction: column; gap: 10px;">
                <input type="text" class="form-control" name="house_number" placeholder="House/Unit Number" required>
                <input type="text" class="form-control" name="street" placeholder="Street Name" required>
                <select name="district" id="district" class="form-control" required>
                    <option value="">-- Select District --</option>
                    <?php foreach ($districts as $district => $barangays): ?>
                        <option value="<?php echo htmlspecialchars($district); ?>"><?php echo htmlspecialchars($district); ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="barangay" id="barangay" class="form-control" required>
                    <option value="">-- Select Barangay --</option>
                </select>
                <input type="hidden" name="location" id="complete_location">
            </div>
        </div>
        <div class="form-group">
            <label for="contact">Contact Number</label>
            <div class="contact-input-wrapper">
                <span class="prefix">+639</span>
                <input type="text" class="form-control" name="contact" id="contact" maxlength="9" required>
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
            <input type="text" class="form-control" id="service_price" name="price" readonly>
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

<?php
    // Get the HTML content
    $response['html'] = ob_get_clean();

    // Remove debug error_log
    // Debug: Check districts data
    // error_log('Districts data: ' . json_encode($districts));

    // Add the JavaScript with proper JSON encoding
    $districts_json = json_encode($districts, JSON_PRETTY_PRINT);
    $services_prices_json = json_encode($services_prices, JSON_PRETTY_PRINT);
    $response['script'] = <<<JAVASCRIPT
// Global variables and functions
let districtSelect, barangaySelect;
const barangayData = {$districts_json};
const servicePrices = {$services_prices_json};

// Function to update service price
function updatePrice() {
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

// Function to update complete location
function updateCompleteLocation() {
    const houseNumber = document.querySelector('[name="house_number"]').value;
    const street = document.querySelector('[name="street"]').value;
    const district = districtSelect.value;
    const barangay = barangaySelect.value;
    
    if (houseNumber && street && district && barangay) {
        const completeLocation = `\${houseNumber}, \${street}, \${barangay}, \${district}, Davao City`;
        document.getElementById('complete_location').value = completeLocation;
    }
}

// Function to load barangays based on selected district
function loadBarangays() {
    const selectedDistrict = districtSelect.value;
    
    // Clear existing options
    barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';

    if (selectedDistrict && barangayData[selectedDistrict]) {
        const barangays = barangayData[selectedDistrict];
        barangays.forEach(barangay => {
            const option = document.createElement('option');
            option.value = barangay;
            option.textContent = barangay;
            barangaySelect.appendChild(option);
        });
    }
    updateCompleteLocation();
}

// Function to initialize form event listeners
function initializeFormHandlers() {
    const form = document.querySelector('form');
    
    districtSelect = document.getElementById('district');
    barangaySelect = document.getElementById('barangay');
    
    if (!districtSelect || !barangaySelect) {
        return;
    }
    
    // Add event listeners
    districtSelect.addEventListener('change', function(e) {
        loadBarangays();
    });
    
    barangaySelect.addEventListener('change', function(e) {
        updateCompleteLocation();
    });
    
    // Add input event listeners to all address fields
    const addressFields = document.querySelectorAll('.address-fields input, .address-fields select');
    
    addressFields.forEach(field => {
        field.addEventListener('change', function(e) {
            updateCompleteLocation();
        });
        field.addEventListener('input', function(e) {
            updateCompleteLocation();
        });
    });

    // Handle form submission
    form.onsubmit = function(event) {
        updateCompleteLocation();
        handleBookingSubmit(this, event);
    };
    
    // Initialize service price if function exists
    if (typeof updatePrice === 'function') {
        updatePrice();
    }

    // Load barangays if district is pre-selected
    if (districtSelect.value) {
        loadBarangays();
    }
}

// Initialize form handlers when loaded
initializeFormHandlers();
JAVASCRIPT;

    // Clear any previous output and send headers
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode($response);
    exit(); // Add exit to prevent any additional output

} catch (Exception $e) {
    // Clear any previous output and send error response
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
    exit(); // Add exit to prevent any additional output
} 