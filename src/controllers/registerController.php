<?php
require_once '../../src/assets/inc/db.php';
require_once '../../src/models/User.php';

session_start();

$db_instance = new Database();
$db = $db_instance->getConnection();

$user = new User($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $email = $_POST['phone'];
    $email = $_POST['address'];
    $email = $_POST['postalCode'];
    $role = $_POST['role'] ?? 'user';

    try {
        // Create new user
        $user->createUser($username, $firstName, $lastName, $email, '', '', '', $password, $role);

        $_SESSION['register_success'] = "Registration successful!";
        header("Location: ../../public/login.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['register_error'] = $e->getMessage();
        header("Location: ../../public/register.php");
        exit();
    }
}
?>
