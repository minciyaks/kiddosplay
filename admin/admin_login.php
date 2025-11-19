<?php
session_start();
// If admin is already logged in, redirect to the dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php
        // Display error messages if any
        if (isset($_SESSION['error'])) {
            echo '<p class="error">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
        
     <form action="handle_admin_login.php" method="post">
        <div class="input-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
        </div>
        <div class="input-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
    </div>
</body>
</html>