<?php  
// Include the database connection
require_once 'config/database.php';

// Start the session at the beginning of the file
session_start();

// Enable error reporting and output buffering like in test_email.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();

// Function to output debug messages (same as test_email.php)
function debug_log($message, $type = 'info') {
    $color = 'black';
    switch($type) {
        case 'error':
            $color = '#dc3545';
            break;
        case 'success':
            $color = '#28a745';
            break;
        case 'info':
            $color = '#17a2b8';
            break;
        case 'warning':
            $color = '#ffc107';
            break;
    }
    echo "<div style='font-family: monospace; margin: 5px 0; padding: 10px; background: #f8f9fa; border-left: 4px solid {$color};'>";
    echo "[" . date('Y-m-d H:i:s') . "] ";
    echo htmlspecialchars($message);
    echo "</div>";
    
    // Only flush if buffer exists
    if (ob_get_level() > 0) {
        ob_flush();
        flush();
    }
}

// Check for messages in session
$error_message = "";
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
if (isset($_SESSION['success_message'])) {
    $error_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
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
    
    $houseno = trim($_POST['houseno']);
    $street = trim($_POST['street']);
    $city = trim($_POST['city']);
    $district = trim($_POST['district']);
    $barangay = trim($_POST['barangay']);
    $zipcode = trim($_POST['zipcode']);

    // Validate password strength
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>])[A-Za-z\d!@#$%^&*(),.?":{}|<>]{10,}$/', $password)) {
        $_SESSION['error_message'] = "Error: Password must be minimum 10 characters and include uppercase letters, lowercase letters, numbers, and special characters.";
        header("Location: register.php");
        exit();
    }
    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error_message'] = "Error: Passwords do not match.";
        header("Location: register.php");
        exit();
    }
    else {
        // Get database connection
        $conn = Database::getConnection();
        
        if ($conn) {
            // Check if email or username already exists
            $sql = "SELECT id FROM user WHERE email = ? OR username = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ss", $email, $username);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $_SESSION['error_message'] = "Error: Username or email already exists.";
                    header("Location: register.php");
                    exit();
                } else {
                    // Generate OTP
                    $otp = sprintf("%06d", mt_rand(0, 999999));
                    
                    // Hash the password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Insert into user table with OTP, expiry time and is_verified = 0
                    $sql = "INSERT INTO user (fname, lname, username, password, email, contact, houseno, street, city, district, barangay, zipcode, otp_code, otp_expiry, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    if ($insert_stmt = $conn->prepare($sql)) {
                        // Calculate expiry time (2 minutes from now)
                        $otp_expiry = date('Y-m-d H:i:s', strtotime('+2 minutes'));
                        $is_verified = 0;
                        $insert_stmt->bind_param("ssssssssssssssi", $fname, $lname, $username, $hashed_password, $email, $contact, $houseno, $street, $city, $district, $barangay, $zipcode, $otp, $otp_expiry, $is_verified);

                        if ($insert_stmt->execute()) {
                            // Get the newly inserted user's ID
                            $user_id = $conn->insert_id;
                            
                            // Send verification email
                            require_once 'config/mailer.php';
                            try {
                                $mailer = Mailer::getInstance();
                                $name = $fname . ' ' . $lname;
                                
                                if ($mailer->sendVerificationEmail($email, $name, $otp)) {
                                    // Set session variables
                                    $_SESSION['user_id'] = $user_id;
                                    $_SESSION['username'] = $username;
                                    $_SESSION['fname'] = $fname;
                                    $_SESSION['lname'] = $lname;
                                    $_SESSION['email'] = $email;
                                    $_SESSION['needs_verification'] = true;
                                    
                                    // Redirect to verification page
                                    header("Location: verify.php");
                                    exit();
                                } else {
                                    // Delete the user if email sending fails
                                    $delete_stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
                                    $delete_stmt->bind_param("i", $user_id);
                                    $delete_stmt->execute();
                                    
                                    $_SESSION['error_message'] = "Registration failed: Unable to send verification email. Please check if your email address is correct and try again.";
                                    header("Location: register.php");
                                    exit();
                                }
                            } catch (Exception $e) {
                                // Delete the user if there's an exception
                                $delete_stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
                                $delete_stmt->bind_param("i", $user_id);
                                $delete_stmt->execute();
                                
                                $_SESSION['error_message'] = "Registration failed: " . $e->getMessage();
                                header("Location: register.php");
                                exit();
                            }
                        } else {
                            $_SESSION['error_message'] = "Error: " . $insert_stmt->error;
                            header("Location: register.php");
                            exit();
                        }
                        $insert_stmt->close();
                    } else {
                        $_SESSION['error_message'] = "Error preparing insert statement: " . $conn->error;
                        header("Location: register.php");
                        exit();
                    }
                }
                $stmt->close();
            } else {
                $_SESSION['error_message'] = "Error preparing select statement: " . $conn->error;
                header("Location: register.php");
                exit();
            }
        } else {
            $_SESSION['error_message'] = "Database connection failed.";
            header("Location: register.php");
            exit();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Register - Airgo</title>

<!-- Favicon -->
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='20' fill='%2307353f'/%3E%3Cpath d='M30 50c0-11 9-20 20-20s20 9 20 20-9 20-20 20-20-9-20-20zm35 0c0-8.3-6.7-15-15-15s-15 6.7-15 15 6.7 15 15 15 15-6.7 15-15z' fill='%233cd5ed'/%3E%3Cpath d='M50 60c-5.5 0-10-4.5-10-10s4.5-10 10-10 10 4.5 10 10-4.5 10-10 10z' fill='white'/%3E%3C/svg%3E" />
<link rel="icon" type="image/png" sizes="32x32" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAABKhJREFUWEe9V21sU2UUfu7H3a7dmDAgKgwQPxAkKAYnJsyPGCGK0ZAQFfEHJGpigkT8gYrxB5pI/GD+QBNjNCr+ICYaEzWBRDBRIQQIiCIIKLIvGIPtbrfb7b3XnHPvbVc2trs3bU/S7H7c877Pec95z3veK+AeHOIe4MN/B0CWZVkQhHui3LquX5YkSboXk+u6PqwoCud3HEAkEuH8qqrS0NBQYWxsLDY2NhaNRqNRVVVjmqYpuq4rpHwwGHQHAgG3y+VyezweN/1yu91uQRDcBIr+CoIgCILAf222GfRqgKZpGsdHYDRNm6Xr+llZlr1/CcDQ0BCNjZumxXVdVxVFiWmaFlVVNaooSjQejxPQEUVRYpqmxXRd5+vpuu5yOBwet9vtczgcPofD4SWQBMjpdLodDofL6XS6nE4nARFFUeR7QRAIJIMxAVJVVWPQODNBkiSvLMsDkiTJt23BwMAAjVEURY3H4zFFUaKqqkYJiKqqUU3T6G9UVdW4pmkxAqfrOgMWRdHjdDq9BNDhcPgcDofX5XL5XS6Xz+12+10ul9fpdHoJqMvlcjudTjcBJVOYQOm6rsXjcS0Wi8UjkUiU/kYikWgsFovF4/F4LBaLxePxeDQaZUDWFPT39xMAVVXVWCwWVRQlqihKhEAqihIxmUxRXdf52WQyEQCPKIo+URR9BNDpdPqtVqvfYrH4rFar3263++12u89ms3ltNpvXZrN5rFYrAXWRDgioYU7DnMSUoihsXkVRFPJEJBKJRKPR8O0a6O3tNQNgJlRVNayqaphAEgOSJIUlSQobDLgoK0RR9JEGLBaL32q1+m02m5/AEli73e6z2+0+q9Xqc7lcXrvd7rFarW6r1eq2WCwExk2ZQCDJ75IkhXVdD+u6HtZ1PazrehiYEtZ1XTYykCRJYdu27du3j8YbIAJGbIQJpKqqYUmSwgTSYMFNLJAGHA6H32Kx+G02m5/AElgCabfbfTabzWu1Wj0ul8tjs9k8VqvVbbFY3E6n0+10Ot0EkjQgSVKY7qmqGqbxkiSFVVUNU8FTVTVEz7IsywkNbN682QyAM0BV1RCZkEBKkhTSNC0kSVKIGHA4HD6n0+mzWCw+AksmtNlsXpvN5rXb7R6Xy+UhoFar1W21Wl0EkoAaZg1rmhYi89F4TdNCmqaFVFUNkXkTNZHNZvts27aNxhuNCIEI0T2ZkN4bLLhEUfQaueAjDdhsNi+BJD0QUKfT6SYdEFDKAEmSQoqihOjeAJG4/gQ7W7duzQXwP9cBnNexpjhBAAAAAElFTkSuQmCC" />
<link rel="icon" type="image/png" sizes="16x16" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAlpJREFUOE+Nk0tIVFEYx//n3jtz544z4zg6OuqoqKVp9LDUMAqKFtGiWtSiWkQkEUEtahERRBQVLYJoUW2iRdCiKKJFD4JqE0REDxQfw1hq5DiOztz5mHvPPeeMjlr2wVl8fN/v+z++c87HsB8f24d9+H8A0un0IcbYUdd1PSLyiIg8z3MJyPU8zyEix/M8h4hcz/McInI9z7WJyLJt27Isy7IsS7YsS7IsS7YsW2aMWbZtW5ZlWaZpEsdxEgghDCGEwRjbBpBKpQ4yxo66rusRkedRwQQgEpHreZ5DRK7neQ4RuZ7nOZ7n2a7rWo7jWLZtW47jyI7jSLZty47jSI7jSJZlSYwxadOvCSGMbQDJZPIQY+yI67qe53ke/QsgItf1XIeIHNd1Hdd1Hc/zbNu2Ldu2ZMexJMexJNu2JcaYtAXAGDM2AWKx2GHG2BEiIiLPIyLPdd0CQK7ruq7ruq5t25Zt25LjOLLjOJLjOJLjOBJjTNoUlzFmbAJEo9EjjLHDRERE5BERua7rFgByXdd1Xde1bdu2bNuWHceWHceWHMeSHMeSbNuWGGPSFgBjzNgEiEQihxljh4iIiMgjInJd1y0A5Lqu67qu69i2bdm2LTmOLTmOLTmOJTmOJdm2LTHGpC1xGWPGJkA4HD7CGDtIRJ7neUREnut6BYBc13Ud27Yt27Ylx7Elx7Ekx7Ekx7Ek27YlxpgkSRJjjBkbAMFg8BBj7IDruh4ReUREnut6BYBc13Ud27Yt27Ylx7Elx7Ekx7Ek27YlxpgkSRJjjBl/AQgEAgcZY/uJyPsJ8AOvgZzXMm3oPQAAAABJRU5ErkJggg==" />

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

    /* Add error modal styles */
    .error-modal .modal-content {
        border-left: 4px solid #e44;
    }

    .error-modal h3 {
        color: #e44;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .error-modal h3::before {
        content: '⚠️';
    }

    .error-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .error-list li {
        padding: 0.5rem 0;
        color: #e44;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .error-list li::before {
        content: '•';
        color: #e44;
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
        background: #fff2f2;
        color: #e44;
        padding: 0.8rem;
        border-radius: 8px;
        width: 100%;
    }

    .validation-message.error {
        display: block;
        background: #fff2f2;
        color: #e44;
    }

    .validation-message.success {
        display: block;
        background: #f0fff0;
        color: #0a0;
    }

    .validation-message.checking {
        display: block;
        background: #f0f8ff;
        color: #0066cc;
    }

    input.error {
        border-color: #e44;
        margin-bottom: 0;  /* Remove bottom margin when there's an error */
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

    .password-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;  /* Add space below input wrapper */
    }

    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
        transition: color 0.3s ease;
    }

    .toggle-password:hover {
        color: var(--primary-color);
    }

    .toggle-password svg {
        width: 20px;
        height: 20px;
    }

    .toggle-password .eye-off-icon {
        display: none;
    }

    .toggle-password.showing .eye-icon {
        display: none;
    }

    .toggle-password.showing .eye-off-icon {
        display: block;
    }

    .password-requirements {
        margin-top: 1rem;
        font-size: 0.85rem;
        color: #666;
        width: 100%;
    }

    .requirement {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.3rem;
        color: #666;
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

                    <input type="hidden" id="username" name="username">

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="Enter your email">
                    </div>

                                        <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-input-wrapper">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   oninput="validatePassword(this)"
                                   placeholder="Create a password">
                            <button type="button" class="toggle-password" onclick="togglePassword('password')">
                                <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg class="eye-off-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                        <div class="error-message" id="passwordValidation" style="display: none; background: #fff2f2; color: #dc3545; padding: 0.8rem; border-radius: 8px; margin-top: 0.5rem; font-size: 0.9rem;">
                            Password is required
                        </div>
                        <div class="password-requirements">
                            <div class="requirement" id="req-length">Minimum 10 characters</div>
                            <div class="requirement" id="req-uppercase">Must contain at least one uppercase letter</div>
                            <div class="requirement" id="req-lowercase">Must contain at least one lowercase letter</div>
                            <div class="requirement" id="req-number">Must contain at least one number</div>
                            <div class="requirement" id="req-special">Must contain at least one special character (!@#$%^&(),.?":{}|<>)</div>
                        </div>
                        <div class="password-strength">
                            <div class="strength-meter" id="strength-meter"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <div class="password-input-wrapper">
                            <input type="password" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   required
                               oninput="validateConfirmPassword(this)"
                               placeholder="Confirm your password">
                            <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                                <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg class="eye-off-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                        <div class="error-message" id="confirmPasswordValidation" style="display: none; background: #fff2f2; color: #dc3545; padding: 0.8rem; border-radius: 8px; margin-top: 0.5rem; font-size: 0.9rem;">
                            Please confirm your password
                        </div>
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
                        <div class="error-message" id="contactValidation" style="display: none; background: #fff2f2; color: #dc3545; padding: 0.8rem; border-radius: 8px; margin-top: 0.5rem; font-size: 0.9rem;">
                            Contact number is required
                        </div>
                        <small class="contact-hint">Format: +639 followed by 9 digits</small>
                    </div>
                </div>

                <div class="form-col">
                    <div class="address-section">
                        <h3>Address Information</h3>
                        
                        <div class="form-group">
                            <label for="houseno">House Number</label>
                            <input type="text" id="houseno" name="houseno" required placeholder="Enter house number">
                        </div>

                        <div class="form-group">
                            <label for="street">Street</label>
                            <input type="text" id="street" name="street" required placeholder="Enter street name">
                        </div>
                        
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

<!-- Remove error modal HTML -->
<div id="errorModal" class="modal error-modal">
    <div class="modal-content">
        <h3>Registration Error</h3>
        <p>Please fix the following errors:</p>
        <ul class="error-list" id="errorList">
        </ul>
        <div class="modal-actions">
            <button type="button" class="btn-primary" onclick="closeErrorModal()">Fix Errors</button>
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



    function validatePassword(input) {
        const value = input.value;
        const requirements = {
            length: value.length >= 10,
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
            input.classList.remove('error', 'success');
            return false;
        } else if (metRequirements <= 2) {
            strengthMeter.className = 'strength-meter weak';
        } else if (metRequirements <= 4) {
            strengthMeter.className = 'strength-meter medium';
        } else {
            strengthMeter.className = 'strength-meter strong';
        }

        // Check if all requirements are met
        const isValid = Object.values(requirements).every(Boolean);
        
        if (isValid) {
            showFieldSuccess(input, 'Password meets all requirements');
        } else {
            showFieldError(input, 'Please meet all password requirements');
        }

        // Validate confirm password if it has a value
        const confirmInput = document.getElementById('confirm_password');
        if (confirmInput.value) {
            validateConfirmPassword(confirmInput);
        }

        return isValid;
    }

    function validateConfirmPassword(input) {
        const password = document.getElementById('password').value;

        if (input.value.length === 0) {
            input.classList.remove('error', 'success');
            const validationMessage = document.getElementById('confirmPasswordValidation');
            if (validationMessage) {
                validationMessage.style.display = 'none';
            }
            return false;
        } else if (input.value !== password) {
            showFieldError(input, 'Passwords do not match');
            return false;
        } else {
            showFieldSuccess(input, 'Passwords match');
            return true;
        }
    }



    function showConfirmation() {
        // Get form values
        const fname = document.getElementById('fname').value;
        const lname = document.getElementById('lname').value;
        const username = document.getElementById('username').value;
        const email = document.getElementById('email').value;
        const contact = document.getElementById('contact').value;
        const houseno = document.getElementById('houseno').value;
        const street = document.getElementById('street').value;
        const district = document.getElementById('district').value;
        const barangay = document.getElementById('barangay').value;

        // Update confirmation modal
        document.getElementById('confirmName').textContent = `${fname} ${lname}`;
        document.getElementById('confirmEmail').textContent = email;
        document.getElementById('confirmContact').textContent = `+639${contact}`;
        document.getElementById('confirmAddress').textContent = `${houseno} ${street}, ${barangay}, ${district}, Davao City`;

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
        const form = document.querySelector('form');
        form.submit();
        
        // Disable the submit button to prevent double submission
        document.querySelector('.btn-primary').disabled = true;
        document.querySelector('.btn-secondary').disabled = true;
    }

    // Remove error modal related functions
    function showErrorModal(errors) {
        const errorList = document.getElementById('errorList');
        errorList.innerHTML = '';
        
        errors.forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            errorList.appendChild(li);
        });

        document.getElementById('errorModal').style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeErrorModal() {
        document.getElementById('errorModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    async function checkFieldAvailability(field, value) {
        try {
            const response = await fetch('check_user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `field=${field}&value=${encodeURIComponent(value)}`
            });
            const data = await response.json();
            return !data.exists; // Return true if available (not exists)
        } catch (error) {
            console.error(`Error checking ${field} availability:`, error);
            return false;
        }
    }

    function showFieldError(field, message) {
        field.classList.add('error');
        field.classList.remove('success');
        
        // For password field, use the specific password validation element
        if (field.id === 'password') {
            const validationMessage = document.getElementById('passwordValidation');
            if (validationMessage) {
                validationMessage.textContent = message;
                validationMessage.style.display = 'block';
            }
            return;
        }
        
        // For confirm password field, use the specific confirm password validation element
        if (field.id === 'confirm_password') {
            const validationMessage = document.getElementById('confirmPasswordValidation');
            if (validationMessage) {
                validationMessage.textContent = message;
                validationMessage.style.display = 'block';
            }
            return;
        }
        
        // For contact field, use the specific contact validation element
        if (field.id === 'contact') {
            const validationMessage = document.getElementById('contactValidation');
            if (validationMessage) {
                validationMessage.textContent = message;
                validationMessage.style.display = 'block';
            }
            return;
        }
        
        // For other fields, get or create validation message element
        let validationMessage = field.parentElement.querySelector('.validation-message');
        if (!validationMessage) {
            validationMessage = document.createElement('div');
            validationMessage.className = 'validation-message';
            validationMessage.style.background = '#fff2f2';
            validationMessage.style.color = '#dc3545';
            validationMessage.style.padding = '0.8rem';
            validationMessage.style.borderRadius = '8px';
            validationMessage.style.marginTop = '0.5rem';
            validationMessage.style.fontSize = '0.9rem';
            field.parentElement.appendChild(validationMessage);
        }
        
        validationMessage.textContent = message;
        validationMessage.style.display = 'block';
    }

    function showFieldSuccess(field, message) {
        field.classList.add('success');
        field.classList.remove('error');
        
        // For password field, hide the specific password validation element
        if (field.id === 'password') {
            const validationMessage = document.getElementById('passwordValidation');
            if (validationMessage) {
                validationMessage.style.display = 'none';
            }
            return;
        }
        
        // For confirm password field, hide the specific confirm password validation element
        if (field.id === 'confirm_password') {
            const validationMessage = document.getElementById('confirmPasswordValidation');
            if (validationMessage) {
                validationMessage.style.display = 'none';
            }
            return;
        }
        
        // For contact field, hide the specific contact validation element
        if (field.id === 'contact') {
            const validationMessage = document.getElementById('contactValidation');
            if (validationMessage) {
                validationMessage.style.display = 'none';
            }
            return;
        }
        
        // For other fields, hide validation message
        let validationMessage = field.parentElement.querySelector('.validation-message');
        if (validationMessage) {
            validationMessage.style.display = 'none';
        }
    }

    // Update validateForm function to handle password differently
    async function validateForm() {
        let hasErrors = false;
        
        // Reset all validation messages except for password and email checking states
        document.querySelectorAll('.validation-message').forEach(msg => {
            if (!msg.classList.contains('checking') && 
                !msg.parentElement.querySelector('input[type="password"]')) {
                msg.style.display = 'none';
                msg.classList.remove('error', 'success');
            }
        });
        
        // Reset all input states except for password and email checking states
        document.querySelectorAll('input, select').forEach(input => {
            if (!input.parentElement.querySelector('.validation-message.checking') && 
                input.type !== 'password') {
                input.classList.remove('error', 'success');
            }
        });

        // Validate First Name
        const fname = document.getElementById('fname');
        if (!fname.value.trim()) {
            showFieldError(fname, 'First Name is required');
            hasErrors = true;
        } else if (!/^[A-Za-z ]{3,}$/.test(fname.value.trim())) {
            showFieldError(fname, 'First Name must contain only letters and be at least 3 characters long');
            hasErrors = true;
        }

        // Validate Last Name
        const lname = document.getElementById('lname');
        if (!lname.value.trim()) {
            showFieldError(lname, 'Last Name is required');
            hasErrors = true;
        } else if (!/^[A-Za-z ]{3,}$/.test(lname.value.trim())) {
            showFieldError(lname, 'Last Name must contain only letters and be at least 3 characters long');
            hasErrors = true;
        }

        // Validate Email - only check if empty, let checkExistence handle the rest
        const email = document.getElementById('email');
        if (!email.value.trim()) {
            showFieldError(email, 'Email is required');
            hasErrors = true;
        } else {
            // Check if there's an existing error
            const emailValidation = email.parentElement.querySelector('.validation-message');
            if (emailValidation && emailValidation.classList.contains('error')) {
                hasErrors = true;
            }
        }

        // Validate Password - only check if empty, let validatePassword handle the rest
        const password = document.getElementById('password');
        if (!password.value) {
            showFieldError(password, 'Password is required');
            hasErrors = true;
        } else {
            // Check if password is valid
            if (!validatePassword(password)) {
                hasErrors = true;
            }
        }

        // Validate Confirm Password - only check if empty, let validateConfirmPassword handle the rest
        const confirmPassword = document.getElementById('confirm_password');
        if (!confirmPassword.value) {
            showFieldError(confirmPassword, 'Please confirm your password');
            hasErrors = true;
        } else {
            // Check if passwords match
            if (!validateConfirmPassword(confirmPassword)) {
                hasErrors = true;
            }
        }

        // Validate Contact Number
        const contact = document.getElementById('contact');
        if (!contact.value) {
            showFieldError(contact, 'Contact number is required');
            hasErrors = true;
        } else if (contact.value.length !== 9 || !/^\d{9}$/.test(contact.value)) {
            showFieldError(contact, 'Invalid contact number format');
            hasErrors = true;
        }

        // Validate House Number
        const houseno = document.getElementById('houseno');
        if (!houseno.value.trim()) {
            showFieldError(houseno, 'House number is required');
            hasErrors = true;
        }

        // Validate Street
        const street = document.getElementById('street');
        if (!street.value.trim()) {
            showFieldError(street, 'Street is required');
            hasErrors = true;
        }

        // Validate District
        const district = document.getElementById('district');
        if (!district.value) {
            showFieldError(district, 'Please select a district');
            hasErrors = true;
        }

        // Validate Barangay
        const barangay = document.getElementById('barangay');
        if (!barangay.value) {
            showFieldError(barangay, 'Please select a barangay');
            hasErrors = true;
        }

        // If no errors, show confirmation modal
        if (!hasErrors) {
        showConfirmation();
        }
        
        return !hasErrors;
    }

    // Update form submission
    document.querySelector('form').onsubmit = async function(e) {
        e.preventDefault();
        if (await validateForm()) {
            // Form is valid, confirmation modal will be shown
            return false;
        }
        return false;
    };

    // Close error modal when clicking outside
    window.onclick = function(event) {
        const confirmationModal = document.getElementById('confirmationModal');
        if (event.target == confirmationModal) {
            closeModal();
        }
    }

    // Close modals on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    // Function to toggle password visibility
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const button = input.parentElement.querySelector('.toggle-password');
        
        if (input.type === 'password') {
            input.type = 'text';
            button.classList.add('showing');
        } else {
            input.type = 'password';
            button.classList.remove('showing');
        }
    }

    // Debounce function to limit API calls
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Function to check if username/email exists
    function checkExistence(field, value) {
        const input = document.getElementById(field);
        const validationMessage = document.getElementById(field + 'Validation');

        // Don't check if the field is empty
        if (!value.trim()) {
            input.classList.remove('error', 'success');
            if (validationMessage) {
            validationMessage.style.display = 'none';
            }
            return;
        }

        // For email, validate format first
        if (field === 'email' && !value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            showFieldError(input, 'Invalid email format');
            return;
        }

        // Show loading state
        showFieldError(input, 'Checking...');

        fetch('check_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `field=${field}&value=${encodeURIComponent(value)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                showFieldError(input, 'Error checking availability');
            } else {
                if (data.exists) {
                    showFieldError(input, data.message);
                } else {
                    showFieldSuccess(input, data.message);
                }
            }
        })
        .catch(error => {
            showFieldError(input, 'Error checking availability');
        });
    }

    // Create debounced version of the check function for email
    const debouncedCheckEmail = debounce((value) => checkExistence('email', value), 500);

    // Add event listener for email field
    document.getElementById('email').addEventListener('blur', function() {
        debouncedCheckEmail(this.value);
    });

    // Set username value from email before form submission
    document.querySelector('form').addEventListener('submit', function() {
        const email = document.getElementById('email').value;
        const username = email.split('@')[0]; // Use part before @ as username
        document.getElementById('username').value = username;
    });

    // Add event listeners for real-time validation
    document.addEventListener('DOMContentLoaded', function() {
        // Real-time validation for first name
        const fnameInput = document.getElementById('fname');
        fnameInput.addEventListener('input', function() {
            validateName(this, 'First Name');
        });

        // Real-time validation for last name
        const lnameInput = document.getElementById('lname');
        lnameInput.addEventListener('input', function() {
            validateName(this, 'Last Name');
        });

        // Real-time validation for email
        const emailInput = document.getElementById('email');
        emailInput.addEventListener('input', debounce(function() {
            if (this.value.trim()) {
                checkExistence('email', this.value);
            } else {
                showFieldError(this, 'Email is required');
            }
        }, 500));

        // Real-time validation for password
        const passwordInput = document.getElementById('password');
        passwordInput.addEventListener('input', function() {
            validatePassword(this);
        });

        // Real-time validation for confirm password
        const confirmPasswordInput = document.getElementById('confirm_password');
        confirmPasswordInput.addEventListener('input', function() {
            validateConfirmPassword(this);
        });

        // Real-time validation for contact number
        const contactInput = document.getElementById('contact');
        contactInput.addEventListener('input', function() {
            validateContactNumber(this);
        });

        // Real-time validation for house number
        const housenoInput = document.getElementById('houseno');
        housenoInput.addEventListener('input', function() {
            validateText(this, 'House number');
        });

        // Real-time validation for street
        const streetInput = document.getElementById('street');
        streetInput.addEventListener('input', function() {
            validateText(this, 'Street');
        });

        // Real-time validation for district
        const districtSelect = document.getElementById('district');
        districtSelect.addEventListener('change', function() {
            validateSelect(this, 'District');
        });

        // Real-time validation for barangay
        const barangaySelect = document.getElementById('barangay');
        barangaySelect.addEventListener('change', function() {
            validateSelect(this, 'Barangay');
        });
    });

    function validateName(input, fieldName) {
        if (!input.value.trim()) {
            showFieldError(input, `${fieldName} is required`);
            return false;
        } else if (!/^[A-Za-z ]{3,}$/.test(input.value.trim())) {
            showFieldError(input, `${fieldName} must contain only letters and be at least 3 characters long`);
            return false;
        } else {
            showFieldSuccess(input, `${fieldName} is valid`);
            return true;
        }
    }

    function validateSelect(select, fieldName) {
        if (!select.value) {
            showFieldError(select, `Please select a ${fieldName}`);
            return false;
        } else {
            showFieldSuccess(select, `${fieldName} selected`);
            return true;
        }
    }

    function validateText(input, fieldName) {
        if (!input.value.trim()) {
            showFieldError(input, `${fieldName} is required`);
            return false;
        } else {
            showFieldSuccess(input, `${fieldName} is valid`);
            return true;
        }
    }

    // Update the district change handler to include validation
    document.getElementById('district').addEventListener('change', function() {
        loadBarangays();
        validateSelect(this, 'District');
        // Reset and validate barangay when district changes
        const barangaySelect = document.getElementById('barangay');
        barangaySelect.value = '';
        validateSelect(barangaySelect, 'Barangay');
    });

    // Update validateContactNumber function to return boolean
    function validateContactNumber(input) {
        const wrapper = input.closest('.contact-input-wrapper');
        let value = input.value.replace(/\D/g, '');
        
        if (value.length > 9) {
            value = value.slice(0, 9);
        }
        
        input.value = value;

        if (value.length === 0) {
            showFieldError(input, 'Contact number is required');
            wrapper.classList.add('error');
            wrapper.classList.remove('success');
            return false;
        } else if (value.length < 9) {
            showFieldError(input, `Please enter ${9 - value.length} more digit${9 - value.length > 1 ? 's' : ''}`);
            wrapper.classList.add('error');
            wrapper.classList.remove('success');
            return false;
        } else if (!/^\d{9}$/.test(value)) {
            showFieldError(input, 'Please enter numbers only');
            wrapper.classList.add('error');
            wrapper.classList.remove('success');
            return false;
        } else {
            showFieldSuccess(input, 'Valid contact number');
            wrapper.classList.add('success');
            wrapper.classList.remove('error');
            return true;
        }
    }

    // Add blur event listeners for all fields to trigger validation when focus is lost
    document.addEventListener('DOMContentLoaded', function() {
        const fields = [
            { id: 'fname', validate: (input) => validateName(input, 'First Name') },
            { id: 'lname', validate: (input) => validateName(input, 'Last Name') },
            { id: 'email', validate: (input) => input.value.trim() && checkExistence('email', input.value) },
            { id: 'password', validate: validatePassword },
            { id: 'confirm_password', validate: validateConfirmPassword },
            { id: 'contact', validate: validateContactNumber },
            { id: 'houseno', validate: (input) => validateText(input, 'House number') },
            { id: 'street', validate: (input) => validateText(input, 'Street') },
            { id: 'district', validate: (input) => validateSelect(input, 'District') },
            { id: 'barangay', validate: (input) => validateSelect(input, 'Barangay') }
        ];

        fields.forEach(field => {
            const element = document.getElementById(field.id);
            if (element) {
                element.addEventListener('blur', function() {
                    field.validate(this);
                });
            }
        });
    });
</script>

</body>
</html>