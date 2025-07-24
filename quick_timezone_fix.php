<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: register.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = Database::getConnection();

echo "<h2>🚀 Quick Timezone Fix</h2>";

// Generate a fresh OTP with proper timezone handling
$otp = sprintf("%06d", mt_rand(0, 999999));

// Method 1: Use PHP timezone (Asia/Manila)
$expiry_method1 = date('Y-m-d H:i:s', strtotime('+2 minutes'));

// Method 2: Use UTC and convert
$utc_expiry = gmdate('Y-m-d H:i:s', time() + 120); // 2 minutes in UTC
$local_expiry = date('Y-m-d H:i:s', strtotime($utc_expiry . ' UTC'));

// Method 3: Use MySQL NOW() function
$conn->query("UPDATE user SET otp_code = '$otp', otp_expiry = DATE_ADD(NOW(), INTERVAL 2 MINUTE) WHERE id = $user_id");

// Get the result
$result = $conn->query("SELECT otp_expiry, NOW() as current_time FROM user WHERE id = $user_id");
$data = $result->fetch_assoc();

echo "<h3>Timezone Fix Applied!</h3>";
echo "<p><strong>New OTP:</strong> $otp</p>";
echo "<p><strong>MySQL Current Time:</strong> " . $data['current_time'] . "</p>";
echo "<p><strong>MySQL Expiry Time:</strong> " . $data['otp_expiry'] . "</p>";

// Calculate if it's working
$mysql_expiry_timestamp = strtotime($data['otp_expiry']);
$current_timestamp = time();
$time_diff = $mysql_expiry_timestamp - $current_timestamp;

if ($time_diff > 0) {
    $minutes = floor($time_diff / 60);
    $seconds = $time_diff % 60;
    echo "<p style='color: green;'><strong>✅ Success!</strong> Timer should show: {$minutes}:" . sprintf('%02d', $seconds) . "</p>";
} else {
    echo "<p style='color: red;'><strong>❌ Still broken!</strong> Time difference: $time_diff seconds</p>";
}

echo "<script>
console.log('Quick Fix Test:');
console.log('MySQL Expiry:', '" . $data['otp_expiry'] . "');

// Test the fixed parsing
const expiryStr = '" . $data['otp_expiry'] . "';
const expiryDate1 = new Date(expiryStr).getTime();
const expiryDate2 = new Date(expiryStr.replace(' ', 'T')).getTime();
const expiryDate3 = new Date(expiryStr.replace(' ', 'T') + '+08:00').getTime();
const now = new Date().getTime();

console.log('Method 1 (direct):', Math.floor((expiryDate1 - now) / 1000), 'seconds');
console.log('Method 2 (ISO format):', Math.floor((expiryDate2 - now) / 1000), 'seconds');
console.log('Method 3 (with timezone):', Math.floor((expiryDate3 - now) / 1000), 'seconds');

const bestMethod = Math.floor((expiryDate3 - now) / 1000);
if (bestMethod > 0) {
    const mins = Math.floor(bestMethod / 60);
    const secs = bestMethod % 60;
    document.write('<p style=\"color: green; font-size: 1.2em;\"><strong>🎉 JavaScript Test: ' + mins + ':' + (secs < 10 ? '0' : '') + secs + '</strong></p>');
} else {
    document.write('<p style=\"color: red;\">JavaScript still shows expired: ' + bestMethod + 's</p>');
}
</script>";

echo "<p><a href='verify.php' style='background: #07353f; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>→ Test Verification Page</a></p>";
?> 