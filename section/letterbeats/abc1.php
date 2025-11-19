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
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Animal Video Fun 🐺</title>
  <link rel="stylesheet" href="../../icons/css/fontawesome.min.css">
    <link rel="stylesheet" href="../../icons/css/solid.min.css">
    <link rel="stylesheet" href="../../icons/css/brands.min.css">
 <style>
   body {
    margin: 0;
    font-family: 'Comic Sans MS', cursive;
    background: white;
   }

    /* ====== HEADER ====== */
    header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 30px;
        height: 50px;
        background: #a8e6cf;
        border-bottom: 5px solid #77c5b1;
    }
    .logo img { height: 160px; margin-top: 4px; }
    .center-icons { display: flex; align-items: center; gap: 40px; }
    .center-icons i { cursor: pointer; font-size: 1.5rem; transition: transform 0.2s; color: white; }
    .center-icons i:hover { transform: scale(1.2); }
    .title { font-size: 3rem; color: #ff8b94; text-shadow: 2px 2px #fff; }
    .nav-item { position: relative; text-decoration: none; }
    .nav-item::after {
        content: attr(data-tooltip); position: absolute; bottom: -30px; left: 50%;
        transform: translateX(-50%); background: rgba(0,0,0,0.75); color: #fff;
        padding: 4px 8px; border-radius: 6px; font-size: 0.8rem; white-space: nowrap;
        opacity: 0; pointer-events: none; transition: opacity 0.2s ease;
    }
    .nav-item:hover::after { opacity: 1; }

  

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

    /* ====== Animal Grid ====== */
    .animal-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr); 
      gap: 25px;
      margin: 40px;
      max-width: 900px;
      margin-left: auto;
      margin-right: auto;
    }

    /* --- CHANGE 1: Added position: relative --- */
    .animal-card {
      background: transparent; /* No background color */
      padding: 0; /* No extra space */
      text-align: center;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative; /* This is needed to position the name inside the card */
      overflow: hidden; /* Keeps the name tag's rounded corners neat */
    }

    .animal-card:hover img {
      transform: scale(1.1);
    }
    
    .animal-card img {
        width: 100%; 
        max-width: 200px; /* You can adjust the image size */
        height: auto; 
        object-fit: contain;
        transition: transform 0.2s ease-in-out; /* Smooth zoom effect */
    }

    /* --- CHANGE 2: Added styles for the animal name overlay --- */
    .animal-name {
        position: absolute;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        background-color: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 5px 15px;
        border-radius: 15px;
        font-size: 1.2rem;
        opacity: 0; /* Hidden by default */
        transition: opacity 0.2s ease-in-out;
        pointer-events: none; /* Prevents the name tag from interfering with mouse events */
    }

    /* --- CHANGE 3: Show the name on hover --- */
    .animal-card:hover .animal-name {
        opacity: 1; /* Make it visible on hover */
    }

    /* ====== Popup Player (No changes needed) ====== */
    .popup-overlay {
      display: none; position: fixed; top: 0; left: 0;
      width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8);
      justify-content: center; align-items: center; z-index: 1000;
    }
    .popup-content {
      position: relative; background: #000; padding: 10px;
      border-radius: 15px; max-width: 800px; width: 90%;
    }
    .popup-content video { width: 100%; border-radius: 12px; display: block; }
    .close-btn {
      position: absolute; top: -15px; right: -15px; background: #E91E63;
      color: #fff; border: none; border-radius: 50%; width: 35px; height: 35px;
      font-size: 18px; cursor: pointer; z-index: 2000;
    }
    .arrow-btn {
      position: absolute; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.6);
      border: none; border-radius: 50%; width: 40px; height: 40px;
      font-size: 20px; cursor: pointer;
    }
    .arrow-left { left: -60px; }
    .arrow-right { right: -60px; }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .animal-grid {
            grid-template-columns: repeat(2, 1fr); 
            margin: 20px; 
            gap: 15px; 
        }
        .arrow-left { left: 5px; }
        .arrow-right { right: 5px; }
    }
    @media (max-width: 480px) {
        .animal-grid {
            grid-template-columns: repeat(2, 1fr); /* 2 columns for phones looks better with no text */
        }
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

  <header>
    <div class="logo"> <img src="../../images/logo.png" alt="Logo"> </div>
    <div class="center-icons">
        <a href="../../home.php" class="nav-item" data-tooltip="Home"><i class="fas fa-home"></i></a>
        <a href="#" class="nav-item" data-tooltip="Clock">
         <i class="fa fa-clock"></i>
         <span id="time-display">00:00</span>
      </a>
    </div>
    <div class="title">LetterBeats</div>
  </header>

  <main>
    <section class="animal-grid" id="animal-grid-container">
    </section>
  </main>

  <div class="popup-overlay" id="popup">
    <div class="popup-content">
      <button class="close-btn" onclick="closePopup()">✖</button>
      <button class="arrow-btn arrow-left" onclick="prevVideo()">❮</button>
      <video id="popupVideo" controls></video>
      <button class="arrow-btn arrow-right" onclick="nextVideo()">❯</button>
    </div>
  </div>

  <script>
    const animals = [
        { name: 'Bird',    image: 'abc1_images/birds.jpg',   video: 'abc_videos/abc1_videos/birds.mp4' },
        { name: 'Chicken', image: 'abc1_images/chicken.jpg', video: 'abc_videos/abc1_videos/chicken.mp4' },
        { name: 'cow',    image: 'abc1_images/cow.jpg',    video: 'abc_videos/abc1_videos/goat.mp4' },
        { name: 'Dog',     image: 'abc1_images/dog.jpg',     video: 'abc_videos/abc1_videos/dog.mp4' },
        { name: 'Lion',    image: 'abc1_images/lion.jpg',    video: 'abc_videos/abc1_videos/lion.mp4' },
        { name: 'Cat',     image: 'abc1_images/cat.jpg',     video: 'abc_videos/abc1_videos/cat.mp4' },
        { name: 'Snake',   image: 'abc1_images/snake.jpg',   video: 'abc_videos/abc1_videos/snake.mp4' },
        { name: 'Whale',   image: 'abc1_images/whale.jpg',   video: 'abc_videos/abc1_videos/whale.mp4' },
        { name: 'Wolf',    image: 'abc1_images/wolf.jpg',    video: 'abc_videos/abc1_videos/wolf.mp4' }
    ];

    const videos = animals.map(animal => animal.video);
    const animalGrid = document.getElementById('animal-grid-container');
    let currentIndex = 0;
    
    function logPhonicsActivity(category, value) {
        const userId = <?php echo json_encode($current_user_id); ?>;
        if (userId === 0) return;

        const data = new FormData();
        data.append('user_id', userId);
        data.append('category', category);
        data.append('value', value);

        fetch('log_phonics.php', {
            method: 'POST',
            body: data
        })
        .then(res => res.json())
        .then(data => console.log('Server response:', data.message))
        .catch(error => console.error('Error logging activity:', error));
    }

    animals.forEach((animal, index) => {
        const card = document.createElement('div');
        card.className = 'animal-card';
        card.setAttribute('onclick', `openPopup(${index})`);
        
        card.innerHTML = `
            <img src="${animal.image}" alt="${animal.name}">
            <div class="animal-name">${animal.name}</div>
        `;
        
        animalGrid.appendChild(card);
    });

    function openPopup(index) {
        const animalName = animals[index].name;
        logPhonicsActivity('animal_sound', animalName);
        currentIndex = index;
        document.getElementById("popup").style.display = "flex";
        loadVideo();
    }

    function closePopup() {
        document.getElementById("popup").style.display = "none";
        const videoPlayer = document.getElementById("popupVideo");
        videoPlayer.pause();
        videoPlayer.src = ""; 
    }

    function loadVideo() {
        const videoPlayer = document.getElementById("popupVideo");
        videoPlayer.src = videos[currentIndex];
        videoPlayer.play();
    }

    function nextVideo() {
        currentIndex = (currentIndex + 1) % videos.length;
        loadVideo();
        // NEW: Log the new animal sound to the database
        const newAnimalName = animals[currentIndex].name;
        logPhonicsActivity('animal_sound', newAnimalName);
    }

    function prevVideo() {
        currentIndex = (currentIndex - 1 + videos.length) % videos.length;
        loadVideo();
        // NEW: Log the new animal sound to the database
        const newAnimalName = animals[currentIndex].name;
        logPhonicsActivity('animal_sound', newAnimalName);
    }
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
        const currentSection = 'phonics'; // Define the section name here
    </script>
    <script src="../../js/activity-logger.js"></script>
</body>
</html>