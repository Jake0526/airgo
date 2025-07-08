<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone_number = $_POST['phone_number'] ?? '';
$location = $_POST['location'] ?? '';
$service = $_POST['service'] ?? '';
$appointment_date = $_POST['appointment_date'] ?? '';
$appointment_time = $_POST['appointment_time'] ?? '';
$service_price = $_POST['service_price'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
    <style>
        body { font-family: Arial; background: #f0f8ff; padding: 30px; }
        .box { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px #ccc; max-width: 600px; margin: auto; }
    </style>
</head>
<body>
    <div class="box">


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Booking Confirmation</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Playfair+Display:wght@500&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #d0f0ff;
      min-height: 100vh;
      padding-top: 70px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    header {
      background: #07353f;
      padding: 10px 0;
      width: 100%;
      position: fixed;
      top: 0;
      left: 0;
      z-index: 999;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }

    nav.container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1100px;
      margin: 0 auto;
      padding: 0 24px;
    }

    .logo h1 {
      color: #CACBBB;
      font-family: 'Playfair Display', Georgia, serif;
      font-weight: 500;
      letter-spacing: 3px;
      font-size: 1.8rem;
      margin: 0;
      cursor: default;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
      text-transform: uppercase;
    }

    .back-button-wrapper {
      margin-left: auto;
    }

    .btn-Back {
      position: relative;
      padding: 10px 20px;
      color: #07353f;
      background-color: #d0f0ff;
      border-radius: 25px;
      font-weight: 500;
      font-family: 'Poppins', sans-serif;
      text-decoration: none;
      letter-spacing: 1.2px;
      transition: background-color 0.3s ease;
      overflow: hidden;
      display: inline-block;
    }

    .btn-Back .underline {
      content: '';
      position: absolute;
      bottom: 8px;
      left: 20%;
      width: 60%;
      height: 2px;
      background-color: #07353f;
      transform: scaleX(0);
      transform-origin: center;
      transition: transform 0.3s ease-in-out;
    }

    .btn-Back:hover {
      background-color: #d6cec6;
    }

    .btn-Back:hover .underline {
      transform: scaleX(1);
    }

    .confirmation-container {
      background-color: #d0f0ff;
      padding: 30px 25px;
      border-radius: 16px;
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
      max-width: 400px;
      width: 100%;
      text-align: center;
      animation: fadeIn 0.7s ease-in-out;
      margin-top: 30px;
    }

    .confirmation-container h1 {
      font-size: 2rem;
      color: #07353f;
      margin-bottom: 20px;
    }

    .confirmation-container p {
      font-size: 1.05rem;
      margin: 8px 0;
      color: #444;
    }

    .highlight {
      font-weight: 600;
      color: #222;
    }

    .button-container {
      margin-top: 30px;
      display: flex;
      flex-direction: column;
      gap: 15px;
      align-items: center;
    }

    button {
      padding: 12px 20px;
      font-size: 1rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      width: 100%;
      max-width: 280px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .confirm-button {
      background-color: #07353f;
      color: #fff;
    }

    .confirm-button:hover {
      background-color: #0a6271;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(30px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 600px) {
      .confirmation-container {
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>

<!-- Header -->
<header>
  <nav class="container">
    <div class="logo">
      <h1>Airgo</h1>
    </div>
    <div class="back-button-wrapper">
      <a href="booking.php" class="btn-Back">
        Back
        <span class="underline"></span>
      </a>
    </div>
  </nav>
</header>

<!-- Confirmation Box -->
<div class="confirmation-container">
  <h1>Booking Confirmation</h1>
  <p><span class="highlight">Name:</span> <?= $name ?></p>
  <p><span class="highlight">Email:</span> <?= $email ?></p>
  <p><span class="highlight">Location:</span> <?= $location ?></p>
  <p><span class="highlight">Contact Number:</span> <?= $phone_number ?></p>
  <p><span class="highlight">Service:</span> <?= $service ?></p>
  <p><span class="highlight">Price:</span> <?= $service_price ?></p>
  <p><span class="highlight">Date:</span> <?= $appointment_date ?></p>
  <p><span class="highlight">Time:</span> <?= date("h:i A", strtotime($appointment_time)) ?></p>

  <div class="button-container">
    <form action="confirmed_booking.php" method="POST">
  <input type="hidden" name="name" value="<?= $name ?>">
  <input type="hidden" name="email" value="<?= $email ?>">
  <input type="hidden" name="appointment_date" value="<?= $appointment_date ?>">
  <input type="hidden" name="appointment_time" value="<?= $appointment_time ?>">
  <input type="hidden" name="service" value="<?= $service ?>">
  <input type="hidden" name="location" value="<?= $location ?>">
  <input type="hidden" name="phone_number" value="<?= $phone_number ?>">
  <input type="hidden" name="service_price" value="<?= $service_price ?>"> <!-- ✅ ADD THIS -->
  <button type="submit" class="confirm-button">Submit</button>
</form>

    </form>
  </div>
</div>

</body>
</html>
