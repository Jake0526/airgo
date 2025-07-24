<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$calendar_events = [];
$date_counts = [];

try {
    $conn = Database::getConnection();
    
    // Set timezone to Asia/Manila
    date_default_timezone_set('Asia/Manila');
    
    // Get all bookings for slot availability
    $all_result = $conn->query("SELECT appointment_date FROM bookings WHERE status != 'Cancelled'");
    while ($row = $all_result->fetch_assoc()) {
        $date = $row['appointment_date'];
        if (!isset($date_counts[$date])) $date_counts[$date] = 0;
        $date_counts[$date]++;
    }

    // Get user's bookings
    $user_stmt = $conn->prepare("
        SELECT appointment_date, appointment_time, service, status 
        FROM bookings 
        WHERE user_id = ? 
        AND status != 'Cancelled'
        AND appointment_date >= CURDATE()
    ");
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();

    // Add user's bookings first
    while ($row = $user_result->fetch_assoc()) {
        // Create a DateTime object for the appointment date with Asia/Manila timezone
        $appointmentDate = new DateTime($row['appointment_date'], new DateTimeZone('Asia/Manila'));
        $appointmentDate->setTime(0, 0, 0); // Set time to midnight

        $calendar_events[] = [
            "title" => $row['status'] . ' - ' . $row['service'],
            "start" => $appointmentDate->format('Y-m-d'), // Use only the date part
            "display" => "block",
            "backgroundColor" => "#0ea5e9",
            "textColor" => "#ffffff",
            "classNames" => ["user-booking"],
            "allDay" => true,
            "extendedProps" => [
                "type" => "user_booking",
                "time" => $row['appointment_time']
            ]
        ];
    }
    $user_stmt->close();

    // Then add availability background events
    $max_slots_per_day = 46;
    $today = new DateTime('now', new DateTimeZone('Asia/Manila'));
    $today->setTime(0, 0, 0);
    $range_start = strtotime('-1 month');
    $range_end = strtotime('+3 months');

    for ($i = $range_start; $i <= $range_end; $i += 86400) {
        $date = date('Y-m-d', $i);
        $booked = $date_counts[$date] ?? 0;
        $remaining = $max_slots_per_day - $booked;

        if ($date <= $today->format('Y-m-d')) {
            $color = "#e5e7eb"; // Gray color for past dates and today
            $title = "";
        } elseif ($remaining <= 0) {
            $color = "#ff6b6b";
            $title = "";
        } else {
            $color = "#48c78e";
            $title = "";
        }

        $calendar_events[] = [
            "title" => $title,
            "start" => $date,
            "display" => "background",
            "color" => $color,
            "classNames" => ["availability-indicator"],
            "extendedProps" => [
                "type" => "availability"
            ]
        ];
    }

    echo json_encode($calendar_events);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
} 