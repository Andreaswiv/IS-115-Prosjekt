<?php
require_once '../../src/resources/inc/db.php';
require_once '../../src/models/User.php';

session_start();

$db_instance = new Database();
$db = $db_instance->getConnection();

$user = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $postalCode = trim($_POST['postalCode']);
    $role = trim($_POST['role']);

    // Form validation
    $errors = [];
    if (strlen($username) < 4) {
        $errors[] = "Username must be at least 4 characters long.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    if ($errors) {
        $_SESSION['register_errors'] = $errors;
        $_SESSION['form_data'] = $_POST; // Save user input
        header("Location: ../../public/register.php");
        exit();
    }

    try {
        // Create new user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $user->createUser($username, $firstName, $lastName, $email, $phone, $address, $postalCode, $hashedPassword, $role);

        // Automatically log in the user after successful registration
        $_SESSION['user_id'] = $db->lastInsertId(); // Get the last inserted user ID
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;

        // Redirect to the stored URL if it exists
        if (isset($_SESSION['redirect_after_login'])) {
            $redirectUrl = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']); // Clear the session variable
            header("Location: $redirectUrl");
        } else {
            header("Location: ../../public/homePage.php"); // Default redirect after registration
        }
        exit();
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Registration error: " . $e->getMessage());

        $_SESSION['register_errors'] = ["An error occurred while registering. Please try again later."];
        $_SESSION['form_data'] = $_POST; // Save user input
        header("Location: ../../public/register.php");
        exit();
    }
}
