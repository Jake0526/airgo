<?php
session_start();

if (!isset($_SESSION['employee_logged_in']) || !isset($_SESSION['employee_id'])) {
    header("Location: employees_login.php");
    exit();
}

$employee_id = $_SESSION['employee_id'];

// DB connection
$conn = new mysqli("localhost", "root", "", "airgo");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch employee name
$employee_name = "";
$stmt_name = $conn->prepare("SELECT name FROM employees WHERE id = ?");
$stmt_name->bind_param("i", $employee_id);
$stmt_name->execute();
$stmt_name->bind_result($employee_name);
$stmt_name->fetch();
$stmt_name->close();

// Mark service as done
if (isset($_GET['done_id'])) {
    $done_id = (int)$_GET['done_id'];
    $stmt = $conn->prepare("UPDATE bookings SET status = 'done' WHERE id = ? AND employee_id = ?");
    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt->bind_param("ii", $done_id, $employee_id);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add note
    if (isset($_POST['add_note'])) {
        $booking_id = intval($_POST['note_booking_id']);
        $note_text = trim($_POST['note_text']);
        if (!empty($note_text)) {
            $stmt = $conn->prepare("INSERT INTO booking_notes (booking_id, employee_id, note) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $booking_id, $employee_id, $note_text);
            $stmt->execute();
            $stmt->close();
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Edit note
    if (isset($_POST['edit_note'])) {
        $note_id = intval($_POST['edit_note_id']);
        $edit_text = trim($_POST['edit_note_text']);
        if (!empty($edit_text)) {
            $stmt = $conn->prepare("UPDATE booking_notes SET note = ? WHERE id = ? AND employee_id = ?");
            $stmt->bind_param("sii", $edit_text, $note_id, $employee_id);
            $stmt->execute();
            $stmt->close();
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Get assigned bookings
$stmt = $conn->prepare("
    SELECT b.id, u.username AS customer_name, b.service, b.created_at, b.location,
           b.phone, b.appointment_date, b.appointment_time, b.status, b.price
    FROM bookings b
    LEFT JOIN user u ON b.user_id = u.id
    WHERE b.employee_id = ? AND b.status != 'done'
    ORDER BY b.appointment_date DESC, b.created_at DESC
");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>AirGo Employee Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
:root { --bg: #4c7273; --main: #07353f; --accent: #CACBBB; --light: #ffffff; --shadow: rgba(0, 0, 0, 0.1); }
* {margin:0;padding:0;box-sizing:border-box;}
body {font-family:'Segoe UI',sans-serif;background:var(--bg);display:flex;}
.sidebar {width:250px;height:100vh;background:var(--main);color:var(--light);padding:30px 20px;position:fixed;display:flex;flex-direction:column;}
.sidebar h2 {font-size:24px;margin-bottom:30px;text-align:center;}
.sidebar a {color:var(--light);text-decoration:none;margin:12px 0;padding:10px 15px;border-radius:10px;transition:background 0.3s ease;}
.sidebar a:hover {background:var(--accent);color:#000;}
.main-content {margin-left:250px;padding:20px;width:calc(100% - 250px);position:relative;}
.main-content h1 {margin-bottom:20px;color:white;font-size:20px;margin-top:10px;}
.employee-name {position:absolute;top:20px;right:30px;color:white;font-size:14px;background:rgba(0,0,0,0.2);padding:6px 12px;border-radius:8px;}
table {width:90%;background:var(--light);border-collapse:collapse;border-radius:15px;box-shadow:6px 6px 12px var(--shadow), -6px -6px 12px #ffffff;overflow:hidden;margin:30px auto;}
th,td {padding:12px 25px;text-align:left;font-size:12px;}
th {background:var(--main);color:var(--light);}
td {background-color:#fff;color:#333;border-bottom:1px solid #eee;}
tr:hover td {background:#e6f7ff;transition:0.3s ease;}
.done-button,.note-button {display:inline-block;padding:6px 10px;background-color:#07353f;color:white;border:none;border-radius:8px;text-decoration:none;font-weight:bold;font-size:10px;cursor:pointer;transition:background-color 0.3s ease;}
.done-button:hover,.note-button:hover {background-color:#CACBBB;}
.note-form {margin-top:10px;}
.note-form textarea {width:100%;height:60px;padding:5px;font-size:12px;margin-top:5px;}
.note-form button {margin-top:5px;}
.note-list {margin-top:5px;padding:5px;background:#f4f4f4;border-radius:5px;font-size:11px;}
.edit-icon {cursor:pointer;color:#07353f;margin-left:8px;font-size:12px;}
.edit-form {margin-top:5px;}
.edit-form textarea {width:100%;height:50px;padding:5px;font-size:12px;}
.edit-form button {margin-top:5px;}
@media(max-width:768px){.main-content{margin-left:0;width:100%;}.sidebar{display:none;}}
</style>
</head>
<body>
<div class="sidebar">
    <h2>AirGo Employee</h2>
    <a href="employee_dashboard.php">Dashboard</a>
    <a href="booking_history_employees.php">History</a>
    <a href="employees_login.php">Logout</a>
</div>

<div class="main-content">
<?php if (!empty($employee_name)): ?>
<div class="employee-name">Logged in as: <strong><?= htmlspecialchars($employee_name) ?></strong></div>
<?php endif; ?>
<h1>Assigned Bookings</h1>
<table>
<thead>
<tr>
<th>Name</th><th>Service</th><th>Location</th><th>Phone</th><th>Appointment Date</th><th>Time</th><th>Status</th><th>Action</th><th>Price</th>
</tr>
</thead>
<tbody>
<?php if ($result->num_rows > 0): ?>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row['customer_name']) ?></td>
<td><?= htmlspecialchars($row['service']) ?></td>
<td><?= htmlspecialchars($row['location']) ?></td>
<td><?= htmlspecialchars($row['phone']) ?></td>
<td><?= htmlspecialchars($row['appointment_date']) ?></td>
<td><?= date("g:i A", strtotime($row['appointment_time'])) ?></td>
<td><?= htmlspecialchars($row['status'] ? ucfirst($row['status']) : 'Pending') ?></td>
<td>
<?php if (strtolower($row['status']) === 'approved'): ?>
<a href="?done_id=<?= $row['id'] ?>" class="done-button" onclick="return confirm('Mark this booking as done?');">Done</a>
<button class="note-button" onclick="document.getElementById('note-form-<?= $row['id'] ?>').style.display='block';">Add Note</button>
<?php else: ?>
<span style="color:#999;font-size:10px;">No action</span>
<?php endif; ?>
</td>
<td>₱<?= number_format($row['price'], 2) ?></td>
</tr>
<tr>
<td colspan="9">
<div id="note-form-<?= $row['id'] ?>" class="note-form" style="display:none;">
<form method="post">
<input type="hidden" name="note_booking_id" value="<?= $row['id'] ?>">
<textarea name="note_text" placeholder="Enter additional services or notes..."></textarea>
<button type="submit" name="add_note" class="note-button">Save Note</button>
</form>
</div>
<?php
$stmt_notes = $conn->prepare("SELECT id, note, created_at FROM booking_notes WHERE booking_id = ? ORDER BY created_at DESC");
$stmt_notes->bind_param("i", $row['id']);
$stmt_notes->execute();
$notes_result = $stmt_notes->get_result();
if ($notes_result->num_rows > 0):
?>
<div class="note-list">
<strong>Notes:</strong><br>
<?php while ($note = $notes_result->fetch_assoc()): ?>
<div>
• <?= htmlspecialchars($note['note']) ?>
<em>(<?= date("F j, Y g:i A", strtotime($note['created_at'])) ?>)</em>
<span class="edit-icon" onclick="document.getElementById('edit-form-<?= $note['id'] ?>').style.display='block';">✏️ Edit</span>
<div id="edit-form-<?= $note['id'] ?>" class="edit-form" style="display:none;">
<form method="post">
<input type="hidden" name="edit_note_id" value="<?= $note['id'] ?>">
<textarea name="edit_note_text"><?= htmlspecialchars($note['note']) ?></textarea>
<button type="submit" name="edit_note" class="note-button">Update</button>
</form>
</div>
</div>
<?php endwhile; ?>
</div>
<?php endif; $stmt_notes->close(); ?>
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="9" style="text-align:center;color:#555;">No assigned bookings.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</body>
</html>
<?php $conn->close(); ?>
