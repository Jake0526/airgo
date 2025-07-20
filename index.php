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

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='20' fill='%2307353f'/%3E%3Cpath d='M30 50c0-11 9-20 20-20s20 9 20 20-9 20-20 20-20-9-20-20zm35 0c0-8.3-6.7-15-15-15s-15 6.7-15 15 6.7 15 15 15 15-6.7 15-15z' fill='%233cd5ed'/%3E%3Cpath d='M50 60c-5.5 0-10-4.5-10-10s4.5-10 10-10 10 4.5 10 10-4.5 10-10 10z' fill='white'/%3E%3C/svg%3E" />
    <link rel="icon" type="image/png" sizes="32x32" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAABKhJREFUWEe9V21sU2UUfu7H3a7dmDAgKgwQPxAkKAYnJsyPGCGK0ZAQFfEHJGpigkT8gYrxB5pI/GD+QBNjNCr+ICYaEzWBRDBRIQQIiCIIKLIvGIPtbrfb7b3XnHPvbVc2trs3bU/S7H7c877Pec95z3veK+AeHOIe4MN/B0CWZVkQhHui3LquX5YkSboXk+u6PqwoCud3HEAkEuH8qqrS0NBQYWxsLDY2NhaNRqNRVVVjmqYpuq4rpHwwGHQHAgG3y+VyezweN/1yu91uQRDcBIr+CoIgCILAf202G/1qgKZpGsdHYDRNm6Xr+llZlr1/CcDQ0BCNjZumxXVdVxVFiWmaFlVVNaooSjQejxPQEUVRYpqmxXRd5+vpuu5yOBwet9vtczgcPofD4SWQBMjpdLodDofL6XS6nE4nARFFUeR7QRAIJIMxAVJVVWPQODNBkiSvLMsDkiTJt23BwMAAjVEURY3H4zFFUaKqqkYJiKqqUU3T6G9UVdW4pmkxAqfrOgMWRdHjdDq9BNDhcPgcDofX5XL5XS6Xz+12+10ul9fpdHoJqMvlcjudTjcBJVOYQOm6rsXjcS0Wi8UjkUiU/kYikWgsFovF4/F4LBaLxePxeDQaZUDWFPT39xMAVVXVWCwWVRQlqihKhEAqihIxmUxRXdf52WQyEQCPKIo+URR9BNDpdPqtVqvfYrH4rFar3263++12u89ms3ltNpvXZrN5rFYrAXWLosjMEFOapvFzPB6PR6PRaCQSIXAEOBKJRGKxWCwajUYVRYnSO+vZNAX9/f00nhhQY7FYRFXVKIEkoLquk3nYvARCFEUvmZFAWiwWv81m89tsNr/D4fDb7XY/gbXb7T6bzUaA3WazmcASUwTO0L4J0mCJzEv3ZEr6P2PBzZs3aayqqmpMUZSIqqoRVVUjqqpGNE2LkBbIBwZtXgJIviYAFovFb7Va/Xa73W+1Wv0Oh8Nvt9sJqNdut3ttNhsBdVutVjeBNHRADFnvCKiqqn1Op5NpHxwczBSjgYEBGhuPx+OqqkZisViENKCqaphAEgOSJEWNOXWbzeYTRdFPQO12u99qtfptNpvfarX6HQ6H32az+a1Wq89ms3ltNpvHbrd7rFYrAXWRDgioYU7DnMSUoihsXkVRFPJEJBKJRKPR8O0a6O3tNQNgJlRVNayqaphAEgOSJIUlSQobDLgoK0RR9JEGLBaL32q1+m02m5/AEli73e6z2+0+q9Xqc7lcXrvd7rFarW6r1eq2WCwExk2ZQCDJ75IkhXVdD+u6HtZ1PazrehiYEtZ1XTYykCRJYdu27du3j8YbIAJGbIQJpKqqYUmSwgTSYMFNLJAGHA6H32Kx+G02m5/AElgCabfbfTabzWu1Wj0ul8tjs9k8VqvVbbFY3E6n0+10Ot0EkjQgSVKY7qmqGqbxkiSFVVUNU8FTVTVEz7IsywkNbN682QyAM0BV1RCZkEBKkhTSNC0kSVKIGHA4HD6n0+mzWCw+AksmtNlsXpvN5rXb7R6Xy+UhoFar1W21Wl0EkoAaZg1rmhYi89F4TdNCmqaFVFUNkXkTNZHNZvts27aNxhuNCIEI0T2ZkN4bLLhEUfQaueAjDdhsNi+BJD0QUKfT6SYdEFDKAEmSQoqihOjeAJG4/gQ7W7duzQXwP9cBnNexpjhBAAAAAElFTkSuQmCC" />
    <link rel="icon" type="image/png" sizes="16x16" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAlpJREFUOE+Nk0tIVFEYx//n3jtz544z4zg6OuqoqKVp9LDUMAqKFtGiWtSiWkQkEUEtahERRBQVLYJoUW2iRdCiKKJFD4JqE0REDxQfw1hq5DiOztz5mHvPPeeMjlr2wVl8fN/v+z++c87HsB8f24d9+H8A0un0IcbYUdd1PSLyiIg8z3MJyPU8zyEix/M8h4hcz/McInI9z7WJyLJt27Isy7IsS7YsS7IsS7YsW2aMWbZtW5ZlWaZpEsdxEgghDCGEwRjbBpBKpQ4yxo66rusRkedRwQQgEpHreZ5DRK7neQ4RuZ7nOZ7n2a7rWo7jWLZtW47jyI7jSLZty47jSI7jSJZlSYwxadOvCSGMbQDJZPIQY+yI67qe53ke/QsgItf1XIeIHNd1Hdd1Hc/zbNu2Ldu2ZMexZMexJMexJNu2JcaYtAXAGDM2AWKx2GHG2BEiIiLPIyLPdd0CQK7ruq7ruq7ruq5t25Zt25LjOLLjOJLjOJLjOJLjOBJjTNoUlzFmbAJEo9EjjLHDRERE5BERua7rFgByXdd1Xde1bdu2bNuWHceWHceWHMeSHMeSbNuWGGPSFgBjzNgEiEQihxljh4iIiMgjInJd1y0A5Lqu67qu69i2bdm2LTmOLTmOLTmOJTmOJdm2LTHGpC1xGWPGJkA4HD7CGDtIRJ7neUREnut6BYBc13Ud27Yt27Ylx7Elx7Ekx7Ekx7Ek27YlxpgkSRJjjBkbAMFg8BBj7IDruh4ReUREnut6BYBc13Ud27Yt27Ylx7Elx7Ekx7Ek27YlxpgkSRJjjBl/AQgEAgcZY/uJyPsJ8AOvgZzXMm3oPQAAAABJRU5ErkJggg==" />
    
    <!-- Preload fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@400;700;900&display=swap" rel="stylesheet">
    
    <!-- Header styles -->
    <link rel="stylesheet" href="styles/header.css">

    <!-- Scroll Animation Styles -->
    <style>
        .fade-up {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: opacity, transform;
        }

        .fade-up.visible {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }

        .fade-in {
            opacity: 0;
            transition: opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: opacity;
        }

        .fade-in.visible {
            opacity: 1 !important;
        }

        .slide-in-left {
            opacity: 0;
            transform: translateX(-30px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: opacity, transform;
        }

        .slide-in-left.visible {
            opacity: 1 !important;
            transform: translateX(0) !important;
        }

        .slide-in-right {
            opacity: 0;
            transform: translateX(30px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: opacity, transform;
        }

        .slide-in-right.visible {
            opacity: 1 !important;
            transform: translateX(0) !important;
        }

        .scale-up {
            opacity: 0;
            transform: scale(0.95);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: opacity, transform;
        }

        .scale-up.visible {
            opacity: 1 !important;
            transform: scale(1) !important;
        }

        .stagger-animation > * {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: opacity, transform;
        }

        .stagger-animation > *.visible {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }

        .stagger-animation > *:nth-child(1) { transition-delay: 0.1s; }
        .stagger-animation > *:nth-child(2) { transition-delay: 0.2s; }
        .stagger-animation > *:nth-child(3) { transition-delay: 0.3s; }
        .stagger-animation > *:nth-child(4) { transition-delay: 0.4s; }
        .stagger-animation > *:nth-child(5) { transition-delay: 0.5s; }

        .video-section, .services-grid, .how-it-works, .social-media, .testimonials {
            overflow: hidden;
        }
    </style>

    <!-- Scroll Animation Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.2
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        // Don't unobserve - keep watching for visibility changes
                        entry.target.classList.add('keep-observing');
                    }
                });
            }, observerOptions);

            function setupAnimation(elements, animationClass) {
                elements.forEach(el => {
                    if (el) {
                        el.classList.add(animationClass);
                        el.classList.add('keep-observing');
                        observer.observe(el);
                    }
                });
            }

            // Initialize animations after page load
            window.addEventListener('load', () => {
                // Video Section
                setupAnimation([document.querySelector('.video-content')], 'fade-up');
                setupAnimation([document.querySelector('.video-wrapper')], 'scale-up');

                // Services Section
                setupAnimation([document.querySelector('#services .section-title')], 'fade-up');
                document.querySelectorAll('.service-card').forEach((card, index) => {
                    card.style.transitionDelay = `${index * 0.1}s`;
                    setupAnimation([card], 'fade-up');
                });

                // How It Works Section
                setupAnimation([document.querySelector('#how-it-works .section-title')], 'fade-up');
                document.querySelectorAll('.step-card').forEach((card, index) => {
                    card.style.transitionDelay = `${index * 0.2}s`;
                    setupAnimation([card], 'fade-up');
                });

                // Social Media Section
                setupAnimation([document.querySelector('.social-card')], 'scale-up');

                // Testimonials Section
                setupAnimation([document.querySelector('#testimonials .section-title')], 'fade-up');
                document.querySelectorAll('.testimonial-card').forEach((card, index) => {
                    card.style.transitionDelay = `${index * 0.2}s`;
                    setupAnimation([card], 'fade-up');
                });
            });

            // Reset animations on scroll to top
            let lastScrollTop = 0;
            window.addEventListener('scroll', () => {
                const st = window.pageYOffset || document.documentElement.scrollTop;
                if (st < lastScrollTop && st < 100) {
                    document.querySelectorAll('.fade-up, .fade-in, .slide-in-left, .slide-in-right, .scale-up')
                        .forEach(el => {
                            el.style.transition = 'none';
                            el.classList.remove('visible');
                            setTimeout(() => {
                                el.style.transition = '';
                            }, 100);
                        });
                }
                lastScrollTop = st <= 0 ? 0 : st;
            }, false);
        });
    </script>

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
<section id="welcome" class="welcome-section">
    <div class="container">
        <div class="welcome-content">
            <div class="welcome-text">
                <h1 class="welcome-title">
                    Welcome to <span class="highlight">Air<span>go</span></span>
                </h1>
                <p class="welcome-description">
                    Your reliable booking platform for all your aircon services needs
                </p>
                <div class="welcome-features">
                    <div class="feature">
                        <div class="feature-icon">⚡</div>
                        <span>Fast Service</span>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">👨‍🔧</div>
                        <span>Expert Technicians</span>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">💯</div>
                        <span>Quality Guaranteed</span>
                    </div>
                </div>
                <a href="#services" class="cta-button">
                    Explore Our Services
                    <svg viewBox="0 0 24 24" width="24" height="24">
                        <path fill="currentColor" d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                    </svg>
                </a>
            </div>
            <div class="welcome-decoration">
                <div class="decoration-circle"></div>
                <div class="decoration-line"></div>
            </div>
        </div>
    </div>
