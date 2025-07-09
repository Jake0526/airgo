<?php  
// Include the database connection
include('db_connection.php');

// Variable to store error messages
$error_message = "";

// Function to validate username/full name (only letters and spaces, min 3 chars)
function isValidUsername($username) {
    return preg_match('/^[A-Za-z ]{3,}$/', $username);
}

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

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and sanitize it
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = trim($_POST['email']);
    
    // Handle contact number formatting
    $contact = trim($_POST['contact']);
    // Remove any existing +639 prefix to avoid duplication
    $contact = preg_replace('/^\+639/', '', $contact);
    // Add the +639 prefix
    $contact = '+639' . $contact;
    
    $city = trim($_POST['city']);
    $district = trim($_POST['district']);
    $barangay = trim($_POST['barangay']);
    $zipcode = trim($_POST['zipcode']);

    // Validate username as full name
    if (!isValidUsername($username)) {
        $error_message = "Error: Username must contain only letters and spaces.";
    } 
    // Validate password strength
    elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>])[A-Za-z\d!@#$%^&*(),.?":{}|<>]{12,}$/', $password)) {
        $error_message = "Error: Password must be minimum 12 characters and include uppercase letters, lowercase letters, numbers, and special characters.";
    }
    // Check if passwords match
    elseif ($password !== $confirm_password) {
        $error_message = "Error: Passwords do not match.";
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
                    // Hash the password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    echo 'hashed_password: ' . $hashed_password;

                    // Insert into user table
                    $sql = "INSERT INTO user (fname, lname, username, password, email, contact, city, district, barangay, zipcode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    if ($insert_stmt = $conn->prepare($sql)) {
                        $insert_stmt->bind_param("ssssssssss", $fname, $lname, $username, $hashed_password, $email, $contact, $city, $district, $barangay, $zipcode);

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
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Register - Airgo</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@400;700;900&display=swap" rel="stylesheet">
<!-- Header styles -->
<link rel="stylesheet" href="styles/header.css">
<style>
    :root {
        --primary-color: #07353f;
        --secondary-color: #3cd5ed;
        --background-color: #d0f0ff;
        --text-color: #344047;
        --card-bg: #ffffff;
        --card-shadow: rgba(7, 53, 63, 0.1);
        --spacing-unit: clamp(0.5rem, 2vw, 1rem);
        --input-bg: #ffffff;
        --input-border: #e0e0e0;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, var(--background-color), #ffffff);
        color: var(--text-color);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    a { 
        text-decoration: none; 
        color: inherit; 
    }

    #register {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 2rem 1rem;
    }

    .register-form {
        background: var(--card-bg);
        padding: 2.5rem;
        border-radius: 20px;
        box-shadow: 0 10px 30px var(--card-shadow);
        width: min(90%, 1000px);
        margin: 0 auto;
    }

    .register-form h2 {
        font-size: 2rem;
        color: var(--primary-color);
        margin-bottom: 2rem;
        text-align: center;
        font-weight: 700;
        position: relative;
    }

    .register-form h2::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 50px;
        height: 3px;
        background: var(--secondary-color);
    }

    .form-columns {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
        position: relative;
    }

    label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-color);
        font-size: 0.95rem;
        font-weight: 500;
    }

    input, select {
        width: 100%;
        padding: 0.8rem 1rem;
        border: 1px solid var(--input-border);
        border-radius: 8px;
        background: var(--input-bg);
        color: var(--text-color);
        font-size: 0.95rem;
        font-family: 'Poppins', sans-serif;
        transition: all 0.3s ease;
    }

    input:focus, select:focus {
        outline: none;
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 3px rgba(60, 213, 237, 0.1);
    }

    input::placeholder {
        color: #999;
    }

    .address-section {
        margin-top: 1rem;
    }

    .address-section h3 {
        color: var(--primary-color);
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
    }

    button[type="submit"] {
        width: 100%;
        padding: 1rem;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
    }

    button[type="submit"]:hover {
        background: var(--secondary-color);
        transform: translateY(-2px);
    }

    .btn-icon {
        font-size: 1.2rem;
    }

    .login-link {
        display: block;
        text-align: center;
        margin-top: 1.5rem;
        color: var(--primary-color);
        text-decoration: none;
        font-size: 0.95rem;
    }

    .login-link:hover {
        color: var(--secondary-color);
    }

    .error-message, .success-message {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .error-message {
        background: #fee;
        color: #e44;
    }

    .success-message {
        background: #e6ffe6;
        color: #0a0;
    }

    @media (max-width: 768px) {
        .register-form {
            padding: 1.5rem;
        }

        .form-columns {
            grid-template-columns: 1fr;
        }

        .register-form h2 {
            font-size: 1.5rem;
        }
    }

    .contact-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        background: var(--input-bg);
        border: 1px solid var(--input-border);
        border-radius: 8px;
        overflow: hidden;
    }

    .contact-input-wrapper .prefix {
        padding: 0.8rem 0.5rem;
        background: var(--card-bg);
        color: var(--text-color);
        border-right: 1px solid var(--input-border);
        font-size: 0.95rem;
        user-select: none;
    }

    .contact-input-wrapper input {
        border: none;
        border-radius: 0;
        padding-left: 0.5rem;
    }

    .contact-input-wrapper input:focus {
        box-shadow: none;
    }

    .contact-input-wrapper:focus-within {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 3px rgba(60, 213, 237, 0.1);
    }

    .contact-hint {
        display: block;
        margin-top: 0.5rem;
        font-size: 0.8rem;
        color: #666;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        backdrop-filter: blur(5px);
    }

    .modal-content {
        position: relative;
        background: var(--card-bg);
        width: min(90%, 500px);
        margin: 20vh auto;
        padding: 2rem;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        animation: modalSlideIn 0.3s ease;
    }

    @keyframes modalSlideIn {
        from {
            transform: translateY(-10%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-content h3 {
        color: var(--primary-color);
        margin-bottom: 1rem;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .modal-content p {
        color: var(--text-color);
        margin-bottom: 1.5rem;
    }

    .confirmation-details {
        background: rgba(7, 53, 63, 0.05);
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }

    .detail-row {
        display: flex;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid rgba(7, 53, 63, 0.1);
    }

    .detail-row:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .detail-row .label {
        font-weight: 600;
        width: 100px;
        color: var(--primary-color);
    }

    .modal-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .btn-secondary {
        background: transparent;
        color: var(--primary-color);
        border: 2px solid var(--primary-color);
        padding: 0.8rem 1.5rem;
        border-radius: 50px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: rgba(7, 53, 63, 0.1);
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 0.8rem 1.5rem;
        border-radius: 50px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: var(--secondary-color);
        transform: translateY(-2px);
    }

    @media (max-width: 768px) {
        .modal-content {
            margin: 10vh auto;
            padding: 1.5rem;
        }

        .modal-actions {
            flex-direction: column;
        }

        .modal-actions button {
            width: 100%;
        }
    }

    .validation-message {
        display: none;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        padding: 0.5rem 0.8rem;
        border-radius: 6px;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .validation-message.error {
        display: block;
        background: #fff2f2;
        color: #e44;
        border: 1px solid #ffdbdb;
    }

    .validation-message.success {
        display: block;
        background: #f0fff0;
        color: #0a0;
        border: 1px solid #d7f5d7;
    }

    input.error, select.error {
        border-color: #e44;
    }

    input.error:focus, select.error:focus {
        border-color: #e44;
        box-shadow: 0 0 0 3px rgba(238, 68, 68, 0.1);
    }

    .contact-input-wrapper.error {
        border-color: #e44;
    }

    .contact-input-wrapper.error:focus-within {
        border-color: #e44;
        box-shadow: 0 0 0 3px rgba(238, 68, 68, 0.1);
    }

    .contact-input-wrapper.success {
        border-color: #0a0;
    }

    .contact-input-wrapper.success:focus-within {
        border-color: #0a0;
        box-shadow: 0 0 0 3px rgba(10, 170, 0, 0.1);
    }

    .password-requirements {
        margin-top: 0.5rem;
        font-size: 0.85rem;
        color: #666;
    }

    .requirement {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.3rem;
    }

    .requirement::before {
        content: '';
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 1px solid #ccc;
        display: inline-block;
    }

    .requirement.met::before {
        background: #0a0;
        border-color: #0a0;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3E%3Cpath d='M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z'/%3E%3C/svg%3E");
        background-size: 10px;
        background-position: center;
        background-repeat: no-repeat;
    }

    .requirement.unmet::before {
        background: #e44;
        border-color: #e44;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3E%3Cpath d='M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z'/%3E%3C/svg%3E");
        background-size: 10px;
        background-position: center;
        background-repeat: no-repeat;
    }

    .password-strength {
        margin-top: 0.5rem;
        height: 4px;
        background: #eee;
        border-radius: 2px;
        overflow: hidden;
    }

    .strength-meter {
        height: 100%;
        width: 0;
        transition: all 0.3s ease;
        border-radius: 2px;
    }

    .strength-meter.weak { width: 33.33%; background: #e44; }
    .strength-meter.medium { width: 66.66%; background: #f1c40f; }
    .strength-meter.strong { width: 100%; background: #0a0; }
</style>
</head>
<body>

<!-- Header -->
<header class="header">
    <nav class="container">
        <div class="logo">
            <h1>Air<span>go</span></h1>
        </div>
        <div class="header-button">
            <a href="index.php">
                Home
                <span class="btn-icon">→</span>
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
                <div class="form-col">
                    <div class="form-group">
                        <label for="fname">First Name</label>
                        <input type="text" id="fname" name="fname" required pattern="[A-Za-z ]{3,}" 
                               title="Only letters and spaces" placeholder="Enter your first name">
                    </div>

                    <div class="form-group">
                        <label for="lname">Last Name</label>
                        <input type="text" id="lname" name="lname" required pattern="[A-Za-z ]{3,}"
                               title="Only letters and spaces" placeholder="Enter your last name">
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required pattern="[A-Za-z ]{3,}"
                               title="Only letters and spaces" placeholder="Choose a username">
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="Enter your email">
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required 
                               oninput="validatePassword(this)"
                               placeholder="Create a password">
                        <div class="password-requirements">
                            <div class="requirement" id="req-length">At least 12 characters</div>
                            <div class="requirement" id="req-uppercase">Contains uppercase letter</div>
                            <div class="requirement" id="req-lowercase">Contains lowercase letter</div>
                            <div class="requirement" id="req-number">Contains numbers</div>
                            <div class="requirement" id="req-special">Contains special character (!@#$%^&*(),.?":{}|<>)</div>
                        </div>
                        <div class="password-strength">
                            <div class="strength-meter" id="strength-meter"></div>
                        </div>
                        <div class="validation-message" id="passwordValidation"></div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required
                               oninput="validateConfirmPassword(this)"
                               placeholder="Confirm your password">
                        <div class="validation-message" id="confirmPasswordValidation"></div>
                    </div>

                    <div class="form-group">
                        <label for="contact">Contact Number</label>
                        <div class="contact-input-wrapper">
                            <span class="prefix">+639</span>
                            <input type="text" 
                                   id="contact" 
                                   name="contact" 
                                   required 
                                   maxlength="9"
                                   pattern="[0-9]{9}"
                                   placeholder="123456789"
                                   title="Please enter 9 digits"
                                   oninput="validateContactNumber(this)">
                        </div>
                        <div class="validation-message" id="contactValidation"></div>
                        <small class="contact-hint">Format: +639 followed by 9 digits</small>
                    </div>
                </div>

                <div class="form-col">
                    <div class="address-section">
                        <h3>Address Information</h3>
                        
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" value="Davao City" readonly required>
                        </div>

                        <div class="form-group">
                            <label for="district">District</label>
                            <select name="district" id="district" required>
                                <option value="">-- Select District --</option>
                                <?php foreach ($districts as $district => $barangays): ?>
                                    <option value="<?php echo $district; ?>"><?php echo $district; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="barangay">Barangay</label>
                            <select name="barangay" id="barangay" required>
                                <option value="">-- Select Barangay --</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="zipcode">Zipcode</label>
                            <select id="zipcode" name="zipcode" required>
                                <option value="8000">8000 (Davao City)</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit">
                        Create Account
                        <span class="btn-icon">→</span>
                    </button>
                </div>
            </div>
        </form>

        <a href="login.php" class="login-link">Already have an account? Login here</a>
    </div>
</section>

<!-- Add the modal HTML before the closing body tag -->
<div id="confirmationModal" class="modal">
    <div class="modal-content">
        <h3>Confirm Registration</h3>
        <p>Please review your information:</p>
        <div class="confirmation-details">
            <div class="detail-row">
                <span class="label">Name:</span>
                <span id="confirmName"></span>
            </div>
            <div class="detail-row">
                <span class="label">Username:</span>
                <span id="confirmUsername"></span>
            </div>
            <div class="detail-row">
                <span class="label">Email:</span>
                <span id="confirmEmail"></span>
            </div>
            <div class="detail-row">
                <span class="label">Contact:</span>
                <span id="confirmContact"></span>
            </div>
            <div class="detail-row">
                <span class="label">Address:</span>
                <span id="confirmAddress"></span>
            </div>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-secondary" onclick="closeModal()">Edit Information</button>
            <button type="button" class="btn-primary" onclick="submitForm()">Confirm Registration</button>
        </div>
    </div>
</div>

<script>
    // District and Barangay dropdown functionality
    document.addEventListener('DOMContentLoaded', function() {
        const districtSelect = document.getElementById('district');
        const barangaySelect = document.getElementById('barangay');
        const barangayData = <?php echo json_encode($districts); ?>;

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
        }

        // Initialize barangay options on district change
        districtSelect.addEventListener('change', loadBarangays);
    });

    function validateContactNumber(input) {
        const wrapper = input.closest('.contact-input-wrapper');
        const validationMessage = document.getElementById('contactValidation');
        
        // Remove any non-digit characters
        let value = input.value.replace(/\D/g, '');
        
        // Ensure only 9 digits
        if (value.length > 9) {
            value = value.slice(0, 9);
        }
        
        // Update input value
        input.value = value;

        // Validate and show appropriate message
        if (value.length === 0) {
            wrapper.classList.remove('error', 'success');
            validationMessage.classList.remove('error', 'success');
            validationMessage.style.display = 'none';
        } else if (value.length < 9) {
            wrapper.classList.add('error');
            wrapper.classList.remove('success');
            validationMessage.textContent = `Please enter ${9 - value.length} more digit${9 - value.length > 1 ? 's' : ''}`;
            validationMessage.classList.add('error');
            validationMessage.classList.remove('success');
            validationMessage.style.display = 'block';
        } else if (!/^\d{9}$/.test(value)) {
            wrapper.classList.add('error');
            wrapper.classList.remove('success');
            validationMessage.textContent = 'Please enter numbers only';
            validationMessage.classList.add('error');
            validationMessage.classList.remove('success');
            validationMessage.style.display = 'block';
        } else {
            wrapper.classList.add('success');
            wrapper.classList.remove('error');
            validationMessage.textContent = 'Valid contact number';
            validationMessage.classList.add('success');
            validationMessage.classList.remove('error');
            validationMessage.style.display = 'block';
        }
    }

    function validatePassword(input) {
        const value = input.value;
        const requirements = {
            length: value.length >= 12,
            uppercase: /[A-Z]/.test(value),
            lowercase: /[a-z]/.test(value),
            number: /\d/.test(value),
            special: /[!@#$%^&*(),.?":{}|<>]/.test(value)
        };

        // Update requirement indicators
        document.getElementById('req-length').className = 
            `requirement ${requirements.length ? 'met' : 'unmet'}`;
        document.getElementById('req-uppercase').className = 
            `requirement ${requirements.uppercase ? 'met' : 'unmet'}`;
        document.getElementById('req-lowercase').className = 
            `requirement ${requirements.lowercase ? 'met' : 'unmet'}`;
        document.getElementById('req-number').className = 
            `requirement ${requirements.number ? 'met' : 'unmet'}`;
        document.getElementById('req-special').className = 
            `requirement ${requirements.special ? 'met' : 'unmet'}`;

        // Calculate password strength
        const strengthMeter = document.getElementById('strength-meter');
        const metRequirements = Object.values(requirements).filter(Boolean).length;

        if (value.length === 0) {
            strengthMeter.className = 'strength-meter';
            strengthMeter.style.width = '0';
        } else if (metRequirements === 1) {
            strengthMeter.className = 'strength-meter weak';
        } else if (metRequirements === 2) {
            strengthMeter.className = 'strength-meter medium';
        } else if (metRequirements === 3) {
            strengthMeter.className = 'strength-meter strong';
        }

        // Show validation message
        const validationMessage = document.getElementById('passwordValidation');
        if (value.length === 0) {
            validationMessage.style.display = 'none';
            input.classList.remove('error', 'success');
        } else if (!Object.values(requirements).every(Boolean)) {
            validationMessage.textContent = 'Please meet all password requirements';
            validationMessage.className = 'validation-message error';
            validationMessage.style.display = 'block';
            input.classList.add('error');
            input.classList.remove('success');
        } else {
            validationMessage.textContent = 'Password meets all requirements';
            validationMessage.className = 'validation-message success';
            validationMessage.style.display = 'block';
            input.classList.add('success');
            input.classList.remove('error');
        }

        // Validate confirm password if it has a value
        const confirmInput = document.getElementById('confirm_password');
        if (confirmInput.value) {
            validateConfirmPassword(confirmInput);
        }
    }

    function validateConfirmPassword(input) {
        const password = document.getElementById('password').value;
        const validationMessage = document.getElementById('confirmPasswordValidation');

        if (input.value.length === 0) {
            validationMessage.style.display = 'none';
            input.classList.remove('error', 'success');
        } else if (input.value !== password) {
            validationMessage.textContent = 'Passwords do not match';
            validationMessage.className = 'validation-message error';
            validationMessage.style.display = 'block';
            input.classList.add('error');
            input.classList.remove('success');
        } else {
            validationMessage.textContent = 'Passwords match';
            validationMessage.className = 'validation-message success';
            validationMessage.style.display = 'block';
            input.classList.add('success');
            input.classList.remove('error');
        }
    }

    // Update confirm password input section
    document.getElementById('confirm_password').insertAdjacentHTML('afterend', 
        '<div class="validation-message" id="confirmPasswordValidation"></div>');
    document.getElementById('confirm_password').setAttribute('oninput', 'validateConfirmPassword(this)');

    function showConfirmation() {
        // Get form values
        const fname = document.getElementById('fname').value;
        const lname = document.getElementById('lname').value;
        const username = document.getElementById('username').value;
        const email = document.getElementById('email').value;
        const contact = document.getElementById('contact').value;
        const district = document.getElementById('district').value;
        const barangay = document.getElementById('barangay').value;

        // Update confirmation modal
        document.getElementById('confirmName').textContent = `${fname} ${lname}`;
        document.getElementById('confirmUsername').textContent = username;
        document.getElementById('confirmEmail').textContent = email;
        document.getElementById('confirmContact').textContent = `+639${contact}`;
        document.getElementById('confirmAddress').textContent = `${barangay}, ${district}, Davao City`;

        // Show modal
        document.getElementById('confirmationModal').style.display = 'block';
        
        // Prevent page scrolling when modal is open
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('confirmationModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function submitForm() {
        // Submit the form
        document.querySelector('form').submit();
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('confirmationModal');
        if (event.target == modal) {
            closeModal();
        }
    }

    // Close modal on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });
</script>

</body>
</html>