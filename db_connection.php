<?php
$servername = "db";
$username = "root";
$password = "root_password";
$database = "airgo"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $database);

// Proper error handling
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
