<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
<h2>Login</h2>
<form action="controllers/authController.php" method="post">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required><br>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required><br>

    <button type="submit">Log In</button>
</form>
</body>
</html>
<?php
session_start();
require_once './db.php';
require_once './models/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $userData = $user->getUser($username);

    if ($userData && password_verify($password, $userData['password'])) {
        // Store user data in session
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['role'] = $userData['role'];

        // Redirect to index.php
        header("Location: ../index.php");
        exit();
    } else {
        // If login fails, display an error message or redirect back to login page with an error
        echo "Invalid username or password.";
    }
}
?>
