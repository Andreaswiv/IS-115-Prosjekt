<?php
/*
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../src/func/header.php';
require_once '../src/func/security.php';
ensureAdmin();

require_once '../src/resources/inc/db.php';
require_once '../src/models/Room.php';
use models\Room; // Importer klassen fra namespace 'models'


$db = new PDO("mysql:host=localhost;dbname=motell_booking", "root", "");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$roomModel = new Room($db);

$rooms = $roomModel->getAllRooms();
$totalRooms = count($rooms);
$availableRooms = count(array_filter($rooms, fn($room) => $room['is_available'] === 1));

if (isset($_SESSION['error_message'])) {
    echo '<p style="color: red; font-weight: bold; text-align: center;">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
    unset($_SESSION['error_message']);
}
?>


<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motell Booking ADMIN</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
    <link rel="stylesheet" href="../public/assets/css/headerStyle.css">
</head>
<body>
<div class="container">
    <h2>Motell-booking ADMIN</h2>

    <!-- Eksisterende funksjoner -->
    <ul>
        <li><a target="_blank" href="forms/form_exisAdmin.php">Eksisterende bruker, ADMIN</a></li>
        <li><a target="_blank" href="forms/form_newAdmin.php">Ny bruker, ADMIN</a></li>
    </ul>
</div>
<div class= "container">
    <!-- Rask romoversikt -->
    <h2>Romoversikt</h2>
    <ul>
        <li>Totalt antall rom: <?php echo $totalRooms; ?></li>
        <li>Tilgjengelige rom for booking: <?php echo $availableRooms; ?></li>
        <li><a href="forms/form_roomOverview.php" class="btn">Gå til Romoversikt</a></li>
    </ul>
</div>
</body>
</html>

*/
################################ CHATGPT MED NY DB-TILKOBLING ################################################################
/*
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../src/resources/inc/db.php';
require_once '../src/models/Room.php';
require_once '../src/func/header.php';
require_once '../src/func/security.php';
ensureAdmin();

use models\Room;

$database = new Database();
$db = $database->getConnection();
$roomModel = new Room($db);

// Hent totalt antall rom
$rooms = $roomModel->getAllRooms();
$totalRooms = count($rooms);

// Hent ledige rom for alle romtyper for dagens dato
$current_date = date('Y-m-d');
$availableRooms = 0;

// Iterer gjennom alle romtyper
$roomTypes = ['Single', 'Double', 'King Suite'];
foreach ($roomTypes as $roomType) {
    $availableRooms += count($roomModel->getAvailableRoomsByType($roomType, $current_date, $current_date));
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motell Booking ADMIN</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>
<body>
<div class="container">
    <h2>Motell-booking ADMIN</h2>

    <ul>
        <li>Totalt antall rom: <?php echo $totalRooms; ?></li>
        <li>Tilgjengelige rom for booking: <?php echo $availableRooms; ?></li>
    </ul>
</div>
</body>
</html>
*/
################################################################ CHATGPT MED NY DB-TILKOBLING + GAMMEL FUNKSJONALITET ################################################

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
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motell Booking ADMIN</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>
<body>
<div class="container">
    <h2>Motell-booking ADMIN</h2>

    <!-- Funksjoner for administrasjon -->
    <ul>
        <li><a href="forms/form_exisAdmin.php" target="_blank">Eksisterende bruker, ADMIN</a></li>
        <li><a href="forms/form_newAdmin.php" target="_blank">Ny bruker, ADMIN</a></li>
    </ul>
</div>

<div class="container">
    <!-- Romoversikt -->
    <h2>Romoversikt</h2>
    <ul>
        <li>Totalt antall rom: <?php echo htmlspecialchars($totalRooms); ?></li>
        <li>Tilgjengelige rom for booking: <?php echo htmlspecialchars($availableRooms); ?></li>
        <li><a href="forms/form_roomOverview.php" class="btn">Gå til Romoversikt</a></li>
    </ul>
</div>
</body>
</html>
