<?php
// You can include PHP code for dynamic content or user session handling here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="description" content="Airgo - Your reliable booking platform for all your aircon services needs" />
    <meta name="theme-color" content="#07353f" />
    <title>Airgo - Booking System</title>

    <!-- Preload fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@400;700;900&display=swap" rel="stylesheet">
    
    <!-- Header styles -->
    <link rel="stylesheet" href="styles/header.css">

    <!-- Base styles -->
    <style>
        :root {
            --primary-color: #07353f;
            --secondary-color: #3cd5ed;
            --background-color: #d0f0ff;
            --text-color: #344047;
            --card-bg: #e9f0f1;
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
            margin: 0;
            padding: 0;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
            overflow-x: hidden;
        }

        .container {
            width: min(90%, 1200px);
            margin: 0 auto;
            padding: 0 var(--spacing-unit);
        }

        @media (max-width: 768px) {
        .container {
                width: 95%;
                padding: 0 calc(var(--spacing-unit) / 2);
            }
        }

        /* Responsive typography */
        h1 { font-size: clamp(1.5rem, 4vw, 2.5rem); }
        h2 { font-size: clamp(1.3rem, 3vw, 2rem); }
        h3 { font-size: clamp(1.1rem, 2.5vw, 1.75rem); }
        p { font-size: clamp(0.9rem, 2vw, 1.1rem); }

        /* Responsive images */
        img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        /* Responsive grid system */
        .grid {
            display: grid;
            gap: var(--spacing-unit);
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 300px), 1fr));
        }

        /* Responsive flex layouts */
        .flex {
            display: flex;
            gap: var(--spacing-unit);
            flex-wrap: wrap;
        }

        .flex-center {
            justify-content: center;
            align-items: center;
        }

        /* Responsive spacing */
        section {
            padding: clamp(2rem, 5vw, 4rem) 0;
        }

        /* Responsive cards */
        .card {
            background: var(--card-bg);
            border-radius: 15px;
            padding: clamp(1rem, 3vw, 2rem);
            box-shadow: 0 8px 20px var(--card-shadow);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        /* Responsive buttons */
        .btn {
            display: inline-block;
            padding: clamp(0.5rem, 2vw, 1rem) clamp(1rem, 4vw, 2rem);
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
            text-align: center;
        }

        /* Responsive navigation */
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                gap: 1rem;
            }
        }

        /* Responsive video */
        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
        }

        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        a {
            text-decoration: none;
            color: inherit;
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
            border-radius: 50px;
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
            border: 2px solid #3cd5ed;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        header .login-button a:hover {
            background-color: transparent;
            color: #3cd5ed;
            transform: translateY(-2px);
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
<header class="header">
  <nav class="container flex flex-center">
    <div class="logo">
      <h1>Air<span>go</span></h1>
    </div>
    <button class="burger-menu" aria-label="Toggle navigation menu">
      <span></span>
      <span></span>
      <span></span>
    </button>
    <div class="nav-menu">
      <div class="nav-links">
        <a href="#services" class="nav-link">Services</a>
        <a href="#how-it-works" class="nav-link">How It Works</a>
        <a href="#testimonials" class="nav-link">Testimonials</a>
        <div class="mobile-login">
          <a href="login.php" class="btn-primary">Login <span class="btn-icon">→</span></a>
        </div>
      </div>
    </div>
    <div class="header-button desktop-only">
      <a href="login.php">Login <span class="btn-icon">→</span></a>
    </div>
  </nav>
</header>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@600&family=Playfair+Display:wght@900&display=swap');

  .header {
    background: var(--primary-color);
    padding: 1rem 0;
    position: relative;
  }

  .header nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
  }

  .burger-menu {
    display: none;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 24px;
    position: relative;
    z-index: 1001;
  }

  .burger-menu span {
    display: block;
    width: 100%;
    height: 2px;
    background-color: var(--secondary-color);
    margin: 6px 0;
    transition: 0.4s;
    border-radius: 2px;
  }

  .logo h1 {
    color: white;
    font-family: 'Playfair Display', serif;
    font-weight: 900;
    letter-spacing: 1px;
    font-size: clamp(1.8rem, 3vw, 2.2rem);
    margin: 0;
  }

  .logo h1 span {
    color: var(--secondary-color);
    font-style: italic;
  }

  .nav-menu {
    display: flex;
    align-items: center;
  }

  .nav-links {
    display: flex;
    gap: clamp(2rem, 4vw, 3rem);
    margin: 0 2rem;
    align-items: center;
  }

  .nav-link {
    color: rgba(255, 255, 255, 0.9);
    font-weight: 500;
    position: relative;
    padding: 0.5rem;
    font-size: clamp(0.9rem, 1.5vw, 1.1rem);
    transition: color 0.3s ease;
  }

  .nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: var(--secondary-color);
    transform: scaleX(0);
    transform-origin: right;
    transition: transform 0.3s ease;
  }

  .nav-link:hover {
    color: var(--secondary-color);
  }

  .nav-link:hover::after {
    transform: scaleX(1);
    transform-origin: left;
  }

  .mobile-login {
    display: none;
  }

  /* Mobile navigation */
  @media (max-width: 768px) {
    .header nav {
      padding: 0 1rem;
    }

    .burger-menu {
      display: block;
      order: 2;
      margin-left: auto;
    }

    .logo {
      order: 1;
    }

    .nav-menu {
      order: 3;
      position: fixed;
      top: 0;
      left: -100%;
      width: 80%;
      height: 100vh;
      background: var(--primary-color);
      padding: 5rem 2rem;
      transition: left 0.3s ease;
      z-index: 1000;
    }

    .nav-menu.active {
      left: 0;
    }

    .nav-links {
      flex-direction: column;
      align-items: flex-start;
      margin: 0;
      width: 100%;
    }

    .nav-link {
      width: 100%;
      padding: 1rem 0;
      font-size: 1.1rem;
    }

    .mobile-login {
      display: block;
      margin-top: 2rem;
      width: 100%;
    }

    .mobile-login .btn-primary {
      width: 100%;
      justify-content: center;
      padding: 1rem;
    }

    .desktop-only {
      display: none;
    }

    .burger-menu.active span:nth-child(1) {
      transform: rotate(-45deg) translate(-5px, 6px);
    }

    .burger-menu.active span:nth-child(2) {
      opacity: 0;
    }

    .burger-menu.active span:nth-child(3) {
      transform: rotate(45deg) translate(-5px, -6px);
    }

    /* Add overlay when menu is open */
    .nav-menu::before {
      content: '';
      position: fixed;
      top: 0;
      right: 0;
      width: 20%;
      height: 100vh;
      background: rgba(0, 0, 0, 0.5);
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
    }

    .nav-menu.active::before {
      opacity: 1;
      visibility: visible;
    }
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const burgerMenu = document.querySelector('.burger-menu');
  const navMenu = document.querySelector('.nav-menu');

  burgerMenu.addEventListener('click', function() {
    this.classList.toggle('active');
    navMenu.classList.toggle('active');
    document.body.style.overflow = navMenu.classList.contains('active') ? 'hidden' : '';
  });

  // Close menu when clicking outside
  document.addEventListener('click', function(event) {
    const isClickInside = navMenu.contains(event.target) || 
                         burgerMenu.contains(event.target);
    
    if (!isClickInside && navMenu.classList.contains('active')) {
      burgerMenu.classList.remove('active');
      navMenu.classList.remove('active');
      document.body.style.overflow = '';
    }
  });

  // Close menu when clicking a nav link
  navMenu.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      burgerMenu.classList.remove('active');
      navMenu.classList.remove('active');
      document.body.style.overflow = '';
    });
  });
});
</script>

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
<section id="service-video" class="video-section">
  <div class="container">
    <div class="video-wrapper">
    <video
      id="serviceVideo"
      muted
      playsinline
      autoplay
      controls
      preload="auto"
      poster="video-thumbnail.jpg"
    >
      <source src="video.mp4" type="video/mp4" />
      Your browser does not support the video tag.
    </video>
      <div class="play-overlay">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="#fff">
          <path d="M8 5v14l11-7z"/>
        </svg>
      </div>
    </div>
  </div>
