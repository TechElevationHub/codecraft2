<?php
/**
 * Contact Page - contact.php
 * Contact information, map, and operating hours
 */
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - CodeCraftHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f4f7fb;
        }

        /* Navigation Bar - DO NOT CHANGE */
        nav {
            background: #0a2342;
            padding: 1rem 5%;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #00bcd4;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s, border-bottom 0.3s;
            padding-bottom: 2px;
        }

        .nav-links a:hover {
            color: #00bcd4;
            
        }

        /* Hero Section */
       /* Hero Section */
       .hero-section {
            position: relative; /* Needed for pseudo-element positioning */
            overflow: hidden;   /* Prevents blur from spilling outside */
            padding: 5rem 2rem;
            text-align: center;
            color: #0a2342;
        }

        /* Add your own image and blur effect */
        .hero-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('ContactUs.jpg') center/cover no-repeat;
            filter: blur(3px) brightness(0.9); /* Adjust blur and brightness */
            z-index: 0; /* Behind content */
        }

        /* Make sure text stays visible on top */
        .hero-section h1,
        .hero-section p {
            position: relative;
            z-index: 1;
        }

        .hero-section h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .hero-section p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
            color: #ffffffff;
        }

        /* Content */
        .content {
            max-width: 1200px;
            margin: 3rem auto;
            padding: 0 2rem;
        }

        /* Contact Cards */
        .contact-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .contact-card {
            background: #fff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.3s;
            border-top: 4px solid #0a2342;
        }

        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .contact-card i {
            font-size: 3rem;
            color: #0a2342;
            margin-bottom: 1rem;
        }

        .contact-card h3 {
            color: #0a2342;
            margin-bottom: 1rem;
        }

        .contact-card p {
            color: #555;
            margin-bottom: 0.5rem;
        }

        .contact-card a {
            color: #0a2342;
            text-decoration: none;
            font-weight: 600;
        }

        .contact-card a:hover {
            color: #00bcd4;
        }

        .status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            margin-top: 0.5rem;
        }

        .status.open {
            background: #4caf50;
            color: #fff;
        }

        .status.closed {
            background: #f44336;
            color: #fff;
        }

        .social-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
        
        }

        .social-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border-radius: 45px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .social-btn i {
            font-size: 18px;
            margin-top: 20px;
        }

        .social-btn:hover {
            background: #00bcd4;
        }


        /* Map Section */
        .map-section {
            background: #fff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 3rem;
        }

        .map-section h2 {
            color: #0a2342;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .map-container {
            width: 100%;
            height: 400px;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .directions-link {
            display: inline-block;
            background: #0a2342;
            color: #fff;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 600;
        }

        .directions-link:hover {
            background: #00bcd4;
            transform: translateY(-2px);
        }

        .directions-link i {
            margin-right: 0.5rem;
        }

        /* Back to Top Button */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            left: 30px;
            background: #0a2342;
            color: #fff;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s;
            text-decoration: none;
        }

        .back-to-top:hover {
            background: #00bcd4;
            transform: translateY(-5px);
        }

        .back-to-top i {
            font-size: 1.5rem;
        }

       /* Footer */
       footer {
    background: #000532ff;
    color: #fff;
    padding: 1rem 5%;   /* reduced height (was 3rem 5%) */
}

.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;        /* slightly tighter spacing */
}

.footer-section h3 {
    color: #64b5f6;
    margin-bottom: 0.75rem;
}

