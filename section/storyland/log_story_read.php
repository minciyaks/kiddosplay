<?php
session_start();

// Set content type to JSON
header('Content-Type: application/json');

require '../../db_config.php';
// Use the $mysqli_conn object created in the included file
$conn = $mysqli_conn;

if ($conn->connect_error) {
    // If DB fails, notify but allow session check to proceed
    echo json_encode(['success' => false, 'message' => 'Database connection failed for logging.']);
    exit();
}

// --- 2. Soft Check for User ID ---
$user_id = $_SESSION['user_id'] ?? 0;

// If user_id is 0, they are a guest. We skip logging but return a 'success' status 
// so the frontend doesn't show an error to the user.
if ($user_id === 0) {
    echo json_encode(['success' => true, 'message' => 'Logging skipped: User not logged in (Guest).']);
    $conn->close();
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['story_id']) || !isset($input['story_title'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    $conn->close();
    exit();
}

$story_id = $input['story_id'];
$story_title = $input['story_title'];

try {
    // Check if story was already read today
    $check_sql = "SELECT story_log_id FROM stories_read 
                  WHERE user_id = ? AND story_title = ? 
                  AND DATE(read_at) = CURDATE()";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("is", $user_id, $story_title);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $check_stmt->close();
    
    if ($result->num_rows > 0) {
        // Story already read today, just return success
        echo json_encode(['success' => true, 'message' => 'Story already logged today']);
        $conn->close();
        exit();
    }
    
    // Insert new story read record
    $insert_sql = "INSERT INTO stories_read (user_id, story_title, read_at) 
                    VALUES (?, ?, NOW())";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("is", $user_id, $story_title);
    
    if ($insert_stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Story read logged successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to log story read']);
    }
    $insert_stmt->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>