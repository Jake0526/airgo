<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli("localhost", "root", "", "airgo");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
if ($booking_id === 0) {
    die("Invalid booking ID.");
}

// Add new note
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['note']) && !empty($_POST['note'])) {
    $note = $conn->real_escape_string($_POST['note']);
    $stmt = $conn->prepare("INSERT INTO booking_notes (booking_id, note) VALUES (?, ?)");
    $stmt->bind_param("is", $booking_id, $note);
    $stmt->execute();
    $stmt->close();
    header("Location: add_note.php?booking_id=$booking_id");
    exit();
}

// Edit existing note
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_note_id']) && isset($_POST['edited_note']) && !empty($_POST['edited_note'])) {
    $edit_id = intval($_POST['edit_note_id']);
    $edited_note = $conn->real_escape_string($_POST['edited_note']);
    $stmt = $conn->prepare("UPDATE booking_notes SET note = ? WHERE id = ? AND booking_id = ?");
    $stmt->bind_param("sii", $edited_note, $edit_id, $booking_id);
    $stmt->execute();
    $stmt->close();
    header("Location: add_note.php?booking_id=$booking_id");
    exit();
}

// Delete note
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM booking_notes WHERE id = ? AND booking_id = ?");
    $stmt->bind_param("ii", $delete_id, $booking_id);
    $stmt->execute();
    $stmt->close();
    header("Location: add_note.php?booking_id=$booking_id");
    exit();
}

// Get booking info
$booking = $conn->query("SELECT * FROM bookings WHERE id = $booking_id")->fetch_assoc();

// Get notes
$notes = $conn->query("SELECT * FROM booking_notes WHERE booking_id = $booking_id ORDER BY created_at DESC");

// Check edit mode
$edit_mode = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$edit_note_data = null;
if ($edit_mode > 0) {
    $stmt = $conn->prepare("SELECT * FROM booking_notes WHERE id = ? AND booking_id = ?");
    $stmt->bind_param("ii", $edit_mode, $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_note_data = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Notes - Admin Panel</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #e9f1f7;
            margin: 0;
            padding: 0;
        }
        header {
            background: #07353f;
            color: white;
            padding: 15px 30px;
            font-size: 22px;
        }
        .container {
            background: #fff;
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h2 {
            margin-top: 0;
            color: #07353f;
        }
        textarea {
            width: 100%;
            height: 100px;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 10px;
            font-size: 14px;
            margin-bottom: 10px;
        }
        button {
            background-color: #07353f;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 5px;
        }
        button:hover {
            background-color: #05525c;
        }
        .note {
            background: #f1f9ff;
            border-left: 5px solid #07353f;
            padding: 15px;
            margin-top: 15px;
            border-radius: 5px;
        }
        .note time {
            display: block;
            color: #777;
            font-size: 12px;
            margin-top: 5px;
        }
        .note-actions {
            margin-top: 8px;
        }
        .note-actions a {
            text-decoration: none;
            color: #05525c;
            margin-right: 15px;
        }
        .note-actions a:hover {
            text-decoration: underline;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #07353f;
            font-weight: bold;
        }
    </style>
</head>
<body>
<header>
    Admin Panel – Booking Notes
</header>
<div class="container">
    <a class="back-link" href="admin_bookings.php">&larr; Back to Bookings</a>
    <h2>Notes for <?= htmlspecialchars($booking['name']) ?> | <?= htmlspecialchars($booking['service']) ?></h2>

    <?php if ($edit_note_data): ?>
        <!-- Edit Note -->
        <form method="POST">
            <textarea name="edited_note"><?= htmlspecialchars($edit_note_data['note']) ?></textarea>
            <input type="hidden" name="edit_note_id" value="<?= $edit_note_data['id'] ?>">
            <button type="submit">Save Changes</button>
            <a href="add_note.php?booking_id=<?= $booking_id ?>" style="margin-left:10px;">Cancel</a>
        </form>
    <?php else: ?>
        <!-- Add Note -->
        <form method="POST">
            <textarea name="note" placeholder="Write a note..."></textarea>
            <button type="submit">Add Note</button>
        </form>
    <?php endif; ?>

    <h3>Existing Notes</h3>
    <?php if ($notes->num_rows > 0): ?>
        <?php while ($n = $notes->fetch_assoc()): ?>
            <div class="note">
                <p><?= nl2br(htmlspecialchars($n['note'])) ?></p>
                <time>Added: <?= date('F j, Y g:i A', strtotime($n['created_at'])) ?></time>
                <div class="note-actions">
                    <a href="add_note.php?booking_id=<?= $booking_id ?>&edit=<?= $n['id'] ?>">Edit</a>
                    <a href="add_note.php?booking_id=<?= $booking_id ?>&delete=<?= $n['id'] ?>" onclick="return confirm('Are you sure you want to delete this note?');">Delete</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No notes found for this booking.</p>
    <?php endif; ?>
</div>
</body>
</html>
