<?php
/**
 * Logout Script - logout.php
 * Destroys user session and redirects to home page
 */
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to home page
header("Location: login.php?message=You have successfully logged out.");
exit();
?>