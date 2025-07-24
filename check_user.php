<?php
// Include the database connection
require_once 'config/database.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers for JSON response
header('Content-Type: application/json');

// Get the database connection
$conn = Database::getConnection();

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Get the field and value from the request
$field = isset($_POST['field']) ? $_POST['field'] : '';
$value = isset($_POST['value']) ? trim($_POST['value']) : '';

if (empty($field) || empty($value)) {
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

// Prepare and execute the query based on the field
$sql = "SELECT id FROM user WHERE $field = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $stmt->store_result();
    
    $exists = $stmt->num_rows > 0;
    
    echo json_encode([
        'exists' => $exists,
        'message' => $exists ? "This $field is already taken" : "$field is available"
    ]);
    
    $stmt->close();
} else {
    echo json_encode(['error' => 'Query preparation failed']);
}

$conn->close();
?> 