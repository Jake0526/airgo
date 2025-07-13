<?php  
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get current page for active sidebar highlighting
$current_page = basename($_SERVER['PHP_SELF']);

// Include the database connection
require_once 'config/database.php';
$conn = Database::getConnection();

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
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='20' fill='%2307353f'/%3E%3Cpath d='M30 50c0-11 9-20 20-20s20 9 20 20-9 20-20 20-20-9-20-20zm35 0c0-8.3-6.7-15-15-15s-15 6.7-15 15 6.7 15 15 15 15-6.7 15-15z' fill='%233cd5ed'/%3E%3Cpath d='M50 60c-5.5 0-10-4.5-10-10s4.5-10 10-10 10 4.5 10 10-4.5 10-10 10z' fill='white'/%3E%3C/svg%3E" />
    <link rel="icon" type="image/png" sizes="32x32" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAABKhJREFUWEe9V21sU2UUfu7H3a7dmDAgKgwQPxAkKAYnJsyPGCGK0ZAQFfEHJGpigkT8gYrxB5pI/GD+QBNjNCr+ICYaEzWBRDBRIQQIiCIIKLIvGIPtbrfb7b3XnHPvbVc2trs3bU/S7H7c877Pec95z3veK+AeHOIe4MN/B0CWZVkQhHui3LquX5YkSboXk+u6PqwoCud3HEAkEuH8qqrS0NBQYWxsLDY2NhaNRqNRVVVjmqYpuq4rpHwwGHQHAgG3y+VyezweN/1yu91uQRDcBIr+CoIgCILAf222GfRqgKZpGsdHYDRNm6Xr+llZlr1/CcDQ0BCNjZumxXVdVxVFiWmaFlVVNaooSjQejxPQEUVRYpqmxXRd5+vpuu5yOBwet9vtczgcPofD4SWQBMjpdLodDofL6XS6nE4nARFFUeR7QRAIJIMxAVJVVWPQODNBkiSvLMsDkiTJt23BwMAAjVEURY3H4zFFUaKqqkYJiKqqUU3T6G9UVdW4pmkxAqfrOgMWRdHjdDq9BNDhcPgcDofX5XL5XS6Xz+12+10ul9fpdHoJqMvlcjudTjcBJVOYQOm6rsXjcS0Wi8UjkUiU/kYikWgsFovF4/F4LBaLxePxeDQaZUDWFPT39xMAVVXVWCwWVRQlqihKhEAqihIxmUxRXdf52WQyEQCPKIo+URR9BNDpdPqtVqvfYrH4rFar3263++12u89ms3ltNpvXZrN5rFYrAXWRDgioYU7DnMSUoihsXkVRFPJEJBKJRKPR8O0a6O3tNQNgJlRVNayqaphAEgOSJIUlSQobDLgoK0RR9JEGLBaL32q1+m02m5/AEli73e6z2+0+q9Xqc7lcXrvd7rFarW6r1eq2WCwExk2ZQCDJ75IkhXVdD+u6HtZ1PazrehiYEtZ1XTYykCRJYdu27du3j8YbIAJGbIQJpKqqYUmSwgTSYMFNLJAGHA6H32Kx+G02m5/AElgCabfbfTabzWu1Wj0ul8tjs9k8VqvVbbFY3E6n0+10Ot0EkjQgSVKY7qmqGqbxkiSFVVUNU8FTVTVEz7IsywkNbN682QyAM0BV1RCZkEBKkhTSNC0kSVKIGHA4HD6n0+mzWCw+AksmtNlsXpvN5rXb7R6Xy+UhoFar1W21Wl0EkoAaZg1rmhYi89F4TdNCmqaFVFUNkXkTNZHNZvts27aNxhuNCIEI0T2ZkN4bLLhEUfQaueAjDdhsNi+BJD0QUKfT6SYdEFDKAEmSQoqihOjeAJG4/gQ7W7duzQXwP9cBnNexpjhBAAAAAElFTkSuQmCC" />
    <link rel="icon" type="image/png" sizes="16x16" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAlpJREFUOE+Nk0tIVFEYx//n3jtz544z4zg6OuqoqKVp9LDUMAqKFtGiWtSiWkQkEUEtahERRBQVLYJoUW2iRdCiKKJFD4JqE0REDxQfw1hq5DiOztz5mHvPPeeMjlr2wVl8fN/v+z++c87HsB8f24d9+H8A0un0IcbYUdd1PSLyiIg8z3MJyPU8zyEix/M8h4hcz/McInI9z7WJyLJt27Isy7IsS7YsS7IsS7YsW2aMWbZtW5ZlWaZpEsdxEgghDCGEwRjbBpBKpQ4yxo66rusRkedRwQQgEpHreZ5DRK7neQ4RuZ7nOZ7n2a7rWo7jWLZtW47jyI7jSLZty47jSI7jSJZlSYwxadOvCSGMbQDJZPIQY+yI67qe53ke/QsgItf1XIeIHNd1Hdd1Hc/zbNu2Ldu2ZMexJMexJNu2JcaYtAXAGDM2AWKx2GHG2BEiIiLPIyLPdd0CQK7ruq7ruq5t25Zt25LjOLLjOJLjOJLjOJLjOBJjTNoUlzFmbAJEo9EjjLHDRERE5BERua7rFgByXdd1Xde1bdu2bNuWHceWHceWHMeSHMeSbNuWGGPSFgBjzNgEiEQihxljh4iIiMgjInJd1y0A5Lqu67qu69i2bdm2LTmOLTmOLTmOJTmOJdm2LTHGpC1xGWPGJkA4HD7CGDtIRJ7neUREnut6BYBc13Ud27Yt27Ylx7Elx7Ekx7Ekx7Ek27YlxpgkSRJjjBkbAMFg8BBj7IDruh4ReUREnut6BYBc13Ud27Yt27Ylx7Elx7Ekx7Ek27YlxpgkSRJjjBl/AQgEAgcZY/uJyPsJ8AOvgZzXMm3oPQAAAABJRU5ErkJggg==" />
    
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
