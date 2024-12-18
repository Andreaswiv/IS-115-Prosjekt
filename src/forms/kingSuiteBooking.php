<?php
include '../../src/resources/inc/db.php';
include '../../src/resources/inc/db_queries.php';
include '../../src/resources/inc/functions.php';
require_once '../../src/func/security.php';
require_once '../../src/func/header.php';
require_once '../../src/models/Booking.php';
require_once '../../src/models/Room.php';

use models\Booking;
use models\Room;

runSecurityChecks(); // Ensure the user is authenticated

// Create a database connection
$database = new Database();
$db = $database->getConnection();

// Retrieve session or POST data for dates and room type
$start_date = $_SESSION['start_date'] ?? $_POST['start_date'] ?? null;
$end_date = $_SESSION['end_date'] ?? $_POST['end_date'] ?? null;
$room_type = $_GET['room_type'] ?? 'King Suite';
$assignedRoomId = null;

// Check room availability if dates and room type are set
if ($room_type && $start_date && $end_date) {
    $assignedRoomId = getRandomAvailableRoomId($start_date, $end_date, $room_type, $db);

    if ($assignedRoomId) {
        $_SESSION['assignedRoomId'] = $assignedRoomId; // Save assigned room ID to session
    } else {
        unset($_SESSION['assignedRoomId']); // Clear session if no room is found
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'book') {
        $assignedRoomId = $_SESSION['assignedRoomId'] ?? null; // Retrieve room ID from session

        if ($assignedRoomId) {
            $floor = $_POST['floor'] ?? null; // Selected floor preference
            $nearElevator = isset($_POST['near_elevator']) ? 1 : 0; // Elevator proximity preference
            $hasView = isset($_POST['has_view']) ? 1 : 0; // View preference

            $userId = $_SESSION['user_id']; // Retrieve logged-in user's ID

            try {
                $bookingCreated = createBooking(
                    $userId,
                    $assignedRoomId,
                    $room_type,
                    $floor,
                    $nearElevator,
                    $hasView,
                    $start_date,
                    $end_date,
                    $db
                );

                if ($bookingCreated) {
                    echo "<p>Booking successfully created!</p>";
                    unset($_SESSION['assignedRoomId']); // Clear session after successful booking
                } else {
                    echo "<p>Failed to create booking.</p>";
                }
            } catch (Exception $e) {
                echo "<p>Database error: {$e->getMessage()}</p>";
            }
        } else {
            echo "<p>No available room to book. Please search again.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>King Suite Booking</title> <!-- Page title -->
    <link rel="stylesheet" href="../../public/assets/css/roomStyle.css?v1.0.2"> <!-- Stylesheet link -->
</head>
<body>
<div class="search-container">
    <form method="POST" class="search-form"> <!-- Form to select dates -->
        <div class="input-group">
            <label for="start_date">Ankomst</label>
            <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" required>
        </div>
        <div class="input-group">
            <label for="end_date">Utreise</label>
            <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" required>
        </div>
        <button type="submit" name="action" value="search" class="search-button">Søk</button>
    </form>
</div>

<?php if ($assignedRoomId): ?>
    <div class="message-container">
        <div class="message success">
            <p>Det er ledige King Suite rom på disse datoene!</p>
        </div>
    </div>
<?php else: ?>
    <div class="message-container">
        <div class="message error">
            <p>Det er dessverre ingen ledige King Suite rom, prøv andre datoer.</p>
        </div>
    </div>
<?php endif; ?>

<div class="container">
    <div class="room-image">
        <img src="../../public/assets/img/king_suite.jpeg?v1.0.3" alt="King Suite"> <!-- Room image -->
    </div>

    <div class="room-details">
        <h2>King Suite</h2> <!-- Room details -->
        <p><strong>Størrelse:</strong> 75 m²</p>
        <p><strong>Kapasitet:</strong> 5 personer</p>
        <p><strong>Wi-Fi:</strong> Inkludert</p>
    </div>

    <form method="POST"> <!-- Booking preferences form -->
        <input type="hidden" name="start_date" value="<?= htmlspecialchars($start_date ?? '') ?>">
        <input type="hidden" name="end_date" value="<?= htmlspecialchars($end_date ?? '') ?>">
        <select name="floor" id="floor">
            <option value="" selected>Hvilken som helst etasje</option>
            <option value="1">1. Etasje</option>
            <option value="2">2. Etasje</option>
        </select>
        <div>
            <label>
                <input type="checkbox" name="near_elevator"> Helst Nær en Heis
            </label>
        </div>
        <div>
            <label>
                <input type="checkbox" name="has_view"> Helst Rom med Utsikt
            </label>
        </div>
        <button type="submit" name="action" value="book" <?= isset($_SESSION['assignedRoomId']) ? '' : 'disabled' ?>>Book Now</button> <!-- Book button -->
    </form>
</div>
</body>
</html>
