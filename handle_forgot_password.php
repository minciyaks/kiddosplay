<?php
session_start();

// --- HELPER FUNCTIONS ---
function redirectWithError($message) {
    $_SESSION['forgot_password_error'] = $message;
    header("Location: forgot_password.php");
    exit();
}

function redirectWithSuccess($message) {
    $_SESSION['forgot_password_success'] = $message;
    header("Location: login.php"); // CHANGED: Redirect to login page on success is usually better UX
    exit();
}

// --- 1. INPUT VALIDATION ---

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    redirectWithError("Invalid request method.");
}

$email = strtolower(trim($_POST['email'] ?? ''));
// Auto-fill @gmail.com if missing (server-side backup)
if (strpos($email, '@') === false && !empty($email)) {
    $email .= '@gmail.com';
}

if (empty($email) || !preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email)) {
    redirectWithError("Please enter a valid @gmail.com email address.");
}

$new_password = $_POST['new_password'] ?? '';
if (strlen($new_password) < 6 || !preg_match('/[A-Z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
    redirectWithError("Password must be at least 6 characters long, with at least one uppercase letter and one number.");
}

$confirm_password = $_POST['confirm_password'] ?? '';
if ($new_password !== $confirm_password) {
    redirectWithError("Passwords do not match.");
}

require 'db_config.php';
// Use the $mysqli_conn object created in the included file
$conn = $mysqli_conn;

try {
   

    // A. Check if email exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();
        $conn->close();
        redirectWithError("Email not found.");
    }

    $user_data = $result->fetch_assoc();
    $user_id = $user_data['user_id'];
    $stmt->close();

    // B. Update Password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $update_stmt->bind_param("si", $hashed_password, $user_id);
    $update_stmt->execute();
    
    $update_stmt->close();
    $conn->close();

    redirectWithSuccess("Password reset successfully! Please login with your new password.");

} catch (Exception $e) {
    // Catch any database or other errors and show them nicely in the box
    // For debugging you could use: $e->getMessage() BUT don't show that to real users.
    redirectWithError("A system error occurred. Please try again later.");
}