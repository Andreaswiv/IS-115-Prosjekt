<?php

/*
require_once '../src/func/header.php';
require_once '../src/resources/inc/setupdb/setup.php';
global $conn; // Legg til dette i homePage.php etter inkluderingen av setup.php


echo "setup.php inkludert!";

if (!isset($conn) || $conn === null) {
    die("Database connection is not established in homePage.php.");
} else {
    echo "Database connection established in homePage.php.";
}



##########################################
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Home page fungerer!";
##########################################

// Function to search available rooms
function searchAvailableRooms($start_date, $end_date, $guest_count, $conn) {
    $query = "
        SELECT DISTINCT r.room_type, r.capacity
        FROM rooms r
        WHERE r.capacity >= :guest_count
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
        ORDER BY FIELD(r.room_type, 'Single', 'Double', 'King Suite')
    ";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':guest_count', $guest_count, PDO::PARAM_INT);
    $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle form submission
$availableRooms = [];
$start_date = null;
$end_date = null;
$adult_count = 1;
$child_count = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $adult_count = (int) ($_POST['adult_count'] ?? 1);
    $child_count = (int) ($_POST['child_count'] ?? 0);
    $guest_count = $adult_count + $child_count;

    if ($start_date && $end_date && $guest_count > 0) {
        $availableRooms = searchAvailableRooms($start_date, $end_date, $guest_count, $conn);
    }
}

var_dump($start_date, $end_date, $adult_count, $child_count); ##########################################

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Search</title>
    <link rel="stylesheet" href="../public/assets/css/homePageStyle.css?v1.0.6">
</head>
<body>
<h2>Ønsker du å bestille motell rom?</h2>
<h3>Se hva som er ledig her:</h3>
<div class="search-container">
    <form method="POST" action="">
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

        <button type="submit">Søk</button>
    </form>
</div>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <div class="room-container">
        <h2>Ledige Rom</h2>
        <div class="results">
            <?php if (!empty($availableRooms)): ?>
                <?php foreach ($availableRooms as $room): ?>
                    <a href="<?php
                    $roomUrl = match ($room['room_type']) {
                        'Single' => '../src/forms/singleRoomBooking.php',
                        'Double' => '../src/forms/doubleRoomBooking.php',
                        'King Suite' => '../src/forms/kingSuiteBooking.php',
                        default => '../src/forms/defaultRoomBooking.php',
                    };
                    echo $roomUrl . '?' .
                        'room_type=' . urlencode($room['room_type']) .
                        '&capacity=' . urlencode($room['capacity']) .
                        '&start_date=' . urlencode($start_date) .
                        '&end_date=' . urlencode($end_date) .
                        '&adult_count=' . urlencode($adult_count) .
                        '&child_count=' . urlencode($child_count);
                    ?>" class="room-link">
                        <div class="room-card">
                            <img src="<?php
                            echo match ($room['room_type']) {
                                'Single' => '../public/assets/img/single_room.JPG',
                                'Double' => '../public/assets/img/double_room.jpg',
                                'King Suite' => '../public/assets/img/king_suite.jpeg',
                                default => '../public/assets/img/default_room.jpg',
                            };
                            ?>" alt="Room Image">
                            <div class="room-info">
                                <h3>Romtype: <?= htmlspecialchars($room['room_type']) ?></h3>
                                <p>Kapasitet: <?= htmlspecialchars($room['capacity']) ?> personer</p>
                            </div>
                            <div class="room-extra">
                                <p>Pris: <strong>1234 NOK per natt</strong></p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Ingen ledige rom for de valgte datoene og antall gjester.</p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
</body>
</html>
*/



########################################## NY CHATGPT MED NY DB-TILKOBLING ########################################################