</section>

<style>
.welcome-section {
    min-height: 100vh;
    padding: 0;
    background: linear-gradient(135deg, var(--background-color) 0%, #ffffff 100%);
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    margin-top: -76px; /* Adjust this value based on your header height */
    padding-top: 76px; /* Same as margin-top to offset the header */
}

.welcome-content {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    padding: clamp(2rem, 5vw, 4rem) 0;
}

.welcome-text {
    max-width: 900px;
    text-align: center;
    animation: fadeInUp 0.8s ease-out;
    padding: 0 1rem;
}

.welcome-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.5rem, 6vw, 4.5rem);
    color: var(--primary-color);
    margin-bottom: clamp(1.5rem, 3vw, 2rem);
    line-height: 1.2;
    font-weight: 900;
}

.welcome-title .highlight {
    position: relative;
    display: inline-block;
}

.welcome-title .highlight span {
    color: var(--secondary-color);
    font-style: italic;
}

.welcome-title .highlight::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 100%;
    height: 3px;
    background: var(--secondary-color);
    transform: scaleX(0);
    transform-origin: right;
    transition: transform 0.5s ease;
}

.welcome-title .highlight:hover::after {
    transform: scaleX(1);
    transform-origin: left;
}

.welcome-description {
    font-size: clamp(1.2rem, 2.5vw, 1.8rem);
    margin-bottom: clamp(2rem, 4vw, 3rem);
}

