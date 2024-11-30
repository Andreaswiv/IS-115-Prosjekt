<?php
session_start();  // Start the session to access any session data

// Check if there is any login error stored in the session
if (isset($_SESSION['login_error'])) {
    echo '<p style="color: red;">' . $_SESSION['login_error'] . '</p>';
    unset($_SESSION['login_error']);  // Clear the error message after showing it
}
// Check if there is an error message in the session
if (isset($_SESSION['error_message'])) {
    echo '<p style="color: red;">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
    // Clear the error message from the session so it doesn't persist
    unset($_SESSION['error_message']);
}
// Check if there is a logout message
if (isset($_SESSION['logout_message'])) {
    echo '<p style="color: green; font-weight: bold; text-align: center;">' . htmlspecialchars($_SESSION['logout_message']) . '</p>';
    unset($_SESSION['logout_message']);
}
if (isset($_SESSION['register_success'])) {
    echo '<p style="color: green;">' . htmlspecialchars($_SESSION['register_success']) . '</p>';
    unset($_SESSION['register_success']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Innlogging</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<h2>Innlogging</h2>


<!-- Login form -->
<form action="../src/controllers/authController.php" method="post">
    <label for="username">Brukernavn:</label>
    <input type="text" name="username" id="username" required><br>

    <label for="password">Passord:</label>
    <input type="password" name="password" id="password" required><br>

    <button type="submit">Logg Inn</button>
</form>

<!-- Link to Register Page -->
<p>Har du ikke en konto? <a href="register.php">Registrer deg her</a></p>

</body>
</html>
