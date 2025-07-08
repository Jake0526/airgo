<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

// DB Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "airgo";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// ADD EMPLOYEE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_employee'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $position = mysqli_real_escape_string($conn, trim($_POST['position']));
    $hire_date = mysqli_real_escape_string($conn, trim($_POST['hire_date']));
    $status = mysqli_real_escape_string($conn, trim($_POST['status']));
    $password_input = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password_input !== $confirm_password) {
        $message = "❌ Passwords do not match.";
    } else {
        $hashed_password = password_hash($password_input, PASSWORD_DEFAULT);

        $sql_insert = "INSERT INTO employees (name, email, position, hire_date, status, password)
                       VALUES ('$name', '$email', '$position', '$hire_date', '$status', '$hashed_password')";

        if ($conn->query($sql_insert) === TRUE) {
            $message = "✅ Employee added successfully!";
        } else {
            $message = "❌ Error: " . $conn->error;
        }
    }
}

// DELETE EMPLOYEE
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $sql_delete = "DELETE FROM employees WHERE id = '$delete_id'";
    if ($conn->query($sql_delete) === TRUE) {
        $message = "✅ Employee deleted successfully!";
    } else {
        $message = "❌ Error deleting employee: " . $conn->error;
    }
}

// FETCH EMPLOYEES
$sql_employees = "SELECT * FROM employees ORDER BY id DESC";
$result_employees = $conn->query($sql_employees);
$employees = [];
while ($row = $result_employees->fetch_assoc()) {
    $employees[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>AirGo Admin - Employees</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<style>
:root {
    --bg: #4c7273;
    --main: #07353f;
    --accent: #CACBBB;
    --light: #ffffff;
    --shadow: rgba(0,0,0,0.1);
}
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Segoe UI',sans-serif; background:var(--bg); display:flex; }
.sidebar {
    width:250px;
    height:100vh;
    background:var(--main);
    color:var(--light);
    padding:30px 20px;
    position:fixed;
    display:flex;
    flex-direction:column;
}
.sidebar h2 { font-size:24px; margin-bottom:30px; text-align:center; }
.sidebar a {
    color:var(--light);
    text-decoration:none;
    margin:12px 0;
    padding:10px 15px;
    border-radius:10px;
    transition:background 0.3s ease;
}
.sidebar a:hover { background:var(--accent); color:#000; }
.main-content {
    margin-left:300px;
    padding:40px;
    width:calc(100% - 300px);
}
.main-content h1 { margin-bottom:30px; color:white; font-size:20px; }
.form-container {
    background:var(--light);
    padding:20px;
    border-radius:15px;
    box-shadow:3px 3px 6px var(--shadow), -3px -3px 6px #fff;
    margin-bottom:20px;
}
.form-container h2 { margin-bottom:10px; color:var(--main); }
.form-row {
    display:flex;
    flex-wrap:wrap;
    gap:15px;
    margin-bottom:15px;
    position:relative;
}
.form-row input, .form-row select {
    flex:1;
    padding:10px;
    border-radius:8px;
    border:1px solid #ccc;
}
.password-container {
    position: relative;
    flex:1;
}
.password-container input {
    width:100%;
    padding-right:40px;
}
.password-container .toggle-password {
    position:absolute;
    top:50%;
    right:10px;
    transform:translateY(-50%);
    cursor:pointer;
    font-size:18px;
    color:#555;
}
button[type="submit"] {
    background-color:#07353f;
    color:white;
    padding:10px 20px;
    border:none;
    border-radius:8px;
    cursor:pointer;
}
button[type="submit"]:hover { background-color:#094c5d; }
table {
    width:100%;
    background:var(--light);
    border-radius:10px;
    box-shadow:3px 3px 6px var(--shadow), -3px -3px 6px #fff;
    border-collapse:collapse;
}
th, td {
    padding:10px 15px;
    text-align:left;
}
th {
    background:var(--main);
    color:var(--light);
}
tr:nth-child(even) { background:#f9f9f9; }
tr:hover { background:#e0f7f7; }
.edit-btn {
    display:inline-block;
    background-color:#07353f;
    color:white;
    padding:6px 14px;
    border-radius:8px;
    font-size:12px;
    text-decoration:none;
}
.edit-btn:hover { background-color:#094c5d; }
.delete-btn {
    background-color:#721c24;
    margin-left:5px;
}
.delete-btn:hover { background-color:#a71d2a; }
.message {
    padding:10px 20px;
    margin-bottom:20px;
    border-radius:8px;
    font-weight:bold;
}
.success { background:#d4edda; color:#155724; }
.error { background:#f8d7da; color:#721c24; }
@media(max-width:768px){
    .main-content{ margin-left:0; padding:20px; width:100%; }
    .sidebar{ display:none; }
    .form-row{ flex-direction:column; }
}
</style>
</head>
<body>
<div class="sidebar">
    <h2>AirGo Admin</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="admin_bookings.php">Bookings</a>
    <a href="admin_employees.php">Employees</a>
    <a href="booking_history.php">Booking History</a>
    <a href="index.php">Logout</a>
</div>
<div class="main-content">
    <h1>Employees List</h1>
    <?php if (!empty($message)): ?>
        <div class="message <?php echo (strpos($message, '✅') !== false) ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <div class="form-container">
        <h2>Add New Employee</h2>
        <form method="POST">
            <div class="form-row">
                <input type="text" name="name" placeholder="Employee Name" required />
                <input type="email" name="email" placeholder="Employee Email" required />
            </div>
            <div class="form-row">
                <input type="text" name="position" placeholder="Position" required />
                <input type="date" name="hire_date" required />
            </div>
            <div class="form-row">
                <div class="password-container">
                    <input type="password" name="password" id="password" placeholder="Password" required />
                    <span class="toggle-password" onclick="togglePassword('password', this)">👁️</span>
                </div>
                <div class="password-container">
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required />
                    <span class="toggle-password" onclick="togglePassword('confirm_password', this)">👁️</span>
                </div>
            </div>
            <div class="form-row">
                <select name="status" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" name="add_employee">Add Employee</button>
        </form>
    </div>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Position</th>
            <th>Hire Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($employees) > 0): ?>
            <?php foreach ($employees as $employee): ?>
            <tr>
                <td><?php echo htmlspecialchars($employee['id']); ?></td>
                <td><?php echo htmlspecialchars($employee['name']); ?></td>
                <td><?php echo htmlspecialchars($employee['email']); ?></td>
                <td><?php echo htmlspecialchars($employee['position']); ?></td>
                <td><?php echo htmlspecialchars($employee['hire_date']); ?></td>
                <td><?php echo htmlspecialchars($employee['status']); ?></td>
                <td>
                    <a href="edit_employee.php?id=<?php echo $employee['id']; ?>" class="edit-btn">✏️ Edit</a>
                    <a href="admin_employees.php?delete_id=<?php echo $employee['id']; ?>" class="edit-btn delete-btn" onclick="return confirm('Are you sure you want to delete this employee?');">🗑️ Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7">No employees found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<script>
function togglePassword(fieldId, icon) {
    const input = document.getElementById(fieldId);
    if (input.type === "password") {
        input.type = "text";
        icon.textContent = "🙈";
    } else {
        input.type = "password";
        icon.textContent = "👁️";
    }
}
</script>
</body>
</html>
