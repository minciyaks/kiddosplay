<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KiddosPlay – Forgot Password</title>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/login.css">
  <style>
    .error-message {
        background-color: #ffebee; color: #c62828; padding: 10px;
        border-radius: 5px; margin-bottom: 15px; text-align: center; font-weight: 600;
    }
    .success-message {
        background-color: #e8f5e9; color: #2e7d32; padding: 10px;
        border-radius: 5px; margin-bottom: 15px; text-align: center; font-weight: 600;
    }
    .password-helper {
        font-size: 0.8em; color: #666; margin-top: -10px; margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <a href="login.php" class="back-button" aria-label="Back to Login">
    <span>←</span> 
  </a>
  
  <div class="login-wrapper">
    <form class="login-box" action="handle_forgot_password.php" method="POST">
      <h2>Reset Password</h2>

      <?php
        if (isset($_SESSION['forgot_password_error'])) {
            echo '<div class="error-message">' . htmlspecialchars($_SESSION['forgot_password_error']) . '</div>';
            unset($_SESSION['forgot_password_error']);
        }
        if (isset($_SESSION['forgot_password_success'])) {
            echo '<div class="success-message">' . htmlspecialchars($_SESSION['forgot_password_success']) . '</div>';
            unset($_SESSION['forgot_password_success']);
        }
      ?>

      <input type="text" name="email" id="emailInput" placeholder="Email" required>
      
      <div class="password-wrapper">
        <input type="password" id="new_password" name="new_password" placeholder="New Password" required minlength="6">
        <img src="images/eye.png" alt="Toggle Password" id="toggleNewPassword">
      </div>
      <div class="password-helper">Min 6 chars, 1 uppercase letter, 1 number.</div>

      <div class="password-wrapper">
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required minlength="6">
        <img src="images/eye.png" alt="Toggle Password" id="toggleConfirmPassword">
      </div>

      <button type="submit">Reset Password</button>
    </form>
  </div>
  
  <script>
    // --- 1. NEW: Auto-fill @gmail.com ---
    const emailInput = document.getElementById('emailInput');
    emailInput.addEventListener('blur', function() {
         if (this.value.trim() !== '' && !this.value.includes('@')) {
             this.value = this.value.trim() + '@gmail.com';
         }
    });

    // --- 2. Toggle Password Visibility ---
    document.getElementById('toggleNewPassword').addEventListener('click', function() {
      const input = document.getElementById('new_password');
      input.type = input.type === 'password' ? 'text' : 'password';
    });
    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
      const input = document.getElementById('confirm_password');
      input.type = input.type === 'password' ? 'text' : 'password';
    });
  </script>
</body>
</html>