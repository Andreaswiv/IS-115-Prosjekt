<?php


/*
include '../../src/resources/inc/setupdb/setup.php';
include '../../src/resources/inc/functions.php';
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

// Function to get a random available room ID
function getRandomAvailableRoomId($start_date, $end_date, $conn) {
    $query = "
        SELECT r.id
        FROM rooms r
        WHERE r.room_type = 'Single'
          AND (
              r.unavailable_start IS NULL 
              OR r.unavailable_end IS NULL
              OR (:start_date NOT BETWEEN r.unavailable_start AND r.unavailable_end
              AND :end_date NOT BETWEEN r.unavailable_start AND r.unavailable_end)
          )
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

// Retrieve parameters from URL
$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;

// Initialize assignedRoomId
$assignedRoomId = null;
if ($start_date && $end_date) {
    $assignedRoomId = getRandomAvailableRoomId($start_date, $end_date, $conn);
    $_SESSION['assignedRoomId'] = $assignedRoomId; // Store in session
    $_SESSION['start_date'] = $start_date;
    $_SESSION['end_date'] = $end_date;
}

// Handle form submission for booking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'book') {
        if ($_SESSION['assignedRoomId']) {
            $floor = $_POST['floor'] ?? null;
            $nearElevator = isset($_POST['near_elevator']) ? 1 : 0;
            $hasView = isset($_POST['has_view']) ? 1 : 0;

            $booking = new Booking($conn);
            $userId = $_SESSION['user_id'];
            $roomType = 'Single';
            $start_date = $_SESSION['start_date'];
            $end_date = $_SESSION['end_date'];

            $bookingCreated = $booking->createBooking(
                $userId,
                $_SESSION['assignedRoomId'],
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
*/

/* ################################################## NY CHATGPT SOM ENDRER DB-TILKOBLING ############################################################


require_once '../../src/resources/inc/db.php';
require_once '../../src/models/Room.php';
require_once '../../src/models/Booking.php';
require_once '../../src/func/security.php';
require_once '../../src/func/header.php';

use models\Room;
use models\Booking;

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$conn = $database->getConnection();

$room = new Room($conn);
$booking = new Booking($conn);

// Function to get a random available room ID
function getRandomAvailableRoomId($start_date, $end_date, $conn, $roomType = 'Single') {
    $query = "
        SELECT r.id
        FROM rooms r
        WHERE r.room_type = :roomType
          AND (
              r.unavailable_start IS NULL 
              OR r.unavailable_end IS NULL
              OR (:start_date NOT BETWEEN r.unavailable_start AND r.unavailable_end
              AND :end_date NOT BETWEEN r.unavailable_start AND r.unavailable_end)
          )
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
    $stmt->bindParam(':roomType', $roomType, PDO::PARAM_STR);
    $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchColumn();
}

// Retrieve parameters from URL
$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;

// Initialize assignedRoomId
$assignedRoomId = null;
if ($start_date && $end_date) {
    $assignedRoomId = getRandomAvailableRoomId($start_date, $end_date, $conn, 'Single');
    $_SESSION['assignedRoomId'] = $assignedRoomId; // Store in session
    $_SESSION['start_date'] = $start_date;
    $_SESSION['end_date'] = $end_date;
}

// Handle form submission for booking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'book') {
        if ($_SESSION['assignedRoomId']) {
            $floor = $_POST['floor'] ?? null;
            $nearElevator = isset($_POST['near_elevator']) ? 1 : 0;
            $hasView = isset($_POST['has_view']) ? 1 : 0;

            $userId = $_SESSION['user_id'];
            $roomType = 'Single';
            $start_date = $_SESSION['start_date'];
            $end_date = $_SESSION['end_date'];

            $bookingCreated = $booking->createBooking(
                $userId,
                $_SESSION['assignedRoomId'],
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
*/
###################################################### NY GPT SOM KOMBINERER GAMMEL FUNKSJONALITE MED NY DB-KOBLIONG ########################################

