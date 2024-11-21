<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
<h2>Login</h2>

<!-- Login form -->
<form action="../src/controllers/authController.php" method="post">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required><br>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required><br>

    <button type="submit">Log In</button>
</form>

<!-- Link to Register Page -->
<p>Don't have an account? <a href="register.php">Register here</a></p>

<?php
session_start();  // Start the session to access any session data

// Check if there is any login error stored in the session
if (isset($_SESSION['login_error'])) {
    echo '<p style="color: red;">' . $_SESSION['login_error'] . '</p>';
    unset($_SESSION['login_error']);  // Clear the error message after showing it
}
?>

</body>
</html>
