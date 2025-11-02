<?php
/**
 * Logout Script - logout.php
 * Destroys user session and redirects to home page
 */
require_once 'config.php';

session_start();

// Update login activity with session duration before destroying session
if (isset($_SESSION['login_activity_id']) && isset($_SESSION['login_time'])) {
    $login_activity_id = $_SESSION['login_activity_id'];
    $login_time = $_SESSION['login_time'];
    $session_duration = time() - $login_time;
    
    // Update the login_activity record with logout time and session duration
    $update_query = "UPDATE login_activity 
                    SET logout_time = NOW(), 
                        session_duration = '$session_duration' 
                    WHERE id = '$login_activity_id'";
    
    mysqli_query($conn, $update_query);
}

// Destroy all session data
session_unset();
session_destroy();

// Redirect to home page
header("Location: login.php?message=You have successfully logged out.");
exit();
?>