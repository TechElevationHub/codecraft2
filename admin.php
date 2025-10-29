<?php
include 'config.php';

// Redirect if not admin
if (!isset($_SESSION['email']) || (strpos($_SESSION['email'], '@codecraft.com') === false && !isset($_SESSION['admin_id']))) {
    header("Location: login.php");
    exit();
}

// Create tables if they don't exist
$tables_sql = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        user_type ENUM('user', 'admin') DEFAULT 'user',
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS courses (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        course_name VARCHAR(255) NOT NULL,
        expiry_date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS messages (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS login_activity (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11),
        username VARCHAR(255),
        login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        logout_time TIMESTAMP NULL,
        session_duration INT DEFAULT 0,
        ip_address VARCHAR(45),
        user_agent TEXT
    )",
    
    "CREATE TABLE IF NOT EXISTS user_analytics (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11),
        page_visited VARCHAR(255),
        visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        time_spent INT DEFAULT 0,
        ip_address VARCHAR(45)
    )"
];

foreach ($tables_sql as $sql) {
    mysqli_query($conn, $sql);
}

// Handle AJAX CRUD operations
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        // Users CRUD
        case 'update_user':
            $id = intval($_POST['id']);
            $username = mysqli_real_escape_string($conn, $_POST['username']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $type = mysqli_real_escape_string($conn, $_POST['user_type']);
            mysqli_query($conn, "UPDATE users SET username='$username', email='$email', user_type='$type' WHERE id='$id'");
            echo json_encode(['status'=>'updated']); 
            exit();

        case 'delete_user':
            $id = intval($_POST['id']);
            mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
            echo json_encode(['status'=>'deleted']); 
            exit();

        // Courses CRUD
        case 'add_course':
            $name = mysqli_real_escape_string($conn, $_POST['course_name']);
            $expiry = mysqli_real_escape_string($conn, $_POST['expiry_date']);
            mysqli_query($conn, "INSERT INTO courses (course_name, expiry_date) VALUES ('$name', '$expiry')");
            echo json_encode(['status'=>'added']); 
            exit();

        case 'update_course':
            $id = intval($_POST['id']);
            $name = mysqli_real_escape_string($conn, $_POST['course_name']);
            $expiry = mysqli_real_escape_string($conn, $_POST['expiry_date']);
            mysqli_query($conn, "UPDATE courses SET course_name='$name', expiry_date='$expiry' WHERE id='$id'");
            echo json_encode(['status'=>'updated']); 
            exit();

        case 'delete_course':
            $id = intval($_POST['id']);
            mysqli_query($conn, "DELETE FROM courses WHERE id='$id'");
            echo json_encode(['status'=>'deleted']); 
            exit();

        // Messages CRUD
        case 'send_message':
            $subject = mysqli_real_escape_string($conn, $_POST['subject']);
            $message = mysqli_real_escape_string($conn, $_POST['message']);
            mysqli_query($conn, "INSERT INTO messages (subject, message) VALUES ('$subject', '$message')");
            echo json_encode(['status'=>'sent']); 
            exit();

        case 'delete_message':
            $id = intval($_POST['id']);
            mysqli_query($conn, "DELETE FROM messages WHERE id='$id'");
            echo json_encode(['status'=>'deleted']); 
            exit();

        // Settings
        case 'update_profile':
            $username = mysqli_real_escape_string($conn, $_POST['username']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $pass = mysqli_real_escape_string($conn, $_POST['password']);
            $current_email = $_SESSION['email'];
            
            // Hash password if provided
            if (!empty($pass)) {
                $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
                mysqli_query($conn, "UPDATE users SET username='$username', email='$email', password='$hashed_password' WHERE email='$current_email'");
            } else {
                mysqli_query($conn, "UPDATE users SET username='$username', email='$email' WHERE email='$current_email'");
            }
            
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            echo json_encode(['status'=>'updated']); 
            exit();
            
        // Record logout time for analytics
        case 'record_logout':
            $user_id = intval($_POST['user_id']);
            $session_id = intval($_POST['session_id']);
            $duration = intval($_POST['duration']);
            mysqli_query($conn, "UPDATE login_activity SET logout_time=NOW(), session_duration='$duration' WHERE id='$session_id' AND user_id='$user_id'");
            echo json_encode(['status'=>'recorded']);
            exit();
    }
}

