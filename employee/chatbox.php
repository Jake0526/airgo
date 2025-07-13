<?php
session_start();
if (!isset($_SESSION['employee_logged_in']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';
$conn = Database::getConnection();

$employee_id = $_SESSION['employee_id'];

// Get messages for this technician
$sql = "SELECT * FROM messages WHERE receiver_id = ? OR sender_id = ? ORDER BY timestamp ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $employee_id, $employee_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Technician Chatbox</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        .chatbox { background: #fff; border: 1px solid #ccc; padding: 20px; max-width: 600px; margin: auto; }
        .messages { height: 300px; overflow-y: scroll; border: 1px solid #eee; padding: 10px; margin-bottom: 10px; background: #fafafa; }
        .message { margin-bottom: 10px; }
        .message.you { text-align: right; color: green; }
    </style>
</head>
<body>
    <div class="chatbox">
        <h2>Chat with Customers</h2>
        <div class="messages" id="chatMessages">
            <?php while ($msg = $result->fetch_assoc()): ?>
                <div class="message <?= $msg['sender_id'] == $employee_id ? 'you' : '' ?>">
                    <strong><?= $msg['sender_name'] ?>:</strong> <?= $msg['message'] ?>
                </div>
            <?php endwhile; ?>
        </div>

        <form method="post" action="send_message.php">
            <input type="hidden" name="sender_id" value="<?= $employee_id ?>">
            <input type="hidden" name="sender_name" value="<?= $_SESSION['employee_name'] ?>">
            <input type="text" name="message" placeholder="Type your message..." required style="width: 80%;">
            <button type="submit">Send</button>
        </form>
        <br>
        <a href="technician_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
