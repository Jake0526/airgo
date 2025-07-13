<?php
// session_start();
// $employee_id = $_SESSION['employee_id']; // if logged in
$employee_id = 1; // for testing

require_once '../config/database.php';
$conn = Database::getConnection();

// Handle adding a reply
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["reply_note"])) {
    $booking_id = intval($_POST["booking_id"]);
    $note = trim($_POST["note"]);

    $insert_sql = "INSERT INTO booking_notes (booking_id, sender_role, sender_id, note) VALUES (?, 'employee', ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("iis", $booking_id, $employee_id, $note);
    $stmt->execute();
    $stmt->close();
}

// Fetch bookings this employee has notes on
$bookings_sql = "
    SELECT DISTINCT booking_id 
    FROM booking_notes 
    WHERE booking_id IN (
        SELECT id FROM bookings WHERE assigned_employee_id = ?
    )
    ORDER BY booking_id DESC
";
$stmt_bookings = $conn->prepare($bookings_sql);
$stmt_bookings->bind_param("i", $employee_id);
$stmt_bookings->execute();
$bookings_result = $stmt_bookings->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee - Booking Notes</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; }
        h2 { text-align: center; }
        .booking-notes {
            width: 90%;
            margin: 20px auto;
            background: #fff;
            padding: 15px;
            border: 1px solid #ccc;
        }
        .note {
            border-bottom: 1px dashed #ccc;
            padding: 8px;
        }
        .note .meta {
            font-size: 12px;
            color: #555;
        }
        form textarea {
            width: 100%;
            height: 60px;
        }
        form button {
            margin-top: 5px;
            padding: 6px 12px;
        }
    </style>
</head>
<body>

<h2>Employee - Booking Notes & Replies</h2>

<?php if ($bookings_result->num_rows > 0): ?>
    <?php while($b = $bookings_result->fetch_assoc()): ?>
        <div class="booking-notes">
            <h3>Booking ID: <?php echo htmlspecialchars($b['booking_id']); ?></h3>
            <?php
            $notes_sql = "SELECT * FROM booking_notes WHERE booking_id = ? ORDER BY created_at ASC";
            $stmt_notes = $conn->prepare($notes_sql);
            if (!$stmt_notes) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt_notes->bind_param("i", $b['booking_id']);
            $stmt_notes->execute();
            $notes = $stmt_notes->get_result();
            ?>
            <?php while($n = $notes->fetch_assoc()): ?>
                <div class="note">
                    <div class="meta">
                        <?php echo strtoupper(htmlspecialchars($n['sender_role'])); ?>
                        (ID: <?php echo $n['sender_id']; ?>)
                        at <?php echo $n['created_at']; ?>
                    </div>
                    <div class="text">
                        <?php echo nl2br(htmlspecialchars($n['note'])); ?>
                    </div>
                </div>
            <?php endwhile; ?>
            <?php $stmt_notes->close(); ?>

            <form method="post">
                <input type="hidden" name="booking_id" value="<?php echo $b['booking_id']; ?>">
                <textarea name="note" placeholder="Write a reply..."></textarea><br>
                <button type="submit" name="reply_note">Add Reply</button>
            </form>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p style="text-align:center;">No notes found.</p>
<?php endif; ?>

</body>
</html>

<?php
$stmt_bookings->close();
$conn->close();
?>
