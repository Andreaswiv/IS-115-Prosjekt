<?php
require_once '../../src/resources/inc/db.php';        // Updated database connection
require_once '../../src/models/Room.php';             // Room model
require_once '../../src/func/header.php';             // Header (if needed)
require_once '../../src/func/security.php';           // Security functions

use models\Room;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create database connection
$database = new Database();
$conn = $database->getConnection();

// Create Room model
$roomModel = new Room($conn);

// Define variables
$availableSingleRooms = 0;
$availableDoubleRooms = 0;
$availableKingSuites = 0;
$roomPrices = [
    'Single' => 1000,
    'Double' => 1500,
    'King Suite' => 2500,
];

// Handle form submission
$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;
$adult_count = (int) ($_POST['adult_count'] ?? 1);
$child_count = (int) ($_POST['child_count'] ?? 0);
$guest_count = $adult_count + $child_count;

try {
    // Fetch available rooms based on the user input
    if ($start_date && $end_date && $guest_count > 0) {
        $availableSingleRooms = $roomModel->countAvailableRooms($start_date, $end_date, $guest_count, 'Single');
        $availableDoubleRooms = $roomModel->countAvailableRooms($start_date, $end_date, $guest_count, 'Double');
        $availableKingSuites = $roomModel->countAvailableRooms($start_date, $end_date, $guest_count, 'King Suite');
    }
} catch (Exception $e) {
    // Handle any errors that occur during fetching
    $error_message = "Feil ved henting av ledige rom: " . $e->getMessage();
    echo "<p>Debug Error: $error_message</p>";
}

// Stores data when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['start_date'] = $start_date;
    $_SESSION['end_date'] = $end_date;
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Book et Rom</title>
    <link rel="stylesheet" href="../../public/assets/css/bookingStyle.css?v1.0.3">
</head>
<body>
<!-- Search Container -->
<br>
<div class="search-container">
    <form method="POST" class="search-form">
        <div class="input-group">
            <label for="start_date">Ankomst</label>
            <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" required>
        </div>
        <div class="input-group">
            <label for="end_date">Utreise</label>
            <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" required>
        </div>
        <div class="input-group">
            <label for="adult_count">Antall Voksne</label>
            <input type="number" id="adult_count" name="adult_count" value="<?= htmlspecialchars($adult_count) ?>" min="1" required>
        </div>
        <div class="input-group">
            <label for="child_count">Antall Barn</label>
            <input type="number" id="child_count" name="child_count" value="<?= htmlspecialchars($child_count) ?>" min="0">
        </div>
        <button type="submit" class="search-button">Søk</button>
    </form>
</div>
<div class="room-cards">
    <a href="../forms/singleRoomBooking.php?room_type=Single&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>&guest_count=<?= urlencode($guest_count) ?>" class="room-card">
        <img src="../../public/assets/img/single_room.JPG" alt="Single Room">
        <div class="room-details">
            <h3>Single Room</h3>
            <ul>
                <li>25 m²</li>
                <li>Plass til 1 person</li>
                <li>Wi-Fi inkludert</li>
            </ul>
            <p>Pris: <?= number_format($roomPrices['Single'], 0, ',', ' ') ?> NOK per natt</p>
            <br>
            <p>Ledige rom: <?= htmlspecialchars($availableSingleRooms) ?></p>
        </div>
    </a>
    <a href="../forms/doubleRoomBooking.php?room_type=Double&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>&guest_count=<?= urlencode($guest_count) ?>" class="room-card">
        <img src="../../public/assets/img/double_room.jpg" alt="Double Room">
        <div class="room-details">
            <h3>Double Room</h3>
            <ul>
                <li>30 m²</li>
                <li>Plass til 2 personer</li>
                <li>Wi-Fi inkludert</li>
            </ul>
            <p>Pris: <?= number_format($roomPrices['Double'], 0, ',', ' ') ?> NOK per natt</p>
            <br>
            <p>Ledige rom: <?= htmlspecialchars($availableDoubleRooms) ?></p>
        </div>
    </a>
    <a href="../forms/kingSuiteBooking.php?room_type=King Suite&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>&guest_count=<?= urlencode($guest_count) ?>" class="room-card">
        <img src="../../public/assets/img/king_suite.jpeg" alt="King Suite">
        <div class="room-details">
            <h3>King Suite</h3>
            <ul>
                <li>50 m²</li>
                <li>Plass til 4 personer</li>
                <li>Wi-Fi inkludert</li>
            </ul>
            <p>Pris: <?= number_format($roomPrices['King Suite'], 0, ',', ' ') ?> NOK per natt</p>
            <br>
            <p>Ledige rom: <?= htmlspecialchars($availableKingSuites) ?></p>
        </div>
    </a>
</div>

<script src="../../public/assets/js/bookingScript.js"></script>
</body>
</html>
