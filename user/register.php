<!--?php  
// Include the database connection
include('db_connection.php');

// Variable to store error messages
$error_message = "";

// Function to validate username/full name (only letters and spaces, min 3 chars)
function isValidUsername($username) {
    return preg_match('/^[A-Za-z ]{3,}$/', $username);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and sanitize it
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $city = trim($_POST['city']);
    $district = trim($_POST['district']);
    $barangay = trim($_POST['barangay']);
    $zipcode = trim($_POST['zipcode']);

    // Validate username as full name
    if (!isValidUsername($username)) {
        $error_message = "Error: Username must contain only letters and spaces.";
    } 
    // Validate password strength
    elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
        $error_message = "Error: Password must be minimum 8 characters, include letters and numbers.";
    }
    else {
        if ($conn && $conn instanceof mysqli) {
            // Check if email or username already exists
            $sql = "SELECT id FROM user WHERE email = ? OR username = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ss", $email, $username);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $error_message = "Error: Username or email already exists.";
                } else {
                    // Hash password
                    $password = password_hash($password, PASSWORD_DEFAULT);

                    // Insert into user table
                    $sql = "INSERT INTO user (fname, lname, username, password, email, contact, city, district, barangay, zipcode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    if ($insert_stmt = $conn->prepare($sql)) {
                        $insert_stmt->bind_param("ssssssssss", $fname, $lname, $username, $password, $email, $contact, $city, $district, $barangay, $zipcode);

                        if ($insert_stmt->execute()) {
                            $error_message = "Registration successful!";
                        } else {
                            $error_message = "Error: " . $insert_stmt->error;
                        }
                        $insert_stmt->close();
                    } else {
                        $error_message = "Error preparing insert statement: " . $conn->error;
                    }
                }
                $stmt->close();
            } else {
                $error_message = "Error preparing select statement: " . $conn->error;
            }
        } else {
            $error_message = "Database connection failed.";
        }
    }
}

// Close the database connection if valid
if ($conn && $conn instanceof mysqli) {
    $conn->close();
}
?-->
<?php  
// Include the database connection
include('db_connection.php');

// Variable to store error messages
$error_message = "";

// Function to validate username/full name (only letters and spaces, min 3 chars)
function isValidUsername($username) {
    return preg_match('/^[A-Za-z ]{3,}$/', $username);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and sanitize it
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $city = trim($_POST['city']);
    $district = trim($_POST['district']);
    $barangay = trim($_POST['barangay']);
    $zipcode = trim($_POST['zipcode']);

    // Validate username as full name
    if (!isValidUsername($username)) {
        $error_message = "Error: Please fill-up all fields.";
    } 
    // Validate password strength
    elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
        $error_message = "Error: Password must be minimum 8 characters, include letters and numbers.";
    }
    else {
        if ($conn && $conn instanceof mysqli) {
            // Check if email or username already exists
            $sql = "SELECT id FROM user WHERE email = ? OR username = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ss", $email, $username);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $error_message = "Error: Username or email already exists.";
                } else {
                    // Hash the password using bcrypt
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Insert into user table
                    $sql = "INSERT INTO user (fname, lname, username, password, email, contact, city, district, barangay, zipcode) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    if ($insert_stmt = $conn->prepare($sql)) {
                        $insert_stmt->bind_param(
                            "ssssssssss", 
                            $fname, 
                            $lname, 
                            $username, 
                            $hashed_password, 
                            $email, 
                            $contact, 
                            $city, 
                            $district, 
                            $barangay, 
                            $zipcode
                        );

                        if ($insert_stmt->execute()) {
                            $error_message = "Registration successful!";
                        } else {
                            $error_message = "Error: " . $insert_stmt->error;
                        }
                        $insert_stmt->close();
                    } else {
                        $error_message = "Error preparing insert statement: " . $conn->error;
                    }
                }
                $stmt->close();
            } else {
                $error_message = "Error preparing select statement: " . $conn->error;
            }
        } else {
            $error_message = "Database connection failed.";
        }
    }
}

