<?php
// --- FILE: api/unlock.php ---

header('Content-Type: application/json');
session_start();

// Make sure a user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit();
}

// Get the password the user typed
$data = json_decode(file_get_contents('php://input'), true);
$submittedPassword = $data['password'] ?? '';

// --- Database Connection Details ---
require '../db_config.php';
// Use the $mysqli_conn object created in the included file
$conn = $mysqli_conn;

try {
    

    // Get the REAL hashed password from the database for the logged-in user
    $stmt = $pdo->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if ($user && isset($user['password'])) {
        $hashedPasswordFromDB = $user['password'];

        // Use password_verify() to securely check if the submitted password matches the hashed one
        if (password_verify($submittedPassword, $hashedPasswordFromDB)) {
            // Success! The passwords match.
            echo json_encode(['success' => true]);
        } else {
            // Failure. The passwords do not match.
            echo json_encode(['success' => false]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Could not find user']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>