.footer-section p {
    margin-bottom: 0.4rem;
}

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-links a {
            color: #fff;
            font-size: 1.5rem;
            transition: color 0.3s;
        }

        .social-links a:hover {
            color: #64b5f6;
        }

        .copyright {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #333;
        }


        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2rem;
            }

            .contact-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <div class="nav-container">
            <a href="user.php" class="logo">CodeCraftHub</a>
            <ul class="nav-links">
                <li><a href="user.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <h1>Contact Information</h1>
        <p>We're here to help! Reach out to us through any of the channels below. 
           Our team is dedicated to providing you with the best support and assistance.</p>
    </section>

    <!-- Content -->
    <div class="content">
        <!-- Contact Cards -->
        <div class="contact-cards">
            <!-- Contact Us Card -->
            <div class="contact-card">
                <i class="fas fa-envelope"></i>
                <h3>Contact Us</h3>
                <p><strong>Email:</strong><br><a href="mailto:info@codecrafthub.com">info@codecrafthub.com</a></p>
                <p><strong>Phone:</strong><br><a href="tel:+15551234567">+1 (555) 123-4567</a></p>
            </div>

            <!-- Visit Us Card -->
            <div class="contact-card">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Visit Us</h3>
                <p><strong>Address:</strong><br>123 Tech Street<br>Silicon Valley, CA 94025<br>United States</p>
            </div>

            <!-- Operating Hours Card -->
            <div class="contact-card">
                <i class="fas fa-clock"></i>
                <h3>Operating Hours</h3>
                <p><strong>Monday - Friday:</strong><br>9:00 AM - 6:00 PM</p>
                <p><strong>Saturday:</strong><br>10:00 AM - 4:00 PM</p>
                <p><strong>Sunday:</strong><br>Closed</p>
                <div class="status" id="status">Checking...</div>
            </div>

            <!-- Follow Us Card -->
            <div class="contact-card">
                <i class="fas fa-share-alt"></i>
                <h3>Follow Us</h3>
                <p>Connect with us on social media</p>
                <div class="social-buttons">
                    <a href="https://facebook.com" target="_blank" class="social-btn">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com" target="_blank" class="social-btn">
                        <i class="fab fa-twitter"></i> 
                    </a>
                    <a href="https://instagram.com" target="_blank" class="social-btn">
                        <i class="fab fa-instagram"></i> 
                    </a>
                    <a href="https://linkedin.com" target="_blank" class="social-btn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="map-section">
            <h2>Find Us on the Map</h2>
            <div class="map-container">
                <!-- Embedded Google Map -->
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3168.6395816087!2d-122.08524908469225!3d37.38605107982516!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808fb7495bec0189%3A0x7c17d44a466baf9b!2sSilicon%20Valley!5e0!3m2!1sen!2sus!4v1234567890123!5m2!1sen!2sus"
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>
            <center>
                <a href="https://www.google.com/maps/dir//Silicon+Valley,+CA/" target="_blank" class="directions-link">
                    <i class="fas fa-directions"></i> Get Directions
                </a>
            </center>
        </div>
    </div>

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </a>

    
    <!-- Footer -->
    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <h3>CodeCraftHub</h3>
                <p>Your gateway to programming excellence</p>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <p><i class="fas fa-map-marker-alt"></i> 123 Tech Street, Silicon Valley, CA 94025</p>
                <p><i class="fas fa-phone"></i> +1 (555) 123-4567</p>
                <p><i class="fas fa-envelope"></i> info@codecrafthub.com</p>
            </div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <div class="social-links">
                    <a href="https://facebook.com" target="_blank"><i class="fab fa-facebook"></i></a>
                    <a href="https://twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="https://linkedin.com" target="_blank"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2025 CodeCraftHub. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Check if currently open based on real-time
        function checkOperatingHours() {
            const now = new Date();
            const day = now.getDay(); // 0 = Sunday, 1 = Monday, etc.
            const hours = now.getHours();
            const minutes = now.getMinutes();
            const currentTime = hours + minutes / 60;

            const statusElement = document.getElementById('status');

            // Sunday (0) = Closed
            if (day === 0) {
                statusElement.textContent = 'Closed';
                statusElement.className = 'status closed';
                return;
            }

            // Monday to Friday (1-5): 9:00 AM - 6:00 PM
            if (day >= 1 && day <= 5) {
                if (currentTime >= 9 && currentTime < 18) {
                    statusElement.textContent = 'Open Now';
                    statusElement.className = 'status open';
                } else {
                    statusElement.textContent = 'Closed';
                    statusElement.className = 'status closed';
                }
                return;
            }

            // Saturday (6): 10:00 AM - 4:00 PM
            if (day === 6) {
                if (currentTime >= 10 && currentTime < 16) {
                    statusElement.textContent = 'Open Now';
                    statusElement.className = 'status open';
                } else {
                    statusElement.textContent = 'Closed';
                    statusElement.className = 'status closed';
                }
                return;
            }
        }

        // Smooth scroll to top
        document.querySelector('.back-to-top').addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Check operating hours on page load
        checkOperatingHours();

        // Update every minute
        setInterval(checkOperatingHours, 60000);
    </script>
</body>
</html>