</section>

<style>
  .video-section {
    padding: clamp(3rem, 8vw, 6rem) 0;
    background: linear-gradient(to bottom, var(--background-color), white);
  }

  .video-wrapper {
    position: relative;
    width: 100%;
    max-width: 900px;
    margin: 0 auto;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(7, 53, 63, 0.15);
    aspect-ratio: 16 / 9;
    background: var(--primary-color);
  }

  .video-wrapper video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: opacity 0.3s ease;
  }

  .play-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 80px;
        height: 80px;
    background: rgba(7, 53, 63, 0.8);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    opacity: 0;
  }

  .play-overlay:hover {
    transform: translate(-50%, -50%) scale(1.1);
    background: var(--secondary-color);
  }

  .video-wrapper.playing .play-overlay {
    opacity: 0;
        pointer-events: none;
  }

  @media (max-width: 768px) {
    .video-wrapper {
      border-radius: 10px;
      margin: 0 1rem;
    }

    .play-overlay {
      width: 60px;
      height: 60px;
    }

    .play-overlay svg {
      width: 24px;
      height: 24px;
    }
  }
</style>

<script>
  const video = document.getElementById('serviceVideo');
  const wrapper = video.closest('.video-wrapper');
  const playOverlay = wrapper.querySelector('.play-overlay');

  // Auto-play when the page loads
  document.addEventListener('DOMContentLoaded', () => {
    video.play().catch(error => {
      console.log("Auto-play failed:", error);
    });
    wrapper.classList.add('playing');
  });

  // Play/pause on overlay click
  playOverlay.addEventListener('click', () => {
    if (video.paused) {
      video.play();
      wrapper.classList.add('playing');
    } else {
      video.pause();
      wrapper.classList.remove('playing');
    }
  });

  // Show/hide overlay based on video state
  video.addEventListener('play', () => wrapper.classList.add('playing'));
  video.addEventListener('pause', () => wrapper.classList.remove('playing'));
  video.addEventListener('ended', () => {
    wrapper.classList.remove('playing');
    video.play(); // Auto-replay when ended
  });

  // Intersection Observer for autoplay
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach(entry => {
        if (!entry.isIntersecting && !video.paused) {
          video.pause();
        } else if (entry.isIntersecting && video.paused) {
          video.play().catch(error => {
            console.log("Auto-play failed:", error);
          });
        }
      });
    },
    { threshold: 0.5 }
  );

  observer.observe(video);