/*

// Inkluder header
require_once '../src/func/header.php';


// Inkluder setup.php for databaseforbindelse
require_once '../src/resources/inc/setupdb/setup.php';

// Sørg for at $conn er tilgjengelig globalt
global $conn;



// Funksjon for å søke ledige rom
function searchAvailableRooms($start_date, $end_date, $guest_count, $conn) {
    $query = "
        SELECT DISTINCT r.room_type, r.capacity
        FROM rooms r
        WHERE r.capacity >= :guest_count
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
        ORDER BY FIELD(r.room_type, 'Single', 'Double', 'King Suite')
    ";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':guest_count', $guest_count, PDO::PARAM_INT);
    $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);



    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Håndter forminnsending
$availableRooms = [];
$start_date = null;
$end_date = null;
$guest_count = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "Checkpoint 6: POST-forespørsel mottatt.<br>";

    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $guest_count = (int) ($_POST['guest_count'] ?? 1);

    if ($start_date && $end_date && $guest_count > 0) {
        $availableRooms = searchAvailableRooms($start_date, $end_date, $guest_count, $conn);
        echo "Checkpoint 7: Rom søkt. Resultater:<br>";
        var_dump($availableRooms);
    } else {
        echo "Checkpoint 7: Ugyldige parametere for romsøking.<br>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Search</title>
    <link rel="stylesheet" href="../public/assets/css/homePageStyle.css?v1.0.6">
</head>
<body>
<h2>Ønsker du å bestille motell rom?</h2>
<h3>Se hva som er ledig her:</h3>
<div class="search-container">
    <form method="POST" action="">
        <div class="input-group">
            <label for="start_date">Ankomst</label>
            <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" required>
        </div>

        <div class="input-group">
            <label for="end_date">Utreise</label>
            <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" required>
        </div>

        <div class="input-group">
            <label>Antall Gjester</label>
            <div class="guest-control">
                <button type="button" id="decreaseGuests">-</button>
                <input type="number" id="guest_count" name="guest_count" value="<?= htmlspecialchars($guest_count) ?>" min="1" readonly>
                <button type="button" id="increaseGuests">+</button>
            </div>
        </div>

        <button type="submit">Søk</button>
    </form>
</div>
<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <div class="room-container">
        <h2>Ledige Rom</h2>
        <div class="results">
            <?php if (!empty($availableRooms)): ?>
                <?php foreach ($availableRooms as $room): ?>
                    <div class="room-card">
                        <h3>Romtype: <?= htmlspecialchars($room['room_type']) ?></h3>
                        <p>Kapasitet: <?= htmlspecialchars($room['capacity']) ?> personer</p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Ingen ledige rom for de valgte datoene og antall gjester.</p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const decreaseButton = document.getElementById("decreaseGuests");
        const increaseButton = document.getElementById("increaseGuests");
        const guestInput = document.getElementById("guest_count");

        decreaseButton.addEventListener("click", () => {
            let currentValue = parseInt(guestInput.value);
            if (currentValue > parseInt(guestInput.min)) {
                guestInput.value = currentValue - 1;
            }
        });

        increaseButton.addEventListener("click", () => {
            let currentValue = parseInt(guestInput.value);
            guestInput.value = currentValue + 1;
        });
    });
</script>
</body>
</html>
 */

 ########################################## NY CHATGPT MED GAMLE FUNKSJONALITETER OG NY DB-TILKOBLING ########################################################

// Inkluder header
require_once '../src/func/header.php';

// Inkluder setup.php for databaseforbindelse
require_once '../src/resources/inc/setupdb/setup.php';

// Sørg for at $conn er tilgjengelig globalt
global $conn;

// Funksjon for å søke ledige rom
function searchAvailableRooms($start_date, $end_date, $guest_count, $conn) {
    $query = "
        SELECT DISTINCT r.room_type, r.capacity
        FROM rooms r
        WHERE r.capacity >= :guest_count
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
        ORDER BY FIELD(r.room_type, 'Single', 'Double', 'King Suite')
    ";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':guest_count', $guest_count, PDO::PARAM_INT);
    $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Håndter forminnsending
$availableRooms = [];
$start_date = null;
$end_date = null;
$adult_count = 1;
$child_count = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $adult_count = (int) ($_POST['adult_count'] ?? 1);
    $child_count = (int) ($_POST['child_count'] ?? 0);
    $guest_count = $adult_count + $child_count;

    if ($start_date && $end_date && $guest_count > 0) {
        $availableRooms = searchAvailableRooms($start_date, $end_date, $guest_count, $conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Search</title>
    <link rel="stylesheet" href="../public/assets/css/homePageStyle.css?v1.0.6">
</head>
<body>
<h2>Ønsker du å bestille motell rom?</h2>
<h3>Se hva som er ledig her:</h3>
<div class="search-container">
    <form method="POST" action="">
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

        <button type="submit">Søk</button>
    </form>
</div>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <div class="room-container">
        <h2>Ledige Rom</h2>
        <div class="results">
            <?php if (!empty($availableRooms)): ?>
                <?php foreach ($availableRooms as $room): ?>
                    <a href="<?php
                    $roomUrl = match ($room['room_type']) {
                        'Single' => '../src/forms/singleRoomBooking.php',
                        'Double' => '../src/forms/doubleRoomBooking.php',
                        'King Suite' => '../src/forms/kingSuiteBooking.php',
                        default => '../src/forms/defaultRoomBooking.php',
                    };
                    echo $roomUrl . '?' .
                        'room_type=' . urlencode($room['room_type']) .
                        '&capacity=' . urlencode($room['capacity']) .
                        '&start_date=' . urlencode($start_date) .
                        '&end_date=' . urlencode($end_date) .
                        '&adult_count=' . urlencode($adult_count) .
                        '&child_count=' . urlencode($child_count);
                    ?>" class="room-link">
                        <div class="room-card">
                            <img src="<?php
                            echo match ($room['room_type']) {
                                'Single' => '../public/assets/img/single_room.JPG',
                                'Double' => '../public/assets/img/double_room.jpg',
                                'King Suite' => '../public/assets/img/king_suite.jpeg',
                                default => '../public/assets/img/default_room.jpg',
                            };
                            ?>" alt="Room Image">
                            <div class="room-info">
                                <h3>Romtype: <?= htmlspecialchars($room['room_type']) ?></h3>
                                <p>Kapasitet: <?= htmlspecialchars($room['capacity']) ?> personer</p>
                            </div>
                            <div class="room-extra">
                                <p>Pris: <strong>1234 NOK per natt</strong></p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Ingen ledige rom for de valgte datoene og antall gjester.</p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
</body>
</html>
