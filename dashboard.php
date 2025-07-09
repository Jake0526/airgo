<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$host = getenv('DB_HOST') ?: 'localhost';
$db = getenv('DB_NAME') ?: 'airgo';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

$calendar_events = [];
$date_counts = [];
$today = date('Y-m-d');
$all_result = $conn->query("SELECT appointment_date FROM bookings WHERE status != 'Cancelled'");
while ($row = $all_result->fetch_assoc()) {
    $date = $row['appointment_date'];
    if (!isset($date_counts[$date])) $date_counts[$date] = 0;
    $date_counts[$date]++;
}

$max_slots_per_day = 46;
$range_start = strtotime('-1 month');
$range_end = strtotime('+3 months');

for ($i = $range_start; $i <= $range_end; $i += 86400) {
    $date = date('Y-m-d', $i);
    $booked = $date_counts[$date] ?? 0;
    $remaining = $max_slots_per_day - $booked;

    if ($date < $today) {
        $color = "#ffffff";
        $title = "";
    } elseif ($remaining <= 0) {
        $color = "#ff6b6b";
        $title = "Fully Booked";
    } else {
        $color = "#48c78e";
        $title = "$remaining Slots Left";
    }

    $calendar_events[] = [
        "title" => $title,
        "start" => $date,
        "display" => "background",
        "color" => $color
    ];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>AirGo | Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@400;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
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

        .main-container {
            margin-left: 250px;
            padding: clamp(1.5rem, 4vw, 3rem);
            display: flex;
            flex-direction: row;
            gap: clamp(1.5rem, 4vw, 3rem);
            min-height: 100vh;
        }

        .calendar-container {
            flex: 1.5;
            min-width: 320px;
        }

        #calendar {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px var(--card-shadow);
            transition: transform 0.3s ease;
        }

        #calendar:hover {
            transform: translateY(-5px);
        }

        .content-container {
            flex: 1;
            min-width: 320px;
        }

        .slot-popup {
            animation: fadeInUp 0.3s ease-out;
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 20px 40px var(--card-shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        @keyframes fadeInUp {
            from { 
                opacity: 0; 
                transform: translateY(20px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        .label-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .label-title i {
            color: var(--secondary-color);
        }

        /* Facebook Section Styles */
        #facebook-page {
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 20px 40px var(--card-shadow);
            padding: 1.5rem;
            transition: transform 0.3s ease;
        }

        #facebook-page:hover {
            transform: translateY(-5px);
        }

        .fb-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1rem;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .fb-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
        }

        .fb-image {
            width: 280px;
            height: 280px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px var(--card-shadow);
        }

        .fb-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .fb-image:hover img {
            transform: scale(1.05);
        }

        .fb-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary-color);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid var(--primary-color);
        }

        .fb-button:hover {
            background: transparent;
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        .fb-info {
            text-align: center;
            color: var(--text-color);
        }

        .fb-info h3 {
            color: var(--primary-color);
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .fb-info ul {
            list-style: none;
            padding: 0;
            margin: 0 0 1rem;
        }

        .fb-info li {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .fb-info i {
            color: var(--secondary-color);
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

            .nav-links a {
                margin: 0.5rem;
                padding: 10px 20px;
            }

            .main-container {
                margin-left: 0;
                flex-direction: column;
                padding: 1.5rem;
            }

            #calendar {
                margin-bottom: 1.5rem;
            }
        }

        @media (max-width: 575px) {
            .sidebar {
                padding: 0.5rem;
            }

            .nav-links a {
                padding: 8px 16px;
                font-size: 0.9rem;
                min-width: 120px;
                text-align: center;
                justify-content: center;
            }

            .main-container {
                padding: 1rem;
            }

            #calendar {
                padding: 1rem;
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
        <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="book-now.php"><i class="fas fa-calendar-alt"></i> Booking</a>
        <a href="cancel_booking.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main-container">
    <div class="calendar-container">
        <div class="label-title">
            <i class="fas fa-calendar-check"></i>
            Select a Date to Book
        </div>
        <div id="calendar"></div>
    </div>

    <div class="content-container">
        <!-- Facebook Page Section -->
        <section id="facebook-page">
            <div class="fb-header">
                <i class="fab fa-facebook"></i> Connect With Us
            </div>

            <div class="fb-content">
                <div class="fb-image">
                    <img src="page.png" alt="AirGo Facebook Page" />
                </div>

                <a href="https://web.facebook.com/messages/t/111830037044299" target="_blank" class="fb-button">
                    <i class="fab fa-facebook-messenger"></i> Message Us
                </a>

                <div class="fb-info">
                    <h3>Contact Information</h3>
                    <ul>
                        <li><i class="far fa-clock"></i> Open 24/7</li>
                        <li><i class="fas fa-phone"></i> Sun# 09430510783</li>
                        <li><i class="fas fa-phone"></i> 09976189915</li>
                    </ul>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const slotsDiv = document.getElementById('available-slots');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 550,
        selectable: true,
        events: <?= json_encode($calendar_events) ?>,
        dateClick: function (info) {
            const selected = info.dateStr;
            const today = new Date().toISOString().split('T')[0];
            if (selected < today) {
                alert("You cannot select a past date.");
                return;
            }

            fetch('get_slots.php?date=' + selected)
                .then(res => res.json())
                .then(data => {
                    let html = `
                        <div class="p-3 bg-white rounded shadow-sm slot-popup">
                            <h5 class="text-center mb-3">Available Slots on <strong>${selected}</strong></h5>
                            <div class="row">`;
                    data.forEach(slot => {
                        const btnClass = slot.available ? 'btn-success' : 'btn-secondary';
                        const disabled = slot.available ? '' : 'disabled';
                        html += `
                            <div class="col-md-4 col-sm-6 col-12 mb-3">
<button class="btn ${btnClass} w-100 fw-semibold text-uppercase fs-6 py-2" ${disabled}
                                    onclick="window.location.href='booking.php?date=${selected}&time=${slot.time}'">
                                    ${slot.time}
                                </button>
                            </div>`;
                    });
                    html += "</div></div>";
                    slotsDiv.innerHTML = html;
                });
        }
    });

    calendar.render();

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
