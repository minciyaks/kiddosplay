<?php session_start(); // Start the session at the very top ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>KiddosPlay – Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/login.css">
  <style>
    /* Add this style for a noticeable error message */
    .error-message {
        background-color: #ffebee;
        color: #c62828;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
        text-align: center;
        font-weight: 600;
    }
  </style>
</head>
<body>
  <a href="index.html" class="back-button">
            <span aria-label="Back">←</span> 
        </a>
    <div class="login-wrapper">     
  <form class="login-box" action="auth.php" method="POST">
    <h2>Login</h2>

    <?php
        // Check if a login error message exists in the session
        if (isset($_SESSION['login_error'])) {
            // Display the error message inside a div
            echo '<div class="error-message">' . $_SESSION['login_error'] . '</div>';
            
            // IMPORTANT: Remove the error message from the session
            // so it doesn't show up again if the user reloads the page.
            unset($_SESSION['login_error']);
        }
    ?>

    <input type="text" name="username" placeholder="Username" required>

    <div class="password-wrapper">
      <input type="password" id="password" name="password" placeholder="Password" required>
      <img src="images/eye.png" alt="Toggle Password" id="togglePassword">
    </div>

    <button type="submit">Login</button>

    <div class="reset-password-link">
      <a href="forgot_password.php">Forgot Password?</a>
    </div>
  </form>
      </div>
  <script src="js/login.js"></script>
</body>
</html>