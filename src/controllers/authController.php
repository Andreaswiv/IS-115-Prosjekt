<?php
session_start();
require_once '../assets/inc/db.php';
require_once '../models/User.php';

$db_instance = new Database();
$db = $db_instance->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Valider input
    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = 'Username and password are required.';
        header("Location: ../../public/login.php");
        exit();
    }

    try {
        $user = new User($db);
        $userData = $user->getUser($username);

        if ($userData && password_verify($password, $userData['password'])) {
            // Sett sesjonsvariabler
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['role'] = $userData['role'];
            $_SESSION['username'] = $userData['username'];

            // Debugging for feilsøking
            error_log("User logged in: " . print_r($_SESSION, true));

            // Omdiriger basert på rolle
            if ($userData['role'] === 'admin') {
                // Oppdater sti for AdminIndex.php
                header("Location: ../../src/AdminIndex.php");
            } else {
                header("Location: ../../public/index.php");
            }
            exit();
        } else {
            // Feil brukernavn eller passord
            $_SESSION['login_error'] = 'Invalid username or password.';
            header("Location: ../../public/login.php");
            exit();
        }
    } catch (Exception $e) {
        // Håndter eventuelle feil
        error_log("Login error: " . $e->getMessage());
        $_SESSION['login_error'] = 'Something went wrong. Please try again.';
        header("Location: ../../public/login.php");
        exit();
    }
}
?>