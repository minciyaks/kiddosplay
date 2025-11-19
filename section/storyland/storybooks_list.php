<?php
session_start();

// --- 1. SECURE DATABASE CONNECTION ---
// Assuming this file is in the project root, this path is correct:
require '../../db_config.php';
// Use the $mysqli_conn object created in the included file
$conn = $mysqli_conn;

if ($conn->connect_error) {
    $conn = null; // Set to null if connection fails
}

// --- 2. REQUIRED LOGIC FILE ---
// Path is correct since 'storybook' is a subdirectory of 'storyland'
require_once 'storybook/stories_config.php'; 

// --- 3. Age Filtering Logic (Soft Check) ---
$user_id = $_SESSION['user_id'] ?? 0;
$user = null; 
$currentStorybooks = [];
$defaultAge = 4; // Default age for guests

// Fallback values for HTML display
$ageGroup = normalizeAgeToGroup($defaultAge); 
$message = "Welcome, Guest! Showing default stories. Log in to personalize by age.";
$username = "Guest";

if ($conn && $user_id > 0) {
    // LOGGED IN USER: Check database for specific age
    $sql = "SELECT username, age FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close(); // Close statement

    if ($user) {
        $username = $user['username'];
        $ageGroup = normalizeAgeToGroup($user['age']);
        // The $message variable is correctly updated here with the user's name
        $message = "Welcome, " . htmlspecialchars($username) . "! Showing stories for " . getAgeGroupDisplayName($ageGroup) . ".";
    }
}

// Filter the stories based on the final determined $ageGroup
if (function_exists('getStoriesByAgeGroup')) {
    $currentStorybooks = getStoriesByAgeGroup($ageGroup);
} else {
    // This happens if stories_config.php failed to load
    $currentStorybooks = [];
    $message = "CRITICAL ERROR: Story configuration is missing or broken.";
}

if ($conn) {
    $conn->close(); // Close database connection if it was opened
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Children's Stories - KiddosPlay</title>
    <link rel="stylesheet" href="storybooks_list.css">
      <link rel="stylesheet" href="../../icons/css/fontawesome.min.css">
    <link rel="stylesheet" href="../../icons/css/solid.min.css">
    <link rel="stylesheet" href="../../icons/css/brands.min.css">
</head>
<body>
    <div class="storybooks-container">
        <!-- Header -->
        <header class="storybooks-header">
            <div class="back-button">
                <a href="../../home.php" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Room</span>
                </a>
            </div>
            <div class="header-content">
                <h1>Children's Stories</h1>
                <p>Welcome, <?php echo htmlspecialchars($user['username']); ?>! Showing stories for <?php echo getAgeGroupDisplayName($ageGroup); ?>.</p>
            </div>

             <a href="#" class="nav-item clock-item" data-tooltip="Clock">
                <i class="fa fa-clock"></i>
                <span id="time-display">00:00</span>
            </a>
        </header>

        <!-- Storybooks Grid -->
        <main class="storybooks-main">
            <div class="story-grid">
                <?php foreach ($currentStorybooks as $story): ?>
                <div class="story-card">
                    <a href="storybook_viewer_pdf.php?story_id=<?php echo $story['id']; ?>" onclick="logStoryRead('<?php echo $story['id']; ?>', '<?php echo htmlspecialchars($story['title']); ?>')">
                        <img src="<?php echo $story['cover_image']; ?>" 
                             alt="<?php echo htmlspecialchars($story['title']); ?>" 
                             onerror="this.src='images/logo.png'">
                        <p><?php echo htmlspecialchars($story['title']); ?></p>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <script>
        function logStoryRead(storyId, storyTitle) {
            // Log story opening to database
            fetch('log_story_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    story_id: storyId,
                    story_title: storyTitle
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Story read logged successfully');
                } else {
                    console.error('Error logging story read:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Add hover effects
        document.addEventListener('DOMContentLoaded', function() {
            const storyCards = document.querySelectorAll('.story-card');
            
            storyCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
     <script src="../../js/timer.js"></script>
    
       <div id="lock-screen">
      <div class="lock-box">
        <h2>Time's Up!</h2>
        <p>Please ask a parent to enter the password to continue.</p>
        <input type="password" id="parent-password" placeholder="Parent Password">
        <button id="unlock-button">Unlock</button>
        <p id="error-message" style="color: red;"></p>
      </div>
    </div>
   <script>
        const currentSection = 'story_coverpage'; // Define the section name here
    </script>
    <script src="../../js/activity-logger.js"></script>

</body>
</html>