// Close the database connection if valid
if ($conn && $conn instanceof mysqli) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
    body {
        font-family: 'Arial', sans-serif;
        margin: 0;
        padding: 0;
        background-color:  #d0f0ff;
        color: #333;
    }
    a { text-decoration: none; color: inherit; }
    header {
        background-color: #07353f;
        color:  #d0f0ff;
        padding: 20px 0;
    }
    header nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 20px;
    }
    header .logo h1 {
        margin: 0;
    }
    #register {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 10px;
        background-color:  #d0f0ff;
    }
    .register-form {
        background-color:  #d0f0ff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        width: 100%;
        max-width: 1200px;
        text-align:center;
        border: 3px solid #07353f;
        margin: 0 auto;
        box-sizing: border-box;
        margin-left: 10in;
        margin-right: 10in;
        margin-top: 1px;
    }
    .register-form h2 {
        margin-bottom: 10px;
        margin-top: 0px;
        color: #07353f;
    }
    .register-form label {
        display: block;
        margin-bottom: 0px;
        margin-top: 0px;
        font-weight: bold;
    }
    .register-form input {
        width: 50%;
        padding: 5px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1em;
    }
    .register-form button {
        background-color: #07353f;
        color:  #d0f0ff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 1.1em;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    button:hover {
            background-color: #d0f0ff;
            color: #07353f;
            box-shadow: 0 8px 18px rgba(202, 203, 187, 0.7);
        }

    .register-form .login-link {
        display: block;
        margin-top: 3px;
        font-size: 0.9em;
        color: #07353f;
    }
    footer {
        background-color: #07353f;
        color: white;
        text-align: center;
        padding: 10px 0;
        margin-top: 5px;
    }
    .error-message {
        color: red;
        margin-bottom: 15px;
    }
    .success-message {
        color: green;
        margin-bottom: 15px;
    }
    .address-section {
        background: #eaf7ff;
        border: 2px solid #07353f;
        border-radius: 8px;
        padding: 18px 15px 10px 15px;
        margin-bottom: 18px;
        margin-top: 10px;
        box-shadow: 0 2px 8px rgba(7,53,63,0.07);
        text-align: left;
    }
    .address-section label {
        color: #07353f;
        font-weight: 600;
        margin-top: 8px;
        margin-bottom: 2px;
        display: block;
        letter-spacing: 0.5px;
    }
    .address-section input,
    .address-section select {
        width: 97%;
        padding: 6px 8px;
        margin-bottom: 10px;
        border: 1.5px solid #b3d8e6;
        border-radius: 5px;
        font-size: 1em;
        background: #fafdff;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .address-section input:focus,
    .address-section select:focus {
        border-color: #07353f;
        outline: none;
        box-shadow: 0 0 0 2px #d0f0ff;
    }
    .address-section select:hover,
    .address-section input:hover {
        border-color: #4bb6e6;
    }
    .form-columns {
        display: flex;
        gap: 32px;
        justify-content: center;
        align-items: flex-start;
        margin-bottom: 10px;
        width: 100%;
    }
    .form-col {
        flex: 1 1 0;
        min-width: 220px;
        max-width: 100%;
        box-sizing: border-box;
    }
    .left-col label,
    .right-col label {
        margin-top: 8px;
        margin-bottom: 2px;
        font-weight: 600;
        color: #07353f;
        display: block;
        text-align: left;
        padding-left: 2px;
    }
    .left-col input,
    .right-col input,
    .right-col select {
        width: 98%;
        padding: 6px 8px;
        margin-bottom: 12px;
        border: 1.5px solid #b3d8e6;
        border-radius: 5px;
        font-size: 1em;
        background: #fafdff;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }
    .left-col input:focus,
    .right-col input:focus,
    .right-col select:focus {
        border-color: #07353f;
        outline: none;
        box-shadow: 0 0 0 2px #d0f0ff;
    }
    .left-col input {
        margin-top: 1px;
    }
    @media (max-width: 2400px) {
        .register-form {
            margin-left: 2in;
            margin-right: 2in;
        }
    }
    @media (max-width: 1000px) {
        .register-form {
            margin-left: 0.2in;
            margin-right: 0.2in;
        }
    }
</style>
</head>
<body>

<!-- Header -->
<header style="background: #07353f; padding: 10px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
  <nav class="container" style="display: flex; justify-content: space-between; align-items: center; max-width: 1100px; margin: 0 auto; padding: 0 24px;">
    <div class="logo">
      <h1 style="
        color:  #d0f0ff; 
        font-family: 'Playfair Display', Georgia, serif; 
        font-weight: 100; 
        font-style: normal; 
        letter-spacing: 3px; 
        font-size: 1.9rem; 
        margin: 0; 
        cursor: default;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        text-transform: uppercase;
        ">
        AirGo
      </h1>
    </div>
    <div class="Home-button">
      <a href="index.php" class="btn-Home" style="position: relative; color: #07353f; background-color:  #d0f0ff; padding: 10px 26px; border-radius: 25px; font-weight: 600; font-family: 'Poppins', sans-serif; text-transform: uppercase; letter-spacing: 1.2px; text-decoration: none; overflow: hidden; display: inline-block; transition: background-color 0.3s ease;">
        Home
        <span class="underline"></span>
      </a>
    </div>
  </nav>
</header>


<section id="register">
    <div class="register-form">
        <h2>Create an Account</h2>

        <?php if (!empty($error_message)): ?>
            <div class="<?php echo (strpos($error_message, 'Error') === false) ? 'success-message' : 'error-message'; ?>">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" novalidate>
            <div class="form-columns">
                <div class="form-col left-col">
                    <label for="fname">First Name</label>
                    <input type="text" id="fname" name="fname" required pattern="[A-Za-z ]{3,}" title="Only letters and spaces">
                    <label for="lname">Last Name</label>
                    <input type="text" id="lname" name="lname" required pattern="[A-Za-z ]{3,}" title="Only letters and spaces">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required pattern="[A-Za-z ]{3,}" title="Only letters and spaces">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                    <label for="password">Password </label>
                    <input type="password" id="password" name="password" required pattern="(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}" title="Minimum 8 characters, including letters and numbers">
                    <label for="contact">Cellphone Number</label>
                    <input type="text" value="+63" name="contact" required style="margin-bottom:0;">
                </div>
                <div class="form-col right-col" style="display:flex; flex-direction:column; justify-content:flex-end; align-items:flex-start;">
                    <div style="width:100%;">
                        <div class="address-section">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" value="Davao City" readonly required>
           
							
<?php
// PHP array holding the barangays grouped by district
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
    ],
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>District & Barangay Dropdown</title>
    <script>
        // Embed PHP array into JavaScript
        const barangayData = <?php echo json_encode($districts); ?>;

        function loadBarangays() {
            const districtSelect = document.getElementById("district");
            const barangaySelect = document.getElementById("barangay");
            const selectedDistrict = districtSelect.value;

            // Clear existing options
            barangaySelect.innerHTML = "";

            if (selectedDistrict && barangayData[selectedDistrict]) {
                const barangays = barangayData[selectedDistrict];
                for (let i = 0; i < barangays.length; i++) {
                    const option = document.createElement("option");
                    option.value = barangays[i];
                    option.text = barangays[i];
                    barangaySelect.appendChild(option);
                }
            } else {
                const option = document.createElement("option");
                option.value = "";
                option.text = "-- Select Barangay --";
                barangaySelect.appendChild(option);
            }
        }
    </script>
</head>
<body>

<h3>Select District and Barangay</h3>

<form method="post">
    <label for="district">District:</label>
    <select name="district" id="district" onchange="loadBarangays()">
        <option value="">-- Select District --</option>
        <?php foreach ($districts as $district => $barangays): ?>
            <option value="<?php echo $district; ?>"><?php echo $district; ?></option>
        <?php endforeach; ?>
    </select>

    <br><br>

    <label for="barangay">Barangay:</label>
    <select name="barangay" id="barangay">
        <option value="">-- Select Barangay --</option>
    </select>

    <br><br>
    <!--input type="submit" value="Submit"-->
</form>

<?php
// Display selected output after form submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $selectedDistrict = $_POST['district'] ?? '';
    $selectedBarangay = $_POST['barangay'] ?? '';

    if ($selectedDistrict && $selectedBarangay) {
        echo "<p>You selected <strong>$selectedBarangay</strong> in <strong>$selectedDistrict</strong> district.</p>";
    }
}
?>
							
                            <label for="zipcode">Zipcode</label>
                            <select id="zipcode" name="zipcode" required>
                                <option value="8000" selected>8000 (Davao City)</option>
                            </select>
                        </div>
                        <div style="width:100%; display:flex; flex-direction:column; align-items:flex-start; margin-top:8px;">
                            <button type="submit" style="width:100%; min-width:90px;">Register</button>
                            <a href="login.php" class="login-link" style="margin-top:8px; display:block; text-align:left; font-size:0.95em;">Already have an account? Login here</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

</body>
</html>