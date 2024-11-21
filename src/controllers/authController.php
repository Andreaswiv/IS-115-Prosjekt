<?php

use models\User;

session_start();
require_once 'public/assets/inc/db.php';
require_once 'src/models/User.php';

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
