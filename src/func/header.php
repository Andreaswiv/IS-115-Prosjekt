<?php
session_start();
require_once __DIR__ . '/../../config.php';

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $username = htmlspecialchars($_SESSION['username']);
    $role = htmlspecialchars($_SESSION['role']);
} else {
    $username = null;
    $role = null; // Set a default value for role when user is not logged in
}
?>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/assets/css/headerStyle.css?v1.0.9">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
<div class="header">
    <!-- Logo -->
    <div class="logo-container">
        <img src="<?php echo BASE_URL; ?>public/assets/img/logo.png" alt="Logo" class="logo-image">
        <p>Wahl & Oldeide Motell</p>
    </div>

    <!-- Navigation Links -->
    <div class="nav-links">
        <?php if ($role === 'admin'): ?>
            <a href="<?php echo BASE_URL; ?>src/AdminIndex.php" class="nav-link">Admin Panel</a>
            <a href="<?php echo BASE_URL; ?>src/forms/form_newAdmin.php" class="nav-link">Ny bruker</a>
            <a href="<?php echo BASE_URL; ?>src/forms/form_exisAdmin.php" class="nav-link">Brukerbehandling</a>
            <a href="<?php echo BASE_URL; ?>src/forms/form_roomOverview.php" class="nav-link">Romoversikt</a>
        <?php elseif ($username): ?>
            <a href="<?php echo BASE_URL; ?>public/homePage.php" class="nav-link">Hjem</a>
            <a href="<?php echo BASE_URL; ?>src/forms/form_newBooking.php" class="nav-link">Ny Booking</a>
        <?php else: ?>
        <?php endif; ?>
    </div>

    <!-- User Image -->
    <div class="image-container">
        <?php if ($username): ?>
            <a href="<?php echo BASE_URL; ?>src/forms/form_exisUser.php" class="user-link">
                <img src="<?php echo BASE_URL; ?>public/assets/img/userImage.png" alt="User Image" class="user-image">
            </a>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>public/login.php" class="user-link">
                <img src="<?php echo BASE_URL; ?>public/assets/img/userImage.png" alt="Logg Inn" class="user-image">
                <span class="login-text">Logg Inn</span>
            </a>
        <?php endif; ?>
    </div>
</div>
