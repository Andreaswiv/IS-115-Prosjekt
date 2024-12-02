<?php
include '../../src/resources/inc/db.php';
include '../../src/resources/inc/db_queries.php'; // Include db_queries for reusable functions
include '../../src/resources/inc/functions.php';
require_once '../../src/func/security.php';
require_once '../../src/func/header.php';

runSecurityChecks(); // Ensure the user is logged in

// Create Database Connection
$database = new Database();
$db = $database->getConnection();

// Retrieve session or POST dates
$start_date = $_SESSION['start_date'] ?? $_POST['start_date'] ?? null;
$end_date = $_SESSION['end_date'] ?? $_POST['end_date'] ?? null;
$room_type = $_GET['room_type'] ?? 'King Suite';
$assignedRoomId = null;

// Automatically check availability if room_type, start_date, and end_date are provided
if ($room_type && $start_date && $end_date) {
    $assignedRoomId = getRandomAvailableRoomId($start_date, $end_date, $room_type, $db);

    if ($assignedRoomId) {
        $_SESSION['assignedRoomId'] = $assignedRoomId; // Store in session
    } else {
        unset($_SESSION['assignedRoomId']); // Clear session if no room is found
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'book') {
        // Retrieve room ID from session
        $assignedRoomId = $_SESSION['assignedRoomId'] ?? null;

        if ($assignedRoomId) {
            $floor = $_POST['floor'] ?? null;
            $nearElevator = isset($_POST['near_elevator']) ? 1 : 0;
            $hasView = isset($_POST['has_view']) ? 1 : 0;

            $userId = $_SESSION['user_id'];

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
                    unset($_SESSION['assignedRoomId']); // Clear session after booking
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
    <title>Single Room Booking</title>
    <link rel="stylesheet" href="../../public/assets/css/roomStyle.css?v1.0.2">
</head>
<body>
<div class="search-container">
    <form method="POST" class="search-form">
        <div class="input-group">
            <label for="start_date">Ankomst</label>
            <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date ?? '') ?>" required>
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
            <p>Det er ledige Enkelt rom på disse datoene!</p>
        </div>
    </div>
<?php else: ?>
    <div class="message-container">
        <div class="message error">
            <p>Det er dessverre ingen ledige Enkelt rom, prøv andre datoer.</p>
        </div>
    </div>
<?php endif; ?>

<div class="container">
    <div class="room-image">
        <img src="../../public/assets/img/single_room.JPG" alt="Enkelt rom">
    </div>

    <div class="room-details">
        <h2>Enkelt rom</h2>
        <p><strong>Størrelse:</strong> 25 m²</p>
        <p><strong>Kapasitet:</strong> 1 person</p>
        <p><strong>Wi-Fi:</strong> Inkludert</p>
    </div>
    
    <form method="POST">
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
        <button type="submit" name="action" value="book" <?= isset($_SESSION['assignedRoomId']) ? '' : 'disabled' ?>>Book Now</button>
    </form>
</div>
</body>
</html>