// Fetch data
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
$courses = mysqli_query($conn, "SELECT * FROM courses ORDER BY created_at DESC");
$messages = mysqli_query($conn, "SELECT * FROM messages ORDER BY created_at DESC");

// Counts
$user_count = $users ? mysqli_num_rows($users) : 0;
$course_count = $courses ? mysqli_num_rows($courses) : 0;
$message_count = $messages ? mysqli_num_rows($messages) : 0;

// Expired courses count
$expired = mysqli_query($conn, "SELECT COUNT(*) AS total FROM courses WHERE expiry_date < CURDATE()");
$expired_count = $expired ? mysqli_fetch_assoc($expired)['total'] : 0;

// Active users (logged in within last 30 minutes)
$active_users = mysqli_query($conn, "
    SELECT COUNT(DISTINCT user_id) AS active 
    FROM login_activity 
    WHERE logout_time IS NULL 
    AND login_time >= NOW() - INTERVAL 30 MINUTE
");
$active_users_count = $active_users ? mysqli_fetch_assoc($active_users)['active'] : 0;

// Average session duration
$avg_session = mysqli_query($conn, "
    SELECT AVG(session_duration) AS avg_duration 
    FROM login_activity 
    WHERE session_duration > 0
");
$avg_session_duration = $avg_session ? round(mysqli_fetch_assoc($avg_session)['avg_duration'] / 60, 1) : 0;

// Recent login activity for table
$recent_logins = mysqli_query($conn, "
    SELECT la.*, u.username, u.email 
    FROM login_activity la 
    LEFT JOIN users u ON la.user_id = u.id 
    ORDER BY la.login_time DESC 
    LIMIT 10
");

// Chart data - Login activity last 7 days
$chart_labels = [];
$chart_data = [];
$logins = mysqli_query($conn, "
    SELECT DATE(login_time) AS day, COUNT(*) AS count
    FROM login_activity
    WHERE login_time >= NOW() - INTERVAL 7 DAY
    GROUP BY day
    ORDER BY day ASC
");

if ($logins) {
    while ($r = mysqli_fetch_assoc($logins)) {
        $chart_labels[] = date('M j', strtotime($r['day']));
        $chart_data[] = $r['count'];
    }
}

// User type distribution for pie chart
$user_types = mysqli_query($conn, "
    SELECT user_type, COUNT(*) as count 
    FROM users 
    GROUP BY user_type
");
$user_type_labels = [];
$user_type_data = [];
$user_type_colors = ['#0d6efd', '#6f42c1'];

if ($user_types) {
    while ($row = mysqli_fetch_assoc($user_types)) {
        $user_type_labels[] = ucfirst($row['user_type']);
        $user_type_data[] = $row['count'];
    }
}

// Fill default data if no records
if (empty($chart_labels)) {
    $chart_labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    $chart_data = [12, 19, 8, 15, 12, 18, 14];
}

if (empty($user_type_labels)) {
    $user_type_labels = ['Users', 'Admins'];
    $user_type_data = [5, 1];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Dashboard - CodeCraftHub</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
<style>
:root {
    --primary: #0d6efd;
    --secondary: #6c757d;
    --success: #198754;
    --danger: #dc3545;
    --warning: #ffc107;
    --info: #0dcaf0;
    --dark: #212529;
    --light: #f8f9fa;
}

body {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    min-height: 100vh;
}

/* Sidebar Styles */
.sidebar {
    width: 280px;
    background: linear-gradient(180deg, #2c3e50 0%, #3498db 100%);
    color: white;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    transition: all 0.3s;
    z-index: 1000;
    box-shadow: 3px 0 15px rgba(0,0,0,0.1);
}

.sidebar.collapsed {
    width: 80px;
}

.sidebar-header {
    padding: 20px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    text-align: center;
}

.sidebar-header h2 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
}

.sidebar-header .logo {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 20px 0;
}

.sidebar-menu li {
    margin: 8px 0;
}

.sidebar-menu a {
    color: white;
    text-decoration: none;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.3s;
    border-radius: 8px;
    margin: 0 10px;
}

.sidebar-menu a:hover, .sidebar-menu a.active {
    background: rgba(255,255,255,0.1);
    transform: translateX(5px);
}

.sidebar-menu a i {
    width: 20px;
    text-align: center;
}

.sidebar.collapsed .menu-text {
    display: none;
}

.sidebar.collapsed .sidebar-header h2 {
    font-size: 0;
}

.sidebar.collapsed .sidebar-header h2:after {
    content: "CCH";
    font-size: 1.2rem;
}

/* Main Content */
.main-content {
    margin-left: 280px;
    padding: 20px;
    transition: all 0.3s;
    min-height: 100vh;
}

.main-content.expanded {
    margin-left: 80px;
}

/* Top Navigation */
.top-nav {
    background: white;
    padding: 15px 25px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 25px;
    display: flex;
    justify-content: between;
    align-items: center;
}

.nav-brand {
    display: flex;
    align-items: center;
    gap: 15px;
}

.nav-brand h3 {
    margin: 0;
    color: var(--dark);
    font-weight: 700;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: linear-gradient(45deg, var(--primary), var(--info));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1.2rem;
}

/* Cards */
.dashboard-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    border: none;
    transition: all 0.3s;
    height: 100%;
}

.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.card-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    margin-bottom: 15px;
}

.bg-primary-light { background: rgba(13, 110, 253, 0.1); color: var(--primary); }
.bg-success-light { background: rgba(25, 135, 84, 0.1); color: var(--success); }
.bg-warning-light { background: rgba(255, 193, 7, 0.1); color: var(--warning); }
.bg-danger-light { background: rgba(220, 53, 69, 0.1); color: var(--danger); }
.bg-info-light { background: rgba(13, 202, 240, 0.1); color: var(--info); }
.bg-purple-light { background: rgba(111, 66, 193, 0.1); color: #6f42c1; }

.card-value {
    font-size: 2rem;
    font-weight: 700;
    margin: 10px 0;
    color: var(--dark);
}

.card-label {
    color: var(--secondary);
    font-weight: 600;
}

.card-trend {
    font-size: 0.9rem;
    font-weight: 600;
}

.trend-up { color: var(--success); }
.trend-down { color: var(--danger); }

/* Charts */
.chart-container {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}

.chart-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 20px;
}

.chart-header h5 {
    margin: 0;
    font-weight: 700;
    color: var(--dark);
}

/* Tables */
.data-table {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.table th {
    background: var(--primary);
    color: white;
    border: none;
    padding: 15px;
    font-weight: 600;
}

.table td {
    padding: 15px;
    vertical-align: middle;
    border-color: #f1f3f4;
}

/* Responsive */
.sidebar-toggle {
    display: none;
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--dark);
}

@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }
    
    .sidebar.mobile-open {
        transform: translateX(0);
    }
    
    .main-content {
        margin-left: 0;
        padding: 15px;
    }
    
    .sidebar-toggle {
        display: block;
    }
    
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 999;
        display: none;
    }
    
    .overlay.active {
        display: block;
    }
}

/* Badges */
.badge {
    padding: 8px 12px;
    border-radius: 20px;
    font-weight: 600;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.fade-in {
    animation: fadeIn 0.6s ease-out;
}

/* Toast Customization */
.toast {
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Section Management */
.section {
    display: none;
    animation: fadeIn 0.5s ease-in;
}

.section.active {
    display: block;
}

/* Login Activity Table */
.session-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Mobile Overlay */
.mobile-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 999;
}

.mobile-overlay.active {
    display: block;
}
</style>
</head>
<body>

<!-- Mobile Overlay -->
<div class="mobile-overlay" id="mobileOverlay"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <i class="fas fa-code"></i>
            <h2>CodeCraftHub Admin</h2>
        </div>
    </div>
    
    <ul class="sidebar-menu">
        <li><a href="#dashboard" class="active"><i class="fas fa-chart-line"></i> <span class="menu-text">Dashboard</span></a></li>
        <li><a href="#users"><i class="fas fa-users"></i> <span class="menu-text">User Management</span></a></li>
        <li><a href="#courses"><i class="fas fa-book"></i> <span class="menu-text">Courses</span></a></li>
        <li><a href="#messages"><i class="fas fa-envelope"></i> <span class="menu-text">Messages</span></a></li>
        <li><a href="#analytics"><i class="fas fa-chart-bar"></i> <span class="menu-text">Analytics</span></a></li>
        <li><a href="#settings"><i class="fas fa-cog"></i> <span class="menu-text">Settings</span></a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span class="menu-text">Logout</span></a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content" id="mainContent">
    <!-- Top Navigation -->
    <nav class="top-nav">
        <div class="d-flex align-items-center">
            <button class="sidebar-toggle me-3" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="nav-brand">
                <i class="fas fa-tachometer-alt text-primary"></i>
                <h3>Admin Dashboard</h3>
            </div>
        </div>
        
        <div class="user-info">
            <div class="user-avatar">
                <?php echo strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)); ?>
            </div>
            <div>
                <div class="fw-bold"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></div>
                <small class="text-muted"><?php echo htmlspecialchars($_SESSION['email'] ?? 'admin@codecraft.com'); ?></small>
            </div>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-cog"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#settings"><i class="fas fa-user-cog me-2"></i>Profile Settings</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-bell me-2"></i>Notifications</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- DASHBOARD SECTION -->
    <div id="dashboard" class="section active fade-in">
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="dashboard-card">
                    <div class="card-icon bg-primary-light">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-value"><?php echo $user_count; ?></div>
                    <div class="card-label">Total Users</div>
                    <div class="card-trend trend-up">
                        <i class="fas fa-arrow-up"></i> 12% from last week
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="dashboard-card">
                    <div class="card-icon bg-success-light">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="card-value"><?php echo $active_users_count; ?></div>
                    <div class="card-label">Active Now</div>
                    <div class="card-trend trend-up">
                        <i class="fas fa-arrow-up"></i> 5% currently online
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="dashboard-card">
                    <div class="card-icon bg-warning-light">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="card-value"><?php echo $course_count; ?></div>
                    <div class="card-label">Total Courses</div>
                    <div class="card-trend trend-down">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $expired_count; ?> expired
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="dashboard-card">
                    <div class="card-icon bg-purple-light">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-value"><?php echo $avg_session_duration; ?>m</div>
                    <div class="card-label">Avg. Session</div>
                    <div class="card-trend trend-up">
                        <i class="fas fa-arrow-up"></i> 2min longer
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="chart-container">
                    <div class="chart-header">
                        <h5>User Login Activity (Last 7 Days)</h5>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary active">7D</button>
                            <button class="btn btn-sm btn-outline-primary">1M</button>
                            <button class="btn btn-sm btn-outline-primary">1Y</button>
                        </div>
                    </div>
                    <canvas id="loginChart" height="250"></canvas>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="chart-container">
                    <div class="chart-header">
                        <h5>User Distribution</h5>
                    </div>
                    <canvas id="userTypeChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="chart-container">
                    <div class="chart-header">
                        <h5>Recent User Activity</h5>
                        <button class="btn btn-sm btn-primary" onclick="refreshActivity()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Login Time</th>
                                    <th>Logout Time</th>
                                    <th>Duration</th>
                                    <th>IP Address</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($recent_logins && mysqli_num_rows($recent_logins) > 0): ?>
                                    <?php while ($activity = mysqli_fetch_assoc($recent_logins)): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar me-2" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                                        <?php echo strtoupper(substr($activity['username'] ?? 'U', 0, 1)); ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold"><?php echo htmlspecialchars($activity['username']); ?></div>
                                                        <small class="text-muted"><?php echo htmlspecialchars($activity['email']); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo date('M j, g:i A', strtotime($activity['login_time'])); ?></td>
                                            <td>
                                                <?php if ($activity['logout_time']): ?>
                                                    <?php echo date('M j, g:i A', strtotime($activity['logout_time'])); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Still active</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($activity['session_duration'] > 0): ?>
                                                    <?php echo round($activity['session_duration'] / 60, 1); ?> minutes
                                                <?php else: ?>
                                                    <span class="text-muted">In progress</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><code><?php echo $activity['ip_address'] ?? 'N/A'; ?></code></td>
                                            <td>
                                                <span class="badge <?php echo $activity['logout_time'] ? 'bg-secondary' : 'bg-success'; ?>">
                                                    <?php echo $activity['logout_time'] ? 'Completed' : 'Active'; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-info-circle text-muted me-2"></i>
                                            No login activity recorded yet
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- USERS SECTION -->
    <div id="users" class="section fade-in">
        <div class="chart-container">
            <div class="chart-header">
                <h5>User Management</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-plus me-2"></i>Add User
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($users && mysqli_num_rows($users) > 0): ?>
                            <?php mysqli_data_seek($users, 0); ?>
                            <?php while ($user = mysqli_fetch_assoc($users)): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-2" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                            </div>
                                            <?php echo htmlspecialchars($user['username']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $user['user_type'] === 'admin' ? 'bg-danger' : 'bg-primary'; ?>">
                                            <?php echo ucfirst($user['user_type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary edit-user" 
                                                    data-id="<?php echo $user['id']; ?>"
                                                    data-username="<?php echo htmlspecialchars($user['username']); ?>"
                                                    data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                                    data-type="<?php echo $user['user_type']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger delete-user" data-id="<?php echo $user['id']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-users text-muted me-2"></i>
                                    No users found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- COURSES SECTION -->
    <div id="courses" class="section fade-in">
        <div class="chart-container mb-4">
            <div class="chart-header">
                <h5>Add New Course</h5>
            </div>
            <div class="row g-3">
                <div class="col-md-5">
                    <input type="text" id="courseName" class="form-control" placeholder="Course name">
                </div>
                <div class="col-md-4">
                    <input type="date" id="courseExpiry" class="form-control">
                </div>
                <div class="col-md-3">
                    <button id="addCourse" class="btn btn-primary w-100">
                        <i class="fas fa-plus me-2"></i>Add Course
                    </button>
                </div>
            </div>
        </div>

        <div class="chart-container">
            <div class="chart-header">
                <h5>Course Management</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Course Name</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($courses && mysqli_num_rows($courses) > 0): ?>
                            <?php mysqli_data_seek($courses, 0); ?>
                            <?php while ($course = mysqli_fetch_assoc($courses)): 
                                $isExpired = strtotime($course['expiry_date']) < time();
                            ?>
                                <tr>
                                    <td><?php echo $course['id']; ?></td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm course-name" 
                                               value="<?php echo htmlspecialchars($course['course_name']); ?>">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control form-control-sm course-expiry" 
                                               value="<?php echo $course['expiry_date']; ?>">
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $isExpired ? 'bg-danger' : 'bg-success'; ?>">
                                            <?php echo $isExpired ? 'Expired' : 'Active'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($course['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary update-course" data-id="<?php echo $course['id']; ?>">
                                                <i class="fas fa-save"></i>
                                            </button>
                                            <button class="btn btn-outline-danger delete-course" data-id="<?php echo $course['id']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-book text-muted me-2"></i>
                                    No courses found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MESSAGES SECTION -->
    <div id="messages" class="section fade-in">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="chart-container">
                    <div class="chart-header">
                        <h5>Send Announcement</h5>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" id="messageSubject" class="form-control" placeholder="Enter subject">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea id="messageContent" class="form-control" rows="6" placeholder="Type your message here..."></textarea>
                    </div>
                    <button id="sendMessage" class="btn btn-primary w-100">
                        <i class="fas fa-paper-plane me-2"></i>Send Message
                    </button>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="chart-container">
                    <div class="chart-header">
                        <h5>Message History</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Subject</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($messages && mysqli_num_rows($messages) > 0): ?>
                                    <?php mysqli_data_seek($messages, 0); ?>
                                    <?php while ($message = mysqli_fetch_assoc($messages)): ?>
                                        <tr>
                                            <td><?php echo $message['id']; ?></td>
                                            <td class="fw-bold"><?php echo htmlspecialchars($message['subject']); ?></td>
                                            <td>
                                                <span class="d-inline-block text-truncate" style="max-width: 200px;">
                                                    <?php echo htmlspecialchars($message['message']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, g:i A', strtotime($message['created_at'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-danger delete-message" data-id="<?php echo $message['id']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="fas fa-envelope text-muted me-2"></i>
                                            No messages found
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ANALYTICS SECTION -->
    <div id="analytics" class="section fade-in">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="chart-container">
                    <div class="chart-header">
                        <h5>User Engagement</h5>
                    </div>
                    <canvas id="engagementChart" height="300"></canvas>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="chart-container">
                    <div class="chart-header">
                        <h5>Platform Performance</h5>
                    </div>
                    <div class="row g-3 mt-3">
                        <div class="col-6">
                            <div class="dashboard-card text-center">
                                <div class="card-icon bg-info-light mx-auto">
                                    <i class="fas fa-desktop"></i>
                                </div>
                                <div class="card-value">98.2%</div>
                                <div class="card-label">Uptime</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="dashboard-card text-center">
                                <div class="card-icon bg-success-light mx-auto">
                                    <i class="fas fa-bolt"></i>
                                </div>
                                <div class="card-value">0.8s</div>
                                <div class="card-label">Avg. Response</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SETTINGS SECTION -->
    <div id="settings" class="section fade-in">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="chart-container">
                    <div class="chart-header">
                        <h5>Profile Settings</h5>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" id="adminUsername" class="form-control" value="<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" id="adminEmail" class="form-control" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" id="adminPassword" class="form-control" placeholder="Leave blank to keep current">
                    </div>
                    <button id="updateProfile" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Profile
                    </button>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="chart-container">
                    <div class="chart-header">
                        <h5>System Information</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>PHP Version</span>
                            <span class="badge bg-primary"><?php echo phpversion(); ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Database</span>
                            <span class="badge bg-success">MySQL</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Total Users</span>
                            <span class="badge bg-info"><?php echo $user_count; ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Server Time</span>
                            <span class="badge bg-secondary"><?php echo date('Y-m-d H:i:s'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="fas fa-bell text-primary me-2"></i>
            <strong class="me-auto">Notification</strong>
            <small>Just now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage">
            Hello, world! This is a toast message.
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Global variables
let loginChart, userTypeChart, engagementChart;
const toastEl = document.getElementById('liveToast');
const toast = new bootstrap.Toast(toastEl, { delay: 4000 });

// Show toast notification
function showToast(message, type = 'info') {
    const toastMessage = document.getElementById('toastMessage');
    const toastHeader = toastEl.querySelector('.toast-header');
    
    toastMessage.textContent = message;
    
    // Update icon based on type
    const icon = toastHeader.querySelector('i');
    icon.className = `fas ${getToastIcon(type)} me-2 text-${type}`;
    
    toast.show();
}

function getToastIcon(type) {
    const icons = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
    };
    return icons[type] || 'fa-bell';
}

// Initialize charts
function initializeCharts() {
    // Login Activity Chart
    const loginCtx = document.getElementById('loginChart').getContext('2d');
    loginChart = new Chart(loginCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                label: 'Daily Logins',
                data: <?php echo json_encode($chart_data); ?>,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        borderDash: [5, 5]
                    }
                }
            }
        }
    });

    // User Type Distribution Chart
    const userTypeCtx = document.getElementById('userTypeChart').getContext('2d');
    userTypeChart = new Chart(userTypeCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($user_type_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($user_type_data); ?>,
                backgroundColor: ['#0d6efd', '#6f42c1'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Engagement Chart
    const engagementCtx = document.getElementById('engagementChart').getContext('2d');
    engagementChart = new Chart(engagementCtx, {
        type: 'bar',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Page Views',
                data: [1200, 1900, 1500, 2100, 1800, 2500, 2200],
                backgroundColor: 'rgba(13, 110, 253, 0.8)'
            }, {
                label: 'Unique Visitors',
                data: [800, 1200, 1000, 1400, 1100, 1600, 1300],
                backgroundColor: 'rgba(111, 66, 193, 0.8)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Navigation handling
document.querySelectorAll('.sidebar-menu a').forEach(link => {
    link.addEventListener('click', function(e) {
        if (this.getAttribute('href').startsWith('#')) {
            e.preventDefault();
            
            // Update active states
            document.querySelectorAll('.sidebar-menu a').forEach(a => a.classList.remove('active'));
            document.querySelectorAll('.section').forEach(section => section.classList.remove('active'));
            
            this.classList.add('active');
            const targetId = this.getAttribute('href').substring(1);
            document.getElementById(targetId).classList.add('active');
            
            // Close mobile sidebar if open
            if (window.innerWidth <= 768) {
                document.getElementById('sidebar').classList.remove('mobile-open');
                document.getElementById('mobileOverlay').classList.remove('active');
            }
        }
    });
});

// Sidebar toggle
document.getElementById('sidebarToggle').addEventListener('click', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('mobileOverlay');
    
    if (window.innerWidth <= 768) {
        sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('active');
    } else {
        sidebar.classList.toggle('collapsed');
        document.getElementById('mainContent').classList.toggle('expanded');
    }
});

// Close sidebar when clicking on overlay
document.getElementById('mobileOverlay').addEventListener('click', function() {
    document.getElementById('sidebar').classList.remove('mobile-open');
    this.classList.remove('active');
});

// User Management
document.querySelectorAll('.edit-user').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const username = this.dataset.username;
        const email = this.dataset.email;
        const type = this.dataset.type;
        
        // In a real application, you would open a modal or form
        showToast(`Editing user: ${username}`, 'info');
    });
});

document.querySelectorAll('.delete-user').forEach(btn => {
    btn.addEventListener('click', async function() {
        if (!confirm('Are you sure you want to delete this user?')) return;
        
        const id = this.dataset.id;
        try {
            const response = await fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'delete_user',
                    id: id
                })
            });
            
            const result = await response.json();
            if (result.status === 'deleted') {
                this.closest('tr').remove();
                showToast('User deleted successfully', 'success');
            } else {
                showToast('Error deleting user', 'error');
            }
        } catch (error) {
            showToast('Network error occurred', 'error');
        }
    });
});

