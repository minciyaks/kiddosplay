<?php
// Always start the session to access it
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page
header("location: login.php");
exit;
?>