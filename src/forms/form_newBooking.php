<?php
include '../../src/assets/inc/setupdb/setup.php';
include '../../src/assets/inc/functions.php';
require_once '../../src/func/header.php';
require_once '../../src/func/security.php';

// Room types and their views
$roomTypes = [
    'Single Room' => ['Room with view', 'Room without view'],
    'Double Room' => ['Room with view', 'Room without view'],
    'King Suite' => [],
];

$floors = [1, 2];

// Fetch prefilled data from the URL
$room_type = $_GET['room_type'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// Get the logged-in user ID
$user_id = $_SESSION['user_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Room</title>
    <link rel="stylesheet" href="../../public/assets/css/bookingStyle.css?v1.0.2">
</head>
<body>
<div class="container">
    <h1>Book a Room</h1>

    <?php if (!empty($room_type)): ?>
        <!-- Display the selected room -->
        <div class="room-cards">
            <a href="form_newBooking.php?room_type=<?= urlencode($room_type) ?>&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>" class="room-card">
                <img src="<?php
                switch ($room_type) {
                    case 'Single Room':
                        echo '../../public/assets/img/single_room.JPG';
                        break;
                    case 'Double Room':
                        echo '../../public/assets/img/double_room.jpg';
                        break;
                    case 'King Suite':
                        echo '../../public/assets/img/king_suite.jpeg';
                        break;
                    default:
                        echo '../../public/assets/img/default_room.jpg';
                }
                ?>" alt="Room Image">
                <div class="room-details">
                    <h3><?= htmlspecialchars($room_type) ?></h3>
                    <p><strong>Fantastisk</strong></p>
                    <ul>
                        <li>Room details based on type...</li>
                    </ul>
                    <p class="price">Price details based on room type...</p>
                </div>
            </a>
        </div>
    <?php else: ?>
        <!-- Display all rooms -->
        <div class="room-cards">
            <a href="../forms/singleRoomBooking.php" class="room-card">
                <img src="../../public/assets/img/single_room.JPG" alt="Single Room">
                <div class="room-details">
                    <h3>Single Room</h3>
                    <ul>
                        <li>25 m²</li>
                        <li>Plass til 1 person</li>
                        <li>Wi-Fi inkludert</li>
                    </ul>
                </div>
            </a>
            <a href="../forms/doubleRoomBooking.php" class="room-card">
                <img src="../../public/assets/img/double_room.jpg" alt="Double Room">
                <div class="room-details">
                    <h3>Double Room</h3>
                    <ul>
                        <li>30 m²</li>
                        <li>Plass til 2 personer</li>
                        <li>Wi-Fi inkludert</li>
                    </ul>
                </div>
            </a>
            <a href="../forms/kingSuiteBooking.php" class="room-card">
                <img src="../../public/assets/img/king_suite.jpeg" alt="King Suite">
                <div class="room-details">
                    <h3>King Suite</h3>
                    <ul>
                        <li>50 m²</li>
                        <li>Plass til 4 personer</li>
                        <li>Wi-Fi inkludert</li>
                    </ul>
                </div>
            </a>
        </div>

    <?php endif; ?>
</div>

<script src="../../public/assets/js/bookingScript.js"></script>
</body>
</html>