</script>



    <!-- Available Services -->
<section id="services" class="services">
  <div class="container">
    <h2 class="section-title">Available Services</h2>
    <div class="services-grid">
      <!-- Service Card 1 -->
      <div class="service-card">
        <div class="service-icon">🧹</div>
        <h3>Cleaning</h3>
        <ul class="service-list">
        <li>Aircon filter cleaning</li>
        <li>Coil cleaning</li>
        <li>Vent cleaning</li>
      </ul>
    </div>

      <!-- Service Card 2 -->
      <div class="service-card">
        <div class="service-icon">🔍</div>
        <h3>Check-up</h3>
        <ul class="service-list">
        <li>Performance diagnostics</li>
        <li>Leak inspection</li>
        <li>Energy efficiency check</li>
      </ul>
    </div>

      <!-- Service Card 3 -->
      <div class="service-card">
        <div class="service-icon">⚙️</div>
        <h3>Installation</h3>
        <ul class="service-list">
        <li>New unit installation</li>
        <li>System setup</li>
        <li>Calibration</li>
      </ul>
    </div>

      <!-- Service Card 4 -->
      <div class="service-card">
        <div class="service-icon">🚚</div>
        <h3>Relocations</h3>
        <ul class="service-list">
        <li>Unit moving</li>
        <li>Re-installation</li>
        <li>Safety check</li>
      </ul>
    </div>

      <!-- Service Card 5 -->
      <div class="service-card">
        <div class="service-icon">🔧</div>
        <h3>Repair</h3>
        <ul class="service-list">
        <li>Leak repairs</li>
        <li>Component replacement</li>
        <li>Emergency service</li>
      </ul>
    </div>
    </div>
  </div>
</section>

