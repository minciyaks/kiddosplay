
document.addEventListener("DOMContentLoaded", function() {
    // --- Element Selections ---
    const timeDisplay = document.getElementById('time-display'); 
    const lockScreen = document.getElementById('lock-screen');
    const unlockButton = document.getElementById('unlock-button');
    const passwordInput = document.getElementById('parent-password');
    const errorMessage = document.getElementById('error-message');

    // --- Timer Logic ---
    let totalSeconds = parseInt(localStorage.getItem('totalSessionTime')) || 0;
    
    const sessionTimer = setInterval(() => {
        const path = window.location.pathname;
        const isHomePage = path.endsWith('/') || path.endsWith('/index.html') || path.endsWith('/home.html') || path.endsWith('/home.php');
        
        if (!isHomePage) {
            totalSeconds++;
            localStorage.setItem('totalSessionTime', totalSeconds);
        }
        
        let minutes = Math.floor(totalSeconds / 60);
        let seconds = totalSeconds % 60;
        if (timeDisplay) {
            timeDisplay.textContent = 
            `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }
          
        if (totalSeconds >= 1800) {
          if(lockScreen) lockScreen.style.display = 'flex';
          clearInterval(sessionTimer);
        }
    }, 1000);

    // --- Unlock Logic ---
    if (unlockButton) {
        unlockButton.addEventListener('click', function() {
            const password = passwordInput.value;
            
            // Send the password to the back-end to be verified
            fetch('/project/api/unlock.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ password: password })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // If password is correct, hide the lock screen and reset the timer
                    lockScreen.style.display = 'none';
                    localStorage.setItem('totalSessionTime', 0);
                    location.reload(); // Reload the page to restart the timer
                } else {
                    // If wrong, show an error message
                    errorMessage.textContent = 'Incorrect password. Please try again.';
                }
            });
        });
    }
});