// Course Management
document.getElementById('addCourse').addEventListener('click', async function() {
    const name = document.getElementById('courseName').value.trim();
    const expiry = document.getElementById('courseExpiry').value;
    
    if (!name || !expiry) {
        showToast('Please fill in all fields', 'warning');
        return;
    }
    
    try {
        const response = await fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'add_course',
                course_name: name,
                expiry_date: expiry
            })
        });
        
        const result = await response.json();
        if (result.status === 'added') {
            showToast('Course added successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('Error adding course', 'error');
        }
    } catch (error) {
        showToast('Network error occurred', 'error');
    }
});

document.querySelectorAll('.update-course').forEach(btn => {
    btn.addEventListener('click', async function() {
        const id = this.dataset.id;
        const row = this.closest('tr');
        const name = row.querySelector('.course-name').value.trim();
        const expiry = row.querySelector('.course-expiry').value;
        
        if (!name || !expiry) {
            showToast('Please fill in all fields', 'warning');
            return;
        }
        
        try {
            const response = await fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'update_course',
                    id: id,
                    course_name: name,
                    expiry_date: expiry
                })
            });
            
            const result = await response.json();
            if (result.status === 'updated') {
                showToast('Course updated successfully', 'success');
            } else {
                showToast('Error updating course', 'error');
            }
        } catch (error) {
            showToast('Network error occurred', 'error');
        }
    });
});

