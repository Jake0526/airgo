<?php
session_start();

if (!isset($_SESSION['employee_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Get current page for active sidebar highlighting
$current_page = basename($_SERVER['PHP_SELF']);

$employee_id = $_SESSION['employee_id'];

require_once '../config/database.php';
$conn = Database::getConnection();

$sql = "SELECT 
            b.id,
            u.username AS customer_name,
            b.service,
            b.created_at,
            b.location,
            b.phone,
            b.appointment_date,
            b.appointment_time,
            e.name AS employee_name,
            b.status,
            b.employee_id,
            b.price,
            b.payment_proof
        FROM bookings b
        LEFT JOIN user u ON b.user_id = u.id
        LEFT JOIN employees e ON b.employee_id = e.id
        WHERE b.status != 'pending'
          AND b.employee_id = ?
        ORDER BY b.appointment_date DESC, b.created_at DESC";
     
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirGo Employee Booking History</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='20' fill='%2307353f'/%3E%3Cpath d='M30 50c0-11 9-20 20-20s20 9 20 20-9 20-20 20-20-9-20-20zm35 0c0-8.3-6.7-15-15-15s-15 6.7-15 15 6.7 15 15 15 15-6.7 15-15z' fill='%233cd5ed'/%3E%3Cpath d='M50 60c-5.5 0-10-4.5-10-10s4.5-10 10-10 10 4.5 10 10-4.5 10-10 10z' fill='white'/%3E%3C/svg%3E" />
    <link rel="icon" type="image/png" sizes="32x32" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAABKhJREFUWEe9V21sU2UUfu7H3a7dmDAgKgwQPxAkKAYnJsyPGCGK0ZAQFfEHJGpigkT8gYrxB5pI/GD+QBNjNCr+ICYaEzWBRDBRIQQIiCIIKLIvGIPtbrfb7b3XnHPvbVc2trs3bU/S7H7c877Pec95z3veK+AeHOIe4MN/B0CWZVkQhHui3LquX5YkSboXk+u6PqwoCud3HEAkEuH8qqrS0NBQYWxsLDY2NhaNRqNRVVVjmqYpuq4rpHwwGHQHAgG3y+VyezweN/1yu91uQRDcBIr+CoIgCILAf222GfRqgKZpGsdHYDRNm6Xr+llZlr1/CcDQ0BCNjZumxXVdVxVFiWmaFlVVNaooSjQejxPQEUVRYpqmxXRd5+vpuu5yOBwet9vtczgcPofD4SWQBMjpdLodDofL6XS6nE4nARFFUeR7QRAIJIMxAVJVVWPQODNBkiSvLMsDkiTJt23BwMAAjVEURY3H4zFFUaKqqkYJiKqqUU3T6G9UVdW4pmkxAqfrOgMWRdHjdDq9BNDhcPgcDofX5XL5XS6Xz+12+10ul9fpdHoJqMvlcjudTjcBJVOYQOm6rsXjcS0Wi8UjkUiU/kYikWgsFovF4/F4LBaLxePxeDQaZUDWFPT39xMAVVVVWCwWVRQlqihKhEAqihIxmUxRXdf52WQyEQCPKIo+URR9BNDpdPqtVqvfYrH4rFar3263++12u89ms3ltNpvXZrN5rFYrAXWRDgioYU7DnMSUoihsXkVRFPJEJBKJRKPR8O0a6O3tNQNgJlRVNayqaphAEgOSJIUlSQobDLgoK0RR9JEGLBaL32q1+m02m5/AEli73e6z2+0+q9Xqc7lcXrvd7rFarW6r1eq2WCwExk2ZQCDJ75IkhXVdD+u6HtZ1PazrehiYEtZ1XTYykCRJYdu27du3j8YbIAJGbIQJpKqqYUmSwgTSYMFNLJAGHA6H32Kx+G02m5/AElgCabfbfTabzWu1Wj0ul8tjs9k8VqvVbbFY3E6n0+10Ot0EkjQgSVKY7qmqGqbxkiSFVVUNU8FTVTVEz7IsywkNbN682QyAM0BV1RCZkEBKkhTSNC0kSVKIGHA4HD6n0+mzWCw+AksmtNlsXpvN5rXb7R6Xy+UhoFar1W21Wl0EkoAaZg1rmhYi89F4TdNCmqaFVFUNkXkTNZHNZvts27aNxhuNCIEI0T2ZkN4bLLhEUfQaueAjDdhsNi+BJD0QUKfT6SYdEFDKAEmSQoqihOjeAJG4/gQ7W7duzQXwP9cBnNexpjhBAAAAAElFTkSuQmCC" />
    <link rel="icon" type="image/png" sizes="16x16" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAlpJREFUOE+Nk0tIVFEYx//n3jtz544z4zg6OuqoqKVp9LDUMAqKFtGiWtSiWkQkEUEtahERRBQVLYJoUW2iRdCiKKJFD4JqE0REDxQfw1hq5DiOztz5mHvPPeeMjlr2wVl8fN/v+z++c87HsB8f24d9+H8A0un0IcbYUdd1PSLyiIg8z3MJyPU8zyEix/M8h4hcz/McInI9z7WJyLJt27Isy7IsS7YsS7IsS7YsW2aMWbZtW5ZlWaZpEsdxEgghDCGEwRjbBpBKpQ4yxo66rusRkedRwQQgEpHreZ5DRK7neQ4RuZ7nOZ7n2a7rWo7jWLZtW47jyI7jSLZty47jSI7jSJZlSYwxadOvCSGMbQDJZPIQY+yI67qe53ke/QsgItf1XIeIHNd1Hdd1Hc/zbNu2Ldu2ZMexJMexJNu2JcaYtAXAGDM2AWKx2GHG2BEiIiLPIyLPdd0CQK7ruq7ruq5t25Zt25LjOLLjOJLjOJLjOJLjOBJjTNoUlzFmbAJEo9EjjLHDRERE5BERua7rFgByXdd1Xde1bdu2bNuWHceWHceWHMeSHMeSbNuWGGPSFgBjzNgEiEQihxljh4iIiMgjInJd1y0A5Lqu67qu69i2bdm2LTmOLTmOLTmOJTmOJdm2LTHGpC1xGWPGJkA4HD7CGDtIRJ7neUREnut6BYBc13Ud27Yt27Ylx7Elx7Ekx7Ekx7Ek27YlxpgkSRJjjBkbAMFg8BBj7IDruh4ReUREnut6BYBc13Ud27Yt27Ylx7Elx7Ekx7Ek27YlxpgkSRJjjBl/AQgEAgcZY/uJyPsJ8AOvgZzXMm3oPQAAAABJRU5ErkJggg==" />
    
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

        .sidebar a i {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .sidebar a:hover i {
            transform: scale(1.1);
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

        /* Remove loading indicator styles */
        .loading {
            display: none;
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
            gap: 12px;
        }

        .export-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.75rem 1.5rem;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px var(--card-shadow);
        }

        .export-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px var(--card-shadow);
        }

        .date-heading {
            color: var(--primary-color);
            margin: 2rem 0 1rem;
            font-size: 1.2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        table {
            width: 100%;
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 10px 30px var(--card-shadow);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        th {
            background: var(--primary-color);
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 500;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        tr:hover td {
            background: rgba(60, 213, 237, 0.05);
        }

        .delete-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0.5rem 1rem;
            background: #ff6b6b;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .delete-btn:hover {
            background: #ff5252;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 82, 82, 0.2);
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-done {
            background: #48c78e33;
            color: #48c78e;
        }

        .status-cancelled {
            background: #ff6b6b33;
            color: #ff6b6b;
        }

        .status-completed {
            background: #3cd5ed33;
            color: #3cd5ed;
        }

        .status-rejected {
            background: #f1404033;
            color: #f14040;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 10px 30px var(--card-shadow);
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--secondary-color);
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: var(--text-color);
            font-size: 1.1rem;
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
                grid-template-columns: repeat(2, 1fr);
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
                background: #f5f9fc;
                min-height: calc(100vh - 80px);
                flex: 1;
            }

            .main-content h1 {
                font-size: 1.5rem;
                margin-bottom: 1.25rem;
                color: var(--primary-color);
                font-weight: 600;
                padding: 0;
            }

            table {
                display: block;
                overflow-x: auto;
            }

            .export-btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 575px) {
            .nav-links {
                grid-template-columns: repeat(2, 1fr);
            }

            .nav-links a {
                padding: 0.6rem;
                font-size: 0.85rem;
            }

            .nav-links a i {
                font-size: 1rem;
            }

            .main-content {
                padding: 0.75rem;
            }

            .main-content h1 {
                font-size: 1.25rem;
                margin-bottom: 1rem;
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
        <h1><i class="fas fa-clock-rotate-left"></i> Booking History</h1>
        <a href="export_booking_history_employees.php" class="export-btn">
            <i class="fas fa-file-export"></i> Export to CSV
        </a>
     
        <?php
        if (empty($bookings)) {
            echo '<div class="empty-state">
                    <i class="fas fa-history"></i>
                    <p>No booking history available.</p>
                  </div>';
        } else {
            $current_date = null;
            foreach ($bookings as $row):
                if ($current_date !== $row['appointment_date']):
                    if ($current_date !== null) echo "</tbody></table>";
                    $current_date = $row['appointment_date'];
        ?>
        <h2 class="date-heading">
            <i class="fas fa-calendar-day"></i>
            <?= htmlspecialchars(date("F j, Y", strtotime($current_date))) ?>
        </h2>
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Location</th>
                    <th>Phone</th>
                    <th>Time</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
        <?php
                endif;
                $status_class = 'status-' . strtolower($row['status']);
        ?>
            <tr>
                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                <td><?= htmlspecialchars($row['service']) ?></td>
                <td><?= htmlspecialchars($row['location']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td>
                    <?php
                    $time = htmlspecialchars($row['appointment_time']);
                    echo $time ? date("g:i A", strtotime($time)) : "-";
                    ?>
                </td>
                <td>₱<?= number_format($row['price'], 2) ?></td>
                <td>
                    <span class="status-badge <?= $status_class ?>">
                        <?= ucfirst(htmlspecialchars($row['status'])) ?>
                    </span>
                </td>
                <td>
                    <form method="POST" action="delete_booking.php" onsubmit="return confirm('Are you sure you want to delete this booking? This cannot be undone.');">
                        <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
                        <button type="submit" class="delete-btn">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                    </form>
                    <?php if (!empty($row['payment_proof'])): ?>
                        <button class="view-btn" onclick="viewPaymentProof('../<?= htmlspecialchars($row['payment_proof']) ?>')">
                            <i class="fas fa-eye"></i> View
                        </button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php
            endforeach;
            echo "</tbody></table>";
        }
        ?>
    </div>

    <!-- Payment Proof Modal -->
    <div class="modal" id="paymentProofModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-image"></i> Payment Proof</h2>
                <button class="close-btn" onclick="closePaymentProofModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <img id="paymentProofImage" src="" alt="Payment Proof" style="max-width: 100%; height: auto;">
            </div>
        </div>
    </div>

    <style>
        /* Update modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1045;
            overflow-y: auto;
        }

        .modal::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #000;
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
            z-index: -1;
        }

        .modal.active::before {
            opacity: 0.8;
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
            transition: transform 0.2s ease-out, opacity 0.2s ease-out;
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

        /* View Button Styles */
        .view-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0.5rem 1rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            margin-top: 0.5rem;
        }

        .view-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(60, 213, 237, 0.2);
        }

        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                margin: 10px auto;
            }

            .modal-body {
                padding: 1.5rem;
            }
        }
    </style>

    <script>
        function viewPaymentProof(imagePath) {
            const modal = document.getElementById('paymentProofModal');
            const image = document.getElementById('paymentProofImage');
            image.src = imagePath;
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('active'), 10);
        }

        function closePaymentProofModal() {
            const modal = document.getElementById('paymentProofModal');
            modal.classList.remove('active');
            setTimeout(() => modal.style.display = 'none', 300);
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('paymentProofModal');
            if (event.target === modal) {
                closePaymentProofModal();
            }
        }
    </script>
</body>
</html>
