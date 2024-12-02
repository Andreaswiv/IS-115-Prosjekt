<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../src/resources/inc/db.php';
require_once '../src/models/Room.php';
require_once '../src/func/header.php';
require_once '../src/func/security.php';

use models\Room;

ensureAdmin();

// Initialize database connection and Room model
$database = new Database();
$db = $database->getConnection();
$roomModel = new Room($db);

// Default date values
$start_date = date('Y-m-d');
$end_date = date('Y-m-d', strtotime('+1 day'));
$error_message = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'] ?? $start_date;
    $end_date = $_POST['end_date'] ?? $end_date;

    // Validate dates
    if ($start_date >= $end_date) {
        $error_message = ($start_date == $end_date)
            ? "Man må minst reservere et helt døgn."
            : "Ankomstdato kan ikke være etter utreisedato.";
    }
}

// Fetch room data only if no validation errors
if (!$error_message) {
    $totalRooms = count($roomModel->getAllRooms());
    $availableRooms = $roomModel->countAvailableRoomsForPeriod($start_date, $end_date);
} else {
    $totalRooms = $roomModel->getAllRooms() ? count($roomModel->getAllRooms()) : 0;
    $availableRooms = 'N/A';
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motell Booking ADMIN</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
    <link rel="stylesheet" href="../public/assets/css/roomStyle.css?v1.0.1">

</head>
<body>
<br>
<h4>Motell-booking ADMIN</h4>
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
        <button type="submit" class="search-button-two">Søk</button>
    </form>
</div>
<div class="container">
    <!-- Romoversikt -->
    <a href="forms/form_roomOverview.php?start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>" class="room-overview-link">
    <h2>Romoversikt</h2>
    <ul>
        <li>Totalt antall rom: <?php echo htmlspecialchars($totalRooms); ?></li>
        <li>Tilgjengelige rom for den valgte perioden er <?php echo htmlspecialchars($availableRooms); ?></li>
    </ul>
        <br>
    <h3>Trykk på meg for mer informasjon</h3>
    </a>
</div>
</body>
</html>