document.querySelectorAll('.delete-course').forEach(btn => {
    btn.addEventListener('click', async function() {
        if (!confirm('Are you sure you want to delete this course?')) return;
        
        const id = this.dataset.id;
        try {
            const response = await fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'delete_course',
                    id: id
                })
            });
            
            const result = await response.json();
            if (result.status === 'deleted') {
                this.closest('tr').remove();
                showToast('Course deleted successfully', 'success');
            } else {
                showToast('Error deleting course', 'error');
            }
        } catch (error) {
            showToast('Network error occurred', 'error');
        }
    });
});

// Message Management
document.getElementById('sendMessage').addEventListener('click', async function() {
    const subject = document.getElementById('messageSubject').value.trim();
    const message = document.getElementById('messageContent').value.trim();
    
    if (!subject || !message) {
        showToast('Please fill in all fields', 'warning');
        return;
    }
    
    try {
        const response = await fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'send_message',
                subject: subject,
                message: message
            })
        });
        
        const result = await response.json();
        if (result.status === 'sent') {
            showToast('Message sent successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('Error sending message', 'error');
        }
    } catch (error) {
        showToast('Network error occurred', 'error');
    }
});

document.querySelectorAll('.delete-message').forEach(btn => {
    btn.addEventListener('click', async function() {
        if (!confirm('Are you sure you want to delete this message?')) return;
        
        const id = this.dataset.id;
        try {
            const response = await fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'delete_message',
                    id: id
                })
            });
            
            const result = await response.json();
            if (result.status === 'deleted') {
                this.closest('tr').remove();
                showToast('Message deleted successfully', 'success');
            } else {
                showToast('Error deleting message', 'error');
            }
        } catch (error) {
            showToast('Network error occurred', 'error');
        }
    });
});

