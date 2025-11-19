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
$imageData = $_POST['imageData']; // This is the Base64 encoded image string

// --- 3. Save the Image to a File ---
// The image data is sent as a "data URL", so we need to remove the header part.
$imageData = str_replace('data:image/png;base64,', '', $imageData);
$imageData = str_replace(' ', '+', $imageData);
$decodedImage = base64_decode($imageData);

// Create a unique filename to prevent files from being overwritten
$filename = 'drawing_' . $user_id . '_' . uniqid() . '.png';

// This is the local path for saving the file (relative to this script)
$localSavePath = 'image_uploads/' . $filename; 

// This is the full path from the project root (what we should save in the DB)
$databasePath = 'section/Colorfun/image_uploads/' . $filename; 

// Write the image data to the file
if (file_put_contents($localSavePath, $decodedImage)) {
    // --- 4. If File Save is Successful, Save Path to Database ---
    $stmt = $conn->prepare("INSERT INTO drawings (user_id, image_path) VALUES (?, ?)");
    
    // "is" means Integer for user_id, String for image_path
    $stmt->bind_param("is", $user_id, $databasePath);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Drawing saved successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save drawing path to database.']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save image file on server.']);
}

$conn->close();
?>