.welcome-features {
    display: flex;
    justify-content: center;
    gap: clamp(1.5rem, 3vw, 2.5rem);
    margin-bottom: clamp(2rem, 4vw, 3rem);
    opacity: 1;
    animation: fadeInUp 0.8s ease-out 0.4s forwards;
}

.feature {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    padding: 0.8rem 1.5rem;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50px;
    box-shadow: 0 4px 15px rgba(7, 53, 63, 0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.8s ease-out forwards;
}

.feature:nth-child(1) { animation-delay: 0.6s; }
.feature:nth-child(2) { animation-delay: 0.8s; }
.feature:nth-child(3) { animation-delay: 1s; }

.feature:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(7, 53, 63, 0.12);
    background: rgba(255, 255, 255, 1);
}

.feature-icon {
    font-size: 1.8rem;
    line-height: 1;
}

.feature span {
    font-size: clamp(1rem, 1.5vw, 1.2rem);
    font-weight: 500;
    color: var(--primary-color);
}

.cta-button {
    display: inline-flex;
    align-items: center;
    gap: 0.8rem;
    padding: clamp(1rem, 2vw, 1.5rem) clamp(2rem, 4vw, 3rem);
    font-size: clamp(1rem, 1.5vw, 1.2rem);
    color: white;
    background: var(--primary-color);
    border-radius: 50px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid var(--primary-color);
}

