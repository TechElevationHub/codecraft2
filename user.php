<?php
/**
 * Home Page - user.php
 * Main landing page with hero section and chatbot
 */
require_once 'config.php';

// ✅ FIXED: Start session and define variables safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define login status and username safely
$isLoggedIn = isset($_SESSION['user_id']);
$userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeCraftHub - Your Gateway to Programming Knowledge</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background-color: #f4f7fb;
            color: #333;
        }

        /* Navigation Bar */
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

        .logo-container {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo-img {
            height: 60px;
            width: auto;
            transition: all 0.3s ease;
            vertical-align: middle;
        }

        .logo-img:hover {
            transform: scale(1.05);
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #00bcd4;
            text-decoration: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            border: none;
            outline: none;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
            padding-bottom: 2px;
        }

        .nav-links a:hover {
            color: #00bcd4;
        }

        .user-welcome {
            color: #fff;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logout-btn {
            background: #00bcd4;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: #0199b3;
        }

        /* Hamburger Menu */
        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
            gap: 4px;
        }

        .hamburger span {
            width: 25px;
            height: 3px;
            background: white;
            transition: 0.3s;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            right: -300px;
            width: 300px;
            height: 100vh;
            background: #0a2342;
            transition: right 0.3s ease;
            z-index: 1001;
            padding: 2rem;
            box-shadow: -5px 0 15px rgba(0,0,0,0.3);
        }

        .sidebar.active {
            right: 0;
        }

        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #00bcd4;
        }

        .sidebar-close {
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            background: none;
            border: none;
        }

        .sidebar-links {
            list-style: none;
        }

        .sidebar-links li {
            margin-bottom: 1rem;
        }

        .sidebar-links a {
            color: white;
            text-decoration: none;
            font-size: 1.1rem;
            transition: color 0.3s;
            display: block;
            padding: 0.5rem 0;
        }

        .sidebar-links a:hover {
            color: #00bcd4;
        }

        .sidebar-user {
            color: #00bcd4;
            font-weight: 600;
            margin-bottom: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #00bcd4;
        }

        /* Overlay */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            display: none;
        }

        .overlay.active {
            display: block;
        }

        /* Hero Section */
        .hero {
            position: relative;
            min-height: 85vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #333;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('HomePage.jpg') center/cover;
            filter: blur(4px);
            z-index: -1;
        }

        .hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.5);
            z-index: -1;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            color: #0a2342;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            line-height: 1.6;
            color: #000000;
        }

        .btn-primary {
            background: #0a2342;
            color: #fff;
            padding: 1rem 3rem;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: #00bcd4;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 188, 212, 0.3);
        }

        footer {
            background: #000532;
            color: #fff;
            padding: 1rem 5%;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
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

        /* Chatbot Styles remain unchanged */
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <div class="nav-container">
            <a href="user.php" class="logo-container">
                <img src="logo.jpg" alt="CodeCraftHub Logo" class="logo-img">
                <span class="logo">CodeCraftHub</span>
            </a>

            <ul class="nav-links">
                <li><a href="user.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php if ($isLoggedIn): ?>
                    <li class="user-welcome">
                        Welcome, <?php echo htmlspecialchars($userName); ?>!
                        <a href="logout.php" class="logout-btn">Logout</a>
                    </li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>

            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>Menu</h3>
            <button class="sidebar-close" id="sidebarClose">×</button>
        </div>
        <ul class="sidebar-links">
            <li><a href="user.php">Home</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>
            <?php if ($isLoggedIn): ?>
                <li class="sidebar-user">Welcome, <?php echo htmlspecialchars($userName); ?>!</li>
                <li><a href="logout.php" style="color: #00bcd4;">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="overlay" id="overlay"></div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Your Gateway to Programming Knowledge</h1>
            <p>Welcome to CodeCraftHub, your ultimate destination for learning programming languages.</p> 
            <p>Discover comprehensive resources, tutorials, and courses for over 20 programming languages.</p> 
            <p>Start your coding journey today and unlock endless possibilities in software development.</p> 
            <button class="btn-primary" id="getStartedBtn">Get Started</button>
        </div>
    </section>

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

    <div class="chatbot-icon" onclick="toggleChatbot()">
        <i class="fas fa-robot"></i>
    </div>

    <div class="chatbot-window" id="chatbotWindow">
        <div class="chatbot-header">
            <h3>Help Center</h3>
            <span class="chatbot-close" onclick="toggleChatbot()">×</span>
        </div>
        <div class="chatbot-body">
            <!-- chatbot questions remain unchanged -->
        </div>
    </div>

    <script>
        // Toggle chatbot window visibility
        function toggleChatbot() {
            const chatbot = document.getElementById('chatbotWindow');
            chatbot.classList.toggle('active');
        }

        // Get Started button redirects correctly
        document.getElementById('getStartedBtn').addEventListener('click', function() {
            <?php if ($isLoggedIn): ?>
                window.location.href = 'information.php';
            <?php else: ?>
                window.location.href = 'login.php';
            <?php endif; ?>
        });

        // Sidebar functionality
        const hamburger = document.getElementById('hamburger');
        const sidebar = document.getElementById('sidebar');
        const sidebarClose = document.getElementById('sidebarClose');
        const overlay = document.getElementById('overlay');

        hamburger.addEventListener('click', () => {
            sidebar.classList.add('active');
            overlay.classList.add('active');
        });

        sidebarClose.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    </script>
</body>
</html>
