<?php
/*
session_start();

// Allows access to specific pages without login
$current_page = basename($_SERVER['PHP_SELF']);
$allowed_pages = ['login.php', 'register.php', 'authController.php', 'registerController.php'];

// Check if the user is not logged in and the page is not in allowed_pages
if (!isset($_SESSION['user_id']) && !in_array($current_page, $allowed_pages)) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI']; // Save current page URL
    $_SESSION['error_message'] = "You need to log in to access this page.";
    header("Location: /IS-115-Prosjekt/public/login.php");
    exit();
}

// Admin Check Function
function ensureAdmin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        $_SESSION['error_message'] = 'Access denied. Admins only.';
        header("Location: /IS-115-Prosjekt/public/index.php");
        exit();
    }
}
?>
*/

// Sjekk om en økt allerede er startet
if (session_status() === PHP_SESSION_NONE) { #################### ENDRET FRA session_start() ####################
    session_start();
}


// Definer sikkerhetsfunksjoner
function runSecurityChecks() {
    // Tillatte sider uten innlogging
    $current_page = basename($_SERVER['PHP_SELF']);
    $allowed_pages = ['login.php', 'register.php', 'authController.php', 'registerController.php'];

    // Sjekk om brukeren ikke er logget inn og siden ikke er i allowed_pages
    if (!isset($_SESSION['user_id']) && !in_array($current_page, $allowed_pages)) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI']; // Lagre nåværende side-URL
        $_SESSION['error_message'] = "Du må logge inn for å få tilgang til denne siden.";
        header("Location: /IS-115-Prosjekt/public/login.php");
        exit();
    }
}

// Adminsjekkfunksjon
function ensureAdmin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        $_SESSION['error_message'] = 'Access denied. Admins only.';
        header("Location: /IS-115-Prosjekt/public/index.php");
        exit();
    }
}
?>
