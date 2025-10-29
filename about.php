<?php
/**
 * About Page - about.php
 * Company information, mission, vision, and offerings
 */
require_once 'config.php';

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check login status
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - CodeCraftHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ------------------ GLOBAL ------------------ */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background: #f4f7fb; }

        /* ------------------ NAVIGATION ------------------ */
        nav { background: #0a2342; padding: 1rem 5%; position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.2); }
        .nav-container { display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto; }
        .logo-container { display: flex; align-items: center; gap: 8px; }
        .logo-img { height: 60px; width: auto; transition: all 0.3s ease; vertical-align: middle; }
        .logo-img:hover { transform: scale(1.05); }
        .logo { font-size: 1.8rem; font-weight: bold; color: #00bcd4; text-decoration: none; cursor: pointer; display: flex; align-items: center; border: none; outline: none; }
        .nav-links { display: flex; list-style: none; gap: 2rem; align-items: center; }
        .nav-links a { color: white; text-decoration: none; font-weight: bold; transition: color 0.3s; padding-bottom: 2px; }
        .nav-links a:hover { color: #00bcd4; }
        .user-welcome { color: #fff; font-weight: 600; display: flex; align-items: center; gap: 1rem; }
        .logout-btn { background: #00bcd4; color: white; border: none; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer; font-weight: 600; transition: background 0.3s; }
        .logout-btn:hover { background: #0199b3; }

        /* ------------------ HAMBURGER ------------------ */
        .hamburger { display: none; flex-direction: column; cursor: pointer; gap: 4px; }
        .hamburger span { width: 25px; height: 3px; background: white; transition: 0.3s; }

        /* ------------------ SIDEBAR ------------------ */
        .sidebar { position: fixed; top: 0; right: -300px; width: 300px; height: 100vh; background: #0a2342; transition: right 0.3s ease; z-index: 1001; padding: 2rem; box-shadow: -5px 0 15px rgba(0,0,0,0.3); }
        .sidebar.active { right: 0; }
        .sidebar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid #00bcd4; }
        .sidebar-close { color: white; font-size: 1.5rem; cursor: pointer; background: none; border: none; }
        .sidebar-links { list-style: none; }
        .sidebar-links li { margin-bottom: 1rem; }
        .sidebar-links a { color: white; text-decoration: none; font-size: 1.1rem; transition: color 0.3s; display: block; padding: 0.5rem 0; }
        .sidebar-links a:hover { color: #00bcd4; }
        .sidebar-user { color: #00bcd4; font-weight: 600; margin-bottom: 1rem; padding: 1rem 0; border-bottom: 1px solid #00bcd4; }

        /* ------------------ OVERLAY ------------------ */
        .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; display: none; }
        .overlay.active { display: block; }

        /* ------------------ HERO ------------------ */
        .hero-section { position: relative; overflow: hidden; padding: 5rem 2rem; text-align: center; color: #0a2342; }
        .hero-section::before { content: ""; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(rgba(255,255,255,0.3), rgba(255,255,255,0.3)), url('AboutUs.jpg') center/cover no-repeat; filter: blur(4px) brightness(0.9); z-index: 0; }
        .hero-section h1, .hero-section p { position: relative; z-index: 1; }
        .hero-section h1 { font-size: 3rem; margin-bottom: 1rem; }
        .hero-section p { font-size: 1.2rem; max-width: 800px; margin: 0 auto; }

        /* ------------------ CONTENT ------------------ */
        .content { max-width: 1200px; margin: 0 auto; padding: 3rem 2rem; }
        .background-section, .offerings-section { background: #fff; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 3rem; }
        .background-section h2, .offerings-section h2 { color: #0a2342; margin-bottom: 1rem; font-size: 2rem; text-align: center; }
        .background-section p { color: #555; line-height: 1.8; font-size: 1.05rem; text-align: justify; }

        /* Mission & Vision */
        .mission-vision { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-bottom: 3rem; }
        .mv-card { background: #fff; border-left: 5px solid #0a2342; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .mv-card:hover { transform: translateY(-5px); }
        .mv-card h2 { font-size: 2rem; margin-bottom: 1rem; color: #0a2342; }
        .mv-card p { line-height: 1.8; font-size: 1.05rem; color: #555; }

        /* Team Section */
        .team { padding: 80px 20px; text-align: center; background: #f4f7fb; color: #fff; }
        .team-header h2 { font-size: 2.5rem; color: #0a2342; margin-bottom: 10px; }
        .team-header p { font-size: 1rem; color: #555; margin-bottom: 40px; }
        .team-container { display: flex; justify-content: center; flex-wrap: wrap; gap: 40px; }
        .team-member { padding: 20px; border-radius: 10px; width: 300px; transition: transform 0.3s; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .team-member:hover { transform: translateY(-5px); }
        .team-member img { width: 100%; height: 250px; object-fit: cover; border-radius: 200px; margin-bottom: 15px; }
        .team-member h3 { margin-bottom: 5px; color: #0a2342; }
        .team-member p { color: #777; font-size: 0.9rem; }

        /* Programming Languages */
        .languages-section { text-align: center; padding: 3rem 1rem; }
        .languages-section h2 { font-size: 2rem; margin-bottom: 1.5rem; color: #004aad; }
        .dropdown-category { margin: 1rem auto; max-width: 600px; text-align: left; }
        .dropdown-btn { width: 100%; background-color: #0a2342; color: #fff; padding: 12px 18px; font-size: 1rem; font-weight: 600; border: none; border-radius: 6px; cursor: pointer; transition: background-color 0.3s ease; }
        .dropdown-btn:hover { background-color: #00bcd4; color: #fff; }
        .dropdown-content { display: none; background-color: #f9f9f9; padding: 12px 20px; border: 1px solid #ddd; border-radius: 0 0 8px 8px; animation: fadeIn 0.3s ease-in; }
        .dropdown-content span { display: inline-block; background-color: #e6f0ff; color: #004aad; padding: 8px 12px; margin: 5px; border-radius: 4px; font-weight: 500; transition: background-color 0.3s; }
        .dropdown-content span:hover { background-color: #004aad; color: white; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }

        /* ------------------ BACK TO TOP ------------------ */
        .back-to-top { position: fixed; bottom: 30px; left: 30px; background: #0a2342; color: #fff; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s; text-decoration: none; }
        .back-to-top:hover { background: #00bcd4; transform: translateY(-5px); }
        .back-to-top i { font-size: 1.5rem; }

        /* ------------------ FOOTER ------------------ */
        footer { background: #000532; color: #fff; padding: 1rem 5%; }
        .footer-container { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; }
        .footer-section h3 { color: #64b5f6; margin-bottom: 0.75rem; }
        .footer-section p { margin-bottom: 0.4rem; }
        .social-links { display: flex; gap: 1rem; margin-top: 1rem; }
        .social-links a { color: #fff; font-size: 1.5rem; transition: color 0.3s; }
        .social-links a:hover { color: #64b5f6; }
        .copyright { text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #333; }

        /* ------------------ RESPONSIVE ------------------ */
        @media (max-width: 768px) { .hero-section h1 { font-size: 2rem; } .nav-links { display: none; } .hamburger { display: flex; } .user-welcome { flex-direction: column; gap: 0.5rem; } }
        @media (max-width: 480px) { .logo { font-size: 1.5rem; } .logo-img { height: 50px; } }
    </style>
</head>
<body>
    <!-- NAVIGATION -->
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
                    <li class="user-welcome">Welcome, <?php echo htmlspecialchars($userName); ?>! <a href="logout.php" class="logout-btn">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>

            <div class="hamburger" id="hamburger"><span></span><span></span><span></span></div>
        </div>
    </nav>

    <!-- SIDEBAR -->
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

    <!-- HERO -->
    <section class="hero-section">
        <h1>About CodeCraftHub</h1>
        <p>Empowering developers worldwide with comprehensive programming resources and learning pathways. We believe in making quality programming education accessible to everyone.</p>
    </section>

    <!-- CONTENT -->
    <div class="content">
        <div class="background-section">
            <h2>Our Background</h2>
            <p>CodeCraftHub was founded in 2023 with a singular vision: to democratize programming education and make it accessible to aspiring developers worldwide. Our journey began when a group of passionate software engineers recognized the overwhelming amount of scattered resources available online and saw an opportunity to create a centralized platform that curates the best learning materials. Today, CodeCraftHub serves thousands of users globally, helping them navigate their coding education with confidence and clarity.</p>
        </div>

        <div class="mission-vision">
            <div class="mv-card">
                <h2>Our Mission</h2>
                <p>Provide a comprehensive, user-friendly platform connecting learners with the highest quality programming resources. Curate trusted courses, tutorials, and documentation for over 20 programming languages.</p>
            </div>
            <div class="mv-card">
                <h2>Our Vision</h2>
                <p>Become the world's leading gateway for programming education, fostering a global community of skilled developers with knowledge and confidence to build innovative solutions.</p>
            </div>
        </div>

        <!-- TEAM -->
        <section class="team">
            <div class="team-header"><h2>Meet Our Team</h2><p>Our talented and dedicated team behind CodeCraftHub</p></div>
            <div class="team-container">
                <div class="team-member"><img src="Shaun.jpg" alt="Shaun Shivambu"><h3>Shaun Shivambu</h3><p>Founder & CEO</p></div>
                <div class="team-member"><img src="Tumelo.jpg" alt="Tumelo Madau"><h3>Tumelo Madau</h3><p>UI/UX Designer</p></div>
                <div class="team-member"><img src="Angela.jpg" alt="Angela Nobela"><h3>Angela Nobela</h3><p>UI/UX Designer</p></div>
                <div class="team-member"><img src="Kamogelo.jpg" alt="Kamogelo Molamu"><h3>Kamogelo Molamu</h3><p>Lead Developer</p></div>
                <div class="team-member"><img src="Batseba.jpg" alt="Batseba Leshilo"><h3>Batseba Leshilo</h3><p>Lead Developer</p></div>
            </div>
        </section>

        <!-- PROGRAMMING LANGUAGES -->
        <section class="languages-section">
            <h2>Programming Languages We Cover</h2>

            <div class="dropdown-category">
                <button class="dropdown-btn">Frontend Languages & Frameworks</button>
                <div class="dropdown-content"><span>HTML</span><span>CSS</span><span>JavaScript</span><span>React</span><span>Bootstrap</span><span>jQuery</span><span>TypeScript</span></div>
            </div>

            <div class="dropdown-category">
                <button class="dropdown-btn">Backend Languages & Frameworks</button>
                <div class="dropdown-content"><span>Node.js</span><span>PHP</span><span>Python</span><span>Java</span><span>C#</span><span>Ruby</span><span>Go</span><span>Scala</span><span>Kotlin</span><span>Rust</span></div>
            </div>

            <div class="dropdown-category">
                <button class="dropdown-btn">Styling Frameworks</button>
                <div class="dropdown-content"><span>Bootstrap</span><span>Tailwind CSS</span><span>Bulma</span><span>Foundation</span><span>Materialize</span></div>
            </div>

            <div class="dropdown-category">
                <button class="dropdown-btn">Databases & Query Languages</button>
                <div class="dropdown-content"><span>SQL</span><span>MySQL</span><span>PostgreSQL</span><span>MongoDB</span><span>SQLite</span></div>
            </div>
        </section>
    </div>

    <!-- BACK TO TOP -->
    <a href="#" class="back-to-top"><i class="fas fa-arrow-up"></i></a>

    <!-- FOOTER -->
    <footer>
        <div class="footer-container">
            <div class="footer-section"><h3>CodeCraftHub</h3><p>Your gateway to programming excellence</p></div>
            <div class="footer-section"><h3>Contact</h3>
                <p><i class="fas fa-map-marker-alt"></i> 123 Tech Street, Silicon Valley, CA 94025</p>
                <p><i class="fas fa-phone"></i> +1 (555) 123-4567</p>
                <p><i class="fas fa-envelope"></i> info@codecrafthub.com</p>
            </div>
            <div class="footer-section"><h3>Follow Us</h3>
                <div class="social-links">
                    <a href="https://facebook.com" target="_blank"><i class="fab fa-facebook"></i></a>
                    <a href="https://twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="https://linkedin.com" target="_blank"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
        <div class="copyright">&copy; 2025 CodeCraftHub. All rights reserved.</div>
    </footer>

    <!-- SCRIPTS -->
    <script>
        // Scroll to top
        document.querySelector('.back-to-top').addEventListener('click', function(e){
            e.preventDefault(); window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Dropdown toggle
        document.querySelectorAll('.dropdown-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const content = btn.nextElementSibling;
                content.style.display = content.style.display === 'block' ? 'none' : 'block';
            });
        });

        // Sidebar toggle
        const hamburger = document.getElementById('hamburger');
        const sidebar = document.getElementById('sidebar');
        const sidebarClose = document.getElementById('sidebarClose');
        const overlay = document.getElementById('overlay');

        hamburger.addEventListener('click', () => { sidebar.classList.add('active'); overlay.classList.add('active'); });
        sidebarClose.addEventListener('click', () => { sidebar.classList.remove('active'); overlay.classList.remove('active'); });
        overlay.addEventListener('click', () => { sidebar.classList.remove('active'); overlay.classList.remove('active'); });
        document.querySelectorAll('.sidebar-links a').forEach(link => link.addEventListener('click', () => { sidebar.classList.remove('active'); overlay.classList.remove('active'); }));
    </script>
</body>
</html>
