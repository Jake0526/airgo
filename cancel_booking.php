<?php  
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get current page for active sidebar highlighting
$current_page = basename($_SERVER['PHP_SELF']);

// Include the database connection
include('db_connection.php');

$user_id = $_SESSION['user_id'];

// Fetch from booking_history_customer
$stmt1 = $conn->prepare("SELECT *, booking_date AS moved_at, 'History' AS source FROM booking_history_customer WHERE user_id = ?");
$stmt1->bind_param("i", $user_id);
$stmt1->execute();
$result1 = $stmt1->get_result();
$history = $result1->fetch_all(MYSQLI_ASSOC);
$stmt1->close();

// Fetch from cancel_booking
$stmt2 = $conn->prepare("SELECT *, booking_date AS moved_at, 'Cancelled' AS source FROM cancel_booking WHERE user_id = ?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$cancelled = $result2->fetch_all(MYSQLI_ASSOC);
$stmt2->close();

$conn->close();

// Combine arrays
$combined = array_merge($history, $cancelled);

// Sort by date+time descending
usort($combined, function($a, $b) {
    $datetimeA = strtotime($a['moved_at'].' '.$a['booking_time']);
    $datetimeB = strtotime($b['moved_at'].' '.$b['booking_time']);
    return $datetimeB <=> $datetimeA;
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking History - AirGo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@400;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
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
            background: linear-gradient(135deg, var(--background-color), #ffffff);
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
            min-height: 100vh;
            font-size: 16px;
            line-height: 1.6;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-color), #052830);
            padding: 2rem 1.5rem;
            color: white;
            box-shadow: 4px 0 20px var(--card-shadow);
            transition: all 0.3s ease;
            z-index: 1000;
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

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 1rem 0;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 12px 16px;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            position: relative;
            overflow: hidden;
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

        .main {
            margin-left: 250px;
            padding: clamp(1.5rem, 4vw, 3rem);
        }

        .main h1 {
            color: var(--primary-color);
            font-size: clamp(1.8rem, 3vw, 2.5rem);
            margin-bottom: 2rem;
            font-weight: 600;
        }

        .history-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px var(--card-shadow);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            border-left: 5px solid var(--secondary-color);
        }

        .history-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px var(--card-shadow);
        }

        .history-card h5 {
            color: var(--primary-color);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .history-card p {
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }

        .history-card strong {
            color: var(--primary-color);
        }

        .delete-link {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            text-decoration: none;
        }

        .delete-icon {
            color: #dc3545;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .delete-icon:hover {
            color: #a71d2a;
            transform: scale(1.1);
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        @media (max-width: 991px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                padding: 1rem;
            }

            .sidebar h2 {
                display: none;
            }

            .nav-links {
                display: flex;
                justify-content: center;
                gap: 1rem;
                flex-wrap: wrap;
            }

            .sidebar a {
                margin: 0.5rem;
                padding: 10px 20px;
            }

            .main {
                margin-left: 0;
                padding: 1.5rem;
            }
        }

        @media (max-width: 575px) {
            .sidebar {
                padding: 0.5rem;
            }

            .sidebar a {
                padding: 8px 16px;
                font-size: 0.9rem;
            }

            .main {
                padding: 1rem;
            }

            .history-card {
                padding: 1.5rem;
            }
        }

        /* Simple loading indicator */
        .loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--secondary-color);
            z-index: 9999;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .loading.active {
            transform: scaleX(1);
        }
    </style>
</head>
<body>
<div class="loading"></div>

<div class="sidebar">
    <h2>Air<span>go</span></h2>
    <div class="nav-links">
        <a href="dashboard.php" class="<?= $current_page === 'dashboard.php' ? 'active' : '' ?>"><i class="fa-solid fa-house"></i> Dashboard</a>
        <a href="book-now.php" class="<?= $current_page === 'book-now.php' ? 'active' : '' ?>"><i class="fa-solid fa-calendar-plus"></i> Booking</a>
        <a href="cancel_booking.php" class="<?= $current_page === 'cancel_booking.php' ? 'active' : '' ?>"><i class="fa-solid fa-clock-rotate-left"></i> History</a>
    <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</div>

<div class="main">
    <h1>Booking History</h1>

    <?php if (!empty($combined)): ?>
        <?php foreach ($combined as $b): ?>
            <div class="history-card">
                <a class="delete-link"
                   href="delete_booking_history.php?id=<?= urlencode($b['id']) ?>&source=<?= urlencode(strtolower($b['source'])) ?>"
                   onclick="return confirm('Are you sure you want to delete this record?')">
                    <i class="fas fa-trash delete-icon"></i>
                </a>
                <div>
                    <h5><?= htmlspecialchars($b['service_type'] ?? 'Service') ?></h5>
                    <p><strong>Date:</strong> <?= htmlspecialchars($b['moved_at']) ?></p>
                    <p><strong>Time:</strong> <?= htmlspecialchars(date("g:i A", strtotime($b['booking_time']))) ?></p>
                    <p><strong>Technician:</strong> <?= htmlspecialchars($b['technician_name'] ?? 'N/A') ?></p>
                    <p><strong>Price:</strong> ₱<?= htmlspecialchars(number_format($b['price'] ?? 0, 2)) ?></p>
                    <p><strong>Record Type:</strong> <?= htmlspecialchars($b['source']) ?></p>
                    <?php
                        $status = isset($b['status']) ? strtolower($b['status']) : 'unknown';
                        $badgeClass = 'secondary';
                        if ($status === 'cancelled') {
                            $badgeClass = 'danger';
                        } elseif ($status === 'done') {
                            $badgeClass = 'success';
                        }
                    ?>
                    <p><strong>Status:</strong>
                        <span class="badge bg-<?= $badgeClass ?>">
                            <?= htmlspecialchars(ucfirst($status)) ?>
                        </span>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-muted">No booking history available.</p>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Simple page transition
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', function(e) {
            if (!this.href.includes('logout.php')) {
                e.preventDefault();
                const href = this.href;
                
                // Show loading indicator
                document.querySelector('.loading').classList.add('active');
                
                // Navigate after delay
                setTimeout(() => {
                    window.location.href = href;
                }, 300);
            }
        });
    });
});
</script>
</body>
</html>
