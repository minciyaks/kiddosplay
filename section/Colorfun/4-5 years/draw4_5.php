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
        <link rel="stylesheet" href="draw4_5.css" />
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
            <div class="sidebar">
                <h3>Shapes</h3>
                <div class="category">
                    <div class="image-gallery">
                        <div class="shape-container" draggable="true" data-path="M10,10 H90 V90 H10 Z" title="Square"> <svg viewBox="0 0 100 100" class="shape-image"> <path d="M10,10 H90 V90 H10 Z" stroke="black" stroke-width="4" fill="transparent" style="pointer-events: none" /> </svg> </div>
                        <div class="shape-container" draggable="true" data-path="M90,50 A40,40 0 1,1 10,50 A40,40 0 1,1 90,50 Z" title="Circle"> <svg viewBox="0 0 100 100" class="shape-image"> <path d="M90,50 A40,40 0 1,1 10,50 A40,40 0 1,1 90,50 Z" stroke="black" stroke-width="4" fill="transparent" style="pointer-events: none" /> </svg> </div>
                        <div class="shape-container" draggable="true" data-path="M50,10 L90,90 L10,90 Z" title="Triangle"> <svg viewBox="0 0 100 100" class="shape-image"> <path d="M50,10 L90,90 L10,90 Z" stroke="black" stroke-width="4" fill="transparent" style="pointer-events: none" /> </svg> </div>
                        <div class="shape-container" draggable="true" data-path="M50,0 L61,35 L98,35 L68,57 L79,91 L50,70 L21,91 L32,57 L2,35 L39,35 Z" title="Star"> <svg viewBox="0 0 100 100" class="shape-image"> <path d="M50,0 L61,35 L98,35 L68,57 L79,91 L50,70 L21,91 L32,57 L2,35 L39,35 Z" stroke="black" stroke-width="4" fill="transparent" style="pointer-events: none" /> </svg> </div>
                        <div class="shape-container" draggable="true" data-path="M50,90 L10,50 C10,20 40,10 50,30 C60,10 90,20 90,50 Z" title="Heart"> <svg viewBox="0 0 100 100" class="shape-image"> <path d="M50,90 L10,50 C10,20 40,10 50,30 C60,10 90,20 90,50 Z" stroke="black" stroke-width="4" fill="transparent" style="pointer-events: none" /> </svg> </div>
                        <div class="shape-container" draggable="true" data-path="M10,10 L10,90 L90,90 Z" title="Right-Angled Triangle"> <svg viewBox="0 0 100 100" class="shape-image"> <path d="M10,10 L10,90 L90,90 Z" stroke="black" stroke-width="4" fill="transparent" style="pointer-events: none" ></path> </svg> </div>
                        <div class="shape-container" draggable="true" data-path="M50,10 L90,30 L90,70 L50,90 L10,70 L10,30 Z" title="Hexagon"> <svg viewBox="0 0 100 100" class="shape-image"> <path d="M50,10 L90,30 L90,70 L50,90 L10,70 L10,30 Z" stroke="black" stroke-width="4" fill="transparent" style="pointer-events: none" ></path> </svg> </div>
                        <div class="shape-container" draggable="true" data-path="M30,10 H70 A20,20 0 0,1 90,30 V70 A20,20 0 0,1 70,90 H30 A20,20 0 0,1 10,70 V30 A20,20 0 0,1 30,10 Z" title="Rounded Rectangle"> <svg viewBox="0 0 100 100" class="shape-image"> <path d="M30,10 H70 A20,20 0 0,1 90,30 V70 A20,20 0 0,1 70,90 H30 A20,20 0 0,1 10,70 V30 A20,20 0 0,1 30,10 Z" stroke="black" stroke-width="4" fill="transparent" style="pointer-events: none" ></path> </svg> </div>
                        <div class="shape-container" draggable="true" data-path="M50,10 L90,50 L50,90 L10,50 Z" title="Diamond"> <svg viewBox="0 0 100 100" class="shape-image"> <path d="M50,10 L90,50 L50,90 L10,50 Z" stroke="black" stroke-width="4" fill="transparent" style="pointer-events: none" ></path> </svg> </div>
                        <div class="shape-container" draggable="true" data-path="M50,10 L90,40 L75,90 L25,90 L10,40 Z" title="Pentagon"> <svg viewBox="0 0 100 100" class="shape-image"> <path d="M50,10 L90,40 L75,90 L25,90 L10,40 Z" stroke="black" stroke-width="4" fill="transparent" style="pointer-events: none" ></path> </svg> </div>
                        
                        <div class="shape-container" draggable="true" data-path="M50,15 A40,30 0 1,0 50,85 A40,30 0 1,0 50,15 Z" title="Ellipse">
                            <svg viewBox="0 0 100 100" class="shape-image">
                              <path d="M50,15 A40,30 0 1,0 50,85 A40,30 0 1,0 50,15 Z" stroke="black" stroke-width="4" fill="transparent" style="pointer-events: none" />
                             </svg>
                        </div>

                        <div class="shape-container" draggable="true" data-path="M15,10 L85,10 L75,90 L5,90 Z" title="Parallelogram">
                            <svg viewBox="0 0 100 100" class="shape-image">
                                <path d="M15,10 L85,10 L75,90 L5,90 Z" stroke="black" stroke-width="4" fill="transparent" style="pointer-events: none" />
                            </svg>
                        </div>

                        <div class="shape-container" draggable="true" data-path="M20,10 L80,10 L90,90 L10,90 Z" title="Trapezoid">
                            <svg viewBox="0 0 100 100" class="shape-image">
                                <path d="M20,10 L80,10 L90,90 L10,90 Z" stroke="black" stroke-width="4" fill="transparent" style="pointer-events: none" />
                            </svg>
                        </div>

                        <div class="shape-container" draggable="true" data-path="M10,40 L70,40 L70,20 L90,50 L70,80 L70,60 L10,60 Z" title="Arrow">
                            <svg viewBox="0 0 100 100" class="shape-image">
                                <path d="M10,40 L70,40 L70,20 L90,50 L70,80 L70,60 L10,60 Z" stroke="black" stroke-width="4" fill="transparent" style="pointer-events: none" />
                            </svg>
                        </div>

                        <div class="shape-container" draggable="true" data-path="M70,25 A50,50 0 0,0 25,75 A30,30 0 0,1 70,25 Z" title="Crescent">
                            <svg viewBox="0 0 100 100" class="shape-image">
                                <path d="M70,25 A50,50 0 0,0 25,75 A30,30 0 0,1 70,25 Z" stroke="black" stroke-width="4" fill="transparent" style="pointer-events: none" />
                            </svg>
                        </div>

                        <div class="shape-container" draggable="true" data-path="M30,10 L70,10 L90,30 L90,70 L70,90 L30,90 L10,70 L10,30 Z" title="Octagon">
                            <svg viewBox="0 0 100 100" class="shape-image">
                                <path d="M30,10 L70,10 L90,30 L90,70 L70,90 L30,90 L10,70 L10,30 Z" stroke="black" stroke-width="4" fill="transparent" style="pointer-events: none" />
                            </svg>
                        </div>
                       <div class="shape-container" draggable="true" data-path="M20,60 C10,60 10,30 30,30 C30,10 60,10 60,30 C80,30 80,60 70,60 H20 Z" title="Cloud">
                             <svg viewBox="0 0 100 100" class="shape-image">
                                <path d="M20,60 C10,60 10,30 30,30 C30,10 60,10 60,30 C80,30 80,60 70,60 H20 Z" stroke="black" stroke-width="4" fill="transparent" style="pointer-events: none" />
                              </svg>
                        </div>
                        
                        <div class="shape-container" draggable="true" data-path="M50,10 L30,50 L45,50 L25,90 L75,40 L60,40 Z" title="Lightning Bolt">
                            <svg viewBox="0 0 100 100" class="shape-image">
                                <path d="M50,10 L30,50 L45,50 L25,90 L75,40 L60,40 Z" stroke="black" stroke-width="4" fill="transparent" style="pointer-events: none" />
                            </svg>
                        </div>
                        
                        <div class="shape-container" draggable="true" data-path="M30,10 H70 V30 H90 V70 H70 V90 H30 V70 H10 V30 H30 Z" title="Cross">
                            <svg viewBox="0 0 100 100" class="shape-image">
                                <path d="M30,10 H70 V30 H90 V70 H70 V90 H30 V70 H10 V30 H30 Z" stroke="black" stroke-width="4" fill="transparent" style="pointer-events: none" />
                            </svg>
                        </div>

                        <div class="shape-container" draggable="true" data-path="M50,10 L80,50 L50,90 L20,50 Z" title="Rhombus">
                            <svg viewBox="0 0 100 100" class="shape-image">
                              <path d="M50,10 L80,50 L50,90 L20,50 Z" stroke="black" stroke-width="4" fill="transparent" style="pointer-events: none" />
                            </svg>
                        </div>

                        <div class="shape-container" draggable="true" data-path="M10,50 H90 A40,40 0 0,0 10,50 Z" title="Semicircle">
                            <svg viewBox="0 0 100 100" class="shape-image">
                               <path d="M10,50 H90 A40,40 0 0,0 10,50 Z" stroke="black" stroke-width="4" fill="transparent" style="pointer-events: none" />
                             </svg>
                         </div>

                        </div>
                </div>
            </div>

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
        <script src="draw4_5.js"></script>

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