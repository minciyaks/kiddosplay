<?php
session_start();

// --- 1. DIRECT DATABASE CONNECTION (Standardized Method) ---
// Note: This connects to the DB, but is not essential for PDF viewing unless you were logging here.
// We include it for consistency and safety, closing it later.
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
$hasPDF = !empty($story['pdf_file']) && file_exists($story['pdf_file']);

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
    <style>
        .pdf-container {
            width: 100%;
            aspect-ratio: 4/3;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f8f9fa;
            border-radius: 8px;
            max-height: 70vh;
            overflow: hidden;
            position: relative;
            border: 2px solid #FFD700;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        
        .pdf-loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .pdf-error {
            text-align: center;
            padding: 40px;
            color: #dc3545;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            margin: 20px;
        }
        
        .pdf-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin: 20px 0;
        }
        
        .pdf-controls button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .pdf-controls button:hover {
            background: #0056b3;
        }
        
        .pdf-controls button:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        
        .pdf-page-info {
            background: #e9ecef;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
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
                    <?php if ($hasPDF): ?>
                        <!-- PDF Viewer Container -->
                        <div class="pdf-container" id="pdfContainer">
                            <div class="pdf-loading">
                                <i class="fas fa-spinner fa-spin"></i>
                                <p>Loading PDF...</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Image Fallback -->
                        <img id="pageImage" src="" alt="Story Page" onerror="this.src='images/logo.png'">
                    <?php endif; ?>
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
                <button id="playPauseBtn" class="audio-btn">
                    <i class="fas fa-play"></i>
                </button>
            </div>
            <button id="nextBtn" class="nav-btn next-btn">
                <span>Next</span>
                <i class="fas fa-chevron-right"></i>
            </button>
        </nav>
    </div>

    <!-- Audio Player -->
    <audio id="audioPlayer" preload="metadata">
        <source src="<?php echo $story['audio_file']; ?>" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>

    <!-- PDF.js Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        // Configure PDF.js
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        
        // Story data from PHP
        const storyData = <?php echo json_encode($story); ?>;
        const totalPages = <?php echo $totalPages; ?>;
        const hasPDF = <?php echo $hasPDF ? 'true' : 'false'; ?>;
        let currentPageIndex = 0;
        let isPlaying = false;
        let isMuted = false;
        let autoPlayEnabled = false;
        let pdfDoc = null;
        let currentPDFPage = 1;
        
        // DOM elements
        const pageImage = document.getElementById('pageImage');
        const pageText = document.getElementById('pageText');
        const currentPageSpan = document.getElementById('currentPage');
        const totalPagesSpan = document.getElementById('totalPages');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const playPauseBtn = document.getElementById('playPauseBtn');
        const audioPlayer = document.getElementById('audioPlayer');
        const pdfContainer = document.getElementById('pdfContainer');
        
        // Initialize
        function init() {
            console.log('Initializing story viewer...');
            console.log('Story data:', storyData);
            console.log('Has PDF:', hasPDF);
            console.log('PDF file path:', storyData.pdf_file);
            
            if (hasPDF) {
                loadPDF();
            } else {
                console.log('No PDF file, using fallback mode');
                loadPage(0);
            }
            updateNavigation();
        }
        
        // Load PDF
        async function loadPDF() {
            try {
                const loadingDiv = pdfContainer.querySelector('.pdf-loading');
                loadingDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i><p>Loading PDF...</p>';
                
                console.log('Attempting to load PDF:', storyData.pdf_file);
                
                // Load PDF document directly
                pdfDoc = await pdfjsLib.getDocument({
                    url: storyData.pdf_file,
                    cMapUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/cmaps/',
                    cMapPacked: true,
                    disableAutoFetch: false,
                    disableStream: false,
                    disableRange: false
                }).promise;
                
                console.log('PDF loaded successfully:', pdfDoc.numPages, 'pages');
                
                // Render first page
                await renderPDFPage(1);
                loadPage(0); // Load first page text and audio
                
            } catch (error) {
                console.error('Error loading PDF:', error);
                // Show error message
                pdfContainer.innerHTML = `
                    <div class="pdf-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Failed to load PDF: ${error.message}</p>
                        <p>PDF Path: ${storyData.pdf_file}</p>
                        <button onclick="location.reload()" style="margin-top: 10px; padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Retry</button>
                    </div>
                `;
            }
        }
        
        // Render PDF page
        async function renderPDFPage(pageNumber) {
            if (!pdfDoc) return;
            
            try {
                const page = await pdfDoc.getPage(pageNumber);
                
                // Create canvas
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                
                // Get container dimensions
                const containerWidth = pdfContainer.clientWidth;
                const containerHeight = pdfContainer.clientHeight;
                const targetAspectRatio = 4/3;
                
                // Calculate the maximum size that fits within the container while maintaining 4:3 ratio
                let canvasWidth, canvasHeight;
                
                if (containerWidth / containerHeight > targetAspectRatio) {
                    // Container is wider than 4:3, fit by height
                    canvasHeight = containerHeight;
                    canvasWidth = canvasHeight * targetAspectRatio;
                } else {
                    // Container is taller than 4:3, fit by width
                    canvasWidth = containerWidth;
                    canvasHeight = canvasWidth / targetAspectRatio;
                }
                
                // Calculate scale to fit the PDF page within our 4:3 canvas
                const originalViewport = page.getViewport({ scale: 1.0 });
                const scaleX = canvasWidth / originalViewport.width;
                const scaleY = canvasHeight / originalViewport.height;
                const scale = Math.min(scaleX, scaleY);
                
                const viewport = page.getViewport({ scale: scale });
                
                // Set canvas dimensions
                canvas.width = canvasWidth;
                canvas.height = canvasHeight;
                
                // Style the canvas
                canvas.style.width = canvasWidth + 'px';
                canvas.style.height = canvasHeight + 'px';
                canvas.style.maxWidth = '100%';
                canvas.style.maxHeight = '100%';
                canvas.style.objectFit = 'contain';
                canvas.style.border = '1px solid #ddd';
                canvas.style.borderRadius = '8px';
                canvas.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
                canvas.style.background = 'white';
                
                // Clear container and add canvas
                pdfContainer.innerHTML = '';
                pdfContainer.appendChild(canvas);
                
                // Center the PDF content within the 4:3 canvas
                const offsetX = (canvasWidth - viewport.width) / 2;
                const offsetY = (canvasHeight - viewport.height) / 2;
                
                // Render page
                const renderContext = {
                    canvasContext: context,
                    viewport: viewport,
                    transform: [1, 0, 0, 1, offsetX, offsetY]
                };
                
                await page.render(renderContext).promise;
                currentPDFPage = pageNumber;
                
                console.log(`Rendered page ${pageNumber} with 4:3 aspect ratio`);
                
            } catch (error) {
                console.error('Error rendering PDF page:', error);
                pdfContainer.innerHTML = `
                    <div class="pdf-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Failed to render page ${pageNumber}</p>
                        <p>Error: ${error.message}</p>
                    </div>
                `;
            }
        }
        
        // Load page (text and audio)
        function loadPage(pageIndex) {
            if (pageIndex < 0 || pageIndex >= totalPages) return;
            
            currentPageIndex = pageIndex;
            const page = storyData.pages[pageIndex];
            
            // Update text
            pageText.textContent = page.text;
            currentPageSpan.textContent = pageIndex + 1;
            
            // Update audio
            if (storyData.audio_file) {
                audioPlayer.src = storyData.audio_file;
                audioPlayer.load();
            } else {
                console.warn('No audio file specified for story');
            }
            
            // Update navigation
            updateNavigation();
            
            // Stop current audio
            stopAudio();
            
            console.log(`Loaded page ${pageIndex + 1}: ${page.text}`);
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
        
        // Audio controls
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
                            advanceToNextPage();
                        } else {
                            pauseAudio();
                        }
                    }
                }
            }
        }
        
        // Auto-advance to next page
        async function advanceToNextPage() {
            if (currentPageIndex < totalPages - 1) {
                const newPageIndex = currentPageIndex + 1;
                if (hasPDF) {
                    await renderPDFPage(newPageIndex + 1);
                }
                loadPage(newPageIndex);
                // Wait a bit then play audio
                setTimeout(() => {
                    if (autoPlayEnabled) playAudio();
                }, 150);
            }
        }
        
        // Event listeners
        prevBtn.addEventListener('click', async () => {
            if (currentPageIndex > 0) {
                const newPageIndex = currentPageIndex - 1;
                if (hasPDF) {
                    await renderPDFPage(newPageIndex + 1);
                }
                loadPage(newPageIndex);
            }
        });
        
        nextBtn.addEventListener('click', async () => {
            if (currentPageIndex < totalPages - 1) {
                const newPageIndex = currentPageIndex + 1;
                if (hasPDF) {
                    await renderPDFPage(newPageIndex + 1);
                }
                loadPage(newPageIndex);
            } else {
                // Story finished
                alert('Story completed! Great job!');
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
        
        // Audio events
        audioPlayer.addEventListener('ended', () => {
            // Auto-advance if enabled and not on last page
            if (autoPlayEnabled && currentPageIndex < totalPages - 1) {
                advanceToNextPage();
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
        
        // Keyboard navigation
        document.addEventListener('keydown', (event) => {
            switch(event.key) {
                case 'ArrowLeft':
                    if (currentPageIndex > 0) {
                        prevBtn.click();
                    }
                    break;
                case 'ArrowRight':
                    if (currentPageIndex < totalPages - 1) {
                        nextBtn.click();
                    }
                    break;
                case ' ':
                    event.preventDefault();
                    playPauseBtn.click();
                    break;
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', () => {
            if (hasPDF && pdfDoc && currentPDFPage) {
                // Debounce resize events
                clearTimeout(window.resizeTimeout);
                window.resizeTimeout = setTimeout(() => {
                    renderPDFPage(currentPDFPage);
                }, 250);
            }
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
