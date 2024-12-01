<?php
session_start();
session_unset();
session_destroy();

// Start a new session to store the logout message
session_start();
$_SESSION['logout_message'] = "Du har blitt logget ut.";

// Redirect to login page
header("Location: homePage.php");
exit();
?>
