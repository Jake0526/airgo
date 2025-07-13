<?php
// Prevent any output before our JSON response
ob_start();

session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => true, 'message' => 'Not authenticated']);
    exit();
}

try {
    $user_id = $_SESSION['user_id'];
    $conn = Database::getConnection();

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
    $phone = $user['contact'] ?? '';

    // Close the statement
    $stmt->close();

    $appointment_date = $_GET['date'] ?? '';
    $appointment_time = $_GET['time'] ?? '';

    // Start capturing HTML
    ob_start();
    include 'templates/booking_form.php';  // This will include your HTML form template
    $html = ob_get_clean();

    // Prepare the JavaScript data
    $districts_json = json_encode($districts);
    $services_prices_json = json_encode($services_prices);

    // Create the JavaScript without HTML tags
    $script = "
        // Global variables and functions
        let districtSelect, barangaySelect;
        const barangayData = $districts_json;
        const servicePrices = $services_prices_json;

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

        // Function to update location
        function updateLocation() {
            const houseNumber = document.getElementById('house_number').value.trim();
            const street = document.getElementById('street').value.trim();
            const district = document.getElementById('district').value;
            const barangay = document.getElementById('barangay').value;
            
            if (houseNumber && street && district && barangay) {
                const location = `\${houseNumber}, \${street}, \${barangay}, \${district}, Davao City`;
                document.getElementById('location').value = location;
            }
        }

        // Function to update barangay options
        function updateBarangayOptions() {
            const district = document.getElementById('district').value;
            const barangaySelect = document.getElementById('barangay');
            
            // Clear existing options
            barangaySelect.innerHTML = '<option value=\"\">Select Barangay</option>';
            
            // Enable/disable barangay select based on district selection
            barangaySelect.disabled = !district;
            
            if (district && barangayData[district]) {
                barangayData[district].forEach(barangay => {
                    const option = document.createElement('option');
                    option.value = barangay;
                    option.textContent = barangay;
                    barangaySelect.appendChild(option);
                });
            }
        }

        // Initialize all form handlers
        initializeFormHandlers();
    ";

    // Send JSON response with both HTML and script
    header('Content-Type: application/json');
    echo json_encode([
        'error' => false,
        'html' => $html,
        'script' => $script
    ]);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?> 