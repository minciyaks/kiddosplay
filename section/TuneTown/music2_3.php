<?php
// Start the session to access session variables
session_start();

// Get the user_id from the session.
// We assume it was stored as 'user_id' when the user logged in.
if (isset($_SESSION["user_id"])) {
    $current_user_id = $_SESSION["user_id"];
} else {
    // Handle case where user isn't logged in.
    $current_user_id = 0; // Set to 0 if no user is logged in
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kiddos Video Fun</title>
  <link rel="stylesheet" href="../../icons/css/fontawesome.min.css">
    <link rel="stylesheet" href="../../icons/css/solid.min.css">
    <link rel="stylesheet" href="../../icons/css/brands.min.css">
  <style>
    body {
      margin: 0;
       font-family: Comic Sans MS, cursive;
      background: #ffb347; /* orange background */
      color: #1C2C4C;
    }

    /* Updated header */
    header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 20px;
      height: 50px;
      background: linear-gradient(to bottom, #e85d04, #f48c06);
      color: white;
      font-weight: bold;
      font-size: 1.8rem;

      /* Divider line below header */
      border-bottom: 5px solid #fa6a16; /* white line, adjust color/thickness if needed */
    }

    .logo img {
      height: 165px;
      width: auto;
      padding: 30px;
      margin-top: 4px;
    }

    .center-icons {
      display: flex;
      align-items: center;
      gap: 50px;
    }

    .center-icons i {
      cursor: pointer;
      font-size: 1.5rem;
      transition: transform 0.2s;
    }

    .center-icons i:hover {
      transform: scale(1.2);
    }

    /* Tooltip */
    .nav-item {
      position: relative;
      color: white;
      text-decoration: none;
    }

    .nav-item::after {
      content: attr(data-tooltip);
      position: absolute;
      bottom: -30px;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(0,0,0,0.75);
      color: #fff;
      padding: 4px 8px;
      border-radius: 6px;
      font-size: 0.8rem;
      white-space: nowrap;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.2s ease;
    }

    .nav-item:hover::after {
      opacity: 1;
    }

    /* Add just these styles for the clock on your section page */

.nav-item {
  /* This makes the icon and the text sit side-by-side nicely */
  display: flex;
  align-items: center;
}

.nav-item .fa-clock {
  /* This adds a small space between the icon and the numbers */
  margin-right: 8px;
}

#time-display {
  /* This styles the numbers of the timer */
  font-size: 1em;
  font-weight: bold;
  color: white; /* Or whatever color fits your header */
}

    /* Tune Town title */
    .title {
      font-size: 3rem;
      color: #bc1900;
      text-shadow: 2px 2px #fff;
    }

    /* Video grid */
    .grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 40px;
      padding: 30px;
    }

    .video-card {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      cursor: pointer;
      box-shadow: 0 6px 12px rgba(0,0,0,0.15);
      transition: transform 0.2s;
      position: relative;
    }

    .video-card:hover { transform: scale(1.03); }

    .thumb {
      position: relative;
      padding-top: 56.25%;
      background-size: cover;
      background-position: center;
    }

    .play-btn {
      position: absolute;
      bottom: 10px;
      right: 10px;
      background: #fff;
      border-radius: 50%;
      width: 50px;
      height: 50px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 10px rgba(0,0,0,0.25);
    }

    .play-btn::before {
      content: "";
      display: block;
      width: 0;
      height: 0;
      border-left: 16px solid #00b894;
      border-top: 10px solid transparent;
      border-bottom: 10px solid transparent;
      margin-left: 4px;
    }

    .video-card .title {
      padding: 12px;
      font-weight: bold;
      text-align: center;
      color: black; /* plain black for video captions */
      font-size: 1rem; /* smaller than Tune Town */
    }

    /* Modal */
    .modal {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.7);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .modal.open { display: flex; }

    .video-wrapper {
      position: relative;
      width: 80vw;
      max-width: 900px;
    }

    .video-wrapper iframe,
    .video-wrapper video {
      width: 100%;
      aspect-ratio: 16 / 9;
      border: none;
      border-radius: 12px;
    }

    .close-btn {
      position: absolute;
      top: -12px;
      right: -12px;
      background: white;
      border: none;
      border-radius: 50%;
      width: 35px;
      height: 35px;
      cursor: pointer;
      font-size: 18px;
      font-weight: bold;
      box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }
    /* Add this CSS to the <style> tag on your section pages */

#lock-screen {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.85);
  display: none; /* Starts hidden */
  justify-content: center;
  align-items: center;
  z-index: 9999; /* Ensures it's on top of everything */
}

.lock-box {
  background: white;
  padding: 30px 40px;
  border-radius: 15px;
  text-align: center;
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}
  </style>
