<?php
require_once '../../src/assets/inc/db.php';
require_once '../../src/models/user.php';

session_start(); // Start session to access session data

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Store form data in session for persistence
    $_SESSION['form_data'] = $_POST;

    $errors = [];

    // Manual form validation
    if (empty($_POST['username'])) {
        $errors[] = "Username is required";
    }
    if (empty($_POST['firstName'])) {
        $errors[] = "First name is required";
    }
    if (empty($_POST['lastName'])) {
        $errors[] = "Last name is required";
    }
    if (empty($_POST['email'])) {
        $errors[] = "Email is required";
    }
    if (empty($_POST['phone'])) {
        $errors[] = "Phone number is required";
    }
    if (empty($_POST['address'])) {
        $errors[] = "Address is required";
    }
    if (empty($_POST['postnummer'])) {
        $errors[] = "Postal number is required";
    }
    if (empty($_POST['password'])) {
        $errors[] = "Password is required";
    }

    // If there are validation errors, redirect back to the form with errors
    if (!empty($errors)) {
        $_SESSION['register_errors'] = $errors;
        header("Location: ../../public/register.php");
        exit();
    }

    try {
        // Create user object
        $user = new User($db);

        // Attempt to create a new user in the database
        $result = $user->createUser(
            $_POST['username'],
            $_POST['firstName'],
            $_POST['lastName'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['address'],
            $_POST['postnummer'],
            $_POST['password'],
            $_POST['role']
        );

        // Success: clear form data and redirect to login page
        unset($_SESSION['form_data']);
        $_SESSION['register_success'] = "User registered successfully!";
        header("Location: ../../public/index.php");
        exit();
    } catch (Exception $e) {
        // Handle errors
        $_SESSION['register_error'] = $e->getMessage();
        header("Location: ../../public/register.php");
        exit();
    }
}
?>