<style>
  .services {
    background: var(--background-color);
    padding: clamp(3rem, 8vw, 6rem) 0;
  }

  .section-title {
    text-align: center;
    color: var(--primary-color);
    margin-bottom: clamp(2rem, 5vw, 4rem);
    font-family: 'Playfair Display', serif;
    font-weight: 700;
    position: relative;
  }

  .section-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 3px;
    background: var(--secondary-color);
    border-radius: 2px;
  }

  .services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr));
    gap: clamp(1.5rem, 4vw, 2.5rem);
    padding: 1rem;
  }

  .service-card {
    background: white;
    border-radius: 20px;
    padding: clamp(1.5rem, 4vw, 2.5rem);
    box-shadow: 0 10px 30px rgba(7, 53, 63, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  .service-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: var(--secondary-color);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s ease;
  }

  .service-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(7, 53, 63, 0.15);
  }

  .service-card:hover::before {
    transform: scaleX(1);
  }

  .service-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    display: inline-block;
    padding: 1rem;
    background: var(--background-color);
    border-radius: 15px;
  }

  .service-card h3 {
    color: var(--primary-color);
    margin-bottom: 1rem;
    font-weight: 600;
  }

  .service-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .service-list li {
    padding: 0.5rem 0;
    color: var(--text-color);
    position: relative;
    padding-left: 1.5rem;
  }

  .service-list li::before {
    content: '→';
    position: absolute;
    left: 0;
    color: var(--secondary-color);
  }

  @media (max-width: 768px) {
    .services-grid {
      grid-template-columns: 1fr;
      padding: 0.5rem;
    }

    .service-card {
      padding: 1.5rem;
    }
  }
</style>



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




  <section id="how-it-works" class="how-it-works">
  <div class="container">
    <h2 class="section-title">How It Works</h2>
    <div class="steps-grid">
      <!-- Step 1 -->
      <div class="step-card">
        <div class="step-number">1</div>
        <div class="step-icon">👤</div>
        <h3>Create Account</h3>
        <p>Sign up for an account and log in securely to access our services.</p>
      </div>

      <!-- Step 2 -->
      <div class="step-card">
        <div class="step-number">2</div>
        <div class="step-icon">🔍</div>
        <h3>Browse Services</h3>
        <p>Search and explore our wide range of aircon services easily.</p>
      </div>

      <!-- Step 3 -->
      <div class="step-card">
        <div class="step-number">3</div>
        <div class="step-icon">📅</div>
        <h3>Book & Confirm</h3>
        <p>Schedule your service and get instant confirmation.</p>
      </div>
    </div>
  </div>
</section>

<style>
  .how-it-works {
    background: linear-gradient(to bottom, var(--background-color), white);
    padding: clamp(4rem, 10vw, 8rem) 0;
  }

  .steps-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(100%, 300px), 1fr));
    gap: clamp(2rem, 5vw, 4rem);
    margin-top: clamp(3rem, 8vw, 5rem);
  }

  .step-card {
    background: white;
    border-radius: 20px;
    padding: clamp(2rem, 5vw, 3rem);
    text-align: center;
    position: relative;
    box-shadow: 0 10px 30px rgba(7, 53, 63, 0.1);
    transition: all 0.3s ease;
    overflow: hidden;
  }

  .step-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: var(--secondary-color);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s ease;
  }

  .step-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(7, 53, 63, 0.15);
  }

  .step-card:hover::before {
    transform: scaleX(1);
  }

  .step-number {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 40px;
    height: 40px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.2rem;
    box-shadow: 0 5px 15px rgba(7, 53, 63, 0.2);
  }

  .step-icon {
    font-size: 3rem;
    margin-bottom: 1.5rem;
    display: inline-block;
    padding: 1rem;
    background: var(--background-color);
    border-radius: 20px;
    transition: transform 0.3s ease;
  }

  .step-card:hover .step-icon {
    transform: scale(1.1) rotate(5deg);
  }

  .step-card h3 {
    color: var(--primary-color);
    margin-bottom: 1rem;
    font-size: clamp(1.2rem, 3vw, 1.5rem);
  }

  .step-card p {
    color: var(--text-color);
    line-height: 1.6;
    margin: 0;
  }

  @media (max-width: 768px) {
    .steps-grid {
      grid-template-columns: 1fr;
      gap: 2rem;
      padding: 0 1rem;
    }

    .step-card {
      padding: 2rem;
    }

    .step-icon {
      font-size: 2.5rem;
    }
  }

  @media (min-width: 769px) {
    .steps-grid {
          position: relative;
    }

    .steps-grid::after {
            content: '';
            position: absolute;
      top: 40%;
            left: 0;
            width: 100%;
            height: 2px;
      background: var(--secondary-color);
      opacity: 0.2;
      z-index: -1;
    }
  }
</style>


   <!-- Social Media Section -->
