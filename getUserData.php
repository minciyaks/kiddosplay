<?php
// MUST BE THE VERY FIRST LINE
session_start();

// Tell the browser you are sending JSON data
header('Content-Type: application/json');

// Check if the user is logged in by looking for the session variable
if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true) {
    // If they are logged in, send back a success status and their username
    echo json_encode([
        'isLoggedIn' => true,
        'username'   => $_SESSION['username'] ?? 'User'
        // You can add other data like role, email, etc. here
    ]);
} else {
    // If they are not logged in, send back a failure status
    echo json_encode(['isLoggedIn' => false]);
}
?>