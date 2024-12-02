<?php
session_start();

// Sjekk om sesjonsvariabler eksisterer
if (isset($_SESSION['user_id'])) {
    echo "User ID: " . $_SESSION['user_id'] . "<br>";
    echo "Role: " . $_SESSION['role'] . "<br>";
    echo "Username: " . $_SESSION['username'] . "<br>";
} else {
    echo "No session variables found. Are you logged in?";
}
?>