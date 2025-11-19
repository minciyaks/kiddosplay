<?php
header("Content-Type: application/json");

// --- Database Connection and Configuration ---
require '../db_config.php';
// Use the $mysqli_conn object created in the included file
$conn = $mysqli_conn;

if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $conn->connect_error,
    ]);
    exit();
}

// --- Input Handling ---
// The user_id is the ID of the child whose name and age we want to fetch.
$user_id = isset($_GET["user_id"]) ? intval($_GET["user_id"]) : 0;

if ($user_id === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid user ID provided. Cannot fetch child details.",
    ]);
    $conn->close();
    exit();
}

// --- Fetch Child's Username and Age ---
$sql_fetch_child_details = "SELECT username, age FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql_fetch_child_details);

if ($stmt === false) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to prepare statement: " . $conn->error,
    ]);
    $conn->close();
    exit();
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$child_details = $result->fetch_assoc();

if ($child_details) {
    echo json_encode([
        "status" => "success",
        "username" => $child_details["username"],
        "age" => $child_details["age"],
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Child with ID " . $user_id . " not found.",
    ]);
}

$stmt->close();
$conn->close();
?>
