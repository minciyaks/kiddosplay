document.addEventListener('DOMContentLoaded', () => {

    // --- Element Selections ---
    const startScreen = document.getElementById('startScreen');
    const startButton = document.getElementById('startButton');
    const roomContainer = document.querySelector('.room-container');
    const soundToggleBtn = document.getElementById('soundToggleBtn');
    const hotspots = document.querySelectorAll('.hotspot');
    const characterModalOverlay = document.getElementById('characterModalOverlay');
    const characterMessageElement = document.getElementById('characterMessage');
    const modalProceedButton = document.getElementById('modalProceedButton');
    const modalCloseButton = document.getElementById('modalCloseButton');
    const soundIcon = document.getElementById('soundIcon');
    const loginAvatarBtn = document.getElementById('loginAvatarBtn');
    const loginDropdown = document.getElementById('loginDropdown');
    const audioPlayer = document.getElementById('messageAudioPlayer');
 // --- NEW LOGIC TO HANDLE WELCOME SEQUENCE ---
    if (!showWelcome) {
        // If the welcome has already been shown, hide the start screen immediately
        startScreen.style.display = 'none';
        // And make sure the room is visible
        roomContainer.style.display = 'block'; 
        soundToggleBtn.style.display = 'flex';
    } else {
        // Otherwise, make sure the room is hidden until the user clicks "Start"
        roomContainer.style.display = 'none';
        soundToggleBtn.style.display = 'none';
    }
    // --- State Variables ---
    let currentTargetModule = '';
    let isSoundOn = true;
    let currentLinkElement = null;

   
    // --- Audio Control Function ---
    function playSound(audioSrc) {
        if (isSoundOn && audioSrc && audioPlayer) {
            audioPlayer.src = audioSrc;
            audioPlayer.play();
        }
    }

    // --- Start Button Logic ---
    if (startButton && startScreen) {
        startButton.addEventListener('click', () => {
            startScreen.style.transition = 'opacity 0.5s ease';
            startScreen.style.opacity = 0;
            
            setTimeout(() => {
                startScreen.style.display = 'none';
                showWelcomeAndIntro();
            }, 500);
        });
    }

    // --- Initial Welcome Message ---
    function showWelcomeAndIntro() {
        if (!characterMessageElement || !characterModalOverlay) return;
        characterMessageElement.textContent = "Hello, little explorer! 🕵️‍♂️ Tap on anything in the room to see something cool!";
        characterModalOverlay.classList.add('visible');
        currentTargetModule = '';
        playSound('audio/welcome.mp3');
    }

    // --- Modal Logic ---
    function hideModal() {
        if (!characterModalOverlay) return;
        characterModalOverlay.classList.remove('visible');

        if (roomContainer && roomContainer.style.display === 'none') {
            roomContainer.style.display = 'block';
            soundToggleBtn.style.display = 'flex';
        }
        
        if (audioPlayer) {
            audioPlayer.pause();
            audioPlayer.currentTime = 0;
        }
    }

    // --- Hotspot Click Logic ---
    hotspots.forEach(hotspot => {
        hotspot.addEventListener('click', (event) => {
            currentLinkElement = hotspot.closest('a'); //--redirect
            event.preventDefault(); // to stop the immediate redirect
            const introMessage = hotspot.dataset.introMessage;
            const module = hotspot.dataset.module;
            const audioSrc = hotspot.dataset.audioSrc;

            if (!characterMessageElement || !characterModalOverlay) return;

            characterMessageElement.textContent = introMessage;
            currentTargetModule = module;
            characterModalOverlay.classList.add('visible');
            playSound(audioSrc);
        });
    });

    // --- Modal Buttons ---
    if (modalProceedButton) {
        modalProceedButton.addEventListener('click', () => {
            // Check if we have a valid link to go to
            if (currentLinkElement && currentLinkElement.href) {
                // Navigate to the URL set by age-customizer.js
                window.location.href = currentLinkElement.href;
            }
            hideModal();
        });
    }

    if (modalCloseButton) {
        modalCloseButton.addEventListener('click', hideModal);
    }
    
    // --- Close Modal on Outside Click ---
    if (characterModalOverlay) {
        characterModalOverlay.addEventListener('click', (event) => {
            if (!event.target.closest('.character-display') && !event.target.closest('.character-modal-content')) {
                hideModal();
            }
        });
    }
    
    // --- Sound Toggle ---
    if (soundToggleBtn && soundIcon) {
        soundToggleBtn.addEventListener('click', () => {
            isSoundOn = !isSoundOn;
            if (isSoundOn) {
                soundIcon.classList.replace('fa-volume-mute', 'fa-volume-up');
            } else {
                soundIcon.classList.replace('fa-volume-up', 'fa-volume-mute');
                if (audioPlayer) audioPlayer.pause();
            }
        });
    }

    // --- Login/Avatar Dropdown ---
    if (loginAvatarBtn && loginDropdown) {
        loginAvatarBtn.addEventListener('click', (event) => {
            event.stopPropagation();
            loginDropdown.classList.toggle('visible');
        });

        document.addEventListener('click', (event) => {
            if (!loginAvatarBtn.contains(event.target) && !loginDropdown.contains(event.target)) {
                loginDropdown.classList.remove('visible');
            }
        });
    }

   // --- Top Nav Clicks ---
        document.querySelectorAll('.main-nav .nav-item').forEach(navItem => {
        navItem.addEventListener('click', (event) => {

        // Check if the clicked item is one we want to allow
            if (navItem.classList.contains('parenting-btn') || 
                navItem.classList.contains('parent-dashboard-btn') ||
                navItem.classList.contains('how-to-use-btn')) { // <-- THIS IS THE ADDED LINE

            // If it is, DO NOT prevent the default action.
            // Let the link work as normal.
            } else {
                // For any OTHER nav button, stop the link and show an alert.
                event.preventDefault();
                const tooltipText = navItem.dataset.tooltip;
                alert(`The '${tooltipText}' feature is not yet connected.`);
            }

       
    });

}); // This is the final, correct closing brace and parenthesis.

}); // This is the final, correct closing brace and parenthesis.