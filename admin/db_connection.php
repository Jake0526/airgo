<?php
$host = 'localhost';  // Database host
$username = 'root';   // Database username
$password = '';       // Database password (empty in XAMPP by default)
$database = 'airgo';  // Database name

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>