.cta-button:hover {
    background: transparent;
    color: var(--primary-color);
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(7, 53, 63, 0.15);
}

.cta-button svg {
    width: 24px;
    height: 24px;
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.cta-button:hover svg {
    transform: translateX(5px);
}

.welcome-decoration {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    pointer-events: none;
    z-index: 0;
}

.decoration-circle {
    position: absolute;
    width: clamp(300px, 40vw, 500px);
    height: clamp(300px, 40vw, 500px);
    border-radius: 50%;
    background: linear-gradient(45deg, var(--secondary-color) 0%, transparent 60%);
    opacity: 0.1;
    top: -20%;
    right: -10%;
    animation: rotate 20s linear infinite;
}

.decoration-line {
    position: absolute;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, transparent 45%, var(--secondary-color) 49%, var(--secondary-color) 51%, transparent 55%);
    opacity: 0.05;
    transform: skewY(-6deg);
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

@keyframes rotate {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

@media (max-width: 768px) {
    .welcome-features {
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        padding: 0 1rem;
        opacity: 1;
    }

    .feature {
        width: 100%;
        max-width: 300px;
        justify-content: center;
        padding: 0.6rem 1.2rem;
    }

    .welcome-title {
        padding: 0;
    }

    .welcome-description {
        padding: 0;
    }

    .cta-button {
        width: 100%;
        justify-content: center;
        text-align: center;
    }
}

@media (max-height: 700px) {
    .welcome-section {
        min-height: 100vh;
    }

    .welcome-title {
        font-size: clamp(2rem, 5vw, 3.5rem);
        margin-bottom: 1rem;
    }

    .welcome-description {
        font-size: clamp(1rem, 2vw, 1.4rem);
        margin-bottom: 1.5rem;
    }

    .welcome-features {
        margin-bottom: 1.5rem;
    }

    .feature {
        padding: 0.5rem 1rem;
    }

    .feature-icon {
        font-size: 1.4rem;
    }
}
</style>

<!-- Include Google Fonts -->
<style>
  @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@900&family=Poppins:wght@400;500&display=swap');
</style>



    <!-- Slideshow -->
    <section id="slideshow" class="slideshow-section">
        <div class="container">
            <div class="slideshow-container">
                <div class="slides">
                    <?php for ($i = 1; $i <= 25; $i += 3): ?>
                        <?php if ($i >= 21 && $i <= 25) continue; ?>
                        <div class="slide">
                            <div class="slide-image">
                                <img src="images/<?php echo $i; ?>.jpg" alt="Service Image <?php echo $i; ?>" loading="lazy" />
                            </div>
                            <div class="slide-image">
                                <img src="images/<?php echo $i + 1; ?>.jpg" alt="Service Image <?php echo $i + 1; ?>" loading="lazy" />
                            </div>
                            <div class="slide-image">
                                <img src="images/<?php echo $i + 2; ?>.jpg" alt="Service Image <?php echo $i + 2; ?>" loading="lazy" />
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
                <button class="slide-nav prev" aria-label="Previous slide">❮</button>
                <button class="slide-nav next" aria-label="Next slide">❯</button>
                <div class="slide-dots"></div>
            </div>
        </div>
    </section>

<style>
.slideshow-section {
    padding: clamp(3rem, 8vw, 6rem) 0;
    background: linear-gradient(to bottom, var(--background-color), white);
    overflow: hidden;
}

.slideshow-container {
    position: relative;
    max-width: 1200px;
    margin: 0 auto;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(7, 53, 63, 0.15);
}

.slides {
    display: flex;
    transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide {
    display: flex;
    justify-content: center;
    gap: clamp(0.5rem, 2vw, 1rem);
    min-width: 100%;
    padding: clamp(1rem, 3vw, 2rem);
}

.slide-image {
    flex: 1;
    border-radius: 15px;
    overflow: hidden;
    aspect-ratio: 1;
    position: relative;
}

.slide-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.slide-image:hover img {
    transform: scale(1.05);
}

.slide-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    background: rgba(7, 53, 63, 0.8);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    transition: all 0.3s ease;
    z-index: 2;
}

.slide-nav:hover {
    background: var(--secondary-color);
    transform: translateY(-50%) scale(1.1);
}

.prev {
    left: 20px;
}

.next {
    right: 20px;
}

.slide-dots {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 10px;
    z-index: 2;
}

.dot {
    width: 10px;
    height: 10px;
    background: rgba(255, 255, 255, 0.5);
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
}

.dot.active {
    background: var(--secondary-color);
    transform: scale(1.2);
}

@media (max-width: 768px) {
    .slide {
        flex-direction: column;
    }

    .slide-image {
        aspect-ratio: 16/9;
    }

    .slide-nav {
        width: 35px;
        height: 35px;
        font-size: 1rem;
    }

    .prev {
        left: 10px;
    }

    .next {
        right: 10px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slidesContainer = document.querySelector('.slides');
    const slides = document.querySelectorAll('.slide');
    const prevButton = document.querySelector('.prev');
    const nextButton = document.querySelector('.next');
    const dotsContainer = document.querySelector('.slide-dots');
    
    let currentSlide = 0;
    let isTransitioning = false;
    const totalSlides = slides.length;

    // Create dots
    slides.forEach((_, index) => {
        const dot = document.createElement('div');
        dot.classList.add('dot');
        if (index === 0) dot.classList.add('active');
        dot.addEventListener('click', () => goToSlide(index));
        dotsContainer.appendChild(dot);
    });

    const dots = document.querySelectorAll('.dot');

    function updateDots() {
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide);
        });
    }

    function goToSlide(index) {
        if (isTransitioning) return;
        isTransitioning = true;
        currentSlide = index;
        slidesContainer.style.transform = `translateX(-${currentSlide * 100}%)`;
        updateDots();
        setTimeout(() => {
            isTransitioning = false;
        }, 500);
    }

    function nextSlide() {
        goToSlide((currentSlide + 1) % totalSlides);
    }

    function prevSlide() {
        goToSlide((currentSlide - 1 + totalSlides) % totalSlides);
    }

    prevButton.addEventListener('click', prevSlide);
    nextButton.addEventListener('click', nextSlide);

    // Auto advance slides
    let slideInterval = setInterval(nextSlide, 5000);

    // Pause on hover
    slidesContainer.addEventListener('mouseenter', () => {
        clearInterval(slideInterval);
    });

    slidesContainer.addEventListener('mouseleave', () => {
        slideInterval = setInterval(nextSlide, 5000);
    });

    // Touch support
    let touchStartX = 0;
    let touchEndX = 0;

    slidesContainer.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
    });

    slidesContainer.addEventListener('touchend', e => {
        touchEndX = e.changedTouches[0].screenX;
        if (touchStartX - touchEndX > 50) {
            nextSlide();
        } else if (touchEndX - touchStartX > 50) {
            prevSlide();
        }
    });
});
</script>



