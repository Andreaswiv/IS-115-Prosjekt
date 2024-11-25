<?php
session_start();

// Allows access to specific pages without login
$current_page = basename($_SERVER['PHP_SELF']); // Gets the name from the current server
$allowed_pages = ['login.php', 'register.php','authController.php','registerController.php'  ]; // Allowed pages

if (!isset($_SESSION['user_id']) && !in_array($current_page, $allowed_pages)) {
    $_SESSION['error_message'] = 'You must be logged in to access this page.';
    header("Location: /Prosjekt/IS-115-Prosjekt/public/login.php");
    exit();
}

// Admin Check
function ensureAdmin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        $_SESSION['error_message'] = 'Access denied. Admins only.';
        header("Location: /Prosjekt/IS-115-Prosjekt/public/index.php");
        exit();
    }
}
?>
