<?php
// Start the session to access login information
session_start();


// user name for the avatar area
$username = $_SESSION['username'] ?? 'User';


// 1. Check if the user is actually logged in.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// 2. Decide if the welcome sequence should be shown.
$showWelcomeSequence = false;
if (!isset($_SESSION['welcome_shown'])) {
    $_SESSION['welcome_shown'] = true; // Mark it as shown for this session
    $showWelcomeSequence = true;       // Tell the page to show it this time
}

// 3. Get the user's age from the database.
$userAge = 0;

// --- Database Connection Details ---
require 'db_config.php';

try {
   
    $stmt = $pdo->prepare("SELECT age FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if ($user && isset($user['age'])) {
        $userAge = $user['age'];
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KiddosPlay - Fun Learning Hub</title>
    <link rel="stylesheet" href="css/home.css">
     <link rel="stylesheet" href="icons/css/fontawesome.min.css">
    <link rel="stylesheet" href="icons/css/solid.min.css">
    <link rel="stylesheet" href="icons/css/brands.min.css">
</head>
<body data-user-age="<?php echo htmlspecialchars($userAge); ?>">
    <script>
        const showWelcome = <?php echo json_encode($showWelcomeSequence); ?>;
    </script>
    
    <div id="startScreen" class="start-screen-overlay">
        <button id="startButton" class="start-button">Start Exploring!</button>
    </div>

 
    <header class="main-header">
        <div class="header-content">
            <div class="logo">
                <img src="images/logo.png" alt="KiddosPlay Logo">
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="parent_dashboard/dashboard.php" class="nav-item parent-dashboard-btn" data-tooltip="Parent Dashboard">
                        <i class="fas fa-chart-line"></i>
                    </a></li>
                    <li><a href="help/help.html" class="nav-item how-to-use-btn" data-tooltip="How to Use">
                        <i class="fas fa-question-circle"></i>
                    </a></li>
                    <li><a href="Parent_Guidelines/parent.html" class="nav-item parenting-btn" data-tooltip="Parenting Articles">
                        <i class="fas fa-book-open"></i>
                    </a></li>
                    <li><a href="#" class="nav-item" data-tooltip="Clock">
                       <i class="fa fa-clock"></i>
                       <span id="time-display">00:00</span>
                    </a></li>
                </ul>
            </nav>
            
              <div class="login-avatar-area">
       <button class="login-avatar-btn" id="loginAvatarBtn">
    <img src="images/avatar.png" alt="User Avatar" class="avatar-img">
    <span class="avatar-text"><?php echo htmlspecialchars($username); ?></span>
    </button>
    <div class="login-dropdown" id="loginDropdown">
    <!--<a href="#">My Profile</a>-->
  <a href="home_logout.php">
    <i class="fas fa-sign-out-alt"></i> Logout
</a>
    </div>
       </div>          
    </div>
    </header>

    <main class="room-area-wrapper">
    <div class="room-container">

        <a id="radio-link" href="#">
            <div class="hotspot radio" data-module="tunetown" data-intro-message="Hooray! 🎵 You opened the Radio! Let’s sing songs and learn fun rhymes!" data-audio-src="audio/radio.mp3"></div>
        </a>

        <a id="toybox-link" href="#">
            <div class="hotspot toybox" data-module="letterbeats" data-intro-message="Wow! 🎲 The Toy Box is open! Time to explore letters and sounds!" data-audio-src="audio/toybox.mp3"></div>
        </a>

        <a id="computer-link" href="#">
            <div class="hotspot computer" data-module="brainplay" data-intro-message="Cool! 🖥️ The Computer is open! Let’s play some simple brainy games!" data-audio-src="audio/computer.mp3"></div>
        </a>

        <a id="whiteboard-link" href="#">
            <div class="hotspot whiteboard" data-module="colorfun" data-intro-message="Yay! 🎨 You opened the Whiteboard! Let’s start drawing and coloring!" data-audio-src="audio/whiteboard.mp3"></div>
        </a>

        <a id="bookshelf-link" href="#">
            <div class="hotspot bookshelf" data-module="storyland" data-intro-message="Awesome! 🧸 You unlocked the Bookshelf! Let’s enjoy some fun tales!" data-audio-src="audio/bookshelf.mp3"></div>
        </a>

        <button class="sound-toggle-btn room-positioned-sound-btn" id="soundToggleBtn" data-tooltip="Toggle Sound">
            <i class="fas fa-volume-up" id="soundIcon"></i>
        </button>
    </div>
</main>
    <div class="character-modal-overlay" id="characterModalOverlay">
        <div class="character-display">
            <img src="images/character-talking.png.png" alt="Character Portrait" class="character-portrait">
        </div>
        <div class="character-modal-content">
            <div class="message-area" tabindex="0">
                <p id="characterMessage"></p>
                <div class="modal-buttons">
                    <button id="modalProceedButton">Let's Go!</button>
                    <button id="modalCloseButton">Not Now</button>
                </div>
            </div>
        </div>
    </div>
    <audio id="messageAudioPlayer"></audio>
    <script src="js/home.js"></script>
    <script src="js/timer.js"></script>
    <script src="js/age-customizer.js"></script>
    <script>
        // Define the section name for this specific page (the home page)
        const currentSection = 'dashboard';
    </script>
    <script src="js/activity-logger.js"></script> 
    
</body>
</html>