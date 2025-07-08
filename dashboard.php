<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$db = 'airgo';
$user = 'root';
$pass = '';
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right, #d0f0ff);
            font-family: 'Segoe UI', sans-serif;
            font-size: 17px;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 230px;
            height: 100vh;
            background-color: #07353f;
            padding: 25px 20px;
            color: white;
        }
        .sidebar h2 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 15px 0;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 8px;
            transition: background 0.3s ease;
        }
        .sidebar a:hover {
            background-color: #0ea5e9;
        }
        .main-container {
            margin-left: 250px;
            padding: 40px 30px;
            display: flex;
            flex-direction: row;
            gap: 40px;
        }
        .calendar-container {
            flex: 1;
            min-width: 320px;
        }
        #calendar {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .content-container {
            flex: 1;
            min-width: 320px;
        }
        .slot-popup {
            animation: fadeInUp 0.3s ease-out;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .label-title {
            font-size: 20px;
            font-weight: bold;
            color: #004d40;
            margin-bottom: 10px;
        }
        @media (max-width: 991px) {
            .main-container {
                margin-left: 0;
                padding: 20px;
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                display: flex;
                flex-direction: row;
                justify-content: space-around;
                align-items: center;
                flex-wrap: wrap;
                padding: 10px 0;
            }
            .sidebar h2 {
                display: none;
            }
            .sidebar a {
                margin: 5px;
                padding: 8px 12px;
                font-size: 0.9rem;
            }
        }
        @media (max-width: 575px) {
            #calendar {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>AirGo</h2>
    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="book-now.php"><i class="fas fa-calendar-alt"></i> Booking</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-container">
    <div class="calendar-container">
        <div class="label-title"><i class="fas fa-calendar-check"></i> Select a Date to Book</div>
        <div id="calendar"></div>
    </div>

    <div class="content-container">
        <div id="available-slots" class="slot-popup mb-4"></div>

        <!-- Facebook Page Section -->
        <section id="facebook-page" style="font-family: 'Poppins', sans-serif;">
            <div style="text-align: center; background: linear-gradient(135deg, #29487D, #07353f); color: #f0f4ff; padding: 5px 10px; border-radius: 10px; margin-bottom: 10px; box-shadow: 0 3px 8px rgba(41, 72, 125, 0.6), 0 0 20px rgba(7, 53, 63, 0.4); font-size: 1rem; font-weight: 500; letter-spacing: 1em; text-transform: uppercase;">
                Facebook Page
            </div>

            <div style="display: flex; flex-direction: column; align-items: center; gap: 20px; background-color: #fff; border-radius: 14px; padding: 20px; box-shadow: 0 12px 25px rgba(0, 0, 0, 0.07), 0 6px 10px rgba(7, 53, 63, 0.04); max-width: 740px; margin: 0 auto;">
                <div class="image" style="text-align: center;">
                    <div style="width: 280px; height: 280px; border-radius: 14px; overflow: hidden; box-shadow: 0 8px 20px rgba(41, 72, 125, 0.3); margin-bottom: 10px;">
                        <img src="page.png" alt="AirGo Page" style="width: 100%; height: 100%; object-fit: cover; border-radius: 14px; transition: transform 0.5s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'"/>
                    </div>

                    <a href="https://web.facebook.com/messages/t/111830037044299" target="_blank" style="display: inline-block; color: #29487D; font-weight: 700; text-decoration: none; padding: 10px 18px; background-color: #f0f4ff; border-radius: 8px; box-shadow: 0 4px 12px rgba(41, 72, 125, 0.2); transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#29487D'; this.style.color='#fff'" onmouseout="this.style.backgroundColor='#f0f4ff'; this.style.color='#29487D'">
                        <i class="fab fa-facebook-messenger"></i> Message Us
                    </a>
                </div>

                <div class="info" style="text-align: center; color: #07353f; font-size: 1.1rem; line-height: 1.65;">
                    <p style="margin-top: 16px; font-weight: 600;">Open Hours:</p>
                    <ul style="padding-left: 24px; margin: 8px 0 18px; list-style-type: square; color: #415a7d;">
                        <li>Monday - Sunday: 24 hours</li>
                    </ul>
                    <p style="font-weight: 600;">Contact: <span style="color: #07353f;">Sun# 09430510783 / 09976189915</span></p>
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
});
</script>

</body>
</html>
