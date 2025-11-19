<?php
// In log_activity.php
session_start();
header('Content-Type: application/json');

// Check authentication and required data
if (!isset($_SESSION['isLoggedIn']) || !isset($_POST['eventType']) || !isset($_POST['sectionName'])) {
    echo json_encode(['status' => 'error', 'message' => 'Authentication or required parameters missing.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$event_type = $_POST['eventType'];
$section_name = $_POST['sectionName'];

// --- Database Connection ---
require '../db_config.php';
// Use the $mysqli_conn object created in the included file
$conn = $mysqli_conn;

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit();
}

if ($event_type === 'enter') {
    // --- User is ENTERING a section ---
    // Create a new log entry with a start_time
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, section_name, start_time) VALUES (?, ?, NOW())");
    $stmt->bind_param("is", $user_id, $section_name);
    
    if ($stmt->execute()) {
        $new_log_id = $conn->insert_id;
        echo json_encode(['status' => 'success', 'log_id' => $new_log_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create log entry.']);
    }
    $stmt->close();

} elseif ($event_type === 'leave' && isset($_POST['log_id'])) {
    // --- User is LEAVING a section ---
    // Update the existing log entry with an end_time
    $log_id = intval($_POST['log_id']);
    
    $stmt = $conn->prepare("UPDATE activity_log SET end_time = NOW() WHERE log_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $log_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update log entry.']);
    }
    $stmt->close();
}

$conn->close();
?>