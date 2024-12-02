<?php
require_once '../../src/resources/inc/db.php';
require_once '../../src/models/Room.php';
require_once '../../src/models/Booking.php';
require_once '../../src/func/header.php';
require_once '../../src/func/security.php';

use models\Room;
use models\Booking;

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session if not already started
}

runSecurityChecks(); // Ensure user is authorized

$database = new Database();
$db = $database->getConnection();
$roomModel = new Room($db); // Initialize Room model
$bookingModel = new Booking($db); // Initialize Booking model

// Check if necessary session data is set
if (empty($_SESSION['booking_check_in_date']) || empty($_SESSION['booking_check_out_date'])) {
    $_SESSION['error_message'] = 'Vennligst velg innsjekk- og utsjekksdatoer.';
    header('Location: form_newBooking.php'); // Redirect back to booking form
    exit();
}

$checkInDate = $_SESSION['booking_check_in_date'];
$checkOutDate = $_SESSION['booking_check_out_date'];
$adults = $_SESSION['booking_adults'];
$children = $_SESSION['booking_children'];
$totalGuests = $adults + $children; // Calculate total number of guests
$user_id = $_SESSION['booking_user_id']; // Get user ID from session

// Get room type from URL
$roomType = $_GET['room_type'] ?? '';
if (empty($roomType)) {
    $_SESSION['error_message'] = 'Ingen romtype valgt.';
    header('Location: form_newBooking.php'); // Redirect if no room type is selected
    exit();
}

// Fetch available rooms for the selected type
$availableRooms = $roomModel->getAvailableRoomsByType($roomType, $checkInDate, $checkOutDate);

if (empty($availableRooms)) {
    echo "<p>Ingen ledige rom av typen $roomType for de valgte datoene.</p>";
    echo '<p><a href="form_newBooking.php">Tilbake til søk</a></p>';
    exit();
}

// Select the first available room
$selectedRoom = $availableRooms[0];

// Handle booking confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $floor = $_POST['floor'] ?? null;
    $nearElevator = isset($_POST['near_elevator']) ? 1 : 0;
    $hasView = isset($_POST['has_view']) ? 1 : 0;

    try {
        $bookingModel->createBooking(
            $user_id,
            $selectedRoom['id'],
            $roomType,
            $floor,
            $nearElevator,
            $hasView,
            $checkInDate,
            $checkOutDate
        );

        // Clear session data related to booking
        unset(
            $_SESSION['booking_check_in_date'],
            $_SESSION['booking_check_out_date'],
            $_SESSION['booking_adults'],
            $_SESSION['booking_children']
        );

        echo "<p>Din booking er bekreftet!</p>";
        echo '<p><a href="../../public/homePage.php">Tilbake til forsiden</a></p>';
    } catch (Exception $e) {
        echo "<p>Feil ved opprettelse av booking: {$e->getMessage()}</p>"; // Display error message
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Bekreft Booking - <?php echo htmlspecialchars($roomType); ?></title> <!-- Page title -->
    <link rel="stylesheet" href="../../public/assets/css/roomStyle.css"> <!-- Link to stylesheet -->
</head>
<body>
<div class="container">
    <h1>Bekreft Booking - <?php echo htmlspecialchars($roomType); ?></h1> <!-- Header for the page -->
    <div class="room-image">
        <!-- Display room image based on room type -->
        <img src="../../public/assets/img/<?php echo strtolower(str_replace(' ', '_', $roomType)); ?>.jpg" alt="<?php echo htmlspecialchars($roomType); ?>">
    </div>

    <div class="room-details">
        <h2><?php echo htmlspecialchars($selectedRoom['room_name']); ?></h2> <!-- Display room name -->
        <p><strong>Innsjekk:</strong> <?php echo htmlspecialchars($checkInDate); ?></p> <!-- Check-in date -->
        <p><strong>Utsjekk:</strong> <?php echo htmlspecialchars($checkOutDate); ?></p> <!-- Check-out date -->
        <p><strong>Antall gjester:</strong> <?php echo htmlspecialchars($totalGuests); ?></p> <!-- Total guests -->
    </div>

    <!-- Form for additional preferences -->
    <form method="POST">
        <div>
            <label for="floor">Etasje:</label>
            <select name="floor" id="floor">
                <option value="" selected>Hvilken som helst etasje</option>
                <option value="1">1. Etasje</option>
                <option value="2">2. Etasje</option>
            </select>
        </div>
        <div>
            <label>
                <input type="checkbox" name="near_elevator"> Nær en heis
            </label>
        </div>
        <div>
            <label>
                <input type="checkbox" name="has_view"> Rom med utsikt
            </label>
        </div>
        <button type="submit">Bekreft booking</button> <!-- Submit button -->
    </form>
</div>
</body>
</html>