// Profile Settings
document.getElementById('updateProfile').addEventListener('click', async function() {
    const username = document.getElementById('adminUsername').value.trim();
    const email = document.getElementById('adminEmail').value.trim();
    const password = document.getElementById('adminPassword').value;
    
    if (!username || !email) {
        showToast('Please fill in all required fields', 'warning');
        return;
    }
    
    try {
        const response = await fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'update_profile',
                username: username,
                email: email,
                password: password
            })
        });
        
        const result = await response.json();
        if (result.status === 'updated') {
            showToast('Profile updated successfully', 'success');
        } else {
            showToast('Error updating profile', 'error');
        }
    } catch (error) {
        showToast('Network error occurred', 'error');
    }
});

// Refresh activity
function refreshActivity() {
    showToast('Refreshing activity data...', 'info');
    setTimeout(() => {
        // In a real application, this would fetch new data
        showToast('Activity data refreshed', 'success');
    }, 1000);
}

// Record session duration on page unload
window.addEventListener('beforeunload', function() {
    // This would typically record the session duration
    // For demo purposes, we'll just show a toast
    if (document.visibilityState === 'hidden') {
        // User is leaving the page
        showToast('Session recorded', 'info');
    }
});

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    showToast('Welcome to Admin Dashboard!', 'success');
    
    // Auto-refresh data every 30 seconds
    setInterval(() => {
        // In a real application, this would refresh the data
        console.log('Auto-refreshing data...');
    }, 30000);
});

// Handle window resize
window.addEventListener('resize', function() {
    if (window.innerWidth > 768) {
        document.getElementById('sidebar').classList.remove('mobile-open');
        document.getElementById('mobileOverlay').classList.remove('active');
    }
});
</script>

</body>
</html>