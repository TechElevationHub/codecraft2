<?php
/**
 * Update Session Duration - update_session_duration.php
 * Handles updating session duration when user leaves the site
 */
require_once 'config.php';

session_start();

if (isset($_POST['login_activity_id']) && isset($_POST['session_duration'])) {
    $login_activity_id = intval($_POST['login_activity_id']);
    $session_duration = intval($_POST['session_duration']);
    
    // Update the login_activity record with logout time and session duration
    $update_query = "UPDATE login_activity 
                    SET logout_time = NOW(), 
                        session_duration = '$session_duration' 
                    WHERE id = '$login_activity_id'";
    
    mysqli_query($conn, $update_query);
}

// Also update your logout.php to handle session duration properly:

/**
 * Logout Page - logout.php
 * Handles user logout and session cleanup
 */
require_once 'config.php';

session_start();

// Update login activity with session duration before destroying session
if (isset($_SESSION['login_activity_id']) && isset($_SESSION['login_time'])) {
    $login_activity_id = $_SESSION['login_activity_id'];
    $login_time = $_SESSION['login_time'];
    $session_duration = time() - $login_time;
    
    $update_query = "UPDATE login_activity 
                    SET logout_time = NOW(), 
                        session_duration = '$session_duration' 
                    WHERE id = '$login_activity_id'";
    
    mysqli_query($conn, $update_query);
}

// Destroy all session variables
session_unset();
session_destroy();

// Redirect to login page with logout message
header('Location: login.php?message=Logout successful');
exit();