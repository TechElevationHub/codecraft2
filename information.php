<?php
/**
 * Information Page - information.php
 * Displays programming language cards with search functionality
 */
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['username'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programming Languages - CodeCraftHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
          font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        }
         
        body {
          background-color: #f4f7fb;
          color: #333;
          line-height: 1.6;
          padding: 2rem;
        }

        /* Navigation Bar - DO NOT CHANGE */
        nav {
            background: #0a2342;
            padding: 1rem 5%;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            margin: -2rem -2rem 2rem -2rem;
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
            font-weight: 600;
            transition: color 0.3s, border-bottom 0.3s;
            padding-bottom: 2px;
        }

        .nav-links a:hover {
            color: #00bcd4;
           
        }

        .user-welcome {
            color: #fff;
            font-weight: 600;
        }

        .languages-header {
          text-align: center;
          margin-bottom: 20px;
        }

        .search-wrapper {
          position: relative;
          display: inline-block;
          width: 50%;
          margin-top: 10px;
        }

        #searchInput {
          width: 100%;
          padding: 10px 40px 10px 15px;
          border-radius: 25px;
          border: 1px solid #ccc;
          font-size: 16px;
        }

        .search-icon {
          position: absolute;
          right: 15px;
          top: 50%;
          transform: translateY(-50%);
          color: #888;
          font-size: 18px;
        }

        .languages-section {
          text-align: center;
          margin-top: 3rem;
        }

        .section-title {
          font-size: 2rem;
          color: #0a2342;
          margin-bottom: 0.5rem;
        }

        .section-subtitle {
          font-size: 1rem;
          color: #666;
          margin-bottom: 2rem;
        }

        .languages-container {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
          gap: 1.5rem;
          justify-items: center;
          align-items: stretch; 
        }

        .language-card {
          display: flex;       
          flex-direction: column;  
          justify-content: space-between; 
          background: #fff;
          border-radius: 15px;
          padding: 1.5rem;
          width: 90%;
          max-width: 320px;
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
          transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .language-card:hover {
          transform: translateY(-5px);
          box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .language-icon i {
          font-size: 3rem;
          color: #0a2342;
        }

        .language-title {
          font-size: 1.2rem;
          color: #0a2342;
          margin-bottom: 0.5rem;
        }

        .language-description {
          font-size: 0.95rem;
          color: #555;
          margin-bottom: 1rem;
        }

        .language-button {
          width: 100px;       
          height: 50px;      
          display: inline-flex;  
          align-items: center;   
          justify-content: center; 
          background-color: #0a2342;
          color: #fff;
          text-decoration: none;
          border-radius: 12px;  
          font-weight: 670;
          font-size: 1rem;
          transition: background-color 0.3s ease, transform 0.2s ease;
          border: none;
          cursor: pointer;
        }

        .language-button:hover {
          background-color: #00bcd4;
          color: #fff;
          transform: scale(1.05);
        }

        .popup {
          display: none; 
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(0, 0, 0, 0.6); 
          justify-content: center;
          align-items: center;
          z-index: 1000;
        }

        .popup-content {
          background: #fff;
          padding: 2rem;
          border-radius: 15px;
          text-align: center;
          width: 350px;
          max-height: 80vh;
          overflow-y: auto;
          box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
          animation: popupFade 0.3s ease-in-out;
        }

        .popup h3 {
          color: #0a2342;
          margin-bottom: 1rem;
          font-size: 1.1rem;
        }

        .popup a {
          display: block;
          color: #0a2342;
          text-decoration: none;
          margin: 8px 0;
          font-weight: 600;
          transition: color 0.3s ease;
          padding: 5px;
          border-radius: 5px;
        }

        .popup a:hover {
          color: #00bcd4;
          background-color: rgba(0, 188, 212, 0.1);
        }

        .popup button {
          background-color: #0a2342;
          color: #fff;
          border: none;
          border-radius: 10px;
          padding: 10px 50px;
          cursor: pointer;
          font-size: 1.1rem;
          transition: background-color 0.3s ease;
          margin-top: 15px;
        }

        .popup button:hover {
          background-color: #00bcd4;
          color: #fff;
        }

        @keyframes popupFade {
          from {
            opacity: 0;
            transform: scale(0.9);
          }
          to {
            opacity: 1;
            transform: scale(1);
          }
        }

        .resource-link {
          display: flex;
          align-items: center;
          gap: 8px;
          justify-content: flex-start;
          padding: 8px 12px;
          border-radius: 5px;
          transition: all 0.3s ease;
        }

        .resource-link:hover {
          background: #f0f0f0;
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
            <div style="display: flex; align-items: center; gap: 2rem;">
                <ul class="nav-links" style="margin: 0;">
                    <li><a href="user.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
                <div class="user-welcome">
                    Welcome, <?php echo htmlspecialchars($userName); ?>!
                </div>
            </div>
        </div>
    </nav>

    <div class="languages-header">
        <h1>Programming Languages Library</h1>
        <p>Explore and learn about different programming languages and frameworks.</p>
        
        <div class="search-wrapper">
            <i class="fa fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Search languages..." onkeyup="filterLanguages()">
        </div>
    </div>

    <div class="languages-container">
        <!-- HTML Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-brands fa-html5"></i></div>
            <h3 class="language-title">HTML</h3>
            <p class="language-description">The standard language for creating the basic structure and content of web pages.</p>
            <button class="language-button" onclick="togglePopup('html')">Open</button>
        </div>

        <!-- CSS Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-brands fa-css3-alt"></i></div>
            <h3 class="language-title">CSS</h3>
            <p class="language-description">Adds color, style, and life to websites — making pages visually engaging and user-friendly.</p>
            <button class="language-button" onclick="togglePopup('css')">Open</button>
        </div>

        <!-- JavaScript Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-brands fa-js"></i></div>
            <h3 class="language-title">JavaScript</h3>
            <p class="language-description">Adds interactivity and dynamic functionality to websites and web apps.</p>
            <button class="language-button" onclick="togglePopup('javascript')">Open</button>
        </div>

        <!-- React Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-brands fa-react"></i></div>
            <h3 class="language-title">React</h3>
            <p class="language-description">A popular JavaScript library for building interactive and component-based user interfaces.</p>
            <button class="language-button" onclick="togglePopup('react')">Open</button>
        </div>

        <!-- Bootstrap Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-brands fa-bootstrap"></i></div>
            <h3 class="language-title">Bootstrap</h3>
            <p class="language-description">A front-end framework for designing responsive and mobile-first websites quickly.</p>
            <button class="language-button" onclick="togglePopup('bootstrap')">Open</button>
        </div>

        <!-- jQuery Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-solid fa-code"></i></div>
            <h3 class="language-title">jQuery</h3>
            <p class="language-description">A fast and small JavaScript library that simplifies HTML document traversal and manipulation.</p>
            <button class="language-button" onclick="togglePopup('jquery')">Open</button>
        </div>

        <!-- Python Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-brands fa-python"></i></div>
            <h3 class="language-title">Python</h3>
            <p class="language-description">A versatile language known for simplicity, data analysis, AI, and automation.</p>
            <button class="language-button" onclick="togglePopup('python')">Open</button>
        </div>

        <!-- Java Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-brands fa-java"></i></div>
            <h3 class="language-title">Java</h3>
            <p class="language-description">A robust, platform-independent language for enterprise and mobile apps.</p>
            <button class="language-button" onclick="togglePopup('java')">Open</button>
        </div>

        <!-- PHP Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-brands fa-php"></i></div>
            <h3 class="language-title">PHP</h3>
            <p class="language-description">A server-side scripting language for building dynamic web applications.</p>
            <button class="language-button" onclick="togglePopup('php')">Open</button>
        </div>

        <!-- C Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-solid fa-c"></i></div>
            <h3 class="language-title">C</h3>
            <p class="language-description">A foundational language for systems programming and low-level development.</p>
            <button class="language-button" onclick="togglePopup('c')">Open</button>
        </div>

        <!-- C++ Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-solid fa-code"></i></div>
            <h3 class="language-title">C++</h3>
            <p class="language-description">Used for high-performance systems, gaming engines, and embedded software.</p>
            <button class="language-button" onclick="togglePopup('cpp')">Open</button>
        </div>

        <!-- C# Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-brands fa-microsoft"></i></div>
            <h3 class="language-title">C#</h3>
            <p class="language-description">A modern, object-oriented language for Windows applications and game development with Unity.</p>
            <button class="language-button" onclick="togglePopup('csharp')">Open</button>
        </div>

        <!-- SQL Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-solid fa-database"></i></div>
            <h3 class="language-title">SQL</h3>
            <p class="language-description">The language for managing and analyzing data stored in relational databases.</p>
            <button class="language-button" onclick="togglePopup('sql')">Open</button>
        </div>

        <!-- MySQL Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-solid fa-database"></i></div>
            <h3 class="language-title">MySQL</h3>
            <p class="language-description">A widely-used relational database system for storing and managing structured data.</p>
            <button class="language-button" onclick="togglePopup('mysql')">Open</button>
        </div>

        <!-- Ruby Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-brands fa-ruby"></i></div>
            <h3 class="language-title">Ruby</h3>
            <p class="language-description">A dynamic language often used for web development with Ruby on Rails.</p>
            <button class="language-button" onclick="togglePopup('ruby')">Open</button>
        </div>

        <!-- Swift Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-brands fa-swift"></i></div>
            <h3 class="language-title">Swift</h3>
            <p class="language-description">Apple's programming language for iOS and macOS apps.</p>
            <button class="language-button" onclick="togglePopup('swift')">Open</button>
        </div>

        <!-- Kotlin Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-brands fa-android"></i></div>
            <h3 class="language-title">Kotlin</h3>
            <p class="language-description">Modern language for Android development and JVM-based applications.</p>
            <button class="language-button" onclick="togglePopup('kotlin')">Open</button>
        </div>

        <!-- Node.js Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-brands fa-node-js"></i></div>
            <h3 class="language-title">Node.js</h3>
            <p class="language-description">JavaScript runtime for server-side applications and backend services.</p>
            <button class="language-button" onclick="togglePopup('nodejs')">Open</button>
        </div>

        <!-- Go Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-solid fa-code"></i></div>
            <h3 class="language-title">Go</h3>
            <p class="language-description">Ideal for cloud services, backend systems, and concurrency.</p>
            <button class="language-button" onclick="togglePopup('go')">Open</button>
        </div>

        <!-- R Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-brands fa-r-project"></i></div>
            <h3 class="language-title">R</h3>
            <p class="language-description">Used for statistical computing, data analysis, and visualization.</p>
            <button class="language-button" onclick="togglePopup('r')">Open</button>
        </div>

        <!-- TypeScript Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-solid fa-code"></i></div>
            <h3 class="language-title">TypeScript</h3>
            <p class="language-description">A typed superset of JavaScript that helps write more maintainable code.</p>
            <button class="language-button" onclick="togglePopup('typescript')">Open</button>
        </div>

        <!-- Rust Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-brands fa-rust"></i></div>
            <h3 class="language-title">Rust</h3>
            <p class="language-description">A systems language focused on safety, speed, and concurrency.</p>
            <button class="language-button" onclick="togglePopup('rust')">Open</button>
        </div>

        <!-- Scala Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-solid fa-code"></i></div>
            <h3 class="language-title">Scala</h3>
            <p class="language-description">Combines object-oriented and functional programming on the JVM.</p>
            <button class="language-button" onclick="togglePopup('scala')">Open</button>
        </div>

        <!-- Dart Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-solid fa-code"></i></div>
            <h3 class="language-title">Dart</h3>
            <p class="language-description">Google's language for building Flutter mobile and web applications.</p>
            <button class="language-button" onclick="togglePopup('dart')">Open</button>
        </div>

        <!-- Perl Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-solid fa-code"></i></div>
            <h3 class="language-title">Perl</h3>
            <p class="language-description">Powerful text processing language with strong system administration capabilities.</p>
            <button class="language-button" onclick="togglePopup('perl')">Open</button>
        </div>

        <!-- Lua Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-solid fa-code"></i></div>
            <h3 class="language-title">Lua</h3>
            <p class="language-description">Lightweight scripting language used in game development and embedded systems.</p>
            <button class="language-button" onclick="togglePopup('lua')">Open</button>
        </div>

        <!-- Elixir Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-solid fa-code"></i></div>
            <h3 class="language-title">Elixir</h3>
            <p class="language-description">Functional language for building scalable, maintainable applications.</p>
            <button class="language-button" onclick="togglePopup('elixir')">Open</button>
        </div>

        <!-- MATLAB Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-solid fa-calculator"></i></div>
            <h3 class="language-title">MATLAB</h3>
            <p class="language-description">High-level language for numerical computing and algorithm development.</p>
            <button class="language-button" onclick="togglePopup('matlab')">Open</button>
        </div>

        <!-- Haskell Card -->
        <div class="language-card">
            <div class="language-icon"><i class="fa-solid fa-code"></i></div>
            <h3 class="language-title">Haskell</h3>
            <p class="language-description">Pure functional programming language with strong static typing.</p>
            <button class="language-button" onclick="togglePopup('haskell')">Open</button>
        </div>
       
    </div>

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

    <!-- Popups for all languages -->
    <!-- Popup for HTML -->
    <div class="popup" id="popup-html">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn HTML:</h3>
            <a href="https://www.w3schools.com/html/" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> W3Schools HTML Tutorial
            </a>
            <a href="https://developer.mozilla.org/en-US/docs/Web/HTML" target="_blank" class="resource-link">
                <i class="fa-solid fa-book"></i> MDN Web Docs
            </a>
            <a href="https://www.freecodecamp.org/learn/responsive-web-design/" target="_blank" class="resource-link">
                <i class="fa-solid fa-laptop-code"></i> FreeCodeCamp
            </a>
            <a href="https://www.codecademy.com/learn/learn-html" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> Codecademy
            </a>
            <button onclick="closePopup('html')">Close</button>
        </div>
    </div>

    <!-- Popup for CSS -->
    <div class="popup" id="popup-css">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn CSS:</h3>
            <a href="https://www.w3schools.com/css/" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> W3Schools CSS Tutorial
            </a>
            <a href="https://developer.mozilla.org/en-US/docs/Web/CSS" target="_blank" class="resource-link">
                <i class="fa-solid fa-book"></i> MDN Web Docs
            </a>
            <a href="https://css-tricks.com/" target="_blank" class="resource-link">
                <i class="fa-solid fa-paint-brush"></i> CSS Tricks
            </a>
            <a href="https://www.freecodecamp.org/learn/responsive-web-design/" target="_blank" class="resource-link">
                <i class="fa-solid fa-laptop-code"></i> FreeCodeCamp
            </a>
            <button onclick="closePopup('css')">Close</button>
        </div>
    </div>

    <!-- Popup for JavaScript -->
    <div class="popup" id="popup-javascript">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn JavaScript:</h3>
            <a href="https://www.w3schools.com/js/" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> W3Schools JavaScript
            </a>
            <a href="https://developer.mozilla.org/en-US/docs/Web/JavaScript" target="_blank" class="resource-link">
                <i class="fa-solid fa-book"></i> MDN JavaScript Guide
            </a>
            <a href="https://javascript.info/" target="_blank" class="resource-link">
                <i class="fa-solid fa-info-circle"></i> The Modern JavaScript Tutorial
            </a>
            <a href="https://www.freecodecamp.org/learn/javascript-algorithms-and-data-structures/" target="_blank" class="resource-link">
                <i class="fa-solid fa-laptop-code"></i> FreeCodeCamp JavaScript
            </a>
            <button onclick="closePopup('javascript')">Close</button>
        </div>
    </div>

    <!-- Popup for React -->
    <div class="popup" id="popup-react">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn React:</h3>
            <a href="https://reactjs.org/docs/getting-started.html" target="_blank" class="resource-link">
                <i class="fa-brands fa-react"></i> Official React Docs
            </a>
            <a href="https://www.freecodecamp.org/learn/front-end-development-libraries/#react" target="_blank" class="resource-link">
                <i class="fa-solid fa-laptop-code"></i> FreeCodeCamp React
            </a>
            <a href="https://react-tutorial.app/" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> React Tutorial
            </a>
            <a href="https://www.codecademy.com/learn/react-101" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> Codecademy React
            </a>
            <button onclick="closePopup('react')">Close</button>
        </div>
    </div>

    <!-- Popup for Bootstrap -->
    <div class="popup" id="popup-bootstrap">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn Bootstrap:</h3>
            <a href="https://getbootstrap.com/docs/5.3/getting-started/introduction/" target="_blank" class="resource-link">
                <i class="fa-brands fa-bootstrap"></i> Official Bootstrap Docs
            </a>
            <a href="https://www.w3schools.com/bootstrap5/" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> W3Schools Bootstrap
            </a>
            <a href="https://www.tutorialrepublic.com/twitter-bootstrap-tutorial/" target="_blank" class="resource-link">
                <i class="fa-solid fa-book-open"></i> Tutorial Republic
            </a>
            <button onclick="closePopup('bootstrap')">Close</button>
        </div>
    </div>

    <!-- Popup for jQuery -->
    <div class="popup" id="popup-jquery">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn jQuery:</h3>
            <a href="https://jquery.com/" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> Official jQuery Website
            </a>
            <a href="https://www.w3schools.com/jquery/" target="_blank" class="resource-link">
                <i class="fa-solid fa-book"></i> W3Schools jQuery
            </a>
            <a href="https://learn.jquery.com/" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> jQuery Learning Center
            </a>
            <button onclick="closePopup('jquery')">Close</button>
        </div>
    </div>

    <!-- Popup for Python -->
    <div class="popup" id="popup-python">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn Python:</h3>
            <a href="https://www.python.org/about/gettingstarted/" target="_blank" class="resource-link">
                <i class="fa-brands fa-python"></i> Official Python Docs
            </a>
            <a href="https://www.w3schools.com/python/" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> W3Schools Python
            </a>
            <a href="https://www.learnpython.org/" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> LearnPython.org
            </a>
            <a href="https://www.freecodecamp.org/learn/scientific-computing-with-python/" target="_blank" class="resource-link">
                <i class="fa-solid fa-laptop-code"></i> FreeCodeCamp Python
            </a>
            <button onclick="closePopup('python')">Close</button>
        </div>
    </div>

    <!-- Popup for Java -->
    <div class="popup" id="popup-java">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn Java:</h3>
            <a href="https://docs.oracle.com/javase/tutorial/" target="_blank" class="resource-link">
                <i class="fa-brands fa-java"></i> Official Java Tutorials
            </a>
            <a href="https://www.w3schools.com/java/" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> W3Schools Java
            </a>
            <a href="https://www.learnjavaonline.org/" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> Learn Java Online
            </a>
            <button onclick="closePopup('java')">Close</button>
        </div>
    </div>

    <!-- Popup for PHP -->
    <div class="popup" id="popup-php">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn PHP:</h3>
            <a href="https://www.php.net/manual/en/" target="_blank" class="resource-link">
                <i class="fa-brands fa-php"></i> Official PHP Manual
            </a>
            <a href="https://www.w3schools.com/php/" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> W3Schools PHP
            </a>
            <a href="https://www.phptutorial.net/" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> PHP Tutorial
            </a>
            <button onclick="closePopup('php')">Close</button>
        </div>
    </div>

    <!-- Popup for C -->
    <div class="popup" id="popup-c">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn C:</h3>
            <a href="https://www.learn-c.org/" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> Learn C Online
            </a>
            <a href="https://www.w3schools.com/c/" target="_blank" class="resource-link">
                <i class="fa-solid fa-book"></i> W3Schools C Tutorial
            </a>
            <a href="https://www.tutorialspoint.com/cprogramming/" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> TutorialsPoint C
            </a>
            <button onclick="closePopup('c')">Close</button>
        </div>
    </div>

    <!-- Popup for C++ -->
    <div class="popup" id="popup-cpp">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn C++:</h3>
            <a href="https://www.learncpp.com/" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> LearnCPP.com
            </a>
            <a href="https://cplusplus.com/doc/tutorial/" target="_blank" class="resource-link">
                <i class="fa-solid fa-book"></i> CPlusPlus Tutorial
            </a>
            <a href="https://www.w3schools.com/cpp/" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> W3Schools C++
            </a>
            <button onclick="closePopup('cpp')">Close</button>
        </div>
    </div>

    <!-- Popup for C# -->
    <div class="popup" id="popup-csharp">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn C#:</h3>
            <a href="https://docs.microsoft.com/en-us/dotnet/csharp/" target="_blank" class="resource-link">
                <i class="fa-brands fa-microsoft"></i> Microsoft C# Docs
            </a>
            <a href="https://www.w3schools.com/cs/" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> W3Schools C#
            </a>
            <a href="https://www.learncs.org/" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> Learn C# Online
            </a>
            <button onclick="closePopup('csharp')">Close</button>
        </div>
    </div>

    <!-- Popup for SQL -->
    <div class="popup" id="popup-sql">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn SQL:</h3>
            <a href="https://www.w3schools.com/sql/" target="_blank" class="resource-link">
                <i class="fa-solid fa-database"></i> W3Schools SQL
            </a>
            <a href="https://sqlzoo.net/" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> SQLZoo Interactive
            </a>
            <a href="https://mode.com/sql-tutorial/" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> Mode SQL Tutorial
            </a>
            <button onclick="closePopup('sql')">Close</button>
        </div>
    </div>

    <!-- Popup for MySQL -->
    <div class="popup" id="popup-mysql">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn MySQL:</h3>
            <a href="https://dev.mysql.com/doc/" target="_blank" class="resource-link">
                <i class="fa-solid fa-database"></i> Official MySQL Docs
            </a>
            <a href="https://www.w3schools.com/mysql/" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> W3Schools MySQL
            </a>
            <a href="https://www.mysqltutorial.org/" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> MySQL Tutorial
            </a>
            <button onclick="closePopup('mysql')">Close</button>
        </div>
    </div>

    
    <!-- Popup for Ruby -->
    <div class="popup" id="popup-ruby">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn Ruby:</h3>
            <a href="https://www.ruby-lang.org/en/documentation/" target="_blank" class="resource-link">
                <i class="fa-brands fa-ruby"></i> Official Ruby Docs
            </a>
            <a href="https://www.learnrubyonline.org/" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> Learn Ruby Online
            </a>
            <a href="https://www.codecademy.com/learn/learn-ruby" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> Codecademy Ruby
            </a>
            <button onclick="closePopup('ruby')">Close</button>
        </div>
    </div>

    <!-- Popup for Swift -->
    <div class="popup" id="popup-swift">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn Swift:</h3>
            <a href="https://swift.org/getting-started/" target="_blank" class="resource-link">
                <i class="fa-brands fa-swift"></i> Official Swift Docs
            </a>
            <a href="https://www.hackingwithswift.com/" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> Hacking with Swift
            </a>
            <a href="https://developer.apple.com/swift/" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> Apple Swift Resources
            </a>
            <button onclick="closePopup('swift')">Close</button>
        </div>
    </div>

    <!-- Popup for Kotlin -->
    <div class="popup" id="popup-kotlin">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn Kotlin:</h3>
            <a href="https://kotlinlang.org/docs/getting-started.html" target="_blank" class="resource-link">
                <i class="fa-brands fa-android"></i> Official Kotlin Docs
            </a>
            <a href="https://play.kotlinlang.org/" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> Kotlin Playground
            </a>
            <a href="https://developer.android.com/kotlin" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> Android Kotlin Guide
            </a>
            <button onclick="closePopup('kotlin')">Close</button>
        </div>
    </div>

    <!-- Popup for Node.js -->
    <div class="popup" id="popup-nodejs">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn Node.js:</h3>
            <a href="https://nodejs.org/en/docs/" target="_blank" class="resource-link">
                <i class="fa-brands fa-node-js"></i> Official Node.js Docs
            </a>
            <a href="https://www.w3schools.com/nodejs/" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> W3Schools Node.js
            </a>
            <a href="https://www.freecodecamp.org/news/learn-node-js-with-this-free-course/" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> FreeCodeCamp Node.js
            </a>
            <button onclick="closePopup('nodejs')">Close</button>
        </div>
    </div>

    <!-- Popup for Go -->
    <div class="popup" id="popup-go">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn Go:</h3>
            <a href="https://go.dev/learn/" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> Official Go Tutorial
            </a>
            <a href="https://tour.golang.org/" target="_blank" class="resource-link">
                <i class="fa-solid fa-book"></i> A Tour of Go
            </a>
            <a href="https://gobyexample.com/" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> Go by Example
            </a>
            <button onclick="closePopup('go')">Close</button>
        </div>
    </div>

    <!-- Popup for R -->
    <div class="popup" id="popup-r">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn R:</h3>
            <a href="https://www.r-project.org/about.html" target="_blank" class="resource-link">
                <i class="fa-brands fa-r-project"></i> Official R Project
            </a>
            <a href="https://www.datacamp.com/courses/free-introduction-to-r" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> DataCamp R Course
            </a>
            <a href="https://r4ds.had.co.nz/" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> R for Data Science
            </a>
            <button onclick="closePopup('r')">Close</button>
        </div>
    </div>

    <!-- Popup for TypeScript -->
    <div class="popup" id="popup-typescript">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn TypeScript:</h3>
            <a href="https://www.typescriptlang.org/docs/" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> Official TypeScript Docs
            </a>
            <a href="https://www.typescriptlang.org/play" target="_blank" class="resource-link">
                <i class="fa-solid fa-book"></i> TypeScript Playground
            </a>
            <a href="https://www.codecademy.com/learn/learn-typescript" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> Codecademy TypeScript
            </a>
            <button onclick="closePopup('typescript')">Close</button>
        </div>
    </div>

    <!-- Popup for Rust -->
    <div class="popup" id="popup-rust">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn Rust:</h3>
            <a href="https://www.rust-lang.org/learn" target="_blank" class="resource-link">
                <i class="fa-brands fa-rust"></i> Official Rust Learn
            </a>
            <a href="https://doc.rust-lang.org/book/" target="_blank" class="resource-link">
                <i class="fa-solid fa-book"></i> The Rust Book
            </a>
            <a href="https://rustlings.cool/" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> Rustlings Exercises
            </a>
            <button onclick="closePopup('rust')">Close</button>
        </div>
    </div>

    <!-- Popup for Scala -->
    <div class="popup" id="popup-scala">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn Scala:</h3>
            <a href="https://docs.scala-lang.org/getting-started/" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> Official Scala Docs
            </a>
            <a href="https://www.scala-exercises.org/" target="_blank" class="resource-link">
                <i class="fa-solid fa-book"></i> Scala Exercises
            </a>
            <a href="https://www.coursera.org/learn/scala" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> Coursera Scala
            </a>
            <button onclick="closePopup('scala')">Close</button>
        </div>
    </div>

    <!-- Popup for Dart -->
    <div class="popup" id="popup-dart">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn Dart:</h3>
            <a href="https://dart.dev/guides" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> Official Dart Guides
            </a>
            <a href="https://dartpad.dev/" target="_blank" class="resource-link">
                <i class="fa-solid fa-book"></i> DartPad
            </a>
            <a href="https://flutter.dev/docs/get-started/learn-dart" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> Flutter Dart Guide
            </a>
            <button onclick="closePopup('dart')">Close</button>
        </div>
    </div>

    <!-- Popup for Perl -->
    <div class="popup" id="popup-perl">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn Perl:</h3>
            <a href="https://www.perl.org/learn.html" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> Official Perl Learn
            </a>
            <a href="https://learn.perl.org/" target="_blank" class="resource-link">
                <i class="fa-solid fa-book"></i> Learn Perl
            </a>
            <a href="https://perldoc.perl.org/perlintro" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> Perl Introduction
            </a>
            <button onclick="closePopup('perl')">Close</button>
        </div>
    </div>

    <!-- Popup for Lua -->
    <div class="popup" id="popup-lua">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn Lua:</h3>
            <a href="https://www.lua.org/start.html" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> Official Lua Start
            </a>
            <a href="https://www.learn-lua.org/" target="_blank" class="resource-link">
                <i class="fa-solid fa-book"></i> Learn Lua Online
            </a>
            <a href="https://www.tutorialspoint.com/lua/" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> TutorialsPoint Lua
            </a>
            <button onclick="closePopup('lua')">Close</button>
        </div>
    </div>

    <!-- Popup for Elixir -->
    <div class="popup" id="popup-elixir">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn Elixir:</h3>
            <a href="https://elixir-lang.org/getting-started/introduction.html" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> Official Elixir Guide
            </a>
            <a href="https://elixirschool.com/" target="_blank" class="resource-link">
                <i class="fa-solid fa-book"></i> Elixir School
            </a>
            <a href="https://exercism.org/tracks/elixir" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> Exercism Elixir
            </a>
            <button onclick="closePopup('elixir')">Close</button>
        </div>
    </div>

    <!-- Popup for MATLAB -->
    <div class="popup" id="popup-matlab">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn MATLAB:</h3>
            <a href="https://www.mathworks.com/learn/tutorials/matlab-onramp.html" target="_blank" class="resource-link">
                <i class="fa-solid fa-calculator"></i> MATLAB Onramp
            </a>
            <a href="https://matlabacademy.mathworks.com/" target="_blank" class="resource-link">
                <i class="fa-solid fa-book"></i> MATLAB Academy
            </a>
            <a href="https://www.tutorialspoint.com/matlab/" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> TutorialsPoint MATLAB
            </a>
            <button onclick="closePopup('matlab')">Close</button>
        </div>
    </div>

    <!-- Popup for Haskell -->
    <div class="popup" id="popup-haskell">
        <div class="popup-content">
            <h3>Here are some excellent resources to learn Haskell:</h3>
            <a href="https://www.haskell.org/documentation/" target="_blank" class="resource-link">
                <i class="fa-solid fa-code"></i> Official Haskell Docs
            </a>
            <a href="http://learnyouahaskell.com/" target="_blank" class="resource-link">
                <i class="fa-solid fa-book"></i> Learn You a Haskell
            </a>
            <a href="https://www.haskell.org/tutorial/" target="_blank" class="resource-link">
                <i class="fa-solid fa-graduation-cap"></i> Haskell Tutorial
            </a>
            <button onclick="closePopup('haskell')">Close</button>
        </div>
    </div>

    <script>
        function togglePopup(language) {
            document.getElementById("popup-" + language).style.display = "flex";
        }

        function closePopup(language) {
            document.getElementById("popup-" + language).style.display = "none";
        }

        function filterLanguages() {
            const input = document.getElementById("searchInput");
            const filter = input.value.toLowerCase();
            const cards = document.getElementsByClassName("language-card");

            for (let i = 0; i < cards.length; i++) {
                const title = cards[i].getElementsByClassName("language-title")[0];
                const textValue = title.textContent || title.innerText;

                if (textValue.toLowerCase().indexOf(filter) > -1) {
                    cards[i].style.display = "";
                } else {
                    cards[i].style.display = "none";
                }
            }
        }

        // Close popup when clicking outside
        window.onclick = function(event) {
            const popups = document.getElementsByClassName('popup');
            for (let popup of popups) {
                if (event.target === popup) {
                    popup.style.display = "none";
                }
            }
        }

        // Close popup with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const popups = document.getElementsByClassName('popup');
                for (let popup of popups) {
                    popup.style.display = "none";
                }
            }
        });
    </script>

</body>
</html>