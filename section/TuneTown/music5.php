<?php
// Start the session to access session variables
session_start();

// Get the user_id from the session.
// We assume it was stored as 'user_id' when the user logged in.
if (isset($_SESSION['user_id'])) {
    $current_user_id = $_SESSION['user_id'];
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
      border-bottom: 5px solid #fa6a16;
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

    .center-icons i:hover { transform: scale(1.2); }

    .nav-item {
      position: relative;
      color: white;
      text-decoration: none;
      display: flex;
      align-items: center;
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

    .nav-item:hover::after { opacity: 1; }

    #time-display {
      font-size: 1em;
      font-weight: bold;
      color: white;
    }

    .title {
      font-size: 3rem;
      color: #bc1900;
      text-shadow: 2px 2px #fff;
    }

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
      color: black;
      font-size: 1rem;
    }

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

    #lock-screen {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.85);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 9999;
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

  <header>
    <div class="logo">
      <img src="../../images/logo.png" alt="Logo">
    </div>

    <div class="center-icons">
      <a href="../../home.php" class="nav-item home-btn" data-tooltip="Home">
        <i class="fas fa-home"></i>
      </a>
      <a href="#" class="nav-item" data-tooltip="Clock">
        <i class="fa fa-clock"></i>
        <span id="time-display">00:00</span>
      </a>
    </div>

    <div class="title">TuneTown</div>
  </header>

  <main class="grid">
   
  <div class="video-card" data-video-id="lcl8uB2AWM0">
    <div class="thumb" style="background-image:url('thumbnail/5/shapes.jpg');"></div>
    <div class="play-btn"></div>
    <div class="title">Shapes Are All Around - Pinkfong</div>
  </div>

    <!-- Online YouTube videos -->
    <div class="video-card" data-video-id="BURVtOl4GP8">
      <div class="thumb" style="background-image:url('thumbnail/5/fruits5.jpg');"></div>
      <div class="play-btn"></div>
      <div class="title">Fruits Song - Kids TV</div>
    </div>

    <div class="video-card" data-video-id="Z2xooz6844k">
      <div class="thumb" style="background-image:url('thumbnail/5/sing_pig.jpg');"></div>
      <div class="play-btn"></div>
      <div class="title">Sing - Movie clips</div>
    </div>

    <div class="video-card" data-video-id="L0MK7qz13bU">
      <div class="thumb" style="background-image:url('thumbnail/5/frozen.jpg');"></div>
      <div class="play-btn"></div>
      <div class="title">Let It Go - Idina Menzel</div>
    </div>

    <div class="video-card" data-video-id="oWgTqLCLE8k">
      <div class="thumb" style="background-image:url('thumbnail/5/trolls.jpg');"></div>
      <div class="play-btn"></div>
      <div class="title">Can’t Stop The Feeling! - TROLLS</div>
    </div>

    <div class="video-card" data-video-id="fLCh9kRYLPA">
      <div class="thumb" style="background-image:url('thumbnail/5/realinrio.jpg');"></div>
      <div class="play-btn"></div>
      <div class="title">Real In Rio</div>
    </div>
  </main>

  <div class="modal" id="modal">
    <div class="video-wrapper">
      <button class="close-btn" id="closeBtn">✕</button>
      <!-- This area will dynamically show either iframe or video -->
      <div id="videoContainer"></div>
    </div>
  </div>

  <script>
    const modal = document.getElementById('modal');
    const videoContainer = document.getElementById('videoContainer');
    const closeBtn = document.getElementById('closeBtn');

    function logSongPlay(songTitle) {
        const userId = <?php echo json_encode($current_user_id); ?>;
        if (userId === 0) {
            console.log("Cannot log song, user is not logged in.");
            return;
        }

        const songData = new FormData();
        songData.append('user_id', userId);
        songData.append('song_title', songTitle);

        fetch('log_music.php', {
            method: 'POST',
            body: songData
        })
        .then(res => res.json())
        .then(data => console.log('Server:', data.message))
        .catch(err => console.error('Error logging song:', err));
    }

    document.querySelectorAll('.video-card').forEach(card => {
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

    closeBtn.addEventListener('click', () => {
      modal.classList.remove('open');
      videoContainer.innerHTML = '';
    });

    modal.addEventListener('click', e => {
      if (e.target === modal) {
        modal.classList.remove('open');
        videoContainer.innerHTML = '';
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
    const currentSection = 'music';
  </script>
  <script src="../../js/activity-logger.js"></script>
</body>
</html>