<section id="social-media" class="social-media">
  <div class="container">
    <div class="social-card">
      <div class="social-header">
        <div class="social-icon">
          <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
          </svg>
        </div>
        <h2>Connect With Us</h2>
      </div>
      <div class="social-content">
        <div class="social-info">
          <h3>Visit Our Facebook Page</h3>
          <p>Follow us for updates, tips, and special offers!</p>
          <ul class="contact-info">
            <li>
              <span class="icon">📱</span>
              <span>Sun# 09430510783 / 09976189915</span>
            </li>
            <li>
              <span class="icon">🕒</span>
              <span>Open 24/7</span>
            </li>
            <li>
              <span class="icon">💬</span>
              <span>Fast Response Time</span>
            </li>
      </ul>
          <a href="https://web.facebook.com/messages/t/111830037044299" target="_blank" class="btn btn-facebook">
            Message Us on Facebook
          </a>
        </div>
        <div class="social-image">
          <img src="page.png" alt="Airgo Facebook Page" loading="lazy">
        </div>
      </div>
    </div>
  </div>
</section>

<style>
  .social-media {
    padding: clamp(3rem, 8vw, 6rem) 0;
    background: linear-gradient(to bottom, white, var(--background-color));
  }

  .social-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(7, 53, 63, 0.1);
  }

  .social-header {
    background: var(--primary-color);
    padding: clamp(1.5rem, 4vw, 2.5rem);
    display: flex;
    align-items: center;
    gap: 1rem;
    color: white;
  }

  .social-icon {
    width: 48px;
    height: 48px;
    background: var(--secondary-color);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
  }

  .social-header h2 {
    margin: 0;
    font-size: clamp(1.5rem, 4vw, 2rem);
  }

  .social-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: clamp(2rem, 5vw, 4rem);
    padding: clamp(2rem, 5vw, 4rem);
  }

  .social-info {
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .social-info h3 {
    color: var(--primary-color);
    font-size: clamp(1.2rem, 3vw, 1.8rem);
    margin-bottom: 1rem;
  }

  .social-info p {
    color: var(--text-color);
    margin-bottom: 2rem;
  }

  .contact-info {
    list-style: none;
    padding: 0;
    margin-bottom: 2rem;
  }

  .contact-info li {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    color: var(--text-color);
  }

  .contact-info .icon {
    font-size: 1.5rem;
    color: var(--secondary-color);
  }

  .btn-facebook {
    background: #1877f2;
    color: white;
    border-radius: 30px;
    padding: 1rem 2rem;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s ease;
    display: inline-block;
    margin-top: auto;
  }

  .btn-facebook:hover {
    background: #0d6efd;
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(13, 110, 253, 0.2);
  }

  .social-image {
    position: relative;
    border-radius: 15px;
    overflow: hidden;
    aspect-ratio: 4/3;
  }

  .social-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
  }

  .social-image:hover img {
    transform: scale(1.05);
  }

  @media (max-width: 768px) {
    .social-content {
      grid-template-columns: 1fr;
      padding: 1.5rem;
    }

    .social-image {
      order: -1;
      aspect-ratio: 16/9;
    }

    .social-header {
      padding: 1.5rem;
      flex-direction: column;
      text-align: center;
    }

    .btn-facebook {
      width: 100%;
    }
  }
</style>


   <!-- Testimonials -->