</head>
<body>

  <!-- Header with logo, center icons, and title -->
  <header>
    <!-- Left logo -->
    <div class="logo">
      <img src="../../images/logo.png" alt="Logo">
    </div>

    <!-- Center icons -->
    <div class="center-icons">
      <a href="../../home.php" class="nav-item home-btn" data-tooltip="Home">
        <i class="fas fa-home"></i>
      </a>

      <a href="#" class="nav-item" data-tooltip="Clock">
         <i class="fa fa-clock"></i>
         <span id="time-display">00:00</span>
      </a>
    </div>

    <!-- Right title -->
    <div class="title">TuneTown</div>
  </header>

  <main class="grid">
     <!-- Card 1  -->
    <div class="video-card" data-video-id="tkpfg-1FJLU">
        <div class="thumb" style="background-image:url('thumbnail/2_3/learncolor.jpg');"></div>
        <div class="play-btn"></div>
        <div class="title">Let's Learn The Colors! - By ChuChuTV</div>
    </div>

    <!-- Card 2 -->
    <div class="video-card" data-video-id="D4JJkp_sYDs">
      <div class="thumb" style="background-image:url('thumbnail/2_3/teddybear.jpg');"></div>
      <div class="play-btn"></div>
      <div class="title">Teddy Bear Teddy Bear - Nursery Rhymes by Kids TV</div>
    </div>

    <!-- Card 3 -->
    <div class="video-card" data-video-id="yWirdnSDsV4">
      <div class="thumb" style="background-image:url('thumbnail/2_3/wheels.jpg');"></div>
      <div class="play-btn"></div>
      <div class="title">The Wheels On The Bus - Super Simple Songs</div>
    </div>

    <!-- Card 4 -->
   <div class="video-card" data-video-id="DiV48J0uB_Y">
      <div class="thumb" style="background-image:url('thumbnail/2_3/fruitfriends.jpg');"></div>
      <div class="play-btn"></div>
      <div class="title">The Fruit Friends Song - ChuChu TV</div>
    </div>

    <!-- Card 5 -->
     <div class="video-card" data-video-id="3X4Udn7lbuY">
      <div class="thumb" style="background-image:url('thumbnail/2_3/brushteeth.jpg');"></div>
      <div class="play-btn"></div>
      <div class="title">Brush Your Teeth - Finny The Shark</div>
    </div>


    <!-- Card 6 -->
    <div class="video-card" data-video-id="3zm5JMGIsRU">
      <div class="thumb" style="background-image:url('thumbnail/2_3/babyelephant.jpg');"></div>
      <div class="play-btn"></div>
      <div class="title">Baby Elephant - Super Simple Songs</div>
    </div>

  </main>

  <!-- Modal -->
  <div class="modal" id="modal">
    <div class="video-wrapper">
      <button class="close-btn" id="closeBtn">✕</button>
      <!-- This area will dynamically show either iframe or video -->
      <div id="videoContainer"></div>
    </div>
  </div>


  <script>
    const modal = document.getElementById('modal');
    const closeBtn = document.getElementById('closeBtn');

    // This is the function that sends the message to your PHP file
    // NEW: We created this whole function
    function logSongPlay(songTitle) {
       // It now gets its value from the PHP code at the top of the page.
        const userId = <?php echo json_encode($current_user_id); ?>;

        // If the user is not logged in, userId will be 0.
        if (userId === 0) {
            console.log("Cannot log song, user is not logged in.");
            return; // Stop the function if no user is logged in
        }

        const songData = new FormData();
        songData.append('user_id', userId);
        songData.append('song_title', songTitle);

        // This sends the data to your server
        fetch('log_music.php', {
            method: 'POST',
            body: songData
        })
        .then(response => response.json())
        .then(data => {
            console.log('Server response:', data.message); // For debugging
        })
        .catch(error => {
            console.error('Error logging song:', error);
        });
    }

    // Your existing code that finds all video cards
    document.querySelectorAll('.video-card').forEach(card => {
        // Your existing code that listens for a click
        card.addEventListener('click', () => {
            // Get the video ID and title from the clicked card
            let videoId;
            if(card.dataset.videoSrc) {
                videoId = card.dataset.videoSrc;
            }
            else {
                videoId = card.dataset.videoId;
            }
            console.log('Video ID:', videoId);
            const songTitle = card.querySelector('.title').textContent; // NEW: Get the song title

            // Tell the server that this song was played
            logSongPlay(songTitle); // NEW: Call our messenger function

            // Your existing code to open and play the video

            if(card.dataset.videoSrc) {
                videoContainer.innerHTML = `
            <video controls autoplay>
              <source src="${videoId}" type="video/mp4">
              Your browser does not support the video tag.
            </video>`;
            }
            else if(card.dataset.videoId) {
                videoContainer.innerHTML = `
            <iframe src="https://www.youtube.com/embed/${videoId}?rel=0&autoplay=1&modestbranding=1" allowfullscreen></iframe>`;
            }
            modal.classList.add('open');
        });
    });

    // Your existing code for the close button
    closeBtn.addEventListener('click', () => {
        videoContainer.innerHTML = '';
        modal.classList.remove('open');
    });
    modal.addEventListener('click', e => {
        if (e.target === modal) {
            videoContainer.innerHTML = '';
            modal.classList.remove('open');
        }
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
        const currentSection = 'music'; // Define the section name here
    </script>
    <script src="/project/js/activity-logger.js"></script>
</body>
</html>
