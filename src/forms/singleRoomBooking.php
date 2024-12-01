<?php
include '../../src/assets/inc/setupdb/setup.php';
include '../../src/assets/inc/functions.php';
require_once '../../src/func/security.php';
require_once '../../src/func/header.php';
require_once '../../src/models/Booking.php';
require_once '../../src/models/Room.php';

use models\Booking;
use models\Room;

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Database connection
$conn = new \PDO('mysql:host=localhost;dbname=motell_booking', 'root', '');
$conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

// Create Room object
$room = new Room($conn);

// Function to get a random available room
function getRandomAvailableRoomId($start_date, $end_date, $conn) {
    $query = "
        SELECT r.id
        FROM rooms r
        WHERE r.room_type = 'Single' -- Change 'Single' to desired room type if needed
          AND NOT EXISTS (
                SELECT 1
                FROM bookings b
                WHERE b.room_id = r.id
                  AND (
                      (:start_date BETWEEN b.start_date AND b.end_date)
                      OR (:end_date BETWEEN b.start_date AND b.end_date)
                      OR (b.start_date BETWEEN :start_date AND :end_date)
                      OR (b.end_date BETWEEN :start_date AND :end_date)
                  )
              )
        LIMIT 1
    ";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchColumn(); // Return the first available room ID
}

// Handle form submission
$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;
$assignedRoomId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'search') {
        // Search for available rooms
        $assignedRoomId = getRandomAvailableRoomId($start_date, $end_date, $conn);

        if ($assignedRoomId) {
            $_SESSION['assignedRoomId'] = $assignedRoomId; // Store in session
            $_SESSION['start_date'] = $start_date;         // Store dates in session
            $_SESSION['end_date'] = $end_date;
        } else {
            unset($_SESSION['assignedRoomId']); // Clear session if no room is found
        }
    } elseif ($action === 'book') {
        // Retrieve room ID from session
        $assignedRoomId = $_SESSION['assignedRoomId'] ?? null;

        if ($assignedRoomId) {
            $floor = $_POST['floor'] ?? null;
            $nearElevator = isset($_POST['near_elevator']) ? 1 : 0;
            $hasView = isset($_POST['has_view']) ? 1 : 0;

            $booking = new Booking($conn);
            $userId = $_SESSION['user_id'];
            $roomType = 'Single';
            $start_date = $_SESSION['start_date'] ?? null;
            $end_date = $_SESSION['end_date'] ?? null;

            $bookingCreated = $booking->createBooking(
                $userId,
                $assignedRoomId,
                $roomType,
                $floor,
                $nearElevator,
                $hasView,
                $start_date,
                $end_date
            );

            if ($bookingCreated) {
                echo "<p>Booking successfully created!</p>";
                unset($_SESSION['assignedRoomId']); // Clear session after booking
            } else {
                echo "<p>Failed to create booking.</p>";
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
    <link rel="stylesheet" href="../../public/assets/css/roomStyle.css">
</head>
<body>
<form method="POST" class="search-bar">
    <div>
        <label for="start_date">Ankomst</label>
        <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" required>
    </div>
    <div>
        <label for="end_date">Utreise</label>
        <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" required>
    </div>
    <button type="submit" name="action" value="search">Søk</button>
</form>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'search'): ?>
    <?php if ($assignedRoomId): ?>
        <div class="message-container">
            <div class="message success">
                <p>Det er ledige rom på disse datoene!</p>
            </div>
        </div>
    <?php else: ?>
        <div class="message-container">
            <div class="message error">
                <p>Det er dessverre ingen ledige rom, søk på en ny dato for å se etter ledige rom</p>
            </div>
        </div>
    <?php endif; ?>
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