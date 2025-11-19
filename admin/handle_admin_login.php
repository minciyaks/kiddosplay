<?php
session_start();
require '../db_config.php';
// Use the $mysqli_conn object created in the included file
$conn = $mysqli_conn;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare a statement to SELECT the user if they are an approved admin
    $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE username = ? AND role = 'admin' AND status = 'approved'");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if exactly one admin user was found
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();

        // Verify the submitted password against the hash
        if (password_verify($password, $hashed_password)) {
            // SUCCESS: Password is correct
            session_regenerate_id(true);
            $_SESSION['admin_user_id'] = $user_id;
            $_SESSION['admin_username'] = $username;

            header("Location: admin_dashboard.php");
            exit();
        }
    }

    // FAILURE: If the script reaches this point, the login is invalid
    $_SESSION['error'] = "Invalid username or password.";
    header("Location: admin_login.php");
    exit();

} else {
    // Redirect if accessed directly
    header("Location: admin_login.php");
    exit();
}
?>