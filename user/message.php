<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';
$conn = Database::getConnection();

$user_id = $_SESSION['user_id'];

// Fetch the assigned technician
$stmt = $conn->prepare("SELECT e.id AS technician_id, e.name AS technician_name 
                        FROM bookings b 
                        INNER JOIN employees e ON b.employee_id = e.id 
                        WHERE b.user_id = ? AND b.status = 'Approved' 
                        LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$technician = $result->fetch_assoc();
$stmt->close();

if (!$technician) {
    echo "No approved booking with a technician found.";
    exit();
}

// Handle new message
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['message']) && !empty(trim($_POST['message']))) {
        $message = trim($_POST['message']);
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $technician['technician_id'], $message);
        $stmt->execute();
        $stmt->close();
    }

    // Edit message
    if (isset($_POST['edit_id']) && isset($_POST['edit_message'])) {
        $edit_id = $_POST['edit_id'];
        $edit_message = trim($_POST['edit_message']);
        $stmt = $conn->prepare("UPDATE messages SET message = ? WHERE id = ? AND sender_id = ?");
        $stmt->bind_param("sii", $edit_message, $edit_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    // Delete message
    if (isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];
        $stmt = $conn->prepare("DELETE FROM messages WHERE id = ? AND sender_id = ?");
        $stmt->bind_param("ii", $delete_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: message.php");
    exit();
}

// Fetch messages
$stmt = $conn->prepare("SELECT m.*, 
    CASE WHEN m.sender_id = ? THEN 'user' ELSE 'tech' END AS sender_type 
    FROM messages m 
    WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) 
    ORDER BY m.timestamp ASC");
$stmt->bind_param("iiiii", $user_id, $user_id, $technician['technician_id'], $technician['technician_id'], $user_id);
$stmt->execute();
$messages = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($technician['technician_name']) ?></title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #667eea, #764ba2);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        #chat-container {
            width: 400px;
            background: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
        }

        #chat-box {
            height: 350px;
            overflow-y: auto;
            border: none;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .user-message, .bot-message {
            padding: 10px;
            border-radius: 10px;
            margin: 8px 0;
            max-width: 75%;
            word-wrap: break-word;
            position: relative;
        }

        .user-message {
            background: #B6B6B6;
            color: white;
            align-self: flex-end;
            text-align: right;
        }

        .bot-message {
            background: #8D8D8D;
            color: white;
            align-self: flex-start;
            text-align: left;
        }

        .message-time {
            display: block;
            font-size: 10px;
            margin-top: 5px;
            color: #ddd;
        }

        .message-actions {
            display: none;
            position: absolute;
            bottom: -20px;
            right: 5px;
            font-size: 12px;
        }

        .user-message:hover .message-actions {
            display: block;
        }

        .input-container {
            display: flex;
            margin-top: 15px;
        }

        #user-input {
            flex: 1;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 20px;
            outline: none;
            font-size: 16px;
        }

        button {
            padding: 12px 20px;
            margin-left: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #0056b3;
        }

        .back {
            text-align: center;
            margin-top: 10px;
        }
    </style>

    <script>
        function editMessage(id, currentMessage) {
            const newMessage = prompt("Edit your message:", currentMessage);
            if (newMessage !== null) {
                const form = document.createElement("form");
                form.method = "POST";
                form.innerHTML = `
                    <input type="hidden" name="edit_id" value="${id}">
                    <input type="hidden" name="edit_message" value="${newMessage}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteMessage(id) {
            if (confirm("Are you sure you want to delete this message?")) {
                const form = document.createElement("form");
                form.method = "POST";
                form.innerHTML = `<input type="hidden" name="delete_id" value="${id}">`;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</head>
<body>

<div id="chat-container">
    <h3 style="text-align:center; margin-bottom: 10px;"><?= htmlspecialchars($technician['technician_name']) ?></h3>

    <div id="chat-box">
        <?php while ($msg = $messages->fetch_assoc()):
            $formatted_time = date("F-d-Y h:i A", strtotime($msg['timestamp']));
        ?>
            <div class="<?= $msg['sender_type'] === 'user' ? 'user-message' : 'bot-message' ?>">
                <?= htmlspecialchars($msg['message']) ?>
                <span class="message-time"><?= $formatted_time ?></span>

                <?php if ($msg['sender_type'] === 'user'): ?>
                    <span class="message-actions">
                        <a href="javascript:void(0)" onclick="editMessage(<?= $msg['id'] ?>, <?= json_encode($msg['message']) ?>)">✏</a>
                        <a href="javascript:void(0)" onclick="deleteMessage(<?= $msg['id'] ?>)">🗑</a>
                    </span>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>

    <form method="POST" class="input-container">
        <input type="text" name="message" id="user-input" placeholder="Type a message..." required>
        <button type="submit">Send</button>
    </form>

    <div class="back">
        <a href="dashboard.php">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