/*


require_once '../../src/resources/inc/db.php';
require_once '../../src/models/Room.php';
require_once '../../src/models/Booking.php';
require_once '../../src/func/security.php';
require_once '../../src/func/header.php';

use models\Room;
use models\Booking;

// Ensure the admin is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../public/login.php');
    exit();
}

$database = new Database();
$conn = $database->getConnection();
$room = new Room($conn);
$booking = new Booking($conn);

// Ensure the booking is attributed to the correct customer
$bookingUserId = $_SESSION['booking_user_id'] ?? null;
if (!$bookingUserId) {
    header('Location: form_allCustomersAdmin.php');
    exit();
}

// Initialize variables
$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;
$assignedRoomId = null;

// Function to get a random available room ID
function getRandomAvailableRoomId($start_date, $end_date, $conn, $roomType = 'Single') {
    $query = "
        SELECT r.id
        FROM rooms r
        WHERE r.room_type = :roomType
          AND r.is_available = 1
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
    $stmt->bindParam(':roomType', $roomType, PDO::PARAM_STR);
    $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchColumn();
}

// Fetch random room ID if dates are set
if ($start_date && $end_date) {
    $assignedRoomId = getRandomAvailableRoomId($start_date, $end_date, $conn, 'Single');
    $_SESSION['assignedRoomId'] = $assignedRoomId;
    $_SESSION['start_date'] = $start_date;
    $_SESSION['end_date'] = $end_date;
}

// Handle form submission for booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'book') {
    $floor = $_POST['floor'] ?? null;
    $nearElevator = isset($_POST['near_elevator']) ? 1 : 0;
    $hasView = isset($_POST['has_view']) ? 1 : 0;

    if ($_SESSION['assignedRoomId']) {
        $roomType = 'Single';
        $bookingCreated = $booking->createBooking(
            $bookingUserId,
            $_SESSION['assignedRoomId'],
            $roomType,
            $floor,
            $nearElevator,
            $hasView,
            $_SESSION['start_date'],
            $_SESSION['end_date']
        );

        if ($bookingCreated) {
            echo "<p>Booking successfully created!</p>";
            unset($_SESSION['assignedRoomId']);
        } else {
            echo "<p>Failed to create booking.</p>";
        }
    } else {
        echo "<p>No available room to book. Please search again.</p>";
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
<form method="GET" class="search-bar">
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
        <input type="hidden" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
        <input type="hidden" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
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
*/


// src/forms/form_singleRoomBooking.php

// Aktiver feilrapportering for debugging (fjern i produksjon)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../src/resources/inc/db.php';
require_once '../../src/models/Room.php';
require_once '../../src/models/Booking.php';
require_once '../../src/func/security.php';
require_once '../../src/func/header.php';

use models\Room;
use models\Booking;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

runSecurityChecks(); // Sikrer at brukeren er logget inn

$database = new Database();
$db = $database->getConnection();
$roomModel = new Room($db);

// Sjekk om innsjekk- og utsjekksdatoer er satt
if (empty($_SESSION['booking_check_in_date']) || empty($_SESSION['booking_check_out_date'])) {
    $_SESSION['error_message'] = 'Vennligst velg innsjekk- og utsjekksdatoer.';
    header('Location: form_newBooking.php');
    exit();
}

$checkInDate = $_SESSION['booking_check_in_date'];
$checkOutDate = $_SESSION['booking_check_out_date'];

// Hent tilgjengelige enkeltrom for de angitte datoene
$availableRooms = $roomModel->getAvailableRoomsByType('Single', $checkInDate, $checkOutDate);

if (empty($availableRooms)) {
    echo "<p>Ingen ledige enkeltrom for de valgte datoene.</p>";
    echo '<p><a href="form_newBooking.php">Tilbake til søk</a></p>';
    exit();
}

// For enkelhets skyld, velg det første tilgjengelige rommet
$selectedRoom = $availableRooms[0];

// Håndter booking når brukeren bekrefter
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Her kan du legge til validering for eventuelle ekstra felter i skjemaet

    // Lagre bookingen i databasen
    try {
        $bookingModel = new Booking($db);
        $bookingModel->createBooking(
            $_SESSION['booking_user_id'],
            $selectedRoom['id'],
            'Single', // Romtype
            $selectedRoom['floor'] ?? 1, // Etasje (hentet fra romdata eller standard)
            false,    // Nær heis (tilpass etter behov)
            false,    // Med utsikt (tilpass etter behov)
            $checkInDate,
            $checkOutDate
        );

        // Rydd opp i session-data
        unset($_SESSION['booking_check_in_date'], $_SESSION['booking_check_out_date']);

        echo "<p>Din booking er bekreftet!</p>";
        echo '<p><a href="../../public/index.php">Tilbake til forsiden</a></p>';
    } catch (Exception $e) {
        echo "<p>Feil ved opprettelse av booking: {$e->getMessage()}</p>";
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Book Enkeltrom</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
</head>
<body>
<div class="container">
    <h1>Book Enkeltrom</h1>
    <p>Rom: <?php echo htmlspecialchars($selectedRoom['room_name']); ?></p>
    <p>Innsjekk: <?php echo htmlspecialchars($checkInDate); ?></p>
    <p>Utsjekk: <?php echo htmlspecialchars($checkOutDate); ?></p>
    <form method="post">
        <!-- Legg til eventuelle ekstra felter her -->
        <button type="submit">Bekreft booking</button>
    </form>
</div>
</body>
</html>

