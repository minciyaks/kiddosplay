<?php
// Start the session to access session variables
session_start();

// Get the user_id from the session.
if (isset($_SESSION['user_id'])) {
    $current_user_id = $_SESSION['user_id'];
} else {
    // Set to 0 if no user is logged in
    $current_user_id = 0;
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>ColorFun - KiddosPlay</title>
        <link rel="stylesheet" href="draw2_3.css" />
         <link rel="stylesheet" href="../../../icons/css/fontawesome.min.css">
    <link rel="stylesheet" href="../../../icons/css/solid.min.css">
    <link rel="stylesheet" href="../../../icons/css/brands.min.css">
    </head>
    <body>
        <header>
            <div class="logo">
                <img src="../../../images/logo.png" alt="Logo" />
            </div>
            <div class="center-icons">
                <a href="../../../home.php" class="nav-item" data-tooltip="Home"
                    ><i class="fas fa-home"></i
                ></a>
               
                
                <div class="nav-item" data-tooltip="Time Played">
                    <i class="fas fa-clock"></i>
                    <span id="time-display">00:00</span>
                </div>

            </div>
            <div class="title">COLOR FUN</div>
        </header>

        <div class="main-container">
            <div class="main-content">
                <div class="toolbar">
                    <button class="tool" id="pencil">✏️ Pencil</button>
                    <button class="tool" id="marker">🖊️ Marker</button>
                    <button class="tool" id="fill">🧺 Fill</button>
                    <div class="color-palette">
                        <button class="tool color-btn" data-color="#000000" title="Black"></button>
                        <button class="tool color-btn" data-color="#808080" title="Dark Gray"></button>
                        <button class="tool color-btn" data-color="#C0C0C0" title="Light Gray"></button>
                        <button class="tool color-btn" data-color="#FFFFFF" title="White"></button>
                        <button class="tool color-btn" data-color="#FF0000" title="Red"></button>
                        <button class="tool color-btn" data-color="#800000" title="Maroon"></button>
                        <button class="tool color-btn" data-color="#FF8000" title="Orange"></button>
                        <button class="tool color-btn" data-color="#FFFF00" title="Yellow"></button>
                        <button class="tool color-btn" data-color="#808000" title="Olive"></button>
                        <div class="color-break"></div>
                        <button class="tool color-btn" data-color="#008000" title="Green"></button>
                        <button class="tool color-btn" data-color="#00FF00" title="Lime"></button>
                        <button class="tool color-btn" data-color="#00FFFF" title="Cyan (Aqua)"></button>
                        <button class="tool color-btn" data-color="#008080" title="Teal"></button>
                        <button class="tool color-btn" data-color="#0000FF" title="Blue"></button>
                        <button class="tool color-btn" data-color="#000080" title="Navy"></button>
                        <button class="tool color-btn" data-color="#FF00FF" title="Magenta (Fuchsia)"></button>
                        <button class="tool color-btn" data-color="#800080" title="Purple"></button>
                        <button class="tool color-btn" data-color="#804000" title="Brown"></button>
                    </div>
                    <button class="tool" id="eraser">🧽 Eraser</button>
                    <button class="tool" id="clear">🗑 Clear</button>
                    <button class="tool" id="save">💾 Save</button>
                </div>
                <canvas id="whiteboard" width="1000" height="530"></canvas>
            </div>
        </div>
        <script src="draw2_3.js"></script>

        <div id="lock-screen">
            <div class="lock-box">
                <h2>Time's Up!</h2>
                <p>Please ask a parent to enter the password to continue.</p>
                <input type="password" id="parent-password" placeholder="Parent Password">
                <button id="unlock-button">Unlock</button>
                <p id="error-message" style="color: red;"></p>
            </div>
        </div>

        <script src="../../../js/timer.js"></script> 
        
        <script>
            // This variable is required by your activity-logger.js file.
            const currentSection = 'drawing'; 
            
            // This gets the user ID from the PHP block at the top of the file
            const currentUserId = <?php echo json_encode($current_user_id); ?>;
        </script>
        <script src="../../../js/activity-logger.js"></script>
    </body>
</html>