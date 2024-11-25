<?php
session_start();
require_once '../assets/inc/db.php';
require_once '../models/User.php';

$db_instance = new Database();
$db = $db_instance->getConnection();

$maxAttempts = 3; // Maximum failed login attempts allowed
$lockoutDuration = 3600; // Lockout duration in seconds (1 hour)

// Initialize session variables for failed login attempts if not already set
if (!isset($_SESSION['failed_attempts'])) {
    $_SESSION['failed_attempts'] = 0;
    $_SESSION['lockout_time'] = null;
}

// Check if the user is currently locked out
if ($_SESSION['lockout_time'] && time() < $_SESSION['lockout_time']) {
    $remainingLockout = $_SESSION['lockout_time'] - time();
    $_SESSION['login_error'] = "Your account is locked. Try again in " . ceil($remainingLockout / 60) . " minutes.";
    header("Location: ../../public/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validate input
    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = 'Username and password are required.';
        header("Location: ../../public/login.php");
        exit();
    }

    try {
        $user = new User($db);
        $userData = $user->getUser($username);

        if ($userData && password_verify($password, $userData['password'])) {
            // Reset failed attempts on successful login
            $_SESSION['failed_attempts'] = 0;
            $_SESSION['lockout_time'] = null;

            // Set session variables
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['role'] = $userData['role'];
            $_SESSION['username'] = $userData['username'];

            // Debugging for troubleshooting
            error_log("User logged in: " . print_r($_SESSION, true));

            // Redirect based on role
            if ($userData['role'] === 'admin') {
                header("Location: ../../src/AdminIndex.php");
            } else {
                header("Location: ../../public/index.php");
            }
            exit();
        } else {
            // Increment failed attempts on failed login
            $_SESSION['failed_attempts']++;

            if ($_SESSION['failed_attempts'] >= $maxAttempts) {
                // Lock the user out
                $_SESSION['lockout_time'] = time() + $lockoutDuration;
                $_SESSION['login_error'] = "Too many failed attempts. Your account is locked for 1 hour.";
            } else {
                $_SESSION['login_error'] = "Invalid username or password. Attempt " . $_SESSION['failed_attempts'] . " of $maxAttempts.";
            }

            header("Location: ../../public/login.php");
            exit();
        }
    } catch (Exception $e) {
        // Handle any exceptions
        error_log("Login error: " . $e->getMessage());
        $_SESSION['login_error'] = 'Something went wrong. Please try again.';
        header("Location: ../../public/login.php");
        exit();
    }
}
?>