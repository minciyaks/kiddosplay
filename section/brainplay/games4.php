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
   <link rel="stylesheet" href="../../icons/css/fontawesome.min.css">
    <link rel="stylesheet" href="../../icons/css/solid.min.css">
    <link rel="stylesheet" href="../../icons/css/brands.min.css">
  <title>BrainPlay</title>
  <style>
    /* General Styles */
    body { margin: 0; font-family: 'Comic Neue', sans-serif; background: linear-gradient(to bottom, #FFF8E1, #E1F5FE); min-height: 100vh; display: flex; flex-direction: column; }
   header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    /* CHANGE 1: Set a fixed height for the header itself (e.g., 90px) */
    /* This height must be less than your logo height (145px) */
    height: 90px; 
    padding: 0 30px; /* Adjusted padding to vertical 0 since we fixed height */
    background: #f38d85;
    border-bottom: 5px solid #f07167;
    position: relative; 
    z-index: 10;
    /* Ensure other content is centered vertically */
    }
    .logo {
    /* CHANGE 2: Give the logo container position relative */
    position: relative; 
    /* Give the container a small, fixed size */
    width: 100px; 
    height: 100%; /* Match header height */
    }
    .logo img {
    /* CHANGE 3: Use absolute positioning for the large logo */
    position: absolute; 
    top: 50%; /* Start halfway down the header */
    transform: translateY(-50%); /* Move up by half its own height to perfectly center it */
    left: 0; 
    
    /* CHANGE 4: Set the required minimum height */
    height: 170px; 
    width: auto; /* Maintain aspect ratio */
    
    /* IMPORTANT: Lower the z-index so the logo stays *under* the header's border-bottom */
    /* Use a negative value to put it 'behind' the header itself */
    z-index: 5; 
  }
    .center-icons {
    display: flex;
    align-items: center;
    gap: 40px;
    padding-top: 10px; /* Add a bit of space if the logo is visually covering it */
}
    .center-icons i { cursor: pointer; font-size: 1.5rem; transition: transform 0.2s; color: white; }
    .center-icons i:hover { transform: scale(1.2); }
    .title { font-size: 3rem; color: #00afb9; text-shadow: 2px 2px #fff; font-family: Comic Sans MS, cursive; }
    .nav-item { position: relative; text-decoration: none; display: flex; align-items: center; }
    .nav-item::after { content: attr(data-tooltip); position: absolute; bottom: -30px; left: 50%; transform: translateX(-50%); background: rgba(0,0,0,0.75); color: #fff; padding: 4px 8px; border-radius: 6px; font-size: 0.8rem; white-space: nowrap; opacity: 0; pointer-events: none; transition: opacity 0.2s ease; }
    .nav-item:hover::after { opacity: 1; }
    .quiz-container { flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 20px; margin-top: 20px; transform: scale(1.2); transform-origin: center center; max-width: 800px; width: 90%; margin-left: auto; margin-right: auto; box-sizing: border-box; }
    .question { font-size: 2rem; font-weight: bold; color: #1C2C4C; margin-bottom: 20px; }
    .answers { display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; align-items: center; }
    .answers button, .answers img { cursor: pointer; border: 3px solid transparent; background: white; border-radius: 15px; padding: 15px 25px; font-size: 1.5rem; box-shadow: 0 4px 10px rgba(0,0,0,0.2); transition: transform 0.2s, background 0.2s, border 0.2s; }
    .answers img { width: 100px; height: 100px; object-fit: contain; }
    .answers button:hover, .answers img:hover { transform: scale(1.1); }
    @keyframes shake { 0% { transform: translateX(0); } 25% { transform: translateX(-5px); } 50% { transform: translateX(5px); } 75% { transform: translateX(-5px); } 100% { transform: translateX(0); } }
    .shake { animation: shake 0.4s; }
    .sequence-container { display: flex; gap: 15px; padding: 15px; background-color: rgba(255, 255, 255, 0.5); border-radius: 20px; min-height: 180px; align-items: center; border: 5px solid transparent; transition: border-color 0.5s; }
    .sequence-image { width: 150px; height: 150px; object-fit: cover; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.2); cursor: grab; transition: transform 0.2s, box-shadow 0.2s; }
    .sequence-image.dragging { opacity: 0.5; transform: scale(1.1); box-shadow: 0 8px 20px rgba(0,0,0,0.3); cursor: grabbing; }
    .check-btn { margin-top: 30px; padding: 15px 40px; font-size: 1.5rem; font-weight: bold; color: white; background-color: #f07167; border: none; border-radius: 15px; cursor: pointer; transition: background-color 0.2s; }
    .check-btn:hover { background-color: #fb564b; }
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); z-index: 100; display: flex; justify-content: center; align-items: center; }
    .modal-content { background: white; padding: 30px; border-radius: 20px; width: 90%; max-width: 500px; text-align: center; position: relative; box-shadow: 0 5px 15px rgba(0,0,0,0.3); border: 5px solid #f0a500; }
    .modal-content h2 { font-size: 2rem; color: #ff005e; margin-top: 0; }
    .modal-content p { font-size: 1.2rem; color: #333; }
    .close-btn { position: absolute; top: 10px; right: 15px; font-size: 2rem; color: #888; cursor: pointer; }
    .start-screen { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, #FFF8E1, #E1F5FE); display: flex; justify-content: center; align-items: center; z-index: 1000; }
    .start-btn { padding: 20px 50px; font-size: 3rem; font-family: 'Comic Neue', sans-serif; font-weight: bold; color: white; background-color: #f38d85; border: 5px solid #f07167; border-radius: 25px; cursor: pointer; transition: transform 0.2s; box-shadow: 0 8px 15px rgba(0,0,0,0.2); }
    .start-btn:hover { transform: scale(1.1); }
    .hidden { display: none !important; }
    .question-card { background: rgba(255, 255, 255, 0.4); border-radius: 25px; padding: 25px 30px; box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 2px solid rgba(255, 255, 255, 0.18); display: flex; flex-direction: column; align-items: center; justify-content: center; margin-bottom: 30px; min-width: 400px; min-height: 200px; }
    .nav-item .fa-clock { margin-right: 8px; }
    #time-display { font-size: 1.5rem; font-weight: bold; color: white; }

    /* --- Styles for the Lock Screen --- */
    #lock-screen { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.85); display: none; justify-content: center; align-items: center; z-index: 9999; }
    .lock-box { background: white; padding: 30px 40px; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
  </style>
</head>
<body>

  <div id="start-screen" class="start-screen">
    <button id="start-btn" class="start-btn">Let’s Play!</button>
  </div>

  <header class="hidden">
    <div class="logo"><img src="../../images/logo.png" alt="Logo"></div>
    <div class="center-icons">
      <a href="../../home.php" class="nav-item home-btn" data-tooltip="Home"><i class="fas fa-home"></i></a>
      <a href="#" class="nav-item how-to-use-btn" data-tooltip="How to Use"><i class="fas fa-question-circle"></i></a>
      <div class="nav-item" data-tooltip="Time Played">
        <i class="fas fa-clock"></i>
        <span id="time-display">00:00</span>
      </div>
    </div>
    <div class="title">Brain play</div>
  </header>

  <div class="quiz-container hidden">
    <div id="question-card" class="question-card">
      <div id="question" class="question"></div>
    </div>
    <div id="answers" class="answers"></div>
  </div>

  <div id="how-to-play-modal" class="modal-overlay hidden">
      <div class="modal-content">
          <span class="close-btn">&times;</span>
          <h2>How to Play!</h2>
          <p>Read the question and look at the pictures. Click on the right answer, or drag the pictures into the correct order. Have fun learning!</p>
      </div>
  </div>

 <script>
    const currentUserId = <?php echo json_encode($current_user_id); ?>;

    const questions = [
      // NEW: Added a unique 'id' to each question object
      // Counting
      { id: 121, q: "How many apples?", img: "images4/counting/apple.png", options: ["2","3","4"], answer: "3" , audioSrc: "audio4/c_apples.mp3" },
      { id: 122, q: "How many stars?", img: "images4/counting/stars.png", options: ["4","5","6"], answer: "5",audioSrc: "audio4/c_stars.mp3" },
      { id: 123, q: "How many balls?", img: "images4/counting/ball.png", options: ["1","2","3"], answer: "2" ,audioSrc: "audio4/c_balls.mp3" },
      { id: 124, q: "How many cars?", img: "images4/counting/car.png", options: ["2","3","4"], answer: "4" ,audioSrc: "audio4/c_cars.mp3" },
      { id: 125, q: "How many ice creams?", img: "images4/counting/icecream.png", options: ["1","2","3"], answer: "1" ,audioSrc: "audio4/c_icecreams.mp3" },
      // Sight words
      { id: 221, q: "Which one is CAT?", options: [ {img:"images4/sightwords/cat.png", value:"cat"}, {img:"images4/sightwords/dog.png", value:"dog"} ], answer: "cat" ,audioSrc: "audio4/s_cat.mp3" },
      { id: 222, q: "Which one is DOG?", options: [ {img:"images4/sightwords/cat.png", value:"cat"}, {img:"images4/sightwords/dog.png", value:"dog"} ], answer: "dog" ,audioSrc: "audio4/s_dog.mp3" },
      { id: 223, q: "Which one is SUN?", options: [ {img:"images4/sightwords/sun.png", value:"sun"}, {img:"images4/sightwords/tree.png", value:"tree"} ], answer: "sun" ,audioSrc: "audio4/s_sun.mp3" },
      { id: 224, q: "Which one is TREE?", options: [ {img:"images4/sightwords/fish.png", value:"fish"}, {img:"images4/sightwords/tree.png", value:"tree"} ], answer: "tree" ,audioSrc: "audio4/s_tree.mp3" },
      { id: 225, q: "Which one is FISH?", options: [ {img:"images4/sightwords/fish.png", value:"fish"}, {img:"images4/sightwords/sun.png", value:"sun"} ], answer: "fish" ,audioSrc: "audio4/s_fish.mp3" },
      // Sequencing Stories
      { id: 321, q: "Brushing teeth", type: "sequence", images: [ "images4/sequencing/brushing1.png", "images4/sequencing/brushing2.png", "images4/sequencing/brushing3.png" ], answer: [0,1,2] ,audioSrc: "audio4/sq_brush.mp3" },
      { id: 322, q: "Eating food", type: "sequence", images: [ "images4/sequencing/eating1.png", "images4/sequencing/eating2.png", "images4/sequencing/eating3.png" ], answer: [0,1,2] ,audioSrc: "audio4/sq_eating.mp3" },
      { id: 323, q: "Going to school", type: "sequence", images: [ "images4/sequencing/school1.png", "images4/sequencing/school2.png", "images4/sequencing/school3.png" ], answer: [0,1,2] ,audioSrc: "audio4/sq_school.mp3" },
      { id: 324, q: "Playing outside", type: "sequence", images: [ "images4/sequencing/play1.png", "images4/sequencing/play2.png", "images4/sequencing/play3.png" ], answer: [0,1,2] ,audioSrc: "audio4/sq_play.mp3" },
      { id: 325, q: "Sleeping", type: "sequence", images: [ "images4/sequencing/night1.png", "images4/sequencing/night2.png", "images4/sequencing/night3.png" ], answer: [0,1,2] ,audioSrc: "audio4/sq_night.mp3" },
      // Colors
      { id: 421, q: "What color is this apple?", img: "images4/colors/apple_red.png", options: ["Red","Green","Yellow"], answer: "Red" ,audioSrc: "audio4/cl_apple.mp3" },
      { id: 422, q: "What color is the sky?", img: "images4/colors/sky_blue.png", options: ["Blue","Green","Purple"], answer: "Blue" ,audioSrc: "audio4/cl_sky.mp3"},
      { id: 423, q: "What color is this pumpkin?", img: "images4/colors/pumpkin_orange.png", options: ["Orange", "Brown", "Yellow"], answer: "Orange" ,audioSrc: "audio4/cl_pumpkin.mp3" },
      { id: 424, q: "What color is this Parrot?", img: "images4/colors/parrot_green.png", options: ["Yellow", "Green", "Red"], answer: "Green" ,audioSrc: "audio4/cl_parrot.mp3" },
      { id: 425, q: "What color is this grape?", img: "images4/colors/grape_purple.png", options: ["Purple", "Blue", "Pink"], answer: "Purple" ,audioSrc: "audio4/cl_grape.mp3" }
    ];

    let shuffled = questions.sort(() => 0.5 - Math.random());
    let index = 0;
    let currentAttempts = 1;
    const questionEl = document.getElementById("question");
    const answersEl = document.getElementById("answers");
    const audioPlayer = new Audio();
    audioPlayer.volume = 0.8;

    function saveGameResult(questionId, isCorrect, attempts) {
        if (currentUserId === 0) {
            console.log("Not saving score, as no user is logged in.");
            return;
        }
        const score = isCorrect ? 10 : 0;
        const formData = new FormData();
        formData.append('user_id', currentUserId);
        formData.append('question_id', questionId);
        formData.append('score', score);
        formData.append('is_correct', isCorrect);
        formData.append('attempts', attempts);
        fetch('save_score.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log(data.message);
        })
        .catch(error => {
            console.error('Error saving score:', error);
        });
    }

    function playQuestionAudio(src) {
      if (src) {
        audioPlayer.src = src;
        audioPlayer.play().catch(e => console.error("Error playing audio:", e));
      }
    }

    function handleCorrectAnswer() {
        questionEl.innerHTML = "<h3>🎉 Good job!</h3>";
        const advanceQuestion = () => {
            index++;
            currentAttempts = 1;
            showQuestion();
        };
        audioPlayer.addEventListener('ended', advanceQuestion, { once: true });
        playQuestionAudio("audio/good_job.mp3");
    }

    // THIS IS THE CORRECTED FUNCTION
    function showQuestion() {
      if (index >= shuffled.length) {
        questionEl.textContent = "🎉 All done! You're amazing!";
        answersEl.innerHTML = "";
         answersEl.classList.remove('sequence-container'); 
        answersEl.style.borderColor = 'transparent';
       playQuestionAudio("audio/alldone.mp3"); // Play final audio
        return;
      }
      let current = shuffled[index];
      questionEl.innerHTML = "";
      answersEl.innerHTML = "";
      answersEl.classList.remove('sequence-container');
      answersEl.style.borderColor = 'transparent';
      const textEl = document.createElement("div");
      textEl.textContent = current.q;
      questionEl.appendChild(textEl);
      playQuestionAudio(current.audioSrc);
      if (current.type === 'sequence') {
        showSequenceQuestion(current);
      } else {
        showStandardQuestion(current);
      }
    }

    function showStandardQuestion(current) {
        if (current.img) {
            const isCountingQuestion = current.options.every(opt => !isNaN(opt));
            if (isCountingQuestion) {
                const count = parseInt(current.answer);
                const imgContainer = document.createElement("div");
                imgContainer.style.cssText = "display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; margin: 15px 0;";
                questionEl.appendChild(imgContainer);
                for (let i = 0; i < count; i++) {
                    const imgEl = document.createElement("img");
                    imgEl.src = current.img;
                    imgEl.style.cssText = "width: 80px; height: 80px; object-fit: contain;";
                    imgContainer.appendChild(imgEl);
                }
            } else {
                const imgEl = document.createElement("img");
                imgEl.src = current.img;
                imgEl.style.cssText = "width: 150px; height: 150px; object-fit: contain; margin: 10px 0;";
                questionEl.appendChild(imgEl);
            }
        }
        current.options.forEach(opt => {
            let btn;
            if (typeof opt === "string") {
                btn = document.createElement("button");
                btn.textContent = opt;
                btn.onclick = () => checkAnswer(opt, current.answer, btn);
            } else {
                btn = document.createElement("img");
                btn.src = opt.img;
                btn.onclick = () => checkAnswer(opt.value, current.answer, btn);
            }
            answersEl.appendChild(btn);
        });
    }

    function checkAnswer(selected, correct, btn) {
        const currentQuestion = shuffled[index];
        document.querySelectorAll('.answers button, .answers img').forEach(b => b.onclick = null);
        if (selected === correct) {
            btn.style.border = "3px solid #4CAF50";
            saveGameResult(currentQuestion.id, 1, currentAttempts);
            handleCorrectAnswer();
        } else {
            btn.style.border = "3px solid #f44336";
            btn.classList.add("shake");
            questionEl.innerHTML = "<h3>❌ Try again!</h3>";
            new Audio('audio/oops.mp3').play();
            currentAttempts++;
            setTimeout(() => {
                btn.classList.remove("shake");
                showQuestion();
            }, 1000);
        }
    }

    function showSequenceQuestion(current) {
        answersEl.classList.add('sequence-container');
        const shuffledImages = current.images.map((imgSrc, originalIndex) => ({
            imgSrc,
            originalIndex
        })).sort(() => Math.random() - 0.5);
        shuffledImages.forEach(imgData => {
            const imgEl = document.createElement('img');
            imgEl.src = imgData.imgSrc;
            imgEl.className = 'sequence-image';
            imgEl.draggable = true;
            imgEl.dataset.originalIndex = imgData.originalIndex;
            answersEl.appendChild(imgEl);
        });
        const draggables = document.querySelectorAll('.sequence-image');
        draggables.forEach(draggable => {
            draggable.addEventListener('dragstart', () => draggable.classList.add('dragging'));
            draggable.addEventListener('dragend', () => draggable.classList.remove('dragging'));
        });
        answersEl.addEventListener('dragover', e => {
            e.preventDefault();
            const afterElement = getDragAfterElement(answersEl, e.clientX);
            const dragging = document.querySelector('.dragging');
            if (afterElement == null) answersEl.appendChild(dragging);
            else answersEl.insertBefore(dragging, afterElement);
        });
        const checkButton = document.createElement('button');
        checkButton.textContent = 'Check Order';
        checkButton.className = 'check-btn';
        questionEl.appendChild(checkButton);
        checkButton.onclick = () => {
            const currentQuestion = shuffled[index];
            const userOrder = [...answersEl.querySelectorAll('.sequence-image')].map(img => parseInt(img.dataset.originalIndex));
            const isCorrect = JSON.stringify(userOrder) === JSON.stringify(current.answer);
            if (isCorrect) {
                answersEl.style.borderColor = "#4CAF50";
                saveGameResult(currentQuestion.id, 1, currentAttempts);
                handleCorrectAnswer();
            } else {
                questionEl.innerHTML = "<h3>❌ Try again!</h3>";
                answersEl.classList.add('shake');
                new Audio('audio/oops.mp3').play();
                currentAttempts++;
                setTimeout(() => {
                    answersEl.classList.remove('shake');
                    showQuestion();
                }, 1000);
            }
        };
    }

    function getDragAfterElement(container, x) {
      const draggableElements = [...container.querySelectorAll('.sequence-image:not(.dragging)')];
      return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = x - box.left - box.width / 2;
        if (offset < 0 && offset > closest.offset) {
          return {
            offset: offset,
            element: child
          };
        } else {
          return closest;
        }
      }, {
        offset: Number.NEGATIVE_INFINITY
      }).element;
    }

    // Modal Logic
    const modal = document.getElementById('how-to-play-modal');
    const openBtn = document.querySelector('.how-to-use-btn');
    const closeBtn = document.querySelector('.close-btn');
    openBtn.onclick = (e) => {
      e.preventDefault();
      modal.classList.remove('hidden');
    };
    closeBtn.onclick = () => modal.classList.add('hidden');
    modal.onclick = (e) => {
      if (e.target === modal) modal.classList.add('hidden');
    };

    // Start Button Logic
    const startScreen = document.getElementById('start-screen');
    const startBtn = document.getElementById('start-btn');
    const gameHeader = document.querySelector('header');
    const quizContainer = document.querySelector('.quiz-container');

    startBtn.onclick = () => {
        startScreen.classList.add('hidden');
        gameHeader.classList.remove('hidden');
        quizContainer.classList.remove('hidden');
        showQuestion();
    };
  </script>

  <div id="lock-screen">
    <div class="lock-box">
      <h2>Time's Up!</h2>
      <p>Please ask a parent to enter the password to continue.</p>
      <input type="password" id="parent-password" placeholder="Parent Password">
      <button id="unlock-button">Unlock</button>
      <p id="error-message" style="color: red;"></p>
    </div>
  </div>

  <script src="../../js/timer.js"></script> 
  
  <script>
    // This variable is required by your activity-logger.js file.
    const currentSection = 'quiz'; 
  </script>
  <script src="../../js/activity-logger.js"></script>

</body>
</html>