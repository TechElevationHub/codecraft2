<?php
/**
 * Home Page - user.php
 * Main landing page with hero section and chatbot
 */
require_once 'config.php';
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
            .logo-container {
  display: flex;
  align-items: center;
  gap: 8px; /* space between icon and text */
}

.logo-img {
  height: 60px; /* adjust to visually match text height */
  width: auto;
  transition: var(--transition);
  vertical-align: middle;
}

.logo-img:hover {
  transform: scale(1.05);
}

.logo {
  font-size: 1.8rem;
  font-weight: bold;
  color: #00bcd4;
  text-decoration: none; /* removes underline */
  cursor: pointer;
  display: flex;
  align-items: center;
  border: none;          /* removes borders */
  outline: none;         /* removes focus outline */
}

/* Remove underline on hover, focus, and active states */
.logo:hover,
.logo:focus,
.logo:active {
  text-decoration: none;
  outline: none;
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

        /* Background image with blur */
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('HomePage.jpg') center/cover;
            filter: blur(4px); /* Adjust blur amount (3px-8px recommended) */
            z-index: -1;
        }

        /* White overlay on top of blurred image */
        .hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.5); /* Adjust transparency (0.3-0.5 works well) */
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
            margin-bottom: 2rem;
            line-height: 0;
            color: #000000ff;
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

        /* Chatbot */
        .chatbot-icon {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: #0a2342;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s;
            z-index: 999;
        }

        .chatbot-icon:hover {
            background: #00bcd4;
            transform: scale(1.1);
        }

        .chatbot-icon i {
            font-size: 1.8rem;
            color: #fff;
        }

        .chatbot-window {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 350px;
            max-height: 500px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.3);
            display: none;
            flex-direction: column;
            z-index: 999;
        }

        .chatbot-window.active {
            display: flex;
        }

        .chatbot-header {
            background: #0a2342;
            color: #fff;
            padding: 1rem;
            border-radius: 15px 15px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chatbot-close {
            cursor: pointer;
            font-size: 1.5rem;
        }

        .chatbot-body {
            padding: 1rem;
            overflow-y: auto;
            flex: 1;
        }

        .chat-question {
            background: #f4f7fb;
            padding: 0.8rem;
            margin-bottom: 0.5rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
            color: #0a2342;
            border-left: 3px solid #0a2342;
        }

        .chat-question:hover {
            background: #e8f4f8;
        }

        .chat-answer {
            background: #0a2342;
            color: #fff;
            padding: 1rem;
            margin-top: 0.5rem;
            border-radius: 8px;
            display: none;
        }

        .chat-answer.active {
            display: block;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .nav-links {
                gap: 1rem;
            }

            .chatbot-window {
                width: 90%;
                right: 5%;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <div class="nav-container">
           
        <a href="#" class="logo-container">
  <img src="logo.jpg" alt="CodeCraftHub Logo" class="logo-img">
  <span class="logo">CodeCraftHub</span>
</a>

            <ul class="nav-links">
                <li><a href="user.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Your Gateway to Programming Knowledge</h1>
            <p>Welcome to CodeCraftHub, your ultimate destination for learning programming languages.</p> 
              <p>Discover comprehensive resources, tutorials, and courses 
              for over 20 programming languages. </p> 
              <p>Start your coding journey today and unlock
               endless possibilities in software development.</p> 
            <button class="btn-primary" onclick="handleGetStarted()">Get Started</button>
        </div>
    </section>

    
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

    <!-- Chatbot Icon -->
    <div class="chatbot-icon" onclick="toggleChatbot()">
        <i class="fas fa-robot"></i>
    </div>

    <!-- Chatbot Window -->
    <div class="chatbot-window" id="chatbotWindow">
        <div class="chatbot-header">
            <h3>Help Center</h3>
            <span class="chatbot-close" onclick="toggleChatbot()">×</span>
        </div>
        <div class="chatbot-body">
            <div class="chat-question" onclick="showAnswer(1)">
                <strong>How do I create an account?</strong>
            </div>
            <div class="chat-answer" id="answer1">
                Click on the "Login" button in the navigation bar, then select "Register" to create a new account. Fill in your details and submit the form.
            </div>

            <div class="chat-question" onclick="showAnswer(2)">
                <strong>How to change my password?</strong>
            </div>
            <div class="chat-answer" id="answer2">
                After logging in, go to your profile settings. Click on "Change Password" and enter your current password followed by your new password. Click "Update" to save changes.
            </div>

            <div class="chat-question" onclick="showAnswer(3)">
                <strong>What programming languages are available?</strong>
            </div>
            <div class="chat-answer" id="answer3">
                We offer resources for over 20 programming languages including Python, JavaScript, Java, C++, PHP, Ruby, Swift, and many more. Click "Get Started" to explore all available languages.
            </div>

            <div class="chat-question" onclick="showAnswer(4)">
                <strong>Is CodeCraftHub free to use?</strong>
            </div>
            <div class="chat-answer" id="answer4">
                Yes! CodeCraftHub is completely free. We provide curated links to the best free resources and courses for each programming language.
            </div>

            <div class="chat-question" onclick="showAnswer(5)">
                <strong>How do I access course materials?</strong>
            </div>
            <div class="chat-answer" id="answer5">
                After logging in, click "Get Started" on the home page. Browse or search for your desired programming language, then click "Open" to view external course links and resources.
            </div>
        </div>
    </div>

    <script>
        // Toggle chatbot window visibility
        function toggleChatbot() {
            const chatbot = document.getElementById('chatbotWindow');
            chatbot.classList.toggle('active');
        }

        // Show/hide answer when question is clicked
        function showAnswer(id) {
            const answer = document.getElementById('answer' + id);
            // Hide all other answers
            const allAnswers = document.querySelectorAll('.chat-answer');
            allAnswers.forEach(ans => {
                if (ans.id !== 'answer' + id) {
                    ans.classList.remove('active');
                }
            });
            // Toggle current answer
            answer.classList.toggle('active');
        }

        // Handle Get Started button click
        function handleGetStarted() {
            <?php if (isset($_SESSION['user_id'])): ?>
                // User is logged in, redirect to information page
                window.location.href = 'information.php';
            <?php else: ?>
                // User is not logged in, redirect to login page
                window.location.href = 'login.php';
            <?php endif; ?>
        }
    </script>
</body>
</html>