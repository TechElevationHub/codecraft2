<?php
/**
 * config.php
 * Centralized configuration for database connection and session handling.
 */

// ✅ Start session safely (only once)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Database connection settings
define('DB_HOST', 'localhost');     // usually 'localhost'
define('DB_USER', 'root');          // default user for XAMPP/WAMP
define('DB_PASS', '');              // default password (empty)
define('DB_NAME', 'codecrafthub');  // your database name

// ✅ Establish connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// ✅ Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// ✅ Ensure UTF-8 encoding
mysqli_set_charset($conn, "utf8");

// ✅ Optional: Persistent session (user remains logged in until logout)
if (isset($_SESSION['id']) || isset($_SESSION['admin_id'])) {
    // Refresh session activity time
    $_SESSION['last_activity'] = time();
} else {
    // Do nothing unless user logs in
}

// ✅ (Optional) Session timeout after 30 mins of inactivity
$inactive = 1800; // 1800 seconds = 30 minutes
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $inactive) {
    session_unset();
    session_destroy();
    header("Location: login.php?session_expired=1");
    exit();
} else {
    $_SESSION['last_activity'] = time();
}
?>
