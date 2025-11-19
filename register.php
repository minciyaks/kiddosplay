<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KiddosPlay – Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/register.css">
    <style>
        .error-message {
            background-color: #ffebee; color: #c62828; padding: 10px;
            border-radius: 5px; margin-bottom: 15px; text-align: center; font-weight: 600;
        }
        .input-helper { font-size: 0.8em; color: #666; margin-top: -10px; margin-bottom: 10px; }
    </style>
</head>
<body>

    <a href="index.html" class="back-button" aria-label="Back to Home">
        <span>←</span>
    </a>

    <form class="register-box" action="handle_register.php" method="POST" enctype="multipart/form-data">
        <h2>Register</h2>

        <?php
            if (isset($_SESSION['register_error'])) {
                echo '<div class="error-message">' . $_SESSION['register_error'] . '</div>';
                unset($_SESSION['register_error']);
            }
        ?>

        <label for="username">Username</label>
        <input type="text" name="username" pattern="[a-zA-Z0-9_]{3,20}" title="3-20 characters, letters and numbers only" required>

        <label for="email">Email (@gmail.com only)</label>
        <input type="text" name="email" id="emailInput" placeholder="example@gmail.com" pattern=".+@gmail\.com" title="Please enter a valid @gmail.com address" required>

        <label for="password">Password</label>
        <div class="password-wrapper">
            <input type="password" name="password" id="registerPassword" minlength="6" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 6 or more characters" required>
            <img id="toggleRegisterPassword" src="images/eye.png" alt="Toggle Password">
        </div>
        <p class="input-helper">Min 6 chars, 1 uppercase letter, 1 number.</p>

        <label for="role">Role</label>
        <select name="role" required>
            <option value="">Select Role</option>
            <option value="parent">Parent</option>
            <option value="teacher">Teacher</option>
        </select>

        <label for="age">Child's Age</label>
        <select name="age" required>
            <option value="">Select Age Group</option>
            <option value="2">2 years</option>
            <option value="3">3 years</option>
            <option value="4">4 years</option>
            <option value="5">5 years</option>
        </select>
        
        <label for="document_type">Document Type</label>
        <select name="document_type" required>
            <option value="birth_certificate">Birth Certificate</option>
            <option value="school_id">School ID</option>
        </select>

        <label for="certificate">Upload Certificate (PDF, JPG, PNG)</label>
        <input type="file" name="certificate" accept=".pdf,.jpg,.jpeg,.png" required>

        <button type="submit">Register</button>
    </form>

    <script src="js/register.js"></script>
    <script>
        const emailInput = document.getElementById('emailInput');
        emailInput.addEventListener('blur', function() {
             // If user typed something, but didn't include '@', append @gmail.com
             if (this.value.trim() !== '' && !this.value.includes('@')) {
                 this.value = this.value.trim() + '@gmail.com';
             }
        });
    </script>
</body>
</html>