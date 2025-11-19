<?php
// Set the content type to JSON for the response
header('Content-Type: application/json');

// --- Database Connection Details ---
require '../../db_config.php';
// Use the $mysqli_conn object created in the included file
$conn = $mysqli_conn;

if ($conn->connect_error) {
  echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
  exit();
}

// --- 2. Get Data Sent From JavaScript ---
$user_id = $_POST['user_id'];
$song_title = $_POST['song_title'];

// --- 3. Save to Database ---
$stmt = $conn->prepare("INSERT INTO music_play_log (user_id, song_title) VALUES (?, ?)");
$stmt->bind_param("is", $user_id, $song_title); // "i" for integer, "s" for string

if ($stmt->execute()) {
  // Send a success message back
  echo json_encode(['status' => 'success', 'message' => 'Logged: ' . $song_title]);
} else {
  // Send an error message back
  echo json_encode(['status' => 'error', 'message' => 'Failed to log song.']);
}

$stmt->close();
$conn->close();
?>