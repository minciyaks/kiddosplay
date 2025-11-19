<?php
header('Content-Type: application/json');

// --- 1. Database Connection ---
require '../../db_config.php';
// Use the $mysqli_conn object created in the included file
$conn = $mysqli_conn;

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit();
}

// --- 2. Get Data From JavaScript ---
// We expect user_id, question_id, score, is_correct, and attempts
$user_id = $_POST['user_id'];
$question_id = $_POST['question_id'];
$score = $_POST['score'];
$is_correct = $_POST['is_correct'];
$attempts = $_POST['attempts'];

// --- 3. Save to Database ---
// The SQL query matches your game_scores table structure.
// We don't need to insert score_id (it's AUTO_INCREMENT) or played_at (it has a DEFAULT).
$stmt = $conn->prepare(
    "INSERT INTO game_scores (user_id, question_id, score, is_correct, attempts) VALUES (?, ?, ?, ?, ?)"
);

// "iiiii" means five integer parameters
$stmt->bind_param("iiiii", $user_id, $question_id, $score, $is_correct, $attempts);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Score saved successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save score.']);
}

$stmt->close();
$conn->close();
?>