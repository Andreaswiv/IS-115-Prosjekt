<?php
require_once '../../src/resources/inc/db.php'; // Updated database connection
require_once '../../src/models/Room.php'; // Room model
require_once '../../src/func/header.php'; // Header
require_once '../../src/func/security.php'; // Security functions

use models\Room;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create database connection
$database = new Database();
$conn = $database->getConnection();

// Create Room model
$roomModel = new Room($conn);

// Check if user_id is passed from the previous page
$userId = $_POST['user_id'] ?? $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? 'user'; // Default role is "user"
$displayUserMessage = false;

if ($userId && $role === 'admin') {
    $_SESSION['user_id'] = $userId; // Store it in session for later use
    $displayUserMessage = true;
}

// Define variables
$roomPrices = [
    'Single' => 1000,
    'Double' => 1500,
    'King Suite' => 2500,
];

// Initialize counts
$roomCounts = [
    'Single' => 0,
    'Double' => 0,
    'King Suite' => 0,
];

// Handle form submission
$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;
$adult_count = (int) ($_POST['adult_count'] ?? 1);
$child_count = (int) ($_POST['child_count'] ?? 0);
$guest_count = $adult_count + $child_count;

try {
    // Fetch available rooms based on user input
    if ($start_date && $end_date && $guest_count > 0) {
        $availableRooms = $roomModel->getAvailableRoomsForPeriod($start_date, $end_date, $guest_count);
    } else {
        $availableRooms = $roomModel->getAllRooms(); // Fetch all rooms initially
    }

    // Count rooms by type
    foreach ($availableRooms as $room) {
        $roomCounts[$room['room_type']]++;
    }
} catch (Exception $e) {
    $error_message = "Feil ved henting av ledige rom: " . $e->getMessage();
    echo "<p>Debug Error: $error_message</p>";
}

// Store data when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['start_date'] = $start_date;
    $_SESSION['end_date'] = $end_date;
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book et Rom</title>
    <link rel="stylesheet" href="../../public/assets/css/bookingStyle.css?v1.0.3">
</head>
<body>
<!-- Page Header -->
<div class="container">
    <?php if ($displayUserMessage): ?>
        <h1>Book et rom for Bruker-ID: <?= htmlspecialchars($userId); ?></h1>
    <?php else: ?>
        <h1>Book Rom Her</h1>
    <?php endif; ?>
</div>

<!-- Search Container -->
<div class="search-container">
    <form method="POST" class="search-form">
        <?php if ($displayUserMessage): ?>
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($userId); ?>">
        <?php endif; ?>
        <div class="input-group">
            <label for="start_date">Ankomst</label>
            <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
        </div>
        <div class="input-group">
            <label for="end_date">Utreise</label>
            <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
        </div>
        <div class="input-group">
            <label for="adult_count">Antall Voksne</label>
            <input type="number" id="adult_count" name="adult_count" value="<?= htmlspecialchars($adult_count) ?>" min="1">
        </div>
        <div class="input-group">
            <label for="child_count">Antall Barn</label>
            <input type="number" id="child_count" name="child_count" value="<?= htmlspecialchars($child_count) ?>" min="0">
        </div>
        <button type="submit" class="search-button">Søk</button>
    </form>
</div>

<!-- Room Cards -->
<div class="room-cards">
    <?php foreach (['Single', 'Double', 'King Suite'] as $roomType): ?>
        <a href="../forms/<?= $roomType === 'King Suite' ? 'kingSuiteBooking.php' : ($roomType === 'Single' ? 'singleRoomBooking.php' : 'doubleRoomBooking.php'); ?>?user_id=<?= urlencode($userId); ?>&room_type=<?= urlencode($roomType); ?>&start_date=<?= urlencode($start_date); ?>&end_date=<?= urlencode($end_date); ?>" class="room-card">
            <img src="../../public/assets/img/<?= $roomType === 'King Suite' ? 'king_suite.jpeg' : ($roomType === 'Single' ? 'single_room.JPG' : 'double_room.jpg'); ?>" alt="<?= htmlspecialchars($roomType); ?> Room">
            <div class="room-details">
                <h3><?= htmlspecialchars($roomType); ?> Room</h3>
                <ul>
                    <li>Plass til <?= $roomType === 'Single' ? 1 : ($roomType === 'Double' ? 2 : 4); ?> personer</li>
                    <li>Wi-Fi inkludert</li>
                    <li><?= htmlspecialchars($roomCounts[$roomType]); ?> ledige rom</li>
                </ul>
                <p class="price">Pris: <?= number_format($roomPrices[$roomType], 0, ',', ' ') ?> NOK per natt</p>
            </div>
        </a>
    <?php endforeach; ?>
</div>

</body>
</html>
