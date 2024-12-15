<?php
// Start the session to access session data
session_start();

// Destroy the session to log the user out
session_destroy();

// Redirect to the login page after logout
header('Location: login.php');
exit();
?>
