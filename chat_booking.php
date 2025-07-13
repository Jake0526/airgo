<?php
session_start();
require_once 'config/database.php';
$conn = Database::getConnection();

// Get booking ID
$booking_id = intval($_GET['booking_id'] ?? 0);

// Determine role and user ID
$role = '';
$user_id = 0;

if (isset($_SESSION['admin_logged_in'])) {
    $role = 'admin';
    $user_id = $_SESSION['admin_id'];
} elseif (isset($_SESSION['employee_logged_in'])) {
    $role = 'technician';
    $user_id = $_SESSION['employee_id'];
} elseif (isset($_SESSION['customer_logged_in'])) {
    $role = 'customer';
    $user_id = $_SESSION['customer_id'];
} else {
    header("Location: login.php");
    exit();
}

// Save message if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $msg = trim($_POST['message']);
    $stmt = $conn->prepare("INSERT INTO booking_chat_messages (booking_id, sender_role, sender_id, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $booking_id, $role, $user_id, $msg);
    $stmt->execute();
    $stmt->close();
}

// Get all messages
$stmt = $conn->prepare("SELECT sender_role, message, created_at FROM booking_chat_messages WHERE booking_id = ? ORDER BY created_at ASC");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat for Booking #<?= $booking_id ?></title>
    <style>
        body { font-family: Arial; background: #f5f5f5; padding: 20px; }
        .chat-box { max-width: 700px; margin: auto; background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 0 8px rgba(0,0,0,0.1); }
        .msg { padding: 10px; margin: 8px 0; border-radius: 6px; max-width: 65%; }
        .customer { background: #ffeeba; align-self: flex-start; }
        .technician { background: #d4edda; align-self: flex-end; }
        .admin { background: #cce5ff; align-self: center; }
        .chat-log { display: flex; flex-direction: column; gap: 5px; }
        .chat-form textarea { width: 100%; height: 60px; resize: none; margin-top: 10px; }
        .chat-form button { margin-top: 5px; padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 6px; }
    </style>
</head>
<body>
    <div class="chat-box">
        <h3>Chat – Booking #<?= $booking_id ?></h3>

        <div class="chat-log">
            <?php foreach ($messages as $msg): ?>
                <div class="msg <?= $msg['sender_role'] ?>">
                    <strong><?= ucfirst($msg['sender_role']) ?>:</strong> <?= htmlspecialchars($msg['message']) ?><br>
                    <small><?= $msg['created_at'] ?></small>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="POST" class="chat-form">
            <textarea name="message" placeholder="Type a message..." required></textarea>
            <button type="submit">Send</button>
        </form>
    </div>
</body>
</html>
