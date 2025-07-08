<?php
// You can include PHP code for dynamic content or user session handling here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Airgo - Booking System</title>

    <!-- Inline CSS -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color:  #d0f0ff;
            color: #333;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .container {
            margin-left: 2in;
            margin-right: 2in;
        }

        header {
            background-color: #07353f;
            color: white;
            padding: 20px 0;
        }

        header nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        header .logo h1 {
            margin: 0;
            font-size: 1.8em;
        }

        header .login-button a {
            background-color: #3cd5ed;
            padding: 10px 70px;
            border-radius: 1.8px;
            color: white;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        header .login-button a:hover {
            background-color: #07353f;
        }

        #business {
            background-color: #CACBBB;
            color: #07353f;
            text-align: center;
            padding: 1px 2px;
        }

        #business h2 {
            font-size: 40px;
        }

        #business p {
            font-size: 20px;
            margin: 10px 0;
        }

        #business button {
            background-color: #07353f;
            border: none;
            padding: 10px 20px;
            color: white;
            font-size: 1.1em;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #business button a {
            color: white;
        }

        #business button:hover {
            background-color: #3cd5ed;
        }

        #slideshow {
            position: relative;
            width: 100%;
            overflow: hidden;
            margin: 0;
        }

        .slides {
            display: flex;
            transition: transform 1s ease;
        }

        .slide {
            display: flex;
            justify-content: center;
            gap: 0;
            min-width: 100%;
            height: auto;
        }

        .slide img {
            width: 300px;
            height: 300px;
            margin: 0;
            object-fit: cover;
        }

        #available-officers {
            color: #07353f;
            margin-top: 30px;
        }

        #available-officers h2 {
            text-align: center;
            font-size: 2em;
        }

        .officer-category {
            background-color: #07353f;
            color: white;
            padding: 20px;
            border-radius: 10px;
            width: 200px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .officer-category h3 {
            margin-bottom: 10px;
            font-size: 1.2em;
            color: #3cd5ed;
        }

        .officer-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        #facebook-page {
            margin: 20px 0;
        }

        #facebook-page .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
        }

        #facebook-page .info {
            flex: 1;
        }

        #facebook-page .info h4 {
            margin-bottom: 10px;
            color: #07353f;
        }

        #facebook-page .info p,
        #facebook-page .info ul {
            color: #333;
            margin: 5px 0;
        }

        #facebook-page .info ul {
            list-style: none;
            padding-left: 0;
            margin-bottom: 15px;
        }

        #facebook-page .image {
            flex: 1;
            max-width: 300px;
        }

        #facebook-page .image img {
            width: 100%;
            border-radius: 10px;
            object-fit: cover;
            display: block;
        }

        #how-it-works {
            padding: 20px 0;
            text-align: center;
        }

        #how-it-works h2 {
            font-size: 1.9em;
            margin-bottom: 10px;
            color: #07353f;
        }

        .steps {
            display: flex;
            justify-content: space-around;
            margin-top: 3px;
        }

        .step {
            background-color: #07353f;
            padding: 20px;
            border-radius: 10px;
            width: 15%;
            box-shadow: 0 2px 5px rgba(245, 243, 243, 0.1);
        }

        .step h3 {
            font-size: 1.5em;
            color: #CACBBB;
        }

        .step p {
            color: skyblue;
        }

        #testimonials {
            background-color: #CACBBB;
            padding: 50px 0;
            text-align: center;
        }

        #testimonials h2 {
            font-size: 2em;
            margin-bottom: 20px;
            color: #07353f;
        }

        .testimonial {
            background-color: #07353f;
            margin-bottom: 20px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .testimonial p {
            font-style: italic;
            color: skyblue;
        }

        .testimonial h4 {
            font-weight: bold;
            color: #07353f;
        }

        footer {
            background-color: #07353f;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 30px;
        }

        footer p {
            color: skyblue;
        }
    </style>
</head>
<body>

    <!-- Header -->
<header style="background: #07353f; padding: 10px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
  <nav class="container" style="display: flex; justify-content: space-between; align-items: center; max-width: 1100px; margin: 0 auto; padding: 0 24px;">
    <div class="logo">
      <h1 style="
        color: #CACBBB; 
        font-family: 'Playfair Display', Georgia, serif; 
        font-weight: 100; 
        font-style: normal; 
        letter-spacing: 3px; 
        font-size: 1.3rem; 
        margin: 0; 
        cursor: default;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        text-transform: uppercase;
        ">
        Airgo
      </h1>
    </div>
    <div class="login-button">
      <a href="login.php" class="btn-login" style="position: relative; color: #07353f; background-color:  #d0f0ff; padding: 10px 26px; border-radius: 25px; font-weight: 600; font-family: 'Poppins', sans-serif; text-transform: uppercase; letter-spacing: 1.2px; text-decoration: none; overflow: hidden; display: inline-block; transition: background-color 0.3s ease;">
        Login
        <span class="underline"></span>
      </a>
    </div>
  </nav>
</header>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@600&family=Playfair+Display:wght@900&display=swap');

  .btn-login {
    cursor: pointer;
  }

  .btn-login .underline {
    position: absolute;
    bottom: 8px;
    left: 20%;
    width: 60%;
    height: 3px;
    background-color: #07353f;
    border-radius: 2px;
    transform: scaleX(0);
    transform-origin: center;
    transition: transform 0.35s ease;
  }

  .btn-login:hover {
    background-color: #d6cec6;
    color: #07353f;
  }

  .btn-login:hover .underline {
    transform: scaleX(1);
  }
</style>


  <!-- Business Section -->
<section id="business" style="padding: 20px 5px; background:  #d0f0ff; text-align: center; font-family: 'Poppins', Arial, sans-serif;">
  <div class="container" style="max-width: 500px; margin: 0 auto;">
    <h2 style="
      font-family: 'Playfair Display', serif; 
      font-size: 1.5rem; 
      color: #07353f; 
      margin-bottom: 5px; 
      border-bottom: 1px solid #c9d6d8; 
      display: inline-block; 
      padding-bottom: 5px; 
      letter-spacing: 1.2px;
      font-weight: 500;
      ">
      Welcome to Airgo
    </h2>
    <p style="
      font-family: 'Poppins', sans-serif; 
      font-size: 1.15rem; 
      color: #444444; 
      line-height: 1.6; 
      max-width: 500px; 
      margin: 0 auto;
      font-weight: 500;
      ">
      Your reliable booking platform for all your services needs.
    </p>
  </div>
</section>

<!-- Include Google Fonts -->
<style>
  @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@900&family=Poppins:wght@400;500&display=swap');
</style>



    <!-- Slideshow -->
    <section id="slideshow" class="container">
        <div class="slides">
            <?php for ($i = 1; $i <= 25; $i += 3): ?>
                <?php if ($i >= 21 && $i <= 25) continue; ?>
                <div class="slide">
                    <img src="images/<?php echo $i; ?>.jpg" alt="Slide <?php echo $i; ?>" />
                    <img src="images/<?php echo $i + 1; ?>.jpg" alt="Slide <?php echo $i + 1; ?>" />
                    <img src="images/<?php echo $i + 2; ?>.jpg" alt="Slide <?php echo $i + 2; ?>" />
                </div>
            <?php endfor; ?>
        </div>
    </section>



<!-- Video Section -->
<section id="service-video" class="container" style="margin-top: 40px; max-width: 640px; margin-left: auto; margin-right: auto;">
  <div style="position: relative; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 20px rgba(7, 53, 63, 0.3); cursor: pointer;">
    <video
      id="serviceVideo"
      width="100%"
      height="360"
      muted
      playsinline
      style="object-fit: cover; display: block; transition: filter 0.3s ease;"
      controls
      preload="metadata"
    >
      <source src="video.mp4" type="video/mp4" />
      Your browser does not support the video tag.
    </video>
    <div
      id="playOverlay"
      style="
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 80px;
        height: 80px;
        background: rgba(7, 53, 63, 0.7);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        transition: opacity 0.3s ease;
      "
    >
      <svg width="36" height="36" viewBox="0 0 24 24" fill="#fff" xmlns="http://www.w3.org/2000/svg">
        <path d="M8 5v14l11-7z"/>
      </svg>
    </div>
  </div>
</section>

<script>
  const video = document.getElementById('serviceVideo');
  const playOverlay = document.getElementById('playOverlay');

  // Hide play overlay when video is playing
  video.addEventListener('play', () => {
    playOverlay.style.opacity = '0';
  });
  // Show overlay when paused or ended
  video.addEventListener('pause', () => {
    playOverlay.style.opacity = '1';
  });
  video.addEventListener('ended', () => {
    playOverlay.style.opacity = '1';
  });

  // Clicking overlay toggles play/pause
  playOverlay.parentElement.addEventListener('click', () => {
    if (video.paused) {
      video.play();
    } else {
      video.pause();
    }
  });
</script>



    <!-- Available Services -->
<section id="available-officers" class="container" style="padding: 20px 5px; background: #; font-family: 'Poppins', sans-serif;">
  <h2 style="
    text-align: center;
    font-size: 1.8rem;
    color: #07353f;
    margin-bottom: 30px;
    font-weight: 400;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.05);
  ">Available Services</h2>

  <div style="
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 20px;
    max-width: 900px;
    margin: 0 auto;
  ">
    <!-- Service Card Template -->
    <div class="officer-category" style="
      background: linear-gradient(145deg, #e9f0f1, #c9d6d8);
      flex: 1 1 220px;
      border-radius: 18px;
      box-shadow: 8px 8px 20px #b1babf, -8px -8px 20px #ffffff;
      padding: 25px 20px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
    " onmouseover="this.style.transform='scale(1.06)'; this.style.boxShadow='12px 12px 28px #a2abb0, -12px -12px 28px #ffffff';" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='8px 8px 20px #b1babf, -8px -8px 20px #ffffff';">
      <h3 style="
        font-size: 1.8rem;
        color: #07353f;
        margin-bottom: 15px;
        position: relative;
        font-weight: 700;
      ">
        <!-- Icon placeholder before text -->
        <span style="font-size: 2.5rem; position: absolute; left: 20px; top: 10px;">🧹</span>
        Cleaning
      </h3>
      <ul class="officer-list" style="list-style: none; padding-left: 0; margin-top: 10px; color: #344047; font-weight: 500;">
        <li>Aircon filter cleaning</li>
        <li>Coil cleaning</li>
        <li>Vent cleaning</li>
      </ul>
    </div>

    <div class="officer-category" style="
      background: linear-gradient(145deg, #e9f0f1, #c9d6d8);
      flex: 1 1 220px;
      border-radius: 18px;
      box-shadow: 8px 8px 20px #b1babf, -8px -8px 20px #ffffff;
      padding: 25px 20px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
    " onmouseover="this.style.transform='scale(1.06)'; this.style.boxShadow='12px 12px 28px #a2abb0, -12px -12px 28px #ffffff';" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='8px 8px 20px #b1babf, -8px -8px 20px #ffffff';">
      <h3 style="
        font-size: 1.8rem;
        color: #07353f;
        margin-bottom: 15px;
        position: relative;
        font-weight: 700;
      ">
        <span style="font-size: 2.5rem; position: absolute; left: 20px; top: 10px;">🔍</span>
        Check-up
      </h3>
      <ul class="officer-list" style="list-style: none; padding-left: 0; margin-top: 10px; color: #344047; font-weight: 500;">
        <li>Performance diagnostics</li>
        <li>Leak inspection</li>
        <li>Energy efficiency check</li>
      </ul>
    </div>

    <div class="officer-category" style="
      background: linear-gradient(145deg, #e9f0f1, #c9d6d8);
      flex: 1 1 220px;
      border-radius: 18px;
      box-shadow: 8px 8px 20px #b1babf, -8px -8px 20px #ffffff;
      padding: 25px 20px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
    " onmouseover="this.style.transform='scale(1.06)'; this.style.boxShadow='12px 12px 28px #a2abb0, -12px -12px 28px #ffffff';" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='8px 8px 20px #b1babf, -8px -8px 20px #ffffff';">
      <h3 style="
        font-size: 1.8rem;
        color: #07353f;
        margin-bottom: 15px;
        position: relative;
        font-weight: 700;
      ">
        <span style="font-size: 2.5rem; position: absolute; left: 20px; top: 10px;">⚙️</span>
        Installation
      </h3>
      <ul class="officer-list" style="list-style: none; padding-left: 0; margin-top: 10px; color: #344047; font-weight: 500;">
        <li>New unit installation</li>
        <li>System setup</li>
        <li>Calibration</li>
      </ul>
    </div>

    <div class="officer-category" style="
      background: linear-gradient(145deg, #e9f0f1, #c9d6d8);
      flex: 1 1 220px;
      border-radius: 18px;
      box-shadow: 8px 8px 20px #b1babf, -8px -8px 20px #ffffff;
      padding: 25px 20px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
    " onmouseover="this.style.transform='scale(1.06)'; this.style.boxShadow='12px 12px 28px #a2abb0, -12px -12px 28px #ffffff';" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='8px 8px 20px #b1babf, -8px -8px 20px #ffffff';">
      <h3 style="
        font-size: 1.8rem;
        color: #07353f;
        margin-bottom: 15px;
        position: relative;
        font-weight: 700;
      ">
        <span style="font-size: 2.5rem; position: absolute; left: 20px; top: 10px;">🚚</span>
        Relocations
      </h3>
      <ul class="officer-list" style="list-style: none; padding-left: 0; margin-top: 10px; color: #344047; font-weight: 500;">
        <li>Unit moving</li>
        <li>Re-installation</li>
        <li>Safety check</li>
      </ul>
    </div>

    <div class="officer-category" style="
      background: linear-gradient(145deg, #e9f0f1, #c9d6d8);
      flex: 1 1 220px;
      border-radius: 18px;
      box-shadow: 8px 8px 20px #b1babf, -8px -8px 20px #ffffff;
      padding: 25px 20px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
    " onmouseover="this.style.transform='scale(1.06)'; this.style.boxShadow='12px 12px 28px #a2abb0, -12px -12px 28px #ffffff';" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='8px 8px 20px #b1babf, -8px -8px 20px #ffffff';">
      <h3 style="
        font-size: 1.8rem;
        color: #07353f;
        margin-bottom: 15px;
        position: relative;
        font-weight: 700;
      ">
        <span style="font-size: 2.5rem; position: absolute; left: 20px; top: 10px;">🔧</span>
        Repair
      </h3>
      <ul class="officer-list" style="list-style: none; padding-left: 0; margin-top: 10px; color: #344047; font-weight: 500;">
        <li>Leak repairs</li>
        <li>Component replacement</li>
        <li>Emergency service</li>
      </ul>
    </div>

  </div>
</section>



<script>
    const video = document.getElementById('serviceVideo');

    // Intersection Observer callback
    const observerCallback = (entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                video.play();
            } else {
                video.pause();
            }
        });
    };

    // Create observer
    const observer = new IntersectionObserver(observerCallback, {
        threshold: 0.5 // play when 50% of video is visible
    });

    observer.observe(video);
</script>




  <section id="how-it-works" style="background:  #d0f0ff; padding: 20px 5px; font-family: 'Montserrat', sans-serif; margin-left: 2in; margin-right: 2in;"> 
  <div class="container" style="max-width: 900px; margin: auto; position: relative;">
    <h2 style="
      text-align: center;
      font-size: 1.8rem;
      color: #07353f;
      margin-bottom: 30px;
      font-weight: 400;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    ">How It Works</h2>

    <div class="steps" style="display: flex; justify-content: space-between; gap: 40px; position: relative; z-index: 1; flex-wrap: wrap;">

      <!-- Step 1 -->
      <div class="step" style="
        background: linear-gradient(145deg, #e9f0f1, #c9d6d8);
        flex: 1 1 280px;
        padding: 30px 25px 35px;
        border-radius: 15px;
        box-shadow: 7px 7px 15px #a8b0b2, -7px -7px 15px #ffffff;
        text-align: center;
        position: relative;
        transition: all 0.35s ease;
        cursor: pointer;
      "
      onmouseover="this.style.transform='scale(1.08)'; this.style.boxShadow='10px 10px 20px #9bb0b2, -10px -10px 20px #ffffff';"
      onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='7px 7px 15px #a8b0b2, -7px -7px 15px #ffffff';"
      >
        <div style="
          width: 50px;
          height: 50px;
          background: #07353f;
          border-radius: 50%;
          color: #fff;
          font-weight: 500;
          font-size: 1.8rem;
          display: flex;
          justify-content: center;
          align-items: center;
          position: absolute;
          top: -20px;
          left: 50%;
          transform: translateX(-50%);
          box-shadow: 0 0 12px #07353f88;
        ">1</div>
        <h3 style="margin-top: 40px; font-size: 1.7rem; color: #07353f;">Create Account</h3>
        <p style="color: #2f3e44; font-weight: 300; margin-top: 15px; font-size: 1.1rem;">
          Sign up for an account and log in securely.
        </p>
      </div>

      <!-- Step 2 -->
      <div class="step" style="
        background: linear-gradient(145deg, #e9f0f1, #c9d6d8);
        flex: 1 1 280px;
        padding: 30px 25px 35px;
        border-radius: 15px;
        box-shadow: 7px 7px 15px #a8b0b2, -7px -7px 15px #ffffff;
        text-align: center;
        position: relative;
        transition: all 0.35s ease;
        cursor: pointer;
      "
      onmouseover="this.style.transform='scale(1.08)'; this.style.boxShadow='10px 10px 20px #9bb0b2, -10px -10px 20px #ffffff';"
      onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='7px 7px 15px #a8b0b2, -7px -7px 15px #ffffff';"
      >
        <div style="
          width: 50px;
          height: 50px;
          background: #07353f;
          border-radius: 50%;
          color: #fff;
          font-weight: 500;
          font-size: 1.8rem;
          display: flex;
          justify-content: center;
          align-items: center;
          position: absolute;
          top: -20px;
          left: 50%;
          transform: translateX(-50%);
          box-shadow: 0 0 12px #07353f88;
        ">2</div>
        <h3 style="margin-top: 40px; font-size: 1.7rem; color: #07353f;">Browse Services</h3>
        <p style="color: #2f3e44; font-weight: 300; margin-top: 15px; font-size: 1.1rem;">
          Search for available aircon services easily.
        </p>
      </div>

      <!-- Step 3 -->
      <div class="step" style="
        background: linear-gradient(145deg, #e9f0f1, #c9d6d8);
        flex: 1 1 280px;
        padding: 30px 25px 35px;
        border-radius: 15px;
        box-shadow: 7px 7px 15px #a8b0b2, -7px -7px 15px #ffffff;
        text-align: center;
        position: relative;
        transition: all 0.35s ease;
        cursor: pointer;
      "
      onmouseover="this.style.transform='scale(1.08)'; this.style.boxShadow='10px 10px 20px #9bb0b2, -10px -10px 20px #ffffff';"
      onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='7px 7px 15px #a8b0b2, -7px -7px 15px #ffffff';"
      >
        <div style="
          width: 50px;
          height: 50px;
          background: #07353f;
          border-radius: 50%;
          color: #fff;
          font-weight: 500;
          font-size: 1.8rem;
          display: flex;
          justify-content: center;
          align-items: center;
          position: absolute;
          top: -20px;
          left: 50%;
          transform: translateX(-50%);
          box-shadow: 0 0 12px #07353f88;
        ">3</div>
        <h3 style="margin-top: 40px; font-size: 1.7rem; color: #07353f;">Book & Confirm</h3>
        <p style="color: #2f3e44; font-weight: 300; margin-top: 15px; font-size: 1.1rem;">
          Book your services and get instant confirmation.
        </p>
      </div>

    </div>
  </div>
</section>



   <!-- Facebook Page -->
<section id="facebook-page" class="container" style="margin: 40px 2in 0 2in; font-family: 'Poppins', sans-serif;">
  <!-- Header Box -->
  <div style="
    text-align: center;
    background: linear-gradient(135deg, #29487D, #07353f);
    color: #f0f4ff;
    padding: 22px 20px;
    border-radius: 12px;
    margin-bottom: 30px;
    box-shadow:
      0 3px 8px rgba(41, 72, 125, 0.6),
      0 0 20px rgba(7, 53, 63, 0.4);
    font-size: 1.8rem;
    font-weight: 500;
    letter-spacing: 1.5 em;
    text-transform: uppercase;
    transition: background 0.4s ease;
    cursor: pointer;
  " onmouseover="this.style.background='linear-gradient(135deg, #07353f, #29487D)'" onmouseout="this.style.background='linear-gradient(135deg, #29487D, #07353f)'">
    Visit Our Facebook Page for More Details
  </div>
</section>

  <!-- Content Box -->
  <div style="
    display: flex;
    justify-content: center;
    align-items: flex-start;
    gap: 42px;
    background-color: #fff;
    border-radius: 14px;
    padding: 28px 30px;
    box-shadow:
      0 12px 25px rgba(7, 53, 63, 0.07),
      0 6px 10px rgba(7, 53, 63, 0.04);
    max-width: 740px;
    margin: 0 auto;
  ">
    <!-- Image -->
    <div class="image" style="flex: none; width: 280px; height: 280px; border-radius: 14px; overflow: hidden; box-shadow: 0 8px 20px rgba(41, 72, 125, 0.3);">
      <img src="page.png" alt="Airgo Page" style="width: 100%; height: 100%; object-fit: cover; border-radius: 14px; transition: transform 0.5s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'"/>
    </div>

    <!-- Info -->
    <div class="info" style="max-width: 420px; color: #07353f; font-size: 1.1rem; line-height: 1.65;">
      <p>
        <a href="https://web.facebook.com/messages/t/111830037044299" target="_blank" style="
          color: #29487D;
          text-decoration: none;
          font-weight: 700;
          position: relative;
          padding-bottom: 2px;
          transition: color 0.35s ease;
        " onmouseover="this.style.color='#07353f'" onmouseout="this.style.color='#29487D'">
          📩 Message Us on Facebook
          <span style="
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 2px;
            background: #29487D;
            border-radius: 2px;
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.35s ease;
          "></span>
        </a>
      </p>
      <p style="margin-top: 16px; font-weight: 600; letter-spacing: 0.03em;">Open Hours:</p>
      <ul style="padding-left: 24px; margin: 8px 0 18px; list-style-type: square; color: #415a7d;">
        <li>Monday - Sunday: 24 hours</li>
      </ul>
      <p style="font-weight: 600;">Contact: <span style="color: #07353f;">Sun# 09430510783 / 09976189915</span></p>
    </div>
  </div>
</section>



   <!-- Testimonials --> 
<section id="testimonials" style="background: transparent; padding: 20px 5px; font-family: 'Poppins', sans-serif; color: #07353f;">
  <div class="container" style="max-width: 900px; margin: 0 auto; text-align: center;">
    <h2 style="
      font-size: 1.8rem;
      margin-bottom: 30px;
      font-weight: 400;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: #0e5a64;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.1);
    ">
      What Our Customers Say
    </h2>
    <!-- Example Airgo styled text -->
    <p style="
      font-weight: 400;
      font-size: 1.8rem;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: #0e5a64;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    ">
    </p>
  </div>
</section>


    <div style="display: flex; flex-wrap: wrap; gap: 30px; justify-content: center;">
      
      <div style="
        background: linear-gradient(145deg, #e9f0f1, #c9d6d8);
        border-radius: 20px;
        padding: 30px 40px;
        max-width: 400px;
        box-shadow: 8px 8px 15px #a8b0b2, -8px -8px 15px #ffffff;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: default;
      " 
      onmouseover="this.style.transform='translateY(-8px) scale(1.03)'; this.style.boxShadow='12px 12px 20px #8a99a0, -12px -12px 20px #f2f5f7';" 
      onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='8px 8px 15px #a8b0b2, -8px -8px 15px #ffffff';"
      >
        <p style="font-style: italic; font-size: 1.2rem; line-height: 1.6; color: #2f3e44; margin-bottom: 20px;">
          "Airgo made booking so easy! I will definitely use it again."
        </p>
        <h4 style="
          font-weight: 700;
          font-size: 1.1rem;
          color: #07353f;
          letter-spacing: 0.05em;
          text-transform: uppercase;
        ">- John Doe</h4>
      </div>

      <div style="
        background: linear-gradient(145deg, #e9f0f1, #c9d6d8);
        border-radius: 20px;
        padding: 30px 40px;
        max-width: 400px;
        box-shadow: 8px 8px 15px #a8b0b2, -8px -8px 15px #ffffff;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: default;
      " 
      onmouseover="this.style.transform='translateY(-8px) scale(1.03)'; this.style.boxShadow='12px 12px 20px #8a99a0, -12px -12px 20px #f2f5f7';" 
      onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='8px 8px 15px #a8b0b2, -8px -8px 15px #ffffff';"
      >
        <p style="font-style: italic; font-size: 1.2rem; line-height: 1.6; color: #2f3e44; margin-bottom: 20px;">
          "Great platform with amazing service! Highly recommend."
        </p>
        <h4 style="
          font-weight: 700;
          font-size: 1.1rem;
          color: #07353f;
          letter-spacing: 0.05em;
          text-transform: uppercase;
        ">- Jane Smith</h4>
      </div>

    </div>
  </div>
</section>


    <!-- Footer -->
<footer style="
  background-color: #07353f;
  color: #f0f4ff;
  text-align: center;
  padding: 20px 10px;
  font-family: 'Poppins', sans-serif;
  font-weight: 500;
  letter-spacing: 0.05em;
  font-size: 1rem;
  box-shadow: 0 -3px 8px rgba(7, 53, 63, 0.3);
">
  <p>&copy; 2025 Airgo | All Rights Reserved</p>
</footer>

    <!-- Slideshow JS -->
    <script>
        let currentSlide = 0;
        let direction = 1;
        const slides = document.querySelectorAll('.slide');
        const totalSlides = slides.length;

        function changeSlide() {
            currentSlide += direction;
            if (currentSlide >= totalSlides) {
                direction = -1;
                currentSlide = totalSlides - 1;
            }
            if (currentSlide < 0) {
                direction = 1;
                currentSlide = 0;
            }
            document.querySelector('.slides').style.transform = `translateX(-${currentSlide * 100}%)`;
        }

        setInterval(changeSlide, 3000);
    </script>
</body>
</html>