<?php
require_once '../../public/assets/inc/db.php';
require_once '../../src/models/User.php';

session_start();

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $userData = $user->getUser($username);

    if ($userData && password_verify($password, $userData['password'])) {
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['role'] = $userData['role'];
        header("Location: ../../public/index.php");
        exit();
    } else {
        $_SESSION['login_error'] = 'Invalid username or password.';
        header("Location: ../../public/login.php");
        exit();
    }
}
?>