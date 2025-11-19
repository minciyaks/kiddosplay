<?php
header('Content-Type: application/json');

require '../db_config.php';
// Use the $mysqli_conn object created in the included file
$conn = $mysqli_conn;

if ($conn->connect_error) {
    // Return an error if connection fails
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// ... (Input Handling and Initialization) ...
$child_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d'); // Default to today

// --- NEW: FETCH CHILD'S AGE ---
$child_age = 0; // Default age
if ($child_id > 0) {
    $sql_age = "SELECT age FROM users WHERE user_id = ?";
    // We use a separate query here, not the helper function, because we need the value *now*
    $stmt_age = $conn->prepare($sql_age);
    if ($stmt_age) {
        $stmt_age->bind_param("i", $child_id);
        $stmt_age->execute();
        $result_age = $stmt_age->get_result();
        if ($result_age) {
            $age_row = $result_age->fetch_assoc();
            if ($age_row) {
                $child_age = $age_row['age'];
            }
        }
        $stmt_age->close();
    }
}

// Initialize the final data array
$response_data = [
    'child_name' => 'Child\'s Name', // Placeholder: Would be pulled from the 'users' table
    'selected_date' => $selected_date,
    'status' => 'success'
];

// --- Helper Function for Query Execution ---
function execute_query($conn, $sql, $types, ...$params) {
    $stmt = $conn->prepare($sql);
    if ($stmt === false) return false;
    if ($types) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

// --- 3. Data Calculation Logic (8 Queries) ---

// -------------------------------------------------------------
// A. TIME SUMMARY (activity_log)
// -------------------------------------------------------------
$time_summary = ['total_minutes_used' => 0, 'time_breakdown' => []];

// Query 1: Total Minutes
$sql_total_time = "SELECT SUM(TIMESTAMPDIFF(MINUTE, start_time, end_time)) AS total_minutes
                   FROM activity_log WHERE user_id = ? AND DATE(start_time) = ?";
$result = execute_query($conn, $sql_total_time, "is", $child_id, $selected_date);
if ($result) {
    $time_summary['total_minutes_used'] = $result->fetch_assoc()['total_minutes'] ?? 0;
}

// Query 2: Breakdown
$sql_breakdown = "SELECT section_name AS section, 
                         SUM(TIMESTAMPDIFF(MINUTE, start_time, end_time)) AS minutes
                  FROM activity_log 
                  WHERE user_id = ? AND DATE(start_time) = ?
                  GROUP BY section_name";
$result = execute_query($conn, $sql_breakdown, "is", $child_id, $selected_date);
if ($result) {
    $time_summary['time_breakdown'] = $result->fetch_all(MYSQLI_ASSOC);
}
$response_data['time_summary'] = $time_summary;

// -------------------------------------------------------------
// B. BRAINPLAY (game_scores)
// -------------------------------------------------------------
// Query 3: BrainPlay Metrics
$sql_brainplay = "
    SELECT 
        COUNT(DISTINCT question_id) AS questions_attended,
        SUM(attempts) AS total_attempts,
        SUM(CASE WHEN is_correct = 1 AND attempts = 1 THEN 1 ELSE 0 END) AS first_try_correct
    FROM game_scores
    WHERE user_id = ? AND DATE(played_at) = ?
";
$result = execute_query($conn, $sql_brainplay, "is", $child_id, $selected_date);
$metrics = $result ? $result->fetch_assoc() : [];

$attended = $metrics['questions_attended'] ?? 0;
$total_attempts = $metrics['total_attempts'] ?? 0;
$first_correct = $metrics['first_try_correct'] ?? 0;

// PHP Calculations
$avg_attempts = ($attended > 0) ? round($total_attempts / $attended, 1) : 0;
$success_percent = ($attended > 0) ? round(($first_correct / $attended) * 100) : 0;

$response_data['brainplay'] = [
    'questions_attended' => $attended,
    'avg_attempts' => $avg_attempts,
    'first_try_success_percent' => $success_percent
];

// -------------------------------------------------------------
// C. LETTERBEATS (phonics_progress) - (UPDATED)
// -------------------------------------------------------------

// --- NEW: Decide category and label based on age ---
$phonics_category = '';
$phonics_label = '';

// Assuming 2-3 year olds are <= 3, and 4-5 year olds are > 3
if ($child_age <= 3) {
    $phonics_category = 'animal_sound';
    $phonics_label = 'Sounds';
} else {
    $phonics_category = 'letter';
    $phonics_label = 'Letters';
}

// Query 4: New items completed TODAY
// (The SQL now uses '?' for the category)
$sql_new = "SELECT COUNT(*) AS new_items_today
            FROM phonics_progress
            WHERE user_id = ? AND category = ? AND status = 'completed' AND DATE(completed_at) = ?";
// (We now pass 3 variables: user_id, category, and date)
$result_new = execute_query($conn, $sql_new, "iss", $child_id, $phonics_category, $selected_date)->fetch_assoc();

// Query 5: TOTAL items mastered (Cumulative)
// (The SQL now uses '?' for the category)
$sql_total = "SELECT COUNT(DISTINCT value) AS total_mastered
              FROM phonics_progress
              WHERE user_id = ? AND category = ? AND status = 'completed' AND DATE(completed_at) <= ?";
// (We now pass 3 variables: user_id, category, and date)
$result_total = execute_query($conn, $sql_total, "iss", $child_id, $phonics_category, $selected_date)->fetch_assoc();

$response_data['letterbeats'] = [
    'new_letters_today' => $result_new['new_items_today'] ?? 0,
    'total_mastered' => $result_total['total_mastered'] ?? 0,
    'label' => $phonics_label // <-- NEW: Send the label to the frontend
];


// -------------------------------------------------------------
// D. LISTS: STORYLAND, TUNETOWN, COLORFUN
// -------------------------------------------------------------
// Query 6: StoryLand Titles
$sql_stories = "SELECT DISTINCT story_title AS title FROM stories_read
                WHERE user_id = ? AND DATE(read_at) = ?";
$stories = execute_query($conn, $sql_stories, "is", $child_id, $selected_date)->fetch_all(MYSQLI_ASSOC);
$response_data['storyland'] = [
    'titles' => array_column($stories, 'title'),
    'count' => count($stories)
];

// Query 7: TuneTown Titles
$sql_music = "SELECT DISTINCT song_title AS title FROM music_play_log
              WHERE user_id = ? AND DATE(played_at) = ?";
$music = execute_query($conn, $sql_music, "is", $child_id, $selected_date)->fetch_all(MYSQLI_ASSOC);
$response_data['tunetown'] = [
    'titles' => array_column($music, 'title'),
    'count' => count($music)
];

// Query 8: ColorFun Drawings (Crucial: retrieves the image_path)
$sql_drawings = "SELECT drawing_id AS id, image_path AS path
                 FROM drawings
                 WHERE user_id = ? AND DATE(created_at) = ?";
$drawings = execute_query($conn, $sql_drawings, "is", $child_id, $selected_date)->fetch_all(MYSQLI_ASSOC);
$response_data['colorfun'] = [
    'drawings' => $drawings, // Array of {'id', 'path'} objects
    'count' => count($drawings)
];

// --- 4. Final Output ---
$conn->close();
echo json_encode($response_data);
?>