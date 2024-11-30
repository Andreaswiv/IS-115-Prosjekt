<?php
session_start();
require_once '../src/func/security.php';
require_once '../src/func/header.php';
ensureAdmin();

if (isset($_SESSION['error_message'])) {
    echo '<p style="color: red; font-weight: bold; text-align: center;">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
    unset($_SESSION['error_message']);
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motell Booking ADMIN</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
    <link rel="stylesheet" href="../public/assets/css/headerStyle.css">
</head>
<body>
<div class="container">
    <h3>Motell-booking ADMIN</h3>
    <ul>
        <li><a target="_blank" href="forms/form_exisAdmin.php">Eksisterende bruker, ADMIN</a></li>
        <li><a target="_blank" href="forms/form_newAdmin.php">Ny bruker, ADMIN</a></li>
    </ul>
</div>
</body>
</html>
