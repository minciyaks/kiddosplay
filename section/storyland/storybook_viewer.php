<?php
session_start();

// --- 1. DIRECT DATABASE CONNECTION (Standardized Method) ---
// Note: This connects to the DB, but is not essential for viewing a story unless you were logging here.
require '../../db_config.php';
// Use the $mysqli_conn object created in the included file
$conn = $mysqli_conn;

if ($conn->connect_error) {
    $conn = null;
}

// --- 2. REQUIRED LOGIC FILE ---
// The path 'storybook/stories_config.php' is correct for your structure.
require_once 'storybook/stories_config.php'; 

// --- 3. Soft Check (Replaces strict login check) ---
$user_id = $_SESSION['user_id'] ?? 0; // Allows guests to view the story

// Get story ID from URL
$storyId = $_GET['story_id'] ?? '';

if (empty($storyId)) {
    // If no story ID, redirect back to the list
    header('Location: storybooks_list.php'); 
    exit();
}

// Get story data from configuration
$story = getStoryById($storyId);

if (!$story) {
    // If story ID is bad or not found in config, redirect back to the list
    header('Location: storybooks_list.php');
    exit();
}

$totalPages = $story['total_pages'];

// Close the database connection if it was successfully opened
if ($conn) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($story['title']); ?> - KiddosPlay</title>
    <link rel="stylesheet" href="storybook_viewer.css">
    <link rel="stylesheet" href="../../icons/css/fontawesome.min.css">
    <link rel="stylesheet" href="../../icons/css/solid.min.css">
    <link rel="stylesheet" href="../../icons/css/brands.min.css">
</head>
<body>
    <div class="storybook-viewer">
        <!-- Header -->
        <header class="viewer-header">
            <div class="header-left">
                <a href="storybooks_list.php" class="back-button">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Stories</span>
                </a>
            </div>
            <div class="header-center">
                <h1><?php echo htmlspecialchars($story['title']); ?></h1>
            </div>
             <div class="header-right">
                 <a href="#" class="nav-item clock-item" data-tooltip="Clock">
                 <i class="fa fa-clock"></i>
                 <span id="time-display">00:00</span>
                 </a>
                <div class="page-indicator">
                    <span id="currentPage">1</span> / <span id="totalPages"><?php echo $totalPages; ?></span>
                </div>
            </div>
        </header>

        <!-- Story Content -->
        <main class="story-content">
            <div class="story-page" id="storyPage">
                <div class="page-image">
                    <img id="pageImage" src="" alt="Story Page" onerror="this.src='images/logo.png'">
                </div>
                <div class="page-text">
                    <p id="pageText"></p>
                </div>
            </div>
        </main>

        <!-- Navigation Controls -->
        <nav class="story-navigation">
            <button id="prevBtn" class="nav-btn prev-btn" disabled>
                <i class="fas fa-chevron-left"></i>
                <span>Previous</span>
            </button>
            <div class="audio-controls">
                <button id="playPauseBtn" class="audio-btn" title="Play/Pause">
                    <i class="fas fa-play"></i>
                </button>
            </div>
            <button id="nextBtn" class="nav-btn next-btn">
                <span>Next</span>
                <i class="fas fa-chevron-right"></i>
            </button>
        </nav>

        <!-- Audio Player -->
        <audio id="audioPlayer" preload="auto"></audio>
    </div>

    <script>
        // Story data
        const storyData = <?php echo json_encode($story); ?>;
        const totalPages = <?php echo $totalPages; ?>;
        
        // Current page
        let currentPageIndex = 0;
        let isPlaying = false;
        let isMuted = false;
        let autoPlayEnabled = false;
        
        // DOM elements
        const pageImage = document.getElementById('pageImage');
        const pageText = document.getElementById('pageText');
        const currentPageSpan = document.getElementById('currentPage');
        const totalPagesSpan = document.getElementById('totalPages');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const playPauseBtn = document.getElementById('playPauseBtn');
        const audioPlayer = document.getElementById('audioPlayer');
        
        // Initialize
        function init() {
            loadPage(0);
            updateNavigation();
        }
        
        // Load page
        function loadPage(pageIndex) {
            if (pageIndex < 0 || pageIndex >= totalPages) return;
            
            currentPageIndex = pageIndex;
            const page = storyData.pages[pageIndex];
            
            // Update content - use PDF page images
            const pageImagePath = `storybook/${storyData.id}/pages/page_${String(pageIndex + 1).padStart(2, '0')}.jpg`;
            pageImage.src = pageImagePath;
            pageText.textContent = page.text;
            currentPageSpan.textContent = pageIndex + 1;
            
            // Update audio - set segment times
            audioPlayer.src = storyData.audio_file;
            audioPlayer.load();
            
            // Update navigation
            updateNavigation();
            
            // Stop current audio
            stopAudio();
        }
        
        // Update navigation buttons
        function updateNavigation() {
            prevBtn.disabled = currentPageIndex === 0;
            nextBtn.disabled = currentPageIndex === totalPages - 1;
            
            if (currentPageIndex === totalPages - 1) {
                nextBtn.innerHTML = '<span>Finish</span><i class="fas fa-check"></i>';
            } else {
                nextBtn.innerHTML = '<span>Next</span><i class="fas fa-chevron-right"></i>';
            }
        }
        
        // Audio controls with segment support
        function playAudio() {
            if (audioPlayer.src) {
                const page = storyData.pages[currentPageIndex];
                if (page && page.audio_start !== undefined) {
                    // Convert time format to seconds if needed
                    let startTime = page.audio_start;
                    if (typeof startTime === 'string' && startTime.includes(':')) {
                        const parts = startTime.split(':');
                        startTime = parseInt(parts[0]) * 60 + parseFloat(parts[1]);
                    }
                    audioPlayer.currentTime = startTime;
                }
                
                audioPlayer.play().then(() => {
                    isPlaying = true;
                    playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
                }).catch(error => {
                    console.error('Error playing audio:', error);
                });
            }
        }
        
        function pauseAudio() {
            audioPlayer.pause();
            isPlaying = false;
            playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
        }
        
        function stopAudio() {
            audioPlayer.pause();
            audioPlayer.currentTime = 0;
            isPlaying = false;
            playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
        }
        
        // Mute control removed per new UI layout
        
        // Check if audio should stop at page end
        function checkAudioSegment() {
            if (isPlaying && audioPlayer.currentTime > 0) {
                const page = storyData.pages[currentPageIndex];
                if (page && page.audio_end !== undefined) {
                    // Convert time format to seconds if needed
                    let endTime = page.audio_end;
                    if (typeof endTime === 'string' && endTime.includes(':')) {
                        const parts = endTime.split(':');
                        endTime = parseInt(parts[0]) * 60 + parseFloat(parts[1]);
                    }
                    
                    if (audioPlayer.currentTime >= endTime) {
                        // Auto-advance if enabled and not on last page
                        if (autoPlayEnabled && currentPageIndex < totalPages - 1) {
                            loadPage(currentPageIndex + 1);
                            // Wait for image to load, then play audio
                            const checkImage = setInterval(() => {
                                if (pageImage.complete && pageImage.naturalHeight !== 0) {
                                    clearInterval(checkImage);
                                    setTimeout(() => playAudio(), 100);
                                }
                            }, 50);
                        } else {
                            pauseAudio();
                        }
                    }
                }
            }
        }
        
        // Event listeners
        prevBtn.addEventListener('click', () => {
            if (currentPageIndex > 0) {
                loadPage(currentPageIndex - 1);
            }
        });
        
        nextBtn.addEventListener('click', () => {
            if (currentPageIndex < totalPages - 1) {
                loadPage(currentPageIndex + 1);
            } else {
                // Story finished
                alert('Congratulations! You finished the story!');
                window.location.href = 'storybooks_list.php';
            }
        });
        
        playPauseBtn.addEventListener('click', () => {
            if (isPlaying) {
                pauseAudio();
                autoPlayEnabled = false;
            } else {
                playAudio();
                autoPlayEnabled = true;
            }
        });
        
        // No mute button in the new layout
        
        // Keyboard navigation
        document.addEventListener('keydown', (event) => {
            switch(event.key) {
                case 'ArrowLeft':
                    if (currentPageIndex > 0) {
                        loadPage(currentPageIndex - 1);
                    }
                    break;
                case 'ArrowRight':
                    if (currentPageIndex < totalPages - 1) {
                        loadPage(currentPageIndex + 1);
                    }
                    break;
                case ' ':
                    event.preventDefault();
                    if (isPlaying) {
                        pauseAudio();
                    } else {
                        playAudio();
                    }
                    break;
            }
        });
        
        // Audio events
        audioPlayer.addEventListener('ended', () => {
            // Auto-advance if enabled and not on last page
            if (autoPlayEnabled && currentPageIndex < totalPages - 1) {
                loadPage(currentPageIndex + 1);
                // Wait for image to load, then play audio
                const checkImage = setInterval(() => {
                    if (pageImage.complete && pageImage.naturalHeight !== 0) {
                        clearInterval(checkImage);
                        setTimeout(() => playAudio(), 100);
                    }
                }, 50);
            } else {
                isPlaying = false;
                autoPlayEnabled = false;
                playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
            }
        });
        
        audioPlayer.addEventListener('timeupdate', checkAudioSegment);
        
        audioPlayer.addEventListener('error', (error) => {
            console.error('Audio error:', error);
            const controls = document.querySelector('.audio-controls');
            if (controls) controls.style.display = 'none';
        });
        
        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', init);
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
        const currentSection = 'story'; // Define the section name here
    </script>
    <script src="../../js/activity-logger.js"></script>

</body>
</html>