<!-- Video Section -->
<section id="service-video" class="video-section">
    <div class="container">
        <div class="video-content">
            <h2 class="section-title">Watch Our Services in Action</h2>
            <div class="video-wrapper">
                <video
                    id="serviceVideo"
                    muted
                    playsinline
                    poster="video-thumbnail.jpg"
                    preload="metadata"
                >
                    <source src="video.mp4" type="video/mp4" />
                    Your browser does not support the video tag.
                </video>
                <div class="video-controls">
                    <button class="play-pause" aria-label="Play video">
                        <svg class="play-icon" viewBox="0 0 24 24" width="24" height="24">
                            <path fill="currentColor" d="M8 5v14l11-7z"/>
                        </svg>
                        <svg class="pause-icon" viewBox="0 0 24 24" width="24" height="24">
                            <path fill="currentColor" d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                        </svg>
                    </button>
                    <div class="progress-bar">
                        <div class="progress-filled"></div>
                    </div>
                    <button class="mute" aria-label="Mute video">
                        <svg class="volume-icon" viewBox="0 0 24 24" width="24" height="24">
                            <path fill="currentColor" d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
                        </svg>
                        <svg class="mute-icon" viewBox="0 0 24 24" width="24" height="24">
                            <path fill="currentColor" d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>
                        </svg>
                    </button>
                </div>
                <div class="video-overlay">
                    <div class="overlay-content">
                        <h3>Experience Our Professional Service</h3>
                        <p>Click to watch our expert technicians in action</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.video-section {
    padding: clamp(4rem, 10vw, 8rem) 0;
    background: linear-gradient(to bottom, white, var(--background-color));
}

