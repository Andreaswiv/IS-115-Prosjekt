<?php
include '../../src/resources/inc/db.php';
include '../../src/resources/inc/db_queries.php'; // Include db_queries for reusable functions
include '../../src/resources/inc/functions.php';
require_once '../../src/func/security.php';
require_once '../../src/func/header.php';
require_once '../../src/models/Booking.php';
require_once '../../src/models/Room.php';

use models\Booking;
use models\Room;

runSecurityChecks(); // Ensure the user is logged in

// Create Database Connection
$database = new Database();
$db = $database->getConnection();

// Retrieve session or POST dates
$start_date = $_SESSION['start_date'] ?? $_POST['start_date'] ?? null; // Get start date from session or POST
$end_date = $_SESSION['end_date'] ?? $_POST['end_date'] ?? null; // Get end date from session or POST
$room_type = $_GET['room_type'] ?? 'Double'; // Default room type to 'Double'
$assignedRoomId = null;

// Automatically check availability if room_type, start_date, and end_date are provided
if ($room_type && $start_date && $end_date) {
    $assignedRoomId = getRandomAvailableRoomId($start_date, $end_date, $room_type, $db);

    if ($assignedRoomId) {
        $_SESSION['assignedRoomId'] = $assignedRoomId; // Store assigned room ID in session
    } else {
        unset($_SESSION['assignedRoomId']); // Clear session if no room is found
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? ''; // Determine user action

    if ($action === 'book') {
        $assignedRoomId = $_SESSION['assignedRoomId'] ?? null; // Retrieve room ID from session

        if ($assignedRoomId) {
            $floor = $_POST['floor'] ?? null; // Selected floor, if any
            $nearElevator = isset($_POST['near_elevator']) ? 1 : 0; // Checkbox for proximity to elevator
            $hasView = isset($_POST['has_view']) ? 1 : 0; // Checkbox for room with a view

            $userId = $_SESSION['user_id']; // Current user ID from session

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
                    echo "<p>Booking successfully created!</p>"; // Success message
                    unset($_SESSION['assignedRoomId']); // Clear assigned room ID after booking
                } else {
                    echo "<p>Failed to create booking.</p>"; // Error message
                }
            } catch (Exception $e) {
                echo "<p>Database error: {$e->getMessage()}</p>"; // Display database error
            }
        } else {
            echo "<p>No available room to book. Please search again.</p>"; // Notify user of no available rooms
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Double Room Booking</title>
    <link rel="stylesheet" href="../../public/assets/css/roomStyle.css?v1.0.2">
</head>
<body>
<div class="search-container">
    <form method="POST" class="search-form">
        <div class="input-group">
            <label for="start_date">Ankomst</label> <!-- Label for start date -->
            <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" required>
        </div>
        <div class="input-group">
            <label for="end_date">Utreise</label> <!-- Label for end date -->
            <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" required>
        </div>
        <button type="submit" name="action" value="search" class="search-button">Search</button> <!-- Search button -->
    </form>
</div>

<?php if ($assignedRoomId): ?>
    <div class="message-container">
        <div class="message success">
            <p>Det er ledige Dobbelt rom på disse datoene!</p> <!-- Message if rooms are available -->
        </div>
    </div>
<?php else: ?>
    <div class="message-container">
        <div class="message error">
            <p>Det er dessverre ingen ledige Dobbelt rom, prøv andre datoer.</p> <!-- Message if no rooms are available -->
        </div>
    </div>
<?php endif; ?>

<div class="container">
    <div class="room-image">
        <img src="../../public/assets/img/double_room.jpg" alt="Double Room"> <!-- Room image -->
    </div>

    <div class="room-details">
        <h2>Double Room</h2> <!-- Room title -->
        <p><strong>Size:</strong> 40 m²</p> <!-- Room size -->
        <p><strong>Capacity:</strong> 2 persons</p> <!-- Room capacity -->
        <p><strong>Wi-Fi:</strong> Included</p> <!-- Wi-Fi availability -->
    </div>

    <form method="POST">
        <input type="hidden" name="start_date" value="<?= htmlspecialchars($start_date ?? '') ?>"> <!-- Hidden start date -->
        <input type="hidden" name="end_date" value="<?= htmlspecialchars($end_date ?? '') ?>"> <!-- Hidden end date -->
        <select name="floor" id="floor">
            <option value="" selected>Any Floor</option> <!-- Default option for any floor -->
            <option value="1">1st Floor</option>
            <option value="2">2nd Floor</option>
        </select>
        <div>
            <label>
                <input type="checkbox" name="near_elevator"> Prefer Near Elevator <!-- Elevator preference -->
            </label>
        </div>
        <div>
            <label>
                <input type="checkbox" name="has_view"> Prefer Room with a View <!-- View preference -->
            </label>
        </div>
        <button type="submit" name="action" value="book" <?= isset($_SESSION['assignedRoomId']) ? '' : 'disabled' ?>>Book Now</button> <!-- Booking button -->
    </form>
</div>
</body>
</html>
