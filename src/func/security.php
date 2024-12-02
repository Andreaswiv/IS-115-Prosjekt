<?php
// Check if a session has already been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Define security functions
function runSecurityChecks() {
    // Pages allowed without login
    $current_page = basename($_SERVER['PHP_SELF']);
    $allowed_pages = ['login.php', 'register.php', 'authController.php', 'registerController.php'];

    // Check if the user is not logged in and the page is not in allowed_pages
    if (!isset($_SESSION['user_id']) && !in_array($current_page, $allowed_pages)) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI']; // Save the current page URL
        $_SESSION['error_message'] = "You must log in to access this page.";
        header("Location: /IS-115-Prosjekt/public/login.php");
        exit();
    }
}

// Admin check function
function ensureAdmin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        $_SESSION['error_message'] = 'Access denied. Admins only.';
        header("Location: /IS-115-Prosjekt/public/homePage.php");
        exit();
    }
}
?>