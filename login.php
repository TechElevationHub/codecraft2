<?php
/**
 * Login & Register Page - login.php
 * Handles user authentication and registration
 */
require_once 'config.php';

// Initialize variables
$error = '';
$success = '';

// Define login status and username safely
$isLoggedIn = isset($_SESSION['user_id']);
$userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

// Handle Login
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    // Query to check user
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Record login activity BEFORE setting session
            $user_id = $user['id'];
            $username = $user['username'];
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $user_agent = mysqli_real_escape_string($conn, $_SERVER['HTTP_USER_AGENT']);
            
            // Insert login activity record
            $login_activity_query = "INSERT INTO login_activity (user_id, username, ip_address, user_agent) 
                                   VALUES ('$user_id', '$username', '$ip_address', '$user_agent')";
            mysqli_query($conn, $login_activity_query);
            
            // Get the last inserted login activity ID to store in session
            $login_activity_id = mysqli_insert_id($conn);
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['login_activity_id'] = $login_activity_id; // Store for logout tracking
            $_SESSION['login_time'] = time(); // Store login timestamp for session duration calculation
            
            // Check if email contains @codecraft.com for admin redirect
            if (strpos($user['email'], '@codecraft.com') !== false) {
                header('Location: admin.php');
            } else {
                header('Location: information.php');
            }
            exit();
        } else {
            $error = 'Invalid email or password!';
        }
    } else {
        $error = 'Invalid email or password!';
    }
}

