<?php
session_start();

if (!isset($_SESSION['employee_logged_in']) || !isset($_SESSION['employee_id'])) {
    header("Location: employees_login.php");
    exit();
}

// Get current page for active sidebar highlighting
$current_page = basename($_SERVER['PHP_SELF']);

$employee_id = $_SESSION['employee_id'];

require_once '../config/database.php';
$conn = Database::getConnection();

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

// Pagination setup
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page); // Ensure page is at least 1
$offset = ($page - 1) * $records_per_page;

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM bookings b
              LEFT JOIN user u ON b.user_id = u.id
              WHERE b.employee_id = ? AND b.status != 'done'";
$stmt = $conn->prepare($count_sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$count_result = $stmt->get_result();
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);
$stmt->close();

// Get assigned bookings with pagination
$stmt = $conn->prepare("
    SELECT b.id, b.user_id, u.username AS customer_name, b.service, b.created_at, b.location,
           b.phone, b.appointment_date, b.appointment_time, b.status, b.price, b.note, b.payment_proof
    FROM bookings b
    LEFT JOIN user u ON b.user_id = u.id
    WHERE b.employee_id = ? AND b.status != 'done'
    ORDER BY b.appointment_date, b.appointment_time
    LIMIT ? OFFSET ?
");
$stmt->bind_param("iii", $employee_id, $records_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirGo Employee Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@400;700;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #07353f;
            --secondary-color: #3cd5ed;
            --background-color: #d0f0ff;
            --text-color: #344047;
            --card-bg: #ffffff;
            --card-shadow: rgba(7, 53, 63, 0.1);
            --spacing-unit: clamp(0.5rem, 2vw, 1rem);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--background-color), #ffffff);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            line-height: 1.6;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-color), #052830);
            padding: 2rem 1.5rem;
            color: white;
            position: fixed;
            box-shadow: 4px 0 20px var(--card-shadow);
        }

        .sidebar h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 900;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            letter-spacing: 1px;
        }

        .sidebar h2 span {
            color: var(--secondary-color);
            font-style: italic;
        }

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 12px 16px;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            position: relative;
            overflow: hidden;
            margin: 0;
        }

        .sidebar a i {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .sidebar a::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--secondary-color);
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.3s ease;
            z-index: -1;
            border-radius: 12px;
        }

        .sidebar a:hover {
            color: var(--primary-color);
            transform: translateX(5px);
        }

        .sidebar a:hover::before {
            transform: scaleX(1);
            transform-origin: left;
        }

        .sidebar a.active {
            background: var(--secondary-color);
            color: var(--primary-color);
        }

        .sidebar a.active i {
            color: var(--primary-color);
        }

        .sidebar a.active:hover::before {
            transform: scaleX(0);
        }

        .main-content {
            margin-left: 250px;
            padding: 2rem;
            width: calc(100% - 250px);
        }

        .main-content h1 {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .main-content h1 i {
            color: var(--secondary-color);
        }

        .employee-name {
            background: var(--card-bg);
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px var(--card-shadow);
            margin-bottom: 2rem;
            display: inline-block;
            font-weight: 500;
        }

        .table-container {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 10px 30px var(--card-shadow);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: var(--primary-color);
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 500;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-size: 0.9rem;
        }

        tr:hover td {
            background: rgba(60, 213, 237, 0.05);
        }

        tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            display: inline-block;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-reschedule {
            background: #cce5ff;
            color: #004085;
        }

        .button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            text-decoration: none;
            background: var(--primary-color);
            color: white;
        }

        .button:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px var(--card-shadow);
            color: var(--primary-color);
        }

        .button.secondary {
            background: var(--secondary-color);
            color: var(--primary-color);
        }

        .button.secondary:hover {
            background: var(--primary-color);
            color: white;
        }

        /* Pagination Styles */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
            padding: 1rem;
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 4px 6px var(--card-shadow);
        }

        .pagination-info {
            font-size: 0.9rem;
            color: var(--text-color);
        }

        .pagination {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .pagination-btn {
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 35px;
            height: 35px;
        }

        .pagination-btn:hover,
        .pagination-btn.active {
            background: var(--secondary-color);
            color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px var(--card-shadow);
        }

        .pagination-btn i {
            font-size: 0.8rem;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .button {
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
        }

        .button:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px var(--card-shadow);
            color: var(--primary-color);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow-y: auto;
        }

        .modal-content {
            background: white;
            width: 90%;
            max-width: 600px;
            margin: 20px auto;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: relative;
            transform: translateY(-20px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .modal.active .modal-content {
            transform: translateY(0);
            opacity: 1;
        }

        .modal-header {
            background: var(--primary-color);
            color: white;
            padding: 1.5rem;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            transition: all 0.3s ease;
        }

        .close-btn:hover {
            color: var(--secondary-color);
            transform: scale(1.1);
        }

        .modal-body {
            padding: 2rem;
        }

        /* File Upload Styles */
        .upload-container {
            position: relative;
            margin-top: 0.5rem;
            border: 2px dashed var(--primary-color);
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            transition: all 0.3s ease;
            background: rgba(7, 53, 63, 0.02);
        }

        .upload-container:hover {
            border-color: var(--secondary-color);
            background: rgba(60, 213, 237, 0.05);
        }

        .file-input {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0;
            cursor: pointer;
            z-index: 2;
        }

        .upload-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            background-color: var(--primary-color);
            color: white;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0 auto;
            max-width: 200px;
        }

        .upload-button i {
            font-size: 1.2rem;
        }

        .file-input:hover + .upload-button {
            background-color: var(--secondary-color);
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        .file-name {
            margin-top: 1rem;
            font-size: 0.85rem;
            color: var(--text-color);
            word-break: break-all;
        }

        .image-preview {
            margin-top: 1rem;
            max-width: 300px;
            display: none;
            margin: 1rem auto 0;
        }

        .image-preview img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 991px) {
            body {
                background: #f5f9fc;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                padding: 1rem;
                background: var(--primary-color);
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .sidebar h2 {
                display: none;
            }

            .nav-links {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 0.75rem;
                padding: 0;
            }

            .nav-links a {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                color: rgba(255, 255, 255, 0.9);
                text-decoration: none;
                padding: 0.75rem;
                border-radius: 12px;
                font-size: 0.9rem;
                transition: all 0.3s ease;
                text-align: center;
                background: rgba(255, 255, 255, 0.1);
            }

            .nav-links a i {
                font-size: 1.1rem;
            }

            .nav-links a.active {
                background: var(--secondary-color);
                color: var(--primary-color);
                font-weight: 500;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1rem;
            }

            .employee-name {
                width: 100%;
                text-align: center;
            }

            /* Card style for table rows */
            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead {
                display: none;
            }

            tr {
                margin-bottom: 1rem;
                background: white;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                overflow: hidden;
                position: relative;
            }

            td {
                padding: 0.75rem 1rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            }

            td:before {
                content: attr(data-label);
                font-weight: 500;
                color: var(--primary-color);
                min-width: 100px;
            }

            td:last-child {
                border-bottom: none;
            }

            .button {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .upload-container {
                padding: 0.75rem;
            }

            .upload-button {
                padding: 0.6rem 1rem;
                font-size: 0.85rem;
            }

            .image-preview {
                max-width: 100%;
            }

            .action-buttons {
                flex-direction: column;
            }

            .button {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Air<span>go</span></h2>
        <div class="nav-links">
            <a href="employee_dashboard.php" class="<?= $current_page === 'employee_dashboard.php' ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="booking_history_employees.php" class="<?= $current_page === 'booking_history_employees.php' ? 'active' : '' ?>">
                <i class="fas fa-history"></i> History
            </a>
            <a href="employees_login.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="main-content">
        <?php if (!empty($employee_name)): ?>
            <div class="employee-name">
                <i class="fas fa-user"></i> Logged in as: <strong><?= htmlspecialchars($employee_name) ?></strong>
            </div>
        <?php endif; ?>

        <h1><i class="fas fa-clipboard-list"></i> Assigned Bookings</h1>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>User ID</th>
                        <th>Service</th>
                        <th>Location</th>
                        <th>Contact</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Note</th>
                        <th>Status</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td data-label="Customer"><?= htmlspecialchars($row['customer_name']) ?></td>
                                <td data-label="User ID"><?= htmlspecialchars($row['user_id']) ?></td>
                                <td data-label="Service"><?= htmlspecialchars($row['service']) ?></td>
                                <td data-label="Location"><?= htmlspecialchars($row['location']) ?></td>
                                <td data-label="Contact"><?= htmlspecialchars($row['phone']) ?></td>
                                <td data-label="Date"><?= htmlspecialchars($row['appointment_date']) ?></td>
                                <td data-label="Time"><?= date("g:i A", strtotime($row['appointment_time'])) ?></td>
                                <td data-label="Note"><?= htmlspecialchars($row['note'] ?? 'No note') ?></td>
                                <td data-label="Status">
                                    <span class="status-badge status-<?= strtolower($row['status']) ?>">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>
                                <td data-label="Price">₱<?= number_format($row['price'], 2) ?></td>
                                <td data-label="Actions" class="action-buttons">
                                    <?php if (strtolower($row['status']) === 'approved'): ?>
                                        <a href="?done_id=<?= $row['id'] ?>" class="button" onclick="return confirm('Mark this booking as done?');">
                                            <i class="fas fa-check"></i> Done
                                        </a>
                                    <?php endif; ?>
                                    <button class="button" onclick="openUploadModal(<?= htmlspecialchars(json_encode($row)) ?>)">
                                        <i class="fas fa-upload"></i> Upload
                                    </button>
                                    <?php if ($row['payment_proof']): ?>
                                        <button class="button" onclick="openViewImageModal('<?= htmlspecialchars($row['payment_proof']) ?>')">
                                            <i class="fas fa-image"></i> View
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" style="text-align: center; padding: 2rem;">
                                <i class="fas fa-inbox" style="font-size: 2rem; color: #999; margin-bottom: 1rem; display: block;"></i>
                                No assigned bookings at the moment.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination-container">
                <div class="pagination-info">
                    <span>Showing <?= $offset + 1 ?> to <?= min($offset + $records_per_page, $total_records) ?> of <?= $total_records ?> entries</span>
                </div>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=1" class="pagination-btn">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                        <a href="?page=<?= $page - 1 ?>" class="pagination-btn">
                            <i class="fas fa-angle-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    for ($i = $start_page; $i <= $end_page; $i++):
                    ?>
                        <a href="?page=<?= $i ?>" class="pagination-btn <?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page + 1 ?>" class="pagination-btn">
                            <i class="fas fa-angle-right"></i>
                        </a>
                        <a href="?page=<?= $total_pages ?>" class="pagination-btn">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Upload Modal -->
    <div class="modal" id="uploadModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-upload"></i> Upload Payment Proof</h2>
                <button class="close-btn" onclick="closeUploadModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" onsubmit="uploadPaymentProof(event)">
                    <input type="hidden" id="booking_id" name="booking_id">
                    <div class="form-group">
                        <label for="payment_proof">Upload Image</label>
                        <div class="upload-container">
                            <input type="file" id="payment_proof" name="payment_proof" accept="image/*" class="file-input">
                            <div class="upload-button">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Upload Image</span>
                            </div>
                            <div class="file-name">No file chosen</div>
                        </div>
                        <div id="image-preview" class="image-preview"></div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="button">
                            <i class="fas fa-save"></i> Save
                        </button>
                        <button type="button" class="button" onclick="closeUploadModal()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Image Modal -->
    <div class="modal" id="viewImageModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-image"></i> Payment Proof</h2>
                <button class="close-btn" onclick="closeViewImageModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="image-display" class="image-preview" style="display: block;"></div>
            </div>
        </div>
    </div>

    <script>
        function openUploadModal(booking) {
            document.getElementById('booking_id').value = booking.id;
            const modal = document.getElementById('uploadModal');
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('active'), 10);

            // Show existing payment proof if available
            const imagePreview = document.getElementById('image-preview');
            if (booking.payment_proof) {
                const imagePath = '../' + booking.payment_proof;
                imagePreview.innerHTML = `<img src="${imagePath}" alt="Payment Proof">`;
                imagePreview.style.display = 'block';
            } else {
                imagePreview.style.display = 'none';
                imagePreview.innerHTML = '';
            }
        }

        function closeUploadModal() {
            const modal = document.getElementById('uploadModal');
            modal.classList.remove('active');
            setTimeout(() => modal.style.display = 'none', 300);
        }

        function openViewImageModal(imagePath) {
            const modal = document.getElementById('viewImageModal');
            const imageDisplay = document.getElementById('image-display');
            imageDisplay.innerHTML = `<img src="../${imagePath}" alt="Payment Proof">`;
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('active'), 10);
        }

        function closeViewImageModal() {
            const modal = document.getElementById('viewImageModal');
            modal.classList.remove('active');
            setTimeout(() => modal.style.display = 'none', 300);
        }

        // Handle file input change
        document.getElementById('payment_proof').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            const fileNameDisplay = e.target.parentElement.querySelector('.file-name');
            const imagePreview = document.getElementById('image-preview');
            
            if (fileName) {
                fileNameDisplay.textContent = fileName;
                
                // Show image preview
                const reader = new FileReader();
                reader.onload = function(event) {
                    imagePreview.innerHTML = `<img src="${event.target.result}" alt="Payment Proof Preview">`;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(e.target.files[0]);
            } else {
                fileNameDisplay.textContent = 'No file chosen';
                imagePreview.style.display = 'none';
                imagePreview.innerHTML = '';
            }
        });

        async function uploadPaymentProof(event) {
            event.preventDefault();
            
            const form = event.target;
            const formData = new FormData(form);

            try {
                const response = await fetch('upload_payment_proof.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (result.success) {
                    alert('Payment proof uploaded successfully!');
                    closeUploadModal();
                    window.location.reload();
                } else {
                    alert(result.message || 'Error uploading payment proof');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while uploading the payment proof');
            }
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const uploadModal = document.getElementById('uploadModal');
            const viewImageModal = document.getElementById('viewImageModal');
            if (event.target === uploadModal) {
                closeUploadModal();
            }
            if (event.target === viewImageModal) {
                closeViewImageModal();
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
