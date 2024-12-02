<?php

require_once '../src/func/security.php';
require_once '../src/func/header.php';
// Check if there is an error message in the session
if (isset($_SESSION['error_message'])) {
    echo '<p style="color: red; font-weight: bold; text-align: center;">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
    // Clear the error message from the session so it doesn't persist
    unset($_SESSION['error_message']);
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motell Booking</title>
    <link rel="stylesheet" href="../public/assets/css/style.css?v1.0.2">
</head>
<body>
<div class="container">
    <h3>Book Motell her</h3>
    <ul>
        <li><a target="_blank" href="../src/forms/form_newBooking.php">Book rom her</a></li>
    </ul>
</div>
</body>
</html>