<section id="testimonials" class="testimonials">
  <div class="container">
    <h2 class="section-title">What Our Customers Say</h2>
    <div class="testimonials-grid">
      <!-- Testimonial 1 -->
      <div class="testimonial-card">
        <div class="testimonial-content">
          <div class="quote-icon">❝</div>
          <p>"Airgo made booking so easy! The service was professional and efficient. I will definitely use it again."</p>
          <div class="testimonial-author">
            <div class="author-avatar">JD</div>
            <div class="author-info">
              <h4>John Doe</h4>
              <span>Satisfied Customer</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Testimonial 2 -->
      <div class="testimonial-card">
        <div class="testimonial-content">
          <div class="quote-icon">❝</div>
          <p>"Great platform with amazing service! The technicians were knowledgeable and professional. Highly recommend."</p>
          <div class="testimonial-author">
            <div class="author-avatar">JS</div>
            <div class="author-info">
              <h4>Jane Smith</h4>
              <span>Happy Client</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Testimonial 3 -->
      <div class="testimonial-card">
        <div class="testimonial-content">
          <div class="quote-icon">❝</div>
          <p>"The best aircon service I've ever used. Quick response time and excellent customer service!"</p>
          <div class="testimonial-author">
            <div class="author-avatar">MJ</div>
            <div class="author-info">
              <h4>Mike Johnson</h4>
              <span>Regular Customer</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
  .testimonials {
    background: linear-gradient(to bottom, white, var(--background-color));
    padding: clamp(3rem, 8vw, 6rem) 0;
  }

  .testimonials-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(100%, 350px), 1fr));
    gap: clamp(1.5rem, 4vw, 2.5rem);
    padding: 1rem;
  }

  .testimonial-card {
    background: white;
    border-radius: 20px;
    padding: clamp(1.5rem, 4vw, 2.5rem);
    box-shadow: 0 10px 30px rgba(7, 53, 63, 0.1);
    transition: all 0.3s ease;
    position: relative;
  }

  .testimonial-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(7, 53, 63, 0.15);
  }

  .testimonial-content {
    position: relative;
    z-index: 1;
  }

  .quote-icon {
    font-size: 4rem;
    color: var(--secondary-color);
    opacity: 0.2;
    position: absolute;
    top: -2rem;
    left: -1rem;
    z-index: -1;
  }

  .testimonial-content p {
    font-style: italic;
    color: var(--text-color);
    margin-bottom: 1.5rem;
    line-height: 1.6;
    position: relative;
  }

  .testimonial-author {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 1.5rem;
  }

  .author-avatar {
    width: 50px;
    height: 50px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.2rem;
  }

  .author-info h4 {
    color: var(--primary-color);
    margin: 0;
    font-size: 1.1rem;
  }

  .author-info span {
    color: var(--text-color);
    opacity: 0.8;
    font-size: 0.9rem;
  }

  @media (max-width: 768px) {
    .testimonials-grid {
      grid-template-columns: 1fr;
      padding: 0.5rem;
    }

    .testimonial-card {
      padding: 1.5rem;
    }

    .quote-icon {
      font-size: 3rem;
      top: -1.5rem;
    }
  }
</style>


    <!-- Footer -->
<footer class="footer">
  <div class="container">
    <div class="footer-content">
      <div class="footer-section">
        <h3>About Airgo</h3>
        <p>Your reliable booking platform for all your aircon services needs. Professional, efficient, and trusted service.</p>
      </div>
      <div class="footer-section">
        <h3>Quick Links</h3>
        <ul>
          <li><a href="#services">Services</a></li>
          <li><a href="#how-it-works">How It Works</a></li>
          <li><a href="#testimonials">Testimonials</a></li>
          <li><a href="login.php">Login</a></li>
        </ul>
      </div>
      <div class="footer-section">
        <h3>Contact Us</h3>
        <ul>
          <li>📱 Sun# 09430510783</li>
          <li>📱 09976189915</li>
          <li>🕒 Open 24/7</li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
  <p>&copy; 2025 Airgo | All Rights Reserved</p>
    </div>
  </div>
</footer>

<style>
  .footer {
    background: var(--primary-color);
    color: white;
    padding: clamp(3rem, 6vw, 5rem) 0 1.5rem;
    margin-top: clamp(3rem, 8vw, 6rem);
  }

  .footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: clamp(2rem, 5vw, 4rem);
    margin-bottom: 3rem;
  }

  .footer-section h3 {
    color: var(--secondary-color);
    margin-bottom: 1.5rem;
    font-size: 1.2rem;
    font-weight: 600;
    position: relative;
  }

  .footer-section h3::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 40px;
    height: 2px;
    background: var(--secondary-color);
    border-radius: 2px;
  }

  .footer-section p {
    color: rgba(255, 255, 255, 0.8);
    line-height: 1.6;
  }

  .footer-section ul {
    list-style: none;
    padding: 0;
  }

  .footer-section ul li {
    margin-bottom: 0.8rem;
  }

  .footer-section ul li a {
    color: rgba(255, 255, 255, 0.8);
    transition: all 0.3s ease;
    display: inline-block;
  }

  .footer-section ul li a:hover {
    color: var(--secondary-color);
    transform: translateX(5px);
  }

  .footer-bottom {
    text-align: center;
    padding-top: 2rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
  }

  .footer-bottom p {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.9rem;
  }

  @media (max-width: 768px) {
    .footer {
      padding: 3rem 0 1rem;
    }

    .footer-content {
      grid-template-columns: 1fr;
      gap: 2rem;
      text-align: center;
    }

    .footer-section h3::after {
      left: 50%;
      transform: translateX(-50%);
    }

    .footer-section ul li a:hover {
      transform: translateX(0) scale(1.05);
    }
  }
</style>

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