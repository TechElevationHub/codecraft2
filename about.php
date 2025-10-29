<?php
/**
 * About Page - about.php
 * Company information, mission, vision, and offerings
 */
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - CodeCraftHub</title>
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
       .hero-section {
            position: relative; /* allows pseudo-element positioning */
            overflow: hidden;   /* hides blur overflow */
            padding: 5rem 2rem;
            text-align: center;
            color: #0a2342;
        }

        /* 🔹 Add your custom image + blur effect */
        .hero-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                linear-gradient(rgba(255,255,255,0.3), rgba(255,255,255,0.3)), /* subtle white overlay */
                url('AboutUs.jpg') center/cover no-repeat;
            filter: blur(4px) brightness(0.9); /* blur + brightness for readability */
            z-index: 0; /* keep behind text */
        }

        /* 🔹 Make text stay clear above the blurred background */
        .hero-section h1,
        .hero-section p {
            position: relative;
            z-index: 1;
        }

        .hero-section h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #0a2342;
        }

        .hero-section p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
            color: #0a2342;
        }
        /* Content Sections */
        .content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
           
        }

        .background-section {
            background: #fff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 3rem;
        }

        .background-section h2 {
            color: #0a2342;
            margin-bottom: 1rem;
            font-size: 2rem;
        }

        .background-section p {
            color: #555;
            line-height: 1.8;
            font-size: 1.05rem;
        }

        /* Mission and Vision Cards */
        .mission-vision {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .mv-card {
            background: #fff;
            border-left: 5px solid #0a2342;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .mv-card:hover {
            transform: translateY(-5px);
        }

        .mv-card h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #0a2342;
        }

        .mv-card p {
            line-height: 1.8;
            font-size: 1.05rem;
            color: #555;
        }

        /* Offerings Section */
        .offerings-section {
            background: #fff;
            padding: 3rem 2rem;
            border-radius: 15px;
            margin-bottom: 3rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .offerings-section h2 {
            color: #0a2342;
            font-size: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .offerings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .offering-item {
            background: #f4f7fb;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            transition: all 0.3s;
        }

        .offering-item:hover {
            background: #e8f4f8;
            transform: translateY(-5px);
        }

        .offering-item i {
            font-size: 2.5rem;
            color: #0a2342;
            margin-bottom: 1rem;
        }

        .offering-item h3 {
            color: #0a2342;
            margin-bottom: 0.5rem;
        }

        .offering-item p {
            color: #555;
            font-size: 0.95rem;
        }

        .offer-section {
            text-align: center;
            padding: 3rem 1rem;
            background-color: #f9fafc;
        }

        .offer-section h2 {
           color: #004aad;
           font-size: 2rem;
           margin-bottom: 1.5rem;
        }
        .languages-section {
           text-align: center;
           padding: 3rem 1rem;
        }

        .languages-section h2 {
           font-size: 2rem;
           margin-bottom: 1.5rem;
           color: #004aad;
        }

        .dropdown-category {
           margin: 1rem auto;
           max-width: 600px;
           text-align: left;
        }

.dropdown-btn {
  width: 100%;
  background-color: #0a2342;
  color: #fff;
  padding: 12px 18px;
  font-size: 1rem;
  font-weight: 600;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.dropdown-btn:hover {
  color: #fff;
}

.dropdown-content {
  display: none;
  background-color: #f9f9f9;
  padding: 12px 20px;
  border: 1px solid #ddd;
  border-radius: 0 0 8px 8px;
  animation: fadeIn 0.3s ease-in;
}

.dropdown-content span {
  display: inline-block;
  background-color: #e6f0ff;
  color: #004aad;
  padding: 8px 12px;
  margin: 5px;
  border-radius: 4px;
  font-weight: 500;
  transition: background-color 0.3s;
}

.dropdown-content span:hover {
  background-color: #004aad;
  color: white;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ✅ Responsive */
@media (max-width: 600px) {
  .dropdown-toggle {
    font-size: 1rem;
    padding: 12px;
  }
  .dropdown-menu {
    font-size: 0.95rem;
  }
}

        .team {
    padding: 80px 20px;
    text-align: center;
    background: #f4f7fb;
    color: #fff;
}

.team-header h2 {
    font-size: 2.5rem;
    color: #0a2342;
    margin-bottom: 10px;
}

.team-header p {
    font-size: 1rem;
    color: #555;
    margin-bottom: 40px;
}

.team-container {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 40px;
}

.team-member {
    padding: 20px;
    border-radius: 10px;
    width: 300px;
    transition: transform 0.3s;
}

.team-member:hover {
    transform: translateY(-5px);
}

.team-member img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    border-radius: 200px;
    margin-bottom: 15px;
    
}

.team-member h3 {
    margin-bottom: 5px;
    color: #0a2342;
}

.team-member p {
    color: #777;
    font-size: 0.9rem;
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
        <h1>About CodeCraftHub</h1>
        <p>Empowering developers worldwide with comprehensive programming resources and learning pathways. 
           We believe in making quality programming education accessible to everyone.</p>
    </section>

    <!-- Content -->
    <div class="content">
        <!-- Background Section -->
        <div class="background-section">
            <h2>Our Background</h2>
            <p>CodeCraftHub was founded in 2023 with a singular vision: to democratize programming education and make it accessible to aspiring developers worldwide. Our journey began when a group of passionate software engineers recognized the overwhelming amount of scattered resources available online and saw an opportunity to create a centralized platform that curates the best learning materials. We understand that learning to code can be challenging, which is why we've carefully selected and organized resources from trusted sources, providing clear pathways for learners at every stage of their programming journey. Today, CodeCraftHub serves thousands of users globally, helping them navigate their coding education with confidence and clarity.</p>
        </div>

        <!-- Mission and Vision -->
        <div class="mission-vision">
            <div class="mv-card">
                <h2>Our Mission</h2>
                <p>To provide a comprehensive, user-friendly platform that connects learners with the highest quality programming resources. We strive to eliminate the confusion of information overload by curating trusted courses, tutorials, and documentation for over 20 programming languages, making the path to coding mastery clear and achievable for everyone.</p>
            </div>
            <div class="mv-card">
                <h2>Our Vision</h2>
                <p>To become the world's leading gateway for programming education, fostering a global community of skilled developers who have the knowledge and confidence to build innovative solutions. We envision a future where anyone, regardless of their background, can access quality programming education and transform their career aspirations into reality.</p>
            </div>
        </div>

                
    <!-- Team Section -->
<section class="team">
    <div class="team-header">
        <h2>Meet Our Team</h2>
        <p>Our talented and dedicated team behind CodeCraftHub</p>
    </div>
    <div class="team-container">
        <div class="team-member">
            <img src="Shaun.jpg" alt=" ">
            <h3>Shaun Shivambu</h3>
            <p>Founder & CEO</p>
        </div>
        <div class="team-member">
            <img src="Tumelo.jpg" alt=" ">
            <h3>Tumelo Madau</h3>
            <p>UI/UX Designer</p>
        </div>

        <div class="team-member">
            <img src="Angela.jpg" alt=" ">
            <h3>Angela Nobela</h3>
            <p>UI/UX Designer</p>
        </div>

        <div class="team-member">
            <img src="Kamogelo.jpg" alt=" ">
            <h3>Kamogelo Molamu</h3>
            <p>Lead Developer</p>
        </div>

         <div class="team-member">
            <img src="Batseba.jpg" alt=" ">
            <h3>Batseba Leshilo</h3>
            <p>Lead Developer</p>
        </div>
    </div>
</section>

      <!-- Programming Languages Section -->
<section class="languages-section">
  <h2>Programming Languages We Cover</h2>

  <div class="dropdown-category">
    <button class="dropdown-btn">Frontend Languages & Frameworks</button>
    <div class="dropdown-content">
      <span>HTML</span>
      <span>CSS</span>
      <span>JavaScript</span>
      <span>React</span>
      <span>Bootstrap</span>
      <span>jQuery</span>
      <span>TypeScript</span>
    </div>
  </div>

  <div class="dropdown-category">
    <button class="dropdown-btn">Backend Languages & Frameworks</button>
    <div class="dropdown-content">
      <span>Node.js</span>
      <span>PHP</span>
      <span>Python</span>
      <span>Java</span>
      <span>C#</span>
      <span>Ruby</span>
      <span>Go</span>
      <span>Scala</span>
      <span>Kotlin</span>
      <span>Rust</span>
    </div>
  </div>

  <div class="dropdown-category">
    <button class="dropdown-btn">Styling Frameworks</button>
    <div class="dropdown-content">
      <span>Bootstrap</span>
      <span>Tailwind CSS</span>
      <span>Bulma</span>
      <span>Foundation</span>
      <span>Materialize</span>
    </div>
  </div>

  <div class="dropdown-category">
    <button class="dropdown-btn">Databases & Query Languages</button>
    <div class="dropdown-content">
      <span>SQL</span>
      <span>MySQL</span>
      <span>PostgreSQL</span>
      <span>MongoDB</span>
      <span>SQLite</span>
    </div>
  </div>
</section>

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
        // Smooth scroll to top
        document.querySelector('.back-to-top').addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        
document.querySelectorAll('.dropdown-btn').forEach(button => {
  button.addEventListener('click', () => {
    const content = button.nextElementSibling;
    content.style.display = content.style.display === 'block' ? 'none' : 'block';
  });
});


    </script>
</body>
</html>