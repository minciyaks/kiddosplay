<?php
session_start();

// Database connection
require 'db_config.php';
// Use the $mysqli_conn object created in the included file
$conn = $mysqli_conn;

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$username = $_POST['username'];
$password = $_POST['password'];

// Find user by username
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Verify password
    if (password_verify($password, $user['password'])) {
        
        // Check user status
        if ($user['status'] === 'approved') {
            // SUCCESS: Set session variables and redirect to homepage
            $_SESSION['isLoggedIn'] = true;
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // *** ADD THIS LINE TO RESET THE TIMER ***
            $_SESSION['start_time'] = time();

            header("Location: home.php");
            exit();

        } else if ($user['status'] === 'pending') {
            // User exists but is not yet approved. Redirect to a dedicated page.
            header("Location: pending_approval.html");
            exit();

        } else {
            // User is rejected or inactive
            $_SESSION['login_error'] = "Your account has been rejected or is inactive.";
            header("Location: login.php"); // Redirect back to the form
            exit();
        }
        
    } else {
        // Password does not match
        $_SESSION['login_error'] = "Incorrect username or password.";
        header("Location: login.php"); // Redirect back to the form
        exit();
    }
} else {
    // No user found (use the same message for security)
    $_SESSION['login_error'] = "Incorrect username or password.";
    header("Location: login.php"); // Redirect back to the form
    exit();
}

$stmt->close();
$conn->close();
?>