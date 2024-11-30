<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../src/func/header.php';
require_once '../src/func/security.php';
ensureAdmin();

require_once '../src/assets/inc/db.php';
require_once '../src/models/Room.php';

$db = new PDO("mysql:host=localhost;dbname=motell_booking", "root", "");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$roomModel = new Room($db);

$rooms = $roomModel->getAllRooms();
$totalRooms = count($rooms);
$availableRooms = count(array_filter($rooms, fn($room) => $room['is_available'] === 1));

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

    <!-- Eksisterende funksjoner -->
    <ul>
        <li><a target="_blank" href="forms/form_exisAdmin.php">Eksisterende bruker, ADMIN</a></li>
        <li><a target="_blank" href="forms/form_newAdmin.php">Ny bruker, ADMIN</a></li>
    </ul>

    <!-- Rask romoversikt -->
    <h3>Romoversikt</h3>
    <p>
        Totalt antall rom: <?php echo $totalRooms; ?><br>
        Tilgjengelige rom for booking: <?php echo $availableRooms; ?>
    </p>
    <a href="forms/form_roomOverview.php" class="btn">Gå til Romoversikt</a>
</div>
</body>
</html>
