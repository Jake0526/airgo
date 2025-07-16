<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if user needs verification
require_once __DIR__ . '/../config/database.php';
$conn = Database::getConnection();
$check_verification = $conn->prepare("SELECT is_verified FROM user WHERE id = ?");
$check_verification->bind_param("i", $_SESSION['user_id']);
$check_verification->execute();
$result = $check_verification->get_result();

if ($row = $result->fetch_assoc()) {
    if ($row['is_verified'] == 0) {
        $_SESSION['needs_verification'] = true;
        header("Location: verify.php");
        exit();
    }
} else {
    // User not found in database
    session_destroy();
    header("Location: login.php");
    exit();
}
?> 