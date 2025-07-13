<?php
require_once '../config/database.php';
$conn = Database::getConnection();

if ($conn->connect_error) {
    die(json_encode(['error' => 'DB connection failed']));
}

if (!isset($_GET['date'])) {
    echo json_encode(['error' => 'No date provided']);
    exit;
}

$date = $_GET['date'];
$max_per_slot = 8;
$interval = 100; // minutes
$start_time = strtotime("08:00");
$end_time = strtotime("18:00");

$slots = [];
for ($t = $start_time; $t + ($interval * 60) <= $end_time; $t += ($interval * 60)) {
    $slot_time = date("H:i:s", $t);
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM bookings WHERE appointment_date = ? AND appointment_time = ? AND status != 'Cancelled'");
    $stmt->bind_param("ss", $date, $slot_time);
    $stmt->execute();
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

header('Content-Type: application/json');
echo json_encode($slots);
$conn->close();
