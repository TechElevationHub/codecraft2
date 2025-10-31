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

/* Question container - will be hidden when answer is shown */
.question-container {
    display: block;
}

.question-container.hidden {
    display: none;
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

/* Answer container - shows question + answer together */
.answer-container {
    display: none;
}

.answer-container.active {
    display: block;
}

/* Active question (shown with answer) */
.active-question {
    background: #0a2342;
    color: #fff;
    padding: 0.8rem;
    margin-bottom: 0.5rem;
    border-radius: 8px;
    cursor: pointer;
    border-left: 3px solid #00bcd4;
}

.active-question:hover {
    background: #1a3a5a;
}

.chat-answer {
    background: #e8f4f8;
    color: #0a2342;
    padding: 1rem;
    margin-top: 0.5rem;
    border-radius: 8px;
    border-left: 3px solid #00bcd4;
}

/* Tutorial Options Buttons */
.tutorial-options {
    display: flex;
    margin-top: 1rem;
    gap: 0.8rem;
    justify-content: center;
    flex-wrap: wrap;
}

.tutorial-btn {
    background: #0a2342;
    color: #fff;
    padding: 0.8rem 1.5rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.tutorial-btn:hover {
    background: #00bcd4;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 188, 212, 0.3);
}

.tutorial-btn i {
    font-size: 1.1rem;
}

/* Text Answer (appears inline when Text button is clicked) */
.text-tutorial-answer {
    background: #e8f4f8;
    color: #0a2342;
    padding: 1rem;
    margin-top: 0.5rem;
    border-radius: 8px;
    border-left: 3px solid #00bcd4;
    display: none;
}

.text-tutorial-answer.active {
    display: block;
}

.text-tutorial-answer ol {
    margin-top: 0.5rem;
    padding-left: 1.5rem;
}

.text-tutorial-answer li {
    margin-bottom: 0.5rem;
    line-height: 1.6;
}

/* Video Tutorial Modal */
.video-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    z-index: 2000;
    justify-content: center;
    align-items: center;
}

.video-modal.active {
    display: flex;
}

.video-modal-content {
    background: #fff;
    padding: 2rem;
    border-radius: 15px;
    max-width: 800px;
    width: 90%;
    position: relative;
    box-shadow: 0 10px 40px rgba(0,0,0,0.5);
    max-height: 90vh;
    overflow-y: auto;
}

.modal-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    font-size: 2rem;
    cursor: pointer;
    color: #0a2342;
    background: none;
    border: none;
    font-weight: bold;
}

.modal-close:hover {
    color: #00bcd4;
}

.video-modal-content h3 {
    color: #0a2342;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.video-container {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 aspect ratio */
    height: 0;
    overflow: hidden;
    border-radius: 10px;
    background: #000;
    margin-bottom: 1.5rem;
}

.video-container iframe,
.video-container video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

/* Audio Tutorial Modal */
.audio-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    z-index: 2000;
    justify-content: center;
    align-items: center;
}

.audio-modal.active {
    display: flex;
}

.audio-modal-content {
    background: #fff;
    padding: 2rem;
    border-radius: 15px;
    max-width: 600px;
    width: 90%;
    position: relative;
    box-shadow: 0 10px 40px rgba(0,0,0,0.5);
}

.audio-player {
    width: 100%;
    margin: 1.5rem 0;
    border-radius: 8px;
}

.audio-icon {
    font-size: 4rem;
    color: #0a2342;
    text-align: center;
    margin: 1rem 0;
}

.audio-modal-content h3 {
    color: #0a2342;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.audio-modal-content p {
    color: #555;
    line-height: 1.8;
    margin-top: 1rem;
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
                <p><i class="fas fa-map-marker-alt"></i> Andries Potgieter Blvd, Vanderbijlpark 1911, <br>South Africa</p>
                <p><i class="fas fa-phone"></i> 016 980 8053</p>
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
        
        <!-- All Questions (shown by default) -->
        <div class="question-container" id="questionContainer">
            <div class="chat-question" onclick="showAnswer(1)">
                <strong>How do I create an account?</strong>
            </div>

            <div class="chat-question" onclick="showAnswer(2)">
                <strong>How to change my password?</strong>
            </div>

            <div class="chat-question" onclick="showAnswer(3)">
                <strong>What programming languages are available?</strong>
            </div>

            <div class="chat-question" onclick="showAnswer(4)">
                <strong>Is CodeCraftHub free to use?</strong>
            </div>

            <div class="chat-question" onclick="showAnswer(5)">
                <strong>How do I access course materials?</strong>
            </div>
        </div>

        <!-- Answer 1 Container -->
        <div class="answer-container" id="answerContainer1">
            <div class="active-question" onclick="hideAnswer(1)">
                <strong>How do I create an account?</strong>
            </div>
            <div class="chat-answer">
                Click on the "Login" button in the navigation bar, then select "Register" to create a new account. Fill in your details and submit the form.
            </div>
        </div>

        <!-- Answer 2 Container -->
        <div class="answer-container" id="answerContainer2">
            <div class="active-question" onclick="hideAnswer(2)">
                <strong>How to change my password?</strong>
            </div>
            <div class="chat-answer">
                After logging in, go to your profile settings. Click on "Change Password" and enter your current password followed by your new password. Click "Update" to save changes.
            </div>
        </div>

        <!-- Answer 3 Container -->
        <div class="answer-container" id="answerContainer3">
            <div class="active-question" onclick="hideAnswer(3)">
                <strong>What programming languages are available?</strong>
            </div>
            <div class="chat-answer">
                We offer resources for over 20 programming languages including Python, JavaScript, Java, C++, PHP, Ruby, Swift, and many more. Click "Get Started" to explore all available languages.
            </div>
        </div>

        <!-- Answer 4 Container -->
        <div class="answer-container" id="answerContainer4">
            <div class="active-question" onclick="hideAnswer(4)">
                <strong>Is CodeCraftHub free to use?</strong>
            </div>
            <div class="chat-answer">
                Yes! CodeCraftHub is completely free. We provide curated links to the best free resources and courses for each programming language.
            </div>
        </div>

        <!-- Answer 5 Container (Tutorial Options) -->
        <div class="answer-container" id="answerContainer5">
            <div class="active-question" onclick="hideAnswer(5)">
                <strong>How do I access course materials?</strong>
            </div>
            
            <!-- Tutorial Option Buttons -->
            <div class="tutorial-options">
                <button class="tutorial-btn" onclick="showVideoTutorial()">
                    <i class="fas fa-video"></i> Video Tutorial
                </button>
                <button class="tutorial-btn" onclick="showAudioTutorial()">
                    <i class="fas fa-headphones"></i> Audio Guide
                </button>
                <button class="tutorial-btn" onclick="showTextTutorial()">
                    <i class="fas fa-book"></i> Text Explanation
                </button>
            </div>

            <!-- Text Answer (appears inline when Text button is clicked) -->
            <div class="text-tutorial-answer" id="textTutorialAnswer">
                <strong>Step-by-Step Guide:</strong>
                <ol>
                    <li><strong>Step 1:</strong> Log in to your CodeCraftHub account using your email and password.</li>
                    <li><strong>Step 2:</strong> Click the <strong>"Get Started"</strong> button on the home page.</li>
                    <li><strong>Step 3:</strong> You'll be redirected to the Programming Languages Library page.</li>
                    <li><strong>Step 4:</strong> Use the search bar to find your desired programming language, or scroll through the available cards.</li>
                    <li><strong>Step 5:</strong> Click the <strong>"Open"</strong> button on any language card.</li>
                    <li><strong>Step 6:</strong> A popup will appear with curated external resource links.</li>
                    <li><strong>Step 7:</strong> Click any resource link to access free courses and tutorials.</li>
                </ol>
            </div>
        </div>

    </div>
</div>

<!-- Video Tutorial Modal -->
<div class="video-modal" id="videoModal">
    <div class="video-modal-content">
        <button class="modal-close" onclick="closeVideoTutorial()">&times;</button>
        <h3>📹 Video Tutorial - How to Access Course Materials</h3>
        
        <div class="video-container">
            <!-- Local Video File -->
            <video controls>
                <source src="Video.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    </div>
</div>

<!-- Audio Tutorial Modal -->
<div class="audio-modal" id="audioModal">
    <div class="audio-modal-content">
        <button class="modal-close" onclick="closeAudioTutorial()">&times;</button>
        <h3>🎧 Audio Guide - How to Access Course Materials</h3>
        
        <div class="audio-icon">
            <i class="fas fa-headphones"></i>
        </div>
        
        <!-- Audio Player -->
        <audio class="audio-player" controls>
            <source src="Tutorial-Audio.mp3" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>

       
    </div>
</div>

    <script>

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

        //ChatBot Script
        // Toggle chatbot window visibility
    function toggleChatbot() {
        const chatbot = document.getElementById('chatbotWindow');
        chatbot.classList.toggle('active');
    }

    // Show answer when question is clicked
    function showAnswer(id) {
        // Hide the question container (all questions)
        document.getElementById('questionContainer').classList.add('hidden');
        
        // Hide all answer containers
        const allAnswerContainers = document.querySelectorAll('.answer-container');
        allAnswerContainers.forEach(container => {
            container.classList.remove('active');
        });
        
        // Show the selected answer container
        document.getElementById('answerContainer' + id).classList.add('active');
        
        // If it's question 5, hide the text tutorial answer by default
        if (id === 5) {
            document.getElementById('textTutorialAnswer').classList.remove('active');
        }
    }

    // Hide answer and show all questions again
    function hideAnswer(id) {
        // Hide the current answer container
        document.getElementById('answerContainer' + id).classList.remove('active');
        
        // Show the question container (all questions)
        document.getElementById('questionContainer').classList.remove('hidden');
        
        // If it's question 5, also hide the text tutorial answer
        if (id === 5) {
            document.getElementById('textTutorialAnswer').classList.remove('active');
        }
    }

    // === TEXT TUTORIAL (Shows inline) ===
    function showTextTutorial() {
        const textAnswer = document.getElementById('textTutorialAnswer');
        textAnswer.classList.add('active');
        
        // Scroll to the answer smoothly
        textAnswer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // === VIDEO TUTORIAL FUNCTIONS ===
    function showVideoTutorial() {
        const modal = document.getElementById('videoModal');
        modal.classList.add('active');
        
        // Play the video automatically (for local videos)
        const video = modal.querySelector('video');
        if (video) {
            video.currentTime = 0;
            video.play();
        }
    }

    function closeVideoTutorial() {
        const modal = document.getElementById('videoModal');
        modal.classList.remove('active');
        
        // Stop YouTube video (if using YouTube)
        const iframe = modal.querySelector('iframe');
        if (iframe) {
            const iframeSrc = iframe.src;
            iframe.src = iframeSrc; // Reload iframe to stop video
        }
        
        // Stop local video
        const video = modal.querySelector('video');
        if (video) {
            video.pause();
            video.currentTime = 0;
        }
    }

    // === AUDIO TUTORIAL FUNCTIONS ===
    function showAudioTutorial() {
        const modal = document.getElementById('audioModal');
        modal.classList.add('active');
        
        // Play audio automatically
        const audio = modal.querySelector('audio');
        if (audio) {
            audio.play();
        }
    }

    function closeAudioTutorial() {
        const modal = document.getElementById('audioModal');
        modal.classList.remove('active');
        
        // Stop audio
        const audio = modal.querySelector('audio');
        if (audio) {
            audio.pause();
            audio.currentTime = 0;
        }
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

    // Close modals when clicking outside the content area
    window.addEventListener('click', function(event) {
        const videoModal = document.getElementById('videoModal');
        const audioModal = document.getElementById('audioModal');
        
        if (event.target === videoModal) {
            closeVideoTutorial();
        }
        if (event.target === audioModal) {
            closeAudioTutorial();
        }
    });

    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeVideoTutorial();
            closeAudioTutorial();
        }
    });
    </script>
</body>
</html>
