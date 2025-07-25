<?php  
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

// Include the database connection
require_once '../config/database.php';

// Get database connection
$conn = Database::getConnection();

// Determine selected tab
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'all';

// Pagination setup
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page); // Ensure page is at least 1
$offset = ($page - 1) * $records_per_page;

// Build WHERE clause
$where = "";
switch ($tab) {
    case 'pending':
        $where = "WHERE LOWER(b.status) = 'pending'";
        break;
    case 'reschedule':
        $where = "WHERE LOWER(b.status) = 'rescheduled'";
        break;
    case 'approved':
        $where = "WHERE LOWER(b.status) = 'approved'";
        break;
    case 'completed':
        $where = "WHERE b.status = 'Completed'";
        break;
    case 'cancelled':
        $where = "WHERE LOWER(b.status) = 'cancelled'";
        break;
    case 'all':
    default:
        $where = "WHERE LOWER(b.status) NOT IN ('completed', 'cancelled')";
        break;
}

// Add filter conditions
$filter_name = isset($_GET['filter_name']) ? mysqli_real_escape_string($conn, trim($_GET['filter_name'])) : '';
$filter_service = isset($_GET['filter_service']) ? mysqli_real_escape_string($conn, trim($_GET['filter_service'])) : '';
$filter_location = isset($_GET['filter_location']) ? mysqli_real_escape_string($conn, trim($_GET['filter_location'])) : '';

if (!empty($filter_name)) {
    $where .= $where ? " AND" : "WHERE";
    $where .= " CONCAT(u.fname, ' ', u.lname) LIKE '%$filter_name%'";
}
if (!empty($filter_service)) {
    $where .= $where ? " AND" : "WHERE";
    $where .= " b.service LIKE '%$filter_service%'";
}
if (!empty($filter_location)) {
    $where .= $where ? " AND" : "WHERE";
    $where .= " b.location LIKE '%$filter_location%'";
}

// Add date range filter
$start_date = isset($_GET['start_date']) ? mysqli_real_escape_string($conn, $_GET['start_date']) : '';
$end_date = isset($_GET['end_date']) ? mysqli_real_escape_string($conn, $_GET['end_date']) : '';

if (!empty($start_date) && !empty($end_date)) {
    $where .= $where ? " AND" : "WHERE";
    $where .= " b.appointment_date BETWEEN '$start_date' AND '$end_date'";
}

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM bookings b
              LEFT JOIN user u ON b.user_id = u.id
              LEFT JOIN employees e ON b.employee_id = e.id
              $where";
$count_result = $conn->query($count_sql);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Fetch bookings with pagination
$sql = "SELECT b.id, b.user_id, CONCAT(u.fname, ' ', u.lname) as customer_name, b.service, b.status, b.created_at, b.location, b.phone, 
               b.appointment_date, b.appointment_time, b.employee_id, e.name AS employee_name,
               b.note, b.payment_proof
        FROM bookings b
        LEFT JOIN user u ON b.user_id = u.id
        LEFT JOIN employees e ON b.employee_id = e.id
        $where
        ORDER BY b.appointment_date, b.appointment_time
        LIMIT $records_per_page OFFSET $offset";
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Get unique services for dropdown
$services_sql = "SELECT DISTINCT service FROM bookings ORDER BY service";
$services_result = $conn->query($services_sql);
$services = [];
while ($row = $services_result->fetch_assoc()) {
    $services[] = $row['service'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>AirGo Admin - Bookings</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='20' fill='%2307353f'/%3E%3Cpath d='M30 50c0-11 9-20 20-20s20 9 20 20-9 20-20 20-20-9-20-20zm35 0c0-8.3-6.7-15-15-15s-15 6.7-15 15 6.7 15 15 15 15-6.7 15-15z' fill='%233cd5ed'/%3E%3Cpath d='M50 60c-5.5 0-10-4.5-10-10s4.5-10 10-10 10 4.5 10 10-4.5 10-10 10z' fill='white'/%3E%3C/svg%3E" />
    <link rel="icon" type="image/png" sizes="32x32" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAABKhJREFUWEe9V21sU2UUfu7H3a7dmDAgKgwQPxAkKAYnJsyPGCGK0ZAQFfEHJGpigkT8gYrxB5pI/GD+QBNjNCr+ICYaEzWBRDBRIQQIiCIIKLIvGIPtbrfb7b3XnHPvbVc2trs3bU/S7H7c877Pec95z3veK+AeHOIe4MN/B0CWZVkQhHui3LquX5YkSboXk+u6PqwoCud3HEAkEuH8qqrS0NBQYWxsLDY2NhaNRqNRVVVjmqYpuq4rpHwwGHQHAgG3y+VyezweN/1yu91uQRDcBIr+CoIgCILAf222GfRqgKZpGsdHYDRNm6Xr+llZlr1/CcDQ0BCNjZumxXVdVxVFiWmaFlVVNaooSjQejxPQEUVRYpqmxXRd5+vpuu5yOBwet9vtczgcPofD4SWQBMjpdLodDofL6XS6nE4nARFFUeR7QRAIJIMxAVJVVWPQODNBkiSvLMsDkiTJt23BwMAAjVEURY3H4zFFUaKqqkYJiKqqUU3T6G9UVdW4pmkxAqfrOgMWRdHjdDq9BNDhcPgcDofX5XL5XS6Xz+12+10ul9fpdHoJqMvlcjudTjcBJVOYQOm6rsXjcS0Wi8UjkUiU/kYikWgsFovF4/F4LBaLxePxeDQaZUDWFPT39xMAVVVVWCwWVRQlqihKhEAqihIxmUxRXdf52WQyEQCPKIo+URR9BNDpdPqtVqvfYrH4rFar3263++12u89ms3ltNpvXZrN5rFYrAXWRDgioYU7DnMSUoihsXkVRFPJEJBKJRKPR8O0a6O3tNQNgJlRVNayqaphAEgOSJIUlSQobDLgoK0RR9JEGLBaL32q1+m02m5/AElgCabfbfTabzWu1Wj0ul8tjs9k8VqvVbbFY3E6n0+10Ot0EkjQgSVKY7qmqGqbxkiSFVVUNU8FTVTVEz7IsywkNbN682QyAM0BV1RCZkEBKkhTSNC0kSVKIGHA4HD6n0+mzWCw+AksmtNlsXpvN5rXb7R6Xy+UhoFar1W21Wl0EkoAaZg1rmhYi89F4TdNCmqaFVFUNkXkTNZHNZvts27aNxhuNCIEI0T2ZkN4bLLhEUfQaueAjDdhsNi+BJD0QUKfT6SYdEFDKAEmSQoqihOjeAJG4/gQ7W7duzQXwP9cBnNexpjhBAAAAAElFTkSuQmCC" />
    <link rel="icon" type="image/png" sizes="16x16" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAlpJREFUOE+Nk0tIVFEYx//n3jtz544z4zg6OuqoqKVp9LDUMAqKFtGiWtSiWkQkEUEtahERRBQVLYJoUW2iRdCiKKJFD4JqE0REDxQfw1hq5DiOztz5mHvPPeeMjlr2wVl8fN/v+z++c87HsB8f24d9+H8A0un0IcbYUdd1PSLyiIg8z3MJyPU8zyEix/M8h4hcz/McInI9z7WJyLJt27Isy7IsS7YsS7IsS7YsW2aMWbZtW5ZlWaZpEsdxEgghDCGEwRjbBpBKpQ4yxo66rusRkedRwQQgEpHreZ5DRK7neQ4RuZ7nOZ7n2a7rWo7jWLZtW47jyI7jSLZty47jSI7jSJZlSYwxadOvCSGMbQDJZPIQY+yI67qe53ke/QsgItf1XIeIHNd1Hdd1Hc/zbNu2Ldu2ZMexJMexJNu2JcaYtAXAGDM2AWKx2GHG2BEiIiLPIyLPdd0CQK7ruq7ruq5t25Zt25LjOLLjOJLjOJLjOJLjOBJjTNoUlzFmbAJEo9EjjLHDRERE5BERua7rFgByXdd1Xde1bdu2bNuWHceWHceWHMeSHMeSbNuWGGPSFgBjzNgEiEQihxljh4iIiMgjInJd1y0A5Lqu67qu69i2bdm2LTmOLTmOLTmOJTmOJdm2LTHGpC1xGWPGJkA4HD7CGDtIRJ7neUREnut6BYBc13Ud27Yt27Ylx7Elx7Ekx7Ekx7Ek27YlxpgkSRJjjBl/AQgEAgcZY/uJyPsJ8AOvgZzXMm3oPQAAAABJRU5ErkJggg==" />
    
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
            background: linear-gradient(135deg, var(--background-color), #fff);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
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

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
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
            font-size: 1.4rem;
            min-width: 32px;
            text-align: center;
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
            padding: 2rem;
            width: calc(100% - 250px);
        }

        .main h1 {
            margin-bottom: 2rem;
            color: var(--primary-color);
            font-size: 2.2rem;
            font-family: 'Playfair Display', serif;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0 1rem;
        }

        .main h1 i {
            color: var(--secondary-color);
        }

        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            padding: 0 1rem;
        }

        .tab-link {
            background: var(--card-bg);
            color: var(--text-color);
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px var(--card-shadow);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .tab-link i {
            font-size: 1rem;
            color: var(--primary-color);
        }

        .tab-link.active, .tab-link:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px var(--card-shadow);
        }

        .tab-link.active i, .tab-link:hover i {
            color: var(--secondary-color);
        }

        /* Export Button Styles */
        .export-container {
            display: flex;
            justify-content: flex-end;
            gap: 2rem;  /* Increased from 1rem to 2rem */
            margin: 1rem 0;
            padding: 0 1rem;
        }

        .export-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            background-color: var(--secondary-color);
            color: var(--primary-color);
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            background-color: #2bc4dc;
        }

        .export-btn i {
            font-size: 1.1rem;
        }

        .sales-report-btn {
            background-color: var(--primary-color);
            color: white;
        }

        .sales-report-btn:hover {
            background-color: #0a4956;
        }

        @media (max-width: 768px) {
            .export-container {
                flex-direction: column;
                padding: 0 0.75rem;
            }

            .export-btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Table Styles */
        table {
            width: 100%;
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 10px 20px var(--card-shadow);
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
            margin-top: 1rem;
            table-layout: fixed;
        }

        th, td {
            padding: 0.6rem;
            text-align: left;
            font-size: 0.8rem;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
            min-width: 0;
        }

        /* Column widths */
        th:nth-child(1), td:nth-child(1) { width: 10%; } /* Name */
        th:nth-child(2), td:nth-child(2) { width: 5%; }  /* User ID */
        th:nth-child(3), td:nth-child(3) { width: 10%; } /* Service */
        th:nth-child(4), td:nth-child(4) { width: 15%; } /* Location */
        th:nth-child(5), td:nth-child(5) { width: 8%; }  /* Contact */
        th:nth-child(6), td:nth-child(6) { width: 8%; }  /* Date */
        th:nth-child(7), td:nth-child(7) { width: 8%; }  /* Time */
        th:nth-child(8), td:nth-child(8) { width: 12%; } /* Note */
        th:nth-child(9), td:nth-child(9) { width: 8%; }  /* Status */
        th:nth-child(10), td:nth-child(10) { width: 8%; } /* Technician */
        th:nth-child(11), td:nth-child(11) { width: 8%; } /* Actions */

        td {
            line-height: 1.2;
        }

        th {
            background: var(--primary-color);
            color: var(--card-bg);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-size: 0.75rem;
            position: sticky;
            top: 0;
            z-index: 10;
            padding: 1rem;
            white-space: nowrap;
            text-align: left;
        }

        tr:nth-child(even) {
            background: rgba(208, 240, 255, 0.2);
        }

        tbody tr {
            transition: all 0.3s ease;
        }

        tbody tr:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            color: var(--primary-color);
        }

        .button {
            background: var(--primary-color);
            color: white;
            padding: 0.3rem 0.6rem;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .button i {
            font-size: 0.7rem;
        }

        .button:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px var(--card-shadow);
            color: var(--primary-color);
        }

        /* Pagination Styles */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
            padding: 0 1rem;
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
            min-width: 30px;
            height: 30px;
        }

        .pagination-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px var(--card-shadow);
            color: var(--primary-color);
        }

        .pagination-btn.active {
            background: var(--secondary-color);
            color: var(--primary-color);
            font-weight: 600;
        }

        .pagination-btn i {
            font-size: 0.8rem;
        }

        /* Filter Form Styles */
        .filter-container {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 20px var(--card-shadow);
            margin-bottom: 2rem;
        }

        .filter-container h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .filter-form .form-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .filter-form .form-group {
            flex: 1;
        }

        .filter-form label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .filter-form input,
        .filter-form select {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid var(--background-color);
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .filter-form input:focus,
        .filter-form select:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 4px rgba(60, 213, 237, 0.1);
        }

        .filter-form button,
        .filter-form .button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .filter-form button:hover,
        .filter-form .button:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px var(--card-shadow);
            color: var(--primary-color);
        }

        .filter-form button + .button {
            margin-left: 1rem;
            background: #f8f9fa;
            color: var(--text-color);
        }

        .filter-form button + .button:hover {
            background: #e9ecef;
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .filter-form .form-row {
                flex-direction: column;
            }

            .filter-form button,
            .filter-form .button {
                width: 100%;
                justify-content: center;
                margin: 0.5rem 0;
            }

            .filter-form button + .button {
                margin-left: 0;
            }
        }

        @media (max-width: 1200px) {
            th, td {
                padding: 0.4rem;
                font-size: 0.75rem;
            }
            .button {
                padding: 0.3rem 0.5rem;
                font-size: 0.7rem;
            }
            .main h1 {
                font-size: 2rem;
            }
            .tab-link {
                padding: 0.7rem 1.2rem;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 992px) {
            table {
                font-size: 0.75rem;
            }
            th, td {
                padding: 0.4rem;
            }
            .main h1 {
                font-size: 1.8rem;
            }
            .tab-link {
                padding: 0.6rem 1rem;
                font-size: 0.8rem;
            }
            /* Adjust column widths for medium screens */
            th:nth-child(1), td:nth-child(1) { width: 12%; }
            th:nth-child(2), td:nth-child(2) { width: 15%; }
            th:nth-child(3), td:nth-child(3) { width: 18%; }
            th:nth-child(7), td:nth-child(7) { width: 12%; }
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

            .main {
                margin: 0;
                width: 100%;
                padding: 1rem;
                background: #f5f9fc;
                min-height: calc(100vh - 80px);
                flex: 1;
            }

            .main h1 {
                font-size: 1.5rem;
                margin-bottom: 1.25rem;
                color: var(--primary-color);
                font-weight: 600;
                padding: 0;
            }

            .tabs {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
                margin-bottom: 1.25rem;
                padding: 0;
            }

            .tab-link {
                padding: 0.75rem 1.25rem;
                border-radius: 50px;
                font-size: 0.9rem;
                background: white;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                color: var(--text-color);
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .tab-link.active {
                background: var(--primary-color);
                color: white;
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

            /* Service title header */
            td:first-child {
                background: var(--primary-color);
                color: white;
                font-size: 1rem;
                font-weight: 500;
                padding: 1rem;
                margin: -1px;
                border-radius: 12px 12px 0 0;
                border-bottom: none;
                display: flex;
                justify-content: space-between;
                align-items: center;
                position: relative;
                width: calc(100% + 2px);
            }

            /* Left border accent */
            tr::before {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                width: 4px;
                background: var(--secondary-color);
                border-radius: 12px 0 0 12px;
            }

            /* Actions button */
            td:last-child {
                position: absolute;
                top: 0;
                right: 0;
                padding: 0;
                border: none;
                background: none;
                z-index: 1;
                height: 52px;
                display: flex;
                align-items: center;
                padding-right: 1rem;
            }

            td:last-child .button {
                background: transparent;
                color: rgba(255, 255, 255, 0.75);
                width: 32px;
                height: 32px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: color 0.2s ease;
                border: none;
                padding: 0;
            }

            td:last-child .button:hover,
            td:last-child .button:active {
                color: white;
                background: transparent;
                transform: none;
                box-shadow: none;
            }

            td:last-child .button i {
                font-size: 1rem;
            }

            /* Content rows */
            td:not(:first-child):not(:last-child) {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            }

            td:not(:first-child):not(:last-child):last-of-type {
                border-bottom: none;
            }

            td:not(:first-child):not(:last-child):before {
                content: attr(data-label);
                font-weight: 500;
                color: var(--primary-color);
                min-width: 100px;
            }

            /* Status badges */
            td[data-label="Status"] {
                padding: 0.75rem 1rem;
            }

            td[data-label="Status"]:before {
                content: attr(data-label);
                font-weight: 500;
                color: var(--primary-color);
                min-width: 100px;
            }

            td[data-label="Status"] span {
                padding: 0.3rem 0.8rem;
                border-radius: 20px;
                font-size: 0.8rem;
                font-weight: 500;
                text-transform: uppercase;
            }

            td[data-status="Pending"] span {
                background: #fff3cd;
                color: #856404;
            }

            td[data-status="Approved"] span {
                background: #d4edda;
                color: #155724;
            }

            td[data-status="Reschedule Requested"] span {
                background: #cce5ff;
                color: #004085;
            }

            /* Pagination styles */
            .pagination-container {
                margin-top: 1.5rem;
                padding: 1rem;
                background: white;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .pagination-info {
                text-align: center;
                font-size: 0.85rem;
                color: var(--text-color);
                margin-bottom: 1rem;
            }

            .pagination {
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .pagination-btn {
                padding: 0.5rem 0.75rem;
                font-size: 0.85rem;
                min-width: 35px;
                height: 35px;
                border-radius: 8px;
                background: var(--primary-color);
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                transition: all 0.3s ease;
            }

            .pagination-btn:hover,
            .pagination-btn.active {
                background: var(--secondary-color);
                color: var(--primary-color);
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

            .main {
                padding: 0.75rem;
            }

            .main h1 {
                font-size: 1.25rem;
                margin-bottom: 1rem;
            }

            .tab-link {
                padding: 0.6rem 1rem;
                font-size: 0.85rem;
            }

            tr {
                margin-bottom: 0.75rem;
            }

            td:first-child {
                font-size: 0.95rem;
                padding: 0.75rem;
            }

            td:not(:first-child):not(:last-child) {
                padding: 0.6rem 0.75rem;
                font-size: 0.85rem;
            }

            td:not(:first-child):not(:last-child):before {
                min-width: 90px;
                font-size: 0.8rem;
            }

            .pagination-container {
                padding: 0.75rem;
                margin-top: 1rem;
            }

            .pagination-btn {
                padding: 0.4rem 0.6rem;
                min-width: 32px;
                height: 32px;
                font-size: 0.8rem;
            }

            td:last-child {
                padding-right: 0.5rem;
            }

            td:last-child .button {
                width: 28px;
                height: 28px;
            }

            td:last-child .button i {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Air<span>go</span></h2>
        <div class="nav-links">
            <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="admin_bookings.php" class="<?= basename($_SERVER['PHP_SELF']) === 'admin_bookings.php' ? 'active' : '' ?>"><i class="fas fa-calendar-alt"></i> Bookings</a>
            <a href="admin_employees.php" class="<?= basename($_SERVER['PHP_SELF']) === 'admin_employees.php' ? 'active' : '' ?>"><i class="fas fa-users"></i> Employees</a>
            <!-- <a href="admin_register.php" class="<?= basename($_SERVER['PHP_SELF']) === 'admin_register.php' ? 'active' : '' ?>"><i class="fas fa-user-shield"></i> Administrator</a> -->
            <a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="main">
        <h1><i class="fas fa-calendar-check"></i> Manage Bookings</h1>

        <div class="tabs">
            <a href="?tab=all" class="tab-link <?= ($tab == 'all') ? 'active' : '' ?>">
                <i class="fas fa-list"></i> Active Bookings
            </a>
            <a href="?tab=pending" class="tab-link <?= ($tab == 'pending') ? 'active' : '' ?>">
                <i class="fas fa-clock"></i> Pending
            </a>
            <a href="?tab=reschedule" class="tab-link <?= ($tab == 'reschedule') ? 'active' : '' ?>">
                <i class="fas fa-sync"></i> Rescheduled
            </a>
            <a href="?tab=approved" class="tab-link <?= ($tab == 'approved') ? 'active' : '' ?>">
                <i class="fas fa-check-circle"></i> Approved
            </a>
            <a href="?tab=completed" class="tab-link <?= ($tab == 'completed') ? 'active' : '' ?>">
                <i class="fas fa-check-double"></i> Completed
            </a>
            <a href="?tab=cancelled" class="tab-link <?= ($tab == 'cancelled') ? 'active' : '' ?>">
                <i class="fas fa-ban"></i> Cancelled
            </a>
        </div>

        <div class="form-container filter-container">
            <h2>Filter Bookings</h2>
            <form method="GET" class="filter-form">
                <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>">
                <div class="form-row">
                    <div class="form-group">
                        <label for="filter_name">Customer Full Name</label>
                        <input type="text" id="filter_name" name="filter_name" value="<?= htmlspecialchars($_GET['filter_name'] ?? '') ?>" placeholder="Search by customer's full name..."/>
                    </div>
                    <div class="form-group">
                        <label for="filter_service">Service</label>
                        <select id="filter_service" name="filter_service">
                            <option value="">All Services</option>
                            <?php foreach ($services as $service): ?>
                                <option value="<?= htmlspecialchars($service) ?>" <?= (isset($_GET['filter_service']) && $_GET['filter_service'] === $service) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($service) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filter_location">Location</label>
                        <input type="text" id="filter_location" name="filter_location" value="<?= htmlspecialchars($_GET['filter_location'] ?? '') ?>" placeholder="Search by location..."/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>"/>
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>"/>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="filter-btn">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="?tab=<?= htmlspecialchars($tab) ?>" class="reset-btn">
                        <i class="fas fa-undo"></i> Reset Filters
                    </a>
                </div>
            </form>
        </div>

        <?php if ($tab == 'completed'): ?>
            <div class="export-container">
                <a href="export_completed.php" class="export-btn">
                    <i class="fas fa-file-excel"></i>
                    Export to Excel
                </a>
                <a href="#" onclick="exportSalesReport(event)" class="export-btn sales-report-btn">
                    <i class="fas fa-chart-line"></i>
                    Export Sales Report
                </a>
            </div>
        <?php endif; ?>

        <style>
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .filter-btn, .reset-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .filter-btn {
            background: var(--primary-color);
            color: white;
        }

        .reset-btn {
            background: #f8f9fa;
            color: var(--text-color);
        }

        .filter-btn:hover, .reset-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .reset-btn:hover {
            background: #e9ecef;
            color: var(--primary-color);
        }

        .export-container {
            display: flex;
            justify-content: flex-end;
            gap: 2rem;  /* Increased from 1rem to 2rem */
            margin: 1rem 0;
            padding: 0 1rem;
        }

        .export-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1rem;
            background-color: var(--secondary-color);
            color: var(--primary-color);
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .export-btn i {
            font-size: 1rem;
        }

        .sales-report-btn {
            background-color: var(--primary-color);
            color: white;
        }

        .sales-report-btn:hover {
            background-color: #0a4956;
        }

        @media (max-width: 768px) {
            .form-actions {
                flex-direction: column;
            }

            .filter-btn, .reset-btn {
                width: 100%;
                justify-content: center;
            }

            .export-container {
                flex-direction: column;
                padding: 0 0.75rem;
            }

            .export-btn {
                width: 100%;
                justify-content: center;
            }
        }
        </style>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>USER ID</th>
                        <th>SERVICE</th>
                        <th>LOCATION</th>
                        <th>CONTACT</th>
                        <th>DATE</th>
                        <th>TIME</th>
                        <th>NOTE</th>
                        <th>STATUS</th>
                        <?php if ($tab !== 'cancelled'): ?>
                            <th>TECHNICIAN</th>
                            <th>ACTIONS</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td data-label="Name"><?= htmlspecialchars($row['customer_name'] ?? '') ?></td>
                            <td data-label="User ID"><?= htmlspecialchars($row['user_id'] ?? '') ?></td>
                            <td data-label="Service"><?= htmlspecialchars($row['service'] ?? '') ?></td>
                            <td data-label="Location"><?= htmlspecialchars($row['location'] ?? '') ?></td>
                            <td data-label="Contact"><?= htmlspecialchars($row['phone'] ?? '') ?></td>
                            <td data-label="Date"><?= htmlspecialchars($row['appointment_date'] ?? '') ?></td>
                            <td data-label="Time"><?= htmlspecialchars(date('g:i A', strtotime($row['appointment_time'] ?? ''))) ?></td>
                            <td data-label="Note"><?= htmlspecialchars($row['note'] ?? 'No note') ?></td>
                            <td data-label="Status" data-status="<?= htmlspecialchars($row['status'] ?? '') ?>">
                                <span><?= htmlspecialchars($row['status'] ?? '') ?></span>
                            </td>
                            <?php if ($tab !== 'cancelled'): ?>
                                <td data-label="Technician"><?php
                                    if (in_array(strtolower($row['status'] ?? ''), ['rejected', 'cancelled'])) {
                                        echo 'N/A';
                                    } else {
                                        echo $row['employee_name'] ? htmlspecialchars($row['employee_name']) : 'To be assigned';
                                    }
                                ?></td>
                                <td data-label="Actions">
                                    <button class="button edit-btn" onclick="openEditModal(<?= htmlspecialchars(json_encode($row)) ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($tab === 'completed' && !empty($row['payment_proof'])): ?>
                                        <button class="button view-image-btn" onclick="openImageModal('../<?= htmlspecialchars($row['payment_proof']) ?>')">
                                            <i class="fas fa-image"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="<?= ($tab === 'cancelled' ? '9' : '11') ?>" style="text-align:center;">No bookings found.</td></tr>
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
                    <a href="?tab=<?= $tab ?>&page=1<?= !empty($filter_name) ? '&filter_name='.urlencode($filter_name) : '' ?><?= !empty($filter_service) ? '&filter_service='.urlencode($filter_service) : '' ?><?= !empty($filter_location) ? '&filter_location='.urlencode($filter_location) : '' ?>" class="pagination-btn">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                    <a href="?tab=<?= $tab ?>&page=<?= $page - 1 ?><?= !empty($filter_name) ? '&filter_name='.urlencode($filter_name) : '' ?><?= !empty($filter_service) ? '&filter_service='.urlencode($filter_service) : '' ?><?= !empty($filter_location) ? '&filter_location='.urlencode($filter_location) : '' ?>" class="pagination-btn">
                        <i class="fas fa-angle-left"></i>
                    </a>
                <?php endif; ?>

                <?php
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++):
                ?>
                    <a href="?tab=<?= $tab ?>&page=<?= $i ?><?= !empty($filter_name) ? '&filter_name='.urlencode($filter_name) : '' ?><?= !empty($filter_service) ? '&filter_service='.urlencode($filter_service) : '' ?><?= !empty($filter_location) ? '&filter_location='.urlencode($filter_location) : '' ?>" class="pagination-btn <?= $i == $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?tab=<?= $tab ?>&page=<?= $page + 1 ?><?= !empty($filter_name) ? '&filter_name='.urlencode($filter_name) : '' ?><?= !empty($filter_service) ? '&filter_service='.urlencode($filter_service) : '' ?><?= !empty($filter_location) ? '&filter_location='.urlencode($filter_location) : '' ?>" class="pagination-btn">
                        <i class="fas fa-angle-right"></i>
                    </a>
                    <a href="?tab=<?= $tab ?>&page=<?= $total_pages ?><?= !empty($filter_name) ? '&filter_name='.urlencode($filter_name) : '' ?><?= !empty($filter_service) ? '&filter_service='.urlencode($filter_service) : '' ?><?= !empty($filter_location) ? '&filter_location='.urlencode($filter_location) : '' ?>" class="pagination-btn">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Edit Booking Modal -->
        <div class="modal" id="editBookingModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2><i class="fas fa-edit"></i> Edit Booking</h2>
                    <button class="close-btn" onclick="closeEditModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editBookingForm" method="POST">
                        <input type="hidden" id="booking_id" name="booking_id">
                        <div class="form-group">
                            <label for="username">Customer Name</label>
                            <input type="text" id="username" name="username" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="service">Service</label>
                            <input type="text" id="service" name="service" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" id="location" name="location" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="phone">Contact</label>
                            <input type="text" id="phone" name="phone" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="appointment_date">Appointment Date</label>
                            <input type="date" id="appointment_date" name="appointment_date" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="appointment_time">Appointment Time</label>
                            <input type="time" id="appointment_time" name="appointment_time" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                <option value="Pending">Pending</option>
                                <option value="Approved">Approved</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="employee_id">Assign Technician</label>
                            <select id="employee_id" name="employee_id">
                                <option value="">Select Technician</option>
                                <?php
                                $employees_sql = "SELECT id, name FROM employees WHERE status != 'Inactive'";
                                $employees_result = $conn->query($employees_sql);
                                while ($employee = $employees_result->fetch_assoc()):
                                ?>
                                <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="note">Customer Notes</label>
                            <textarea id="note" name="note" rows="3" readonly></textarea>
                        </div>
                        <div class="form-group payment-proof-group" style="display: none;">
                            <label for="payment_proof">Proof of Payment</label>
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
                            <button type="button" class="save-btn" onclick="handleSaveClick()">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <button type="button" class="cancel-btn" onclick="closeEditModal()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="confirmation-modal" id="confirmationModal">
        <div class="confirmation-content">
            <div class="confirmation-header">
                <h3><i class="fas fa-question-circle"></i> Confirm Action</h3>
                <button class="close-btn" onclick="closeConfirmationModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="confirmation-body">
                <p id="confirmationMessage"></p>
            </div>
            <div class="confirmation-actions">
                <button class="confirm-btn" id="confirmActionBtn">
                    <i class="fas fa-check"></i> Confirm
                </button>
                <button class="cancel-btn" onclick="closeConfirmationModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="image-modal" id="imageModal">
        <div class="image-modal-content">
            <div class="image-modal-header">
                <h3><i class="fas fa-image"></i> Payment Proof</h3>
                <button class="close-btn" onclick="closeImageModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="image-modal-body">
                <img id="modalImage" src="" alt="Payment Proof">
            </div>
        </div>
    </div>

    <style>
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

        /* Update the modal backdrop styles */
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

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        /* Style for readonly inputs */
        .form-group input[readonly],
        .form-group textarea[readonly] {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            cursor: not-allowed;
            opacity: 0.8;
        }

        /* Style for editable fields */
        .form-group select#status,
        .form-group select#employee_id {
            background-color: white;
            border: 1px solid var(--secondary-color);
            cursor: pointer;
        }

        .form-group select#status:focus,
        .form-group select#employee_id:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 2px rgba(60, 213, 237, 0.2);
            outline: none;
        }

        .form-group textarea[readonly] {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 0.75rem;
            width: 100%;
            resize: vertical;
            min-height: 80px;
            color: var(--text-color);
            cursor: default;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
        }

        .save-btn,
        .cancel-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .save-btn {
            background: var(--primary-color);
            color: white;
        }

        .save-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        .cancel-btn {
            background: #f8f9fa;
            color: #495057;
        }

        .cancel-btn:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                margin: 10px auto;
            }

            .modal-body {
                padding: 1.5rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .save-btn,
            .cancel-btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Confirmation Modal Styles */
        .confirmation-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .confirmation-modal.active {
            display: flex !important;
        }

        .confirmation-content {
            background: white;
            padding: 0;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: relative;
            transform: scale(0.7);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .confirmation-modal.active .confirmation-content {
            transform: scale(1);
            opacity: 1;
        }

        .confirmation-header {
            background: var(--primary-color);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .confirmation-header h3 {
            margin: 0;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .confirmation-body {
            padding: 1.5rem;
            text-align: center;
            color: var(--text-color);
        }

        .confirmation-actions {
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
            background: #f8f9fa;
            border-radius: 0 0 12px 12px;
        }

        .confirm-btn,
        .confirmation-actions .cancel-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .confirm-btn {
            background: var(--primary-color);
            color: white;
        }

        .confirm-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        .confirmation-actions .cancel-btn {
            background: #e9ecef;
            color: #495057;
        }

        .confirmation-actions .cancel-btn:hover {
            background: #dee2e6;
            transform: translateY(-2px);
        }

        @media (max-width: 576px) {
            .confirmation-content {
                width: 95%;
                margin: 1rem;
            }
        }

        /* Image Modal Styles */
        .image-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .image-modal.active {
            display: flex;
        }

        .image-modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow: hidden;
            position: relative;
            transform: scale(0.7);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .image-modal.active .image-modal-content {
            transform: scale(1);
            opacity: 1;
        }

        .image-modal-header {
            background: var(--primary-color);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .image-modal-header h3 {
            margin: 0;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .image-modal-body {
            padding: 1rem;
            overflow: auto;
            max-height: calc(90vh - 60px);
        }

        .image-modal-body img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .image-modal-content {
                width: 95%;
            }

            td[data-label="Actions"] {
                justify-content: flex-end;
            }
        }
    </style>

    <script>
        function openEditModal(booking) {
            document.getElementById('booking_id').value = booking.id;
            document.getElementById('username').value = booking.customer_name;
            document.getElementById('service').value = booking.service;
            document.getElementById('location').value = booking.location;
            document.getElementById('phone').value = booking.phone;
            document.getElementById('appointment_date').value = booking.appointment_date;
            document.getElementById('appointment_time').value = booking.appointment_time;
            document.getElementById('status').value = booking.status;
            document.getElementById('employee_id').value = booking.employee_id || '';
            document.getElementById('note').value = booking.note || '';

            // Show/hide payment proof field based on status
            const paymentProofGroup = document.querySelector('.payment-proof-group');
            if (booking.status === 'Completed') {
                paymentProofGroup.style.display = 'block';
            } else {
                paymentProofGroup.style.display = 'none';
            }

            // Show existing payment proof if available
            const imagePreview = document.getElementById('image-preview');
            if (booking.payment_proof && booking.status === 'Completed') {
                // Add a timestamp to prevent caching
                const timestamp = new Date().getTime();
                const imagePath = `../${booking.payment_proof}?t=${timestamp}`;
                imagePreview.innerHTML = `<img src="${imagePath}" alt="Payment Proof">`;
                imagePreview.style.display = 'block';
                
                // Log the image path for debugging
                console.log('Existing image path:', imagePath);
            } else {
                imagePreview.style.display = 'none';
                imagePreview.innerHTML = '';
            }

            const modal = document.getElementById('editBookingModal');
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('active'), 10);
        }

        function handleSaveClick() {
            console.log('Save button clicked'); // Debug log
            const confirmModal = document.getElementById('confirmationModal');
            const confirmMessage = document.getElementById('confirmationMessage');
            
            if (!confirmModal || !confirmMessage) {
                console.error('Modal elements not found:', {
                    modal: confirmModal,
                    message: confirmMessage
                });
                return;
            }

            // Set the message
            confirmMessage.textContent = 'Are you sure you want to update this booking?';
            
            // Show the modal
            confirmModal.style.display = 'flex'; // Changed from 'block' to 'flex'
            // Force a reflow
            confirmModal.offsetHeight;
            confirmModal.classList.add('active');

            // Set up confirm button handler
            const confirmBtn = document.getElementById('confirmActionBtn');
            if (confirmBtn) {
                confirmBtn.onclick = submitBookingForm;
            } else {
                console.error('Confirm button not found');
            }
        }

        async function submitBookingForm() {
            closeConfirmationModal();
            
            const form = document.getElementById('editBookingForm');
            const formData = new FormData(form);

            try {
                const response = await fetch('update_booking.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                console.log('Server response:', result);

                if (result.success) {
                    // If there's a new payment proof path in the response, update the preview
                    if (result.payment_proof) {
                        const imagePreview = document.getElementById('image-preview');
                        // Add a timestamp to prevent caching
                        const timestamp = new Date().getTime();
                        imagePreview.innerHTML = `<img src="../${result.payment_proof}?t=${timestamp}" alt="Payment Proof">`;
                        imagePreview.style.display = 'block';
                        
                        // Update the file name display
                        const fileNameDisplay = document.querySelector('.file-name');
                        const fileName = result.payment_proof.split('/').pop();
                        fileNameDisplay.textContent = fileName;

                        // Log the image path for debugging
                        console.log('New image path:', `../${result.payment_proof}`);
                    }
                    
                    showSuccessMessage('Booking updated successfully!');
                    // Increase the timeout to give more time to see the preview
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showErrorMessage(result.message || 'Error updating booking');
                }
            } catch (error) {
                console.error('Error:', error);
                showErrorMessage('An error occurred. Please check the console for details.');
            }
        }

        function closeEditModal() {
            const modal = document.getElementById('editBookingModal');
            modal.classList.remove('active');
            setTimeout(() => modal.style.display = 'none', 300);
        }

        function showConfirmationModal(message, onConfirm) {
            const modal = document.getElementById('confirmationModal');
            const messageEl = document.getElementById('confirmationMessage');
            const confirmBtn = document.getElementById('confirmActionBtn');
            
            messageEl.textContent = message;
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('active'), 10);

            // Remove any existing click handler
            confirmBtn.replaceWith(confirmBtn.cloneNode(true));
            
            // Add new click handler
            document.getElementById('confirmActionBtn').addEventListener('click', () => {
                closeConfirmationModal();
                onConfirm();
            });
        }

        function closeConfirmationModal() {
            const modal = document.getElementById('confirmationModal');
            if (modal) {
                modal.classList.remove('active');
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 300);
            }
        }

        function showSuccessMessage(message) {
            showConfirmationModal(message);
            setTimeout(() => {
                closeConfirmationModal();
                window.location.reload();
            }, 1500);
        }

        function showErrorMessage(message) {
            showConfirmationModal('Error: ' + message);
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

        function exportSalesReport(event) {
            event.preventDefault();
            
            // Get date range values
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            // Validate date range
            if (!startDate || !endDate) {
                alert('Please select both start and end dates for the sales report.');
                return;
            }
            
            if (startDate > endDate) {
                alert('Start date cannot be later than end date.');
                return;
            }
            
            // Generate the URL with date parameters
            const url = `../generate_sales_report.php?start_date=${startDate}&end_date=${endDate}`;
            
            // Redirect to the report generation script
            window.location.href = url;
        }

        function openImageModal(imagePath) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            
            modalImage.src = imagePath;
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('active'), 10);
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.remove('active');
            setTimeout(() => {
                modal.style.display = 'none';
                document.getElementById('modalImage').src = '';
            }, 300);
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const editModal = document.getElementById('editBookingModal');
            const confirmationModal = document.getElementById('confirmationModal');
            const imageModal = document.getElementById('imageModal');
            if (event.target === editModal) {
                closeEditModal();
            }
            if (event.target === confirmationModal) {
                closeConfirmationModal();
            }
            if (event.target === imageModal) {
                closeImageModal();
            }
        }

        // Add this function to handle status change
        document.getElementById('status').addEventListener('change', function() {
            const paymentProofGroup = document.querySelector('.payment-proof-group');
            if (this.value === 'Completed') {
                paymentProofGroup.style.display = 'block';
            } else {
                paymentProofGroup.style.display = 'none';
                // Clear the file input and preview when status is not completed
                document.getElementById('payment_proof').value = '';
                document.getElementById('image-preview').style.display = 'none';
                document.getElementById('image-preview').innerHTML = '';
                document.querySelector('.file-name').textContent = 'No file chosen';
            }
        });
    </script>
</body>
</html>

