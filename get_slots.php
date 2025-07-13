<?php
// Ensure clean output
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/php_errors.log');

// Log access to this file
error_log("get_slots.php accessed with date: " . ($_GET['date'] ?? 'not set'));

header('Content-Type: application/json');

require_once 'config/database.php';
$conn = Database::getConnection();

try {
    if (!isset($_GET['date'])) {
        throw new Exception("No date provided");
    }

    $date = $_GET['date'];
    if (!strtotime($date)) {
        throw new Exception("Invalid date format");
    }

    $max_per_slot = 8;
    $interval = 100; // minutes
    $start_time = strtotime("08:00");
    $end_time = strtotime("18:00");

    $slots = [];
    for ($t = $start_time; $t + ($interval * 60) <= $end_time; $t += ($interval * 60)) {
        $slot_time = date("H:i:s", $t);
        $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM bookings WHERE appointment_date = ? AND appointment_time = ? AND status != 'Cancelled'");
        if (!$stmt) {
            error_log("Database prepare error: " . $conn->error);
            throw new Exception("Database error: " . $conn->error);
        }
        $stmt->bind_param("ss", $date, $slot_time);
        if (!$stmt->execute()) {
            error_log("Query execution failed: " . $stmt->error);
            throw new Exception("Query execution failed: " . $stmt->error);
        }
        $res = $stmt->get_result()->fetch_assoc();
        $count = $res['count'] ?? 0;
        $remaining = $max_per_slot - $count;
        $slots[] = [
            'time' => date("g:i A", $t),
            'remaining' => max(0, $remaining),
            'available' => $remaining > 0
        ];
        $stmt->close();
    }

    if (empty($slots)) {
        error_log("No slots generated for date: $date");
        echo json_encode(['slots' => [], 'message' => 'No slots available']);
    } else {
        echo json_encode(['slots' => $slots, 'message' => 'Success']);
    }

} catch (Exception $e) {
    error_log("Error in get_slots.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage(),
        'slots' => []
    ]);
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
