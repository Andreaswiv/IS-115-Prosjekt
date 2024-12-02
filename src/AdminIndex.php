<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../src/resources/inc/db.php';
require_once '../src/models/Room.php';
require_once '../src/func/header.php';
require_once '../src/func/security.php';
ensureAdmin();

use models\Room;

// Opprett databaseforbindelse og Room-modell
$database = new Database();
$db = $database->getConnection();
$roomModel = new Room($db);

// Hent alle rom og tell totalt antall rom
$rooms = $roomModel->getAllRooms();
$totalRooms = count($rooms);

// Hent tilgjengelige rom for alle romtyper for dagens dato
$current_date = date('Y-m-d');
$availableRooms = count(array_filter($rooms, fn($room) => $room['is_available'] === 1));

if (isset($_SESSION['error_message'])) {
    echo '<p style="color: red; font-weight: bold; text-align: center;">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
    unset($_SESSION['error_message']);
}



#################################################################################
/*
// Initialize variables
$start_date = date('Y-m-d');
$end_date = date('Y-m-d', strtotime('+1 day'));

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get start_date and end_date from POST
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

}   // Validate end_date is at least 24hr after start_date
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get start_date and end_date from POST
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
   
    // Validate dates
    if ($start_date > $end_date) {
        $_SESSION['error_message'] = "Ankomstdato kan ikke være etter utreisedato.";
    } elseif ($start_date == $end_date){
        $_SESSION['error_message'] = "Man må minst reservere et helt døgn.";
    }
        header("Location: AdminIndex.php");

    
}


// Fetch total number of rooms
$rooms = $roomModel->getAllRooms();
$totalRooms = count($rooms);

// Fetch available rooms for the specified date range
$availableRooms = $roomModel->countAvailableRoomsForPeriod($start_date, $end_date);

if (isset($_SESSION['error_message'])) {
    echo '<p style="color: red; font-weight: bold; text-align: center;">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
    unset($_SESSION['error_message']);
}
*/



if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../src/resources/inc/db.php';
require_once '../src/models/Room.php';
require_once '../src/func/header.php';
require_once '../src/func/security.php';
ensureAdmin();





// Opprett databaseforbindelse og Room-modell
$database = new Database();
$db = $database->getConnection();
$roomModel = new Room($db);

// Initialiser variabler
$start_date = date('Y-m-d');
$end_date = date('Y-m-d', strtotime('+1 day'));

// Sjekk om skjemaet er sendt inn
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Hent start_date og end_date fra POST
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Validering av datoer
    if ($start_date > $end_date) {
        $_SESSION['error_message'] = "Ankomstdato kan ikke være etter utreisedato.";
    } elseif ($start_date == $end_date){
        $_SESSION['error_message'] = "Man må minst reservere et helt døgn.";
    }
}

// Hent totalt antall rom
$rooms = $roomModel->getAllRooms();
$totalRooms = count($rooms);

// Hent antall ledige rom hvis det ikke er noen feil
if (!isset($_SESSION['error_message'])) {
    $availableRooms = $roomModel->countAvailableRoomsForPeriod($start_date, $end_date);
} else {
    $availableRooms = 'N/A';
}

if (isset($_SESSION['error_message'])) {
    echo '<p style="color: red; font-weight: bold; text-align: center;">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
    unset($_SESSION['error_message']);
}




#################################################################################






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
<h2>Motell-booking ADMIN</h2>
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
    <h2>Romoversikt</h2>
    <ul>
        <li>Totalt antall rom: <?php echo htmlspecialchars($totalRooms); ?></li>
        <li>Tilgjengelige rom for den valgte perioden er <?php echo htmlspecialchars($availableRooms); ?></li>
        <li><a href="forms/form_roomOverview.php?start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>" class="btn">Gå til Romoversikt</a></li>
    </ul>
</div>
</body>
</html>
