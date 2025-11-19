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
$user_id = $_POST['user_id'];
$category = $_POST['category']; // 'letter' or 'animal_sound'
$value = $_POST['value'];       // 'A' or 'Cat'
$status = 'completed';

// --- 3. Save to Database (THE FIX IS HERE) ---

// **CHANGE 1: Added `completed_at` to the list of columns**
$stmt = $conn->prepare(
    "INSERT INTO phonics_progress (user_id, category, value, status, completed_at) 
     VALUES (?, ?, ?, ?, NOW())" 
     // **CHANGE 2: Added `NOW()` to insert the current date and time**
);

// "isss" means Integer, String, String, String
// (We don't need to add a type for NOW())
$stmt->bind_param("isss", $user_id, $category, $value, $status);

if ($stmt->execute()) {
  echo json_encode(['status' => 'success', 'message' => "Logged $category: $value"]);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Failed to log activity.']);
}

$stmt->close();
$conn->close();
?>