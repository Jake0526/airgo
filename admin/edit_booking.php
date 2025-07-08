<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "airgo";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];

    $sql = "SELECT b.id, username, b.service, b.status, b.created_at, b.location, b.contact, 
                   b.appointment_date, b.appointment_time, b.employee_id, u.username
            FROM bookings b
            LEFT JOIN user u ON b.user_id = u.id
            WHERE b.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();
    } else {
        die("Booking not found.");
    }

    $employees_sql = "SELECT id, name FROM employees WHERE status != 'Inactive'";
    $employees_result = $conn->query($employees_sql);

    if (!$employees_result) {
        die("Error executing query: " . $conn->error);
    }

    $employees = $employees_result->fetch_all(MYSQLI_ASSOC);
} else {
    die("No booking ID provided.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $service = $_POST['service'];
    $status = $_POST['status'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $employee_id = !empty($_POST['employee_id']) ? $_POST['employee_id'] : NULL;
    $location = $_POST['location'];
    $contact = $_POST['contact'];

    $update_sql = "UPDATE bookings SET  service = ?, status = ?, appointment_date = ?, 
                   appointment_time = ?, employee_id = ?, location = ?, contact = ? WHERE id = ?";

    $update_stmt = $conn->prepare($update_sql);
   if ($update_stmt){ $update_stmt->bind_param("sssssssi", $service, $status, $appointment_date, $appointment_time, $employee_id, $location, $contact, $booking_id);

   if ($update_stmt->execute()) {
            header("Location: admin_bookings.php");
            exit();
        } else {
            echo "Execute failed: " . $update_stmt->error;
        }
    } else {
        echo "Prepare failed: " . $conn->error;
    }
    $update_stmt->close();
} else {
    echo "Prepare failed: " . $conn->error;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Booking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 20px;
            background-color:rgb(59, 59, 59);
        }

        .container {
            max-width: 700px;
            margin: 20px auto;
            padding: 10px;
            background-color: #CACBBB;
            border-radius: 5px;
            border: 3px solid #07353f;
            box-shadow: 0 0 20px white;
        }

        h1 {
            text-align: center;
            color: #07353f;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .form-group {
            flex: 1 1 calc(50% - 20px);
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: 500;
        }

        input, select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1em;
        }

        .form-actions {
            width: 100%;
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        button {
            background-color: #07353f;
            color: white;
            padding: 10px 20px;
            font-size: 1.1em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: skyblue;
            color: black;
        }

        .error-message {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }

        @media (max-width: 600px) {
            .form-group {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Edit Booking</h1>

    <?php if (isset($error_message)): ?>
        <div class="error-message"><?= $error_message ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="name">Customer Name</label>
            <input type="text" id="username" name="username" value="<?= $booking['username'] ?>" required>
        </div>

        <div class="form-group">
            <label for="service">Service</label>
            <input type="text" id="service" name="service" value="<?= $booking['service'] ?>" required>
        </div>

        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" id="location" name="location" value="<?= $booking['location'] ?>" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" id="contact" name="contact" value="<?= $booking['contact'] ?>" required>
        </div>

        <div class="form-group">
            <label for="appointment_date">Appointment Date</label>
            <input type="date" id="appointment_date" name="appointment_date" value="<?= $booking['appointment_date'] ?>" required>
        </div>

        <div class="form-group">
            <label for="appointment_time">Appointment Time</label>
            <input type="time" id="appointment_time" name="appointment_time" value="<?= $booking['appointment_time'] ?>" required>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                <option value="Pending" <?= $booking['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Approved" <?= $booking['status'] == 'Approved' ? 'selected' : '' ?>>Approved</option>
                <option value="Rejected" <?= $booking['status'] == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                <option value="Cancelled" <?= $booking['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                <option value="Completed" <?= $booking['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
            </select>
        </div>

        <div class="form-group">
            <label for="employee_id">Assign Technician</label>
            <select id="employee_id" name="employee_id">
                <option value="">Select Technician</option>
                <?php foreach ($employees as $employee): ?>
                    <option value="<?= $employee['id'] ?>" <?= $employee['id'] == $booking['employee_id'] ? 'selected' : '' ?>>
                        <?= $employee['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit">Update Booking</button>
            <a href="admin_bookings.php"><button type="button">Cancel</button></a>
        </div>
    </form>
</div>
</body>
</html>