// Handle Registration
if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['reg_email']);
    $password = $_POST['reg_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if ($password !== $confirm_password) {
        $error = 'Passwords do not match!';
    } else if (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters!';
    } else {
        // Check if user already exists
        $check_query = "SELECT * FROM users WHERE email = '$email' OR username = '$username'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = 'Username or email already exists!';
        } else {
            // Hash password and insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
            
            if (mysqli_query($conn, $insert_query)) {
                $success = 'Registration successful! You can now login.';
                
                // If it's a @codecraft.com email, show admin note
                if (strpos($email, '@codecraft.com') !== false) {
                    $success .= ' Since you registered with a @codecraft.com email, you will be redirected to admin panel after login.';
                }
            } else {
                $error = 'Registration failed! Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CodeCraftHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f4f7fb 0%, #e8f4f8 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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

        /* Logo section */
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

        /* Nav links */
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
            text-decoration: none;
            display: inline-block;
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

        /* Main Container */
        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .form-container {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
            display: flex;
        }

        .form-section {
            flex: 1;
            padding: 3rem;
        }

        .form-section h2 {
            color: #0a2342;
            margin-bottom: 0.5rem;
            font-size: 2rem;
        }

        .form-section p {
            color: #555;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            color: #0a2342;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #0a2342;
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: #0a2342;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            background: #00bcd4;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 188, 212, 0.3);
        }

        .switch-form {
            text-align: center;
            margin-top: 1rem;
            color: #555;
        }

        .switch-form a {
            color: #0a2342;
            text-decoration: none;
            font-weight: 600;
        }

        .switch-form a:hover {
            color: #00bcd4;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            border-left: 4px solid #c62828;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
        }

        /* Hide register form by default */
        .register-form {
            display: none;
        }

        .register-form.active {
            display: block;
        }

        .login-form.hide {
            display: none;
        }

        .logout-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 10px;
            margin: 20px auto;
            width: 80%;
            max-width: 500px;
            text-align: center;
            border-radius: 6px;
            font-weight: 600;
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Media Queries for Mobile */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .hamburger {
                display: flex;
            }
            
            .form-container {
                flex-direction: column;
            }

            .form-section {
                padding: 2rem;
            }

            .nav-links {
                gap: 1rem;
            }
            
            .user-welcome {
                flex-direction: column;
                gap: 0.5rem;
            }
        }

    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <div class="nav-container">
            <!-- Logo on the top-left -->
            <a href="user.php" class="logo-container">
                <img src="logo.jpg" alt="CodeCraftHub Logo" class="logo-img">
                <span class="logo">CodeCraftHub</span>
            </a>

            <!-- Navigation links -->
            <div style="display: flex; align-items: center; gap: 2rem;">
                <ul class="nav-links" style="margin: 0;">
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
                
                <!-- Hamburger Menu -->
                <div class="hamburger" id="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3 style="color: white;">Menu</h3>
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

    <?php
    if (isset($_GET['message'])) {
        echo "<div class='logout-message'>" . htmlspecialchars($_GET['message']) . "</div>";
    }
    ?>

    <!-- Main Container -->
    <div class="container">
        <div class="form-container">
            <!-- Login Form -->
            <div class="form-section login-form" id="loginForm">
                <h2>Welcome Back!</h2>
                <p>Login to access your account</p>

                <?php if ($error && isset($_POST['login'])): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" id="email" name="email" required placeholder="Enter your email">
                    </div>

                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Password</label>
                        <input type="password" id="password" name="password" required placeholder="Enter your password">
                    </div>

                    <button type="submit" name="login" class="btn-submit">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>

                <div class="switch-form">
                    Don't have an account? <a href="#" onclick="showRegister(); return false;">Register here</a>
                </div>
            </div>

            <!-- Register Form -->
            <div class="form-section register-form" id="registerForm">
                <h2>Create Account</h2>
                <p>Register to get started</p>

                <?php if ($error && isset($_POST['register'])): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user"></i> Username</label>
                        <input type="text" id="username" name="username" required placeholder="Choose a username">
                    </div>

                    <div class="form-group">
                        <label for="reg_email"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" id="reg_email" name="reg_email" required placeholder="Enter your email">
                    </div>

                    <div class="form-group">
                        <label for="reg_password"><i class="fas fa-lock"></i> Password</label>
                        <input type="password" id="reg_password" name="reg_password" required placeholder="Create a password">
                    </div>

                    <div class="form-group">
                        <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
                    </div>

                    <button type="submit" name="register" class="btn-submit">
                        <i class="fas fa-user-plus"></i> Register
                    </button>
                </form>

                <div class="switch-form">
                    Already have an account? <a href="#" onclick="showLogin(); return false;">Login here</a>
                </div>
            </div>
        </div>
    </div>

    <script>
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

        // Close sidebar when clicking on sidebar links
        document.querySelectorAll('.sidebar-links a').forEach(link => {
            link.addEventListener('click', () => {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            });
        });

        // Show register form
        function showRegister() {
            document.getElementById('loginForm').classList.add('hide');
            document.getElementById('registerForm').classList.add('active');
        }

        // Show login form
        function showLogin() {
            document.getElementById('registerForm').classList.remove('active');
            document.getElementById('loginForm').classList.remove('hide');
        }

        // If there's a registration error, show register form
        <?php if ($error && isset($_POST['register'])): ?>
            showRegister();
        <?php endif; ?>

        setTimeout(() => {
            const msg = document.querySelector('.logout-message');
            if (msg) msg.style.display = 'none';
        }, 3000);

        // Track session duration and send to server when page unloads
        window.addEventListener('beforeunload', function() {
            // Only track if user is logged in
            <?php if ($isLoggedIn && isset($_SESSION['login_activity_id'])): ?>
                const loginTime = <?php echo $_SESSION['login_time'] ?? 'null'; ?>;
                if (loginTime) {
                    const currentTime = Math.floor(Date.now() / 1000);
                    const sessionDuration = currentTime - loginTime;
                    
                    // Send session duration to server via AJAX
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'update_session_duration.php', false); // Synchronous request for reliability
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.send('login_activity_id=<?php echo $_SESSION["login_activity_id"]; ?>&session_duration=' + sessionDuration);
                }
            <?php endif; ?>
        });
    </script>
</body>
</html>