.video-content {
    max-width: 1000px;
    margin: 0 auto;
}

.video-wrapper {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    background: var(--primary-color);
    box-shadow: 0 20px 40px rgba(7, 53, 63, 0.15);
    aspect-ratio: 16 / 9;
    margin-top: clamp(2rem, 5vw, 3rem);
}

.video-wrapper video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.video-controls {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.video-wrapper:hover .video-controls {
    opacity: 1;
}

.play-pause, .mute {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.play-pause:hover, .mute:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.1);
}

.pause-icon, .mute-icon {
    display: none;
}

.video-wrapper.playing .play-icon {
    display: none;
}

.video-wrapper.playing .pause-icon {
    display: block;
}

.video-wrapper.muted .volume-icon {
    display: none;
}

.video-wrapper.muted .mute-icon {
    display: block;
}

.progress-bar {
    flex: 1;
    height: 5px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 5px;
    cursor: pointer;
    position: relative;
}

.progress-filled {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    background: var(--secondary-color);
    border-radius: 5px;
    width: 0%;
    transition: width 0.1s linear;
}

.video-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(7, 53, 63, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 1;
    transition: opacity 0.3s ease;
    cursor: pointer;
}

.video-wrapper.playing .video-overlay {
    opacity: 0;
    pointer-events: none;
}

.overlay-content {
    text-align: center;
    color: white;
    padding: 20px;
    transform: translateY(20px);
    transition: transform 0.3s ease;
}

.video-overlay:hover .overlay-content {
    transform: translateY(0);
}

.overlay-content h3 {
    font-size: clamp(1.5rem, 3vw, 2rem);
    margin-bottom: 1rem;
    font-weight: 600;
}

.overlay-content p {
    font-size: clamp(1rem, 2vw, 1.2rem);
    opacity: 0.9;
}

@media (max-width: 768px) {
    .video-wrapper {
        border-radius: 10px;
        margin: 0 1rem;
    }

    .video-controls {
        padding: 15px;
    }

    .play-pause, .mute {
        padding: 6px;
    }

    .overlay-content {
        padding: 15px;
    }

    .overlay-content h3 {
        font-size: 1.2rem;
    }

    .overlay-content p {
        font-size: 0.9rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const wrapper = document.querySelector('.video-wrapper');
    const video = document.getElementById('serviceVideo');
    const playPauseBtn = wrapper.querySelector('.play-pause');
    const muteBtn = wrapper.querySelector('.mute');
    const progress = wrapper.querySelector('.progress-filled');
    const progressBar = wrapper.querySelector('.progress-bar');
    const overlay = wrapper.querySelector('.video-overlay');

    // Play/Pause
    function togglePlay() {
        if (video.paused) {
            video.play();
            wrapper.classList.add('playing');
        } else {
            video.pause();
            wrapper.classList.remove('playing');
        }
    }

    // Mute
    function toggleMute() {
        video.muted = !video.muted;
        wrapper.classList.toggle('muted', video.muted);
    }

    // Update Progress
    function handleProgress() {
        const percent = (video.currentTime / video.duration) * 100;
        progress.style.width = `${percent}%`;
    }

    // Scrub
    function scrub(e) {
        const scrubTime = (e.offsetX / progressBar.offsetWidth) * video.duration;
        video.currentTime = scrubTime;
    }

    // Event Listeners
    playPauseBtn.addEventListener('click', togglePlay);
    overlay.addEventListener('click', togglePlay);
    muteBtn.addEventListener('click', toggleMute);
    video.addEventListener('timeupdate', handleProgress);
    video.addEventListener('ended', () => {
        wrapper.classList.remove('playing');
        progress.style.width = '0%';
    });

    let mousedown = false;
    progressBar.addEventListener('click', scrub);
    progressBar.addEventListener('mousemove', (e) => mousedown && scrub(e));
    progressBar.addEventListener('mousedown', () => mousedown = true);
    progressBar.addEventListener('mouseup', () => mousedown = false);

    // Initialize muted state
    wrapper.classList.toggle('muted', video.muted);
});
</script>



    <!-- Available Services -->
<section id="services" class="services">
    <div class="container">
        <h2 class="section-title fade-up">Available Services</h2>
        <div class="services-grid">
            <div class="service-row first-row">
                <div class="service-card">
                    <div class="service-icon">🧹</div>
                    <h3>Cleaning</h3>
                    <ul class="service-list">
                        <li>Aircon filter cleaning</li>
                        <li>Coil cleaning</li>
                        <li>Vent cleaning</li>
                    </ul>
                </div>

                <div class="service-card">
                    <div class="service-icon">🔍</div>
                    <h3>Check-up</h3>
                    <ul class="service-list">
                        <li>Performance diagnostics</li>
                        <li>Leak inspection</li>
                        <li>Energy efficiency check</li>
                    </ul>
                </div>

                <div class="service-card">
                    <div class="service-icon">⚙️</div>
                    <h3>Installation</h3>
                    <ul class="service-list">
                        <li>New unit installation</li>
                        <li>System setup</li>
                        <li>Calibration</li>
                    </ul>
                </div>
            </div>

            <div class="service-row second-row">
                <div class="service-card">
                    <div class="service-icon">🚚</div>
                    <h3>Relocations</h3>
                    <ul class="service-list">
                        <li>Unit moving</li>
                        <li>Re-installation</li>
                        <li>Safety check</li>
                    </ul>
                </div>

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
    display: flex;
    flex-direction: column;
    gap: clamp(2rem, 4vw, 3rem);
    padding: 1rem;
  }

  .service-row {
    display: flex;
    justify-content: center;
    gap: clamp(1.5rem, 4vw, 2.5rem);
  }

  .first-row {
    flex-wrap: wrap;
  }

  .second-row {
    flex-wrap: wrap;
  }

  .service-card {
    background: white;
    border-radius: 20px;
    padding: clamp(1.5rem, 4vw, 2.5rem);
    box-shadow: 0 10px 30px rgba(7, 53, 63, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    flex: 1;
    min-width: 280px;
    max-width: 350px;
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

  @media (max-width: 992px) {
    .service-row {
      justify-content: center;
    }
    
    .service-card {
      flex: 0 1 calc(50% - 1.25rem);
    }
  }

  @media (max-width: 768px) {
    .service-row {
      flex-direction: column;
      align-items: center;
    }
    
    .service-card {
      flex: 0 1 100%;
      max-width: 100%;
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
        <h2 class="section-title fade-up">How It Works</h2>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">1</div>
                <div class="step-icon">👤</div>
                <h3>Create Account</h3>
                <p>Sign up for an account and log in securely to access our services.</p>
            </div>

            <div class="step-card">
                <div class="step-number">2</div>
                <div class="step-icon">🔍</div>
                <h3>Browse Services</h3>
                <p>Search and explore our wide range of aircon services easily.</p>
            </div>

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
                    <h3>Follow Us on Facebook</h3>
                    <p>Stay updated with our latest services, promotions, and aircon maintenance tips!</p>
                    <ul class="contact-info">
                        <li>
                            <span class="icon">📱</span>
                            <span>Sun# 09430510783 / 09976189915</span>
                        </li>
                        <li>
                            <span class="icon">🕒</span>
                            <span>Available 24/7</span>
                        </li>
                        <li>
                            <span class="icon">💬</span>
                            <span>Quick Response Time</span>
                        </li>
                        <li>
                            <span class="icon">📍</span>
                            <span>Serving Davao City and nearby areas</span>
                        </li>
                    </ul>
                    <div class="social-buttons">
                        <a href="https://web.facebook.com/messages/t/111830037044299" target="_blank" class="btn-facebook">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" class="messenger-icon">
                                <path d="M12 2C6.477 2 2 6.145 2 11.243c0 2.908 1.438 5.504 3.686 7.205V22l3.39-1.869c.924.258 1.902.398 2.924.398 5.523 0 10-4.145 10-9.243C22 6.145 17.523 2 12 2zm1.05 12.443l-2.375-2.375L6.03 14.12l4.82-4.82 2.375 2.375L17.97 9.62l-4.82 4.82z"/>
                            </svg>
                            Message Us
                        </a>
                        <a href="https://www.facebook.com/Aircon.Cleaning.DC" target="_blank" class="btn-facebook btn-follow">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" class="facebook-icon">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            Follow Us
                        </a>
                    </div>
                </div>
                <div class="social-image">
                    <img src="page.png" alt="Airgo Facebook Page" loading="lazy">
                    <div class="image-overlay">
                        <div class="overlay-text">Visit our Facebook Page</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.social-media {
    padding: clamp(3rem, 8vw, 6rem) 0;
    background: linear-gradient(to bottom, white, var(--background-color));
    opacity: 1 !important;
}

.social-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(7, 53, 63, 0.1);
    opacity: 1 !important;
    animation: fadeIn 0.8s ease-out forwards;
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
    opacity: 1 !important;
}

.social-info {
    display: flex;
    flex-direction: column;
    justify-content: center;
    animation: slideInLeft 0.8s ease-out forwards;
}

.social-info h3 {
    color: var(--primary-color);
    font-size: clamp(1.2rem, 3vw, 1.8rem);
    margin-bottom: 1rem;
}

.social-info p {
    color: var(--text-color);
    margin-bottom: 2rem;
    line-height: 1.6;
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
    padding: 0.5rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.contact-info li:hover {
    background: rgba(60, 213, 237, 0.1);
    transform: translateX(5px);
}

.contact-info .icon {
    font-size: 1.5rem;
    color: var(--secondary-color);
    min-width: 24px;
    text-align: center;
}

.social-buttons {
    display: flex;
    gap: 1rem;
    margin-top: auto;
}

.btn-facebook {
    flex: 1;
    background: #1877f2;
    color: white;
    border-radius: 30px;
    padding: 1rem 1.5rem;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    border: 2px solid #1877f2;
}

.btn-facebook.btn-follow {
    background: white;
    color: #1877f2;
}

.btn-facebook:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(13, 110, 253, 0.2);
}

.btn-facebook.btn-follow:hover {
    background: #1877f2;
    color: white;
}

.social-image {
    position: relative;
    border-radius: 15px;
    overflow: hidden;
    aspect-ratio: 16/9;
    animation: slideInRight 0.8s ease-out forwards;
}

.social-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(7, 53, 63, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
}

.overlay-text {
    color: white;
    font-weight: 600;
    font-size: 1.2rem;
    transform: translateY(20px);
    transition: all 0.3s ease;
}

.social-image:hover img {
    transform: scale(1.05);
}

.social-image:hover .image-overlay {
    opacity: 1;
}

.social-image:hover .overlay-text {
    transform: translateY(0);
}

.messenger-icon, .facebook-icon {
    transition: transform 0.3s ease;
}

.btn-facebook:hover .messenger-icon,
.btn-facebook:hover .facebook-icon {
    transform: scale(1.1);
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@media (max-width: 768px) {
    .social-content {
        grid-template-columns: 1fr;
        padding: 1.5rem;
    }

    .social-image {
        order: -1;
    }

    .social-buttons {
        flex-direction: column;
    }

    .contact-info li {
        font-size: 0.9rem;
    }

    .btn-facebook {
        width: 100%;
    }
}
</style>


   <!-- Testimonials -->
<section id="testimonials" class="testimonials">
    <div class="container">
        <h2 class="section-title fade-up">What Our Customers Say</h2>
        <div class="testimonials-grid">
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