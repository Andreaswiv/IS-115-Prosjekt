<?php
/*
include '../../src/resources/inc/setupdb/setup.php';
include '../../src/resources/inc/functions.php';
require_once '../../src/func/header.php';
require_once '../../src/func/security.php';



// Hent antall ledige rom
$availableSingleRooms = getAvailableRooms('Single Room', $conn);
$availableDoubleRooms = getAvailableRooms('Double Room', $conn);
$availableKingSuites = getAvailableRooms('King Suite', $conn);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Room</title>
    <link rel="stylesheet" href="../../public/assets/css/bookingStyle.css?v1.0.2">
</head>
<body>
<div class="button"><a href="./form_allCustomersAdmin.php">Tilbake til Kundeoversikt</a></div>
<div class="container">
    <h1>Book et rom</h1>
    <div class="room-cards">
        <a href="../forms/singleRoomBooking.php" class="room-card">
            <img src="../../public/assets/img/single_room.JPG" alt="Single Room">
            <div class="room-details">
                <h3>Single Room</h3>
                <ul>
                    <li>25 m²</li>
                    <li>Plass til 1 person</li>
                    <li>Wi-Fi inkludert</li>
                </ul>
                <p>Ledige rom: <?= htmlspecialchars($availableSingleRooms) ?></p>
            </div>
        </a>
        <a href="../forms/doubleRoomBooking.php" class="room-card">
            <img src="../../public/assets/img/double_room.jpg" alt="Double Room">
            <div class="room-details">
                <h3>Double Room</h3>
                <ul>
                    <li>30 m²</li>
                    <li>Plass til 2 personer</li>
                    <li>Wi-Fi inkludert</li>
                </ul>
                <p>Ledige rom: <?= htmlspecialchars($availableDoubleRooms) ?></p>
            </div>
        </a>
        <a href="../forms/kingSuiteBooking.php" class="room-card">
            <img src="../../public/assets/img/king_suite.jpeg" alt="King Suite">
            <div class="room-details">
                <h3>King Suite</h3>
                <ul>
                    <li>50 m²</li>
                    <li>Plass til 4 personer</li>
                    <li>Wi-Fi inkludert</li>
                </ul>
                <p>Ledige rom: <?= htmlspecialchars($availableKingSuites) ?></p>
            </div>
        </a>
    </div>
</div>

<script src="../../public/assets/js/bookingScript.js"></script>
</body>
</html>
*/
########################################################## CHATGPT MED NY DB-TILKOBLING ########################################################################################
/*
require_once '../../src/resources/inc/db.php';
require_once '../../src/models/Room.php';
include '../../src/resources/inc/setupdb/setup.php';
include '../../src/resources/inc/functions.php';
require_once '../../src/func/header.php';
require_once '../../src/func/security.php';

use models\Room;

$database = new Database();
$db = $database->getConnection();
$roomModel = new Room($db);

// Fetch available rooms for each type
$current_date = date('Y-m-d');
$availableSingleRooms = count($roomModel->getAvailableRoomsByType('Single', $current_date, $current_date));
$availableDoubleRooms = count($roomModel->getAvailableRoomsByType('Double', $current_date, $current_date));
$availableKingSuites = count($roomModel->getAvailableRoomsByType('King Suite', $current_date, $current_date));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Room</title>
    <link rel="stylesheet" href="../../public/assets/css/bookingStyle.css?v1.0.2">
</head>
<body>
<div class="button"><a href="./form_allCustomersAdmin.php">Tilbake til Kundeoversikt</a></div>
<div class="container">
    <h1>Book et rom</h1>
    <div class="room-cards">
        <a href="../forms/singleRoomBooking.php" class="room-card">
            <img src="../../public/assets/img/single_room.JPG" alt="Single Room">
            <div class="room-details">
                <h3>Single Room</h3>
                <p>Ledige rom: <?= htmlspecialchars($availableSingleRooms) ?></p>
            </div>
        </a>
        <a href="../forms/doubleRoomBooking.php" class="room-card">
            <img src="../../public/assets/img/double_room.jpg" alt="Double Room">
            <div class="room-details">
                <h3>Double Room</h3>
                <p>Ledige rom: <?= htmlspecialchars($availableDoubleRooms) ?></p>
            </div>
        </a>
        <a href="../forms/kingSuiteBooking.php" class="room-card">
            <img src="../../public/assets/img/king_suite.jpeg" alt="King Suite">
            <div class="room-details">
                <h3>King Suite</h3>
                <p>Ledige rom: <?= htmlspecialchars($availableKingSuites) ?></p>
            </div>
        </a>
    </div>
</div>
</body>
</html>
*/
############################################################# CHATGPT MED NY DB-TILKOBLIG + GAMMEL FUNKSJONALITET ##############################################################################################################
/*
require_once '../../src/resources/inc/db.php';
require_once '../../src/models/Room.php';
include '../../src/resources/inc/setupdb/setup.php';
include '../../src/resources/inc/functions.php';
require_once '../../src/func/header.php';
require_once '../../src/func/security.php';

use models\Room;



$database = new Database();
$db = $database->getConnection();
$roomModel = new Room($db);

// Fetch available rooms for each type
$current_date = date('Y-m-d');
$availableSingleRooms = count($roomModel->getAvailableRoomsByType('Single', $current_date, $current_date));
$availableDoubleRooms = count($roomModel->getAvailableRoomsByType('Double', $current_date, $current_date));
$availableKingSuites = count($roomModel->getAvailableRoomsByType('King Suite', $current_date, $current_date));

// Get customer data passed from form_allCustomersAdmin.php
$user_id = $_POST['user_id'] ?? null;
$firstName = $_POST['firstName'] ?? null;
$lastName = $_POST['lastName'] ?? null;

// Store the customer data in session for the booking process
$_SESSION['booking_user_id'] = $user_id;
$_SESSION['booking_firstName'] = $firstName;
$_SESSION['booking_lastName'] = $lastName;

// Redirect back if customer data is missing
if (!$user_id) {
    header('Location: form_allCustomersAdmin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book et Rom</title>
    <link rel="stylesheet" href="../../public/assets/css/bookingStyle.css?v1.0.2">
</head>
<body>
<div class="button"><a href="./form_allCustomersAdmin.php">Tilbake til Kundeoversikt</a></div>
<div class="container">
    <h1>Book et rom for <?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></h1>
    <div class="room-cards">
        <a href="../forms/singleRoomBooking.php" class="room-card">
            <img src="../../public/assets/img/single_room.JPG" alt="Single Room">
            <div class="room-details">
                <h3>Single Room</h3>
                <p>Ledige rom: <?= htmlspecialchars($availableSingleRooms) ?></p>
            </div>
        </a>
        <a href="../forms/doubleRoomBooking.php" class="room-card">
            <img src="../../public/assets/img/double_room.jpg" alt="Double Room">
            <div class="room-details">
                <h3>Double Room</h3>
                <p>Ledige rom: <?= htmlspecialchars($availableDoubleRooms) ?></p>
            </div>
        </a>
        <a href="../forms/kingSuiteBooking.php" class="room-card">
            <img src="../../public/assets/img/king_suite.jpeg" alt="King Suite">
            <div class="room-details">
                <h3>King Suite</h3>
                <p>Ledige rom: <?= htmlspecialchars($availableKingSuites) ?></p>
            </div>
        </a>
    </div>
</div>
</body>
</html>
*/

##################################### CHAT SOM KOMBINERER NY DB-TILKOBLING MED GAMMEL FUNKJSONALITET ############################################################





// src/forms/form_newBooking.php

require_once '../../src/resources/inc/db.php';        // Updated database connection
require_once '../../src/models/Room.php';             // Room model
require_once '../../src/func/header.php';             // Header (if needed)
require_once '../../src/func/security.php';           // Security functions

use models\Room;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Create database connection
$database = new Database();
$conn = $database->getConnection();

// Create Room model
$roomModel = new Room($conn);

// Fetch available rooms
try {
    $availableSingleRooms = count($roomModel->getAvailableRooms('Single Room'));
    $availableDoubleRooms = count($roomModel->getAvailableRooms('Double Room'));
    $availableKingSuites = count($roomModel->getAvailableRooms('King Suite'));
} catch (Exception $e) {
    // Handle any errors that occur during fetching
    $error_message = "Feil ved henting av ledige rom: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Book et Rom</title>
    <link rel="stylesheet" href="../../public/assets/css/bookingStyle.css?v1.0.2">
</head>
<body>
    <div class="button"><a href="./form_allCustomersAdmin.php">Tilbake til Kundeoversikt</a></div>
    <div class="container">
        <h1>Book et rom</h1>

        <?php if (isset($error_message)): ?>
            <p style="color:red;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Room</title>
    <link rel="stylesheet" href="../../public/assets/css/bookingStyle.css?v1.0.2">
</head>
<body>
<div class="button"><a href="./form_allCustomersAdmin.php">Tilbake til Kundeoversikt</a></div>
<div class="container">
    <h1>Book et rom</h1>
    <div class="room-cards">
        <a href="../forms/singleRoomBooking.php" class="room-card">
            <img src="../../public/assets/img/single_room.JPG" alt="Single Room">
            <div class="room-details">
                <h3>Single Room</h3>
                <ul>
                    <li>25 m²</li>
                    <li>Plass til 1 person</li>
                    <li>Wi-Fi inkludert</li>
                </ul>
                <p>Ledige rom: <?= htmlspecialchars($availableSingleRooms) ?></p>
            </div>
        </a>
        <a href="../forms/doubleRoomBooking.php" class="room-card">
            <img src="../../public/assets/img/double_room.jpg" alt="Double Room">
            <div class="room-details">
                <h3>Double Room</h3>
                <ul>
                    <li>30 m²</li>
                    <li>Plass til 2 personer</li>
                    <li>Wi-Fi inkludert</li>
                </ul>
                <p>Ledige rom: <?= htmlspecialchars($availableDoubleRooms) ?></p>
            </div>
        </a>
        <a href="../forms/kingSuiteBooking.php" class="room-card">
            <img src="../../public/assets/img/king_suite.jpeg" alt="King Suite">
            <div class="room-details">
                <h3>King Suite</h3>
                <ul>
                    <li>50 m²</li>
                    <li>Plass til 4 personer</li>
                    <li>Wi-Fi inkludert</li>
                </ul>
                <p>Ledige rom: <?= htmlspecialchars($availableKingSuites) ?></p>
            </div>
        </a>
    </div>
</div>

<script src="../../public/assets/js/bookingScript.js"></script>
</body>
</html>
<?php /*
        <div class="room-cards">
            <a href="../forms/singleRoomBooking.php" class="room-card">
                <img src="../../public/assets/img/single_room.JPG" alt="Single Room">
                <div class="room-details">
                    <h3>Single Room</h3>
                    <ul>
                        <li>25 m²</li>
                        <li>Plass til 1 person</li>
                        <li>Wi-Fi inkludert</li>
                    </ul>
                    <p>Ledige rom: <?= htmlspecialchars($availableSingleRooms) ?></p>
                </div>
            </a>
            <a href="../forms/doubleRoomBooking.php" class="room-card">
                <img src="../../public/assets/img/double_room.jpg" alt="Double Room">
                <div class="room-details">
                    <h3>Double Room</h3>
                    <ul>
                        <li>30 m²</li>
                        <li>Plass til 2 personer</li>
                        <li>Wi-Fi inkludert</li>
                    </ul>
                    <p>Ledige rom: <?= htmlspecialchars($availableDoubleRooms) ?></p>
                </div>
            </a>
            <a href="../forms/kingSuiteBooking.php" class="room-card">
                <img src="../../public/assets/img/king_suite.jpeg" alt="King Suite">
                <div class="room-details">
                    <h3>King Suite</h3>
                    <ul>
                        <li>50 m²</li>
                        <li>Plass til 4 personer</li>
                        <li>Wi-Fi inkludert</li>
                    </ul>
                    <p>Ledige rom: <?= htmlspecialchars($availableKingSuites) ?></p>
                </div>
            </a>
        </div>
    </div>

    <script src="../../public/assets/js/bookingScript.js"></script>
</body>
</html>









<div class="room-cards">
    <a href="../forms/singleRoomBooking.php" class="room-card">
        <img src="../../public/assets/img/single_room.JPG" alt="Single Room">
        <div class="room-details">
            <h3>Single Room</h3>
            <ul>
                <li>25 m²</li>
                <li>Plass til 1 person</li>
                <li>Wi-Fi inkludert</li>
            </ul>
            <p>Ledige rom: <?= htmlspecialchars($availableSingleRooms) ?></p>
        </div>
    </a>
    <a href="doubleRoomBooking.php" class="room-card">
        <img src="../../public/assets/img/double_room.jpg" alt="Double Room">
        <div class="room-details">
            <h3>Double Room</h3>
            <ul>
                <li>30 m²</li>
                <li>Plass til 2 personer</li>
                <li>Wi-Fi inkludert</li>
            </ul>
            <p>Ledige rom: <?= htmlspecialchars($availableDoubleRooms) ?></p>
        </div>
    </a>
    <a href="kingSuiteBooking.php" class="room-card">
        <img src="../../public/assets/img/king_suite.jpeg" alt="King Suite">
        <div class="room-details">
            <h3>King Suite</h3>
            <ul>
                <li>50 m²</li>
                <li>Plass til 4 personer</li>
                <li>Wi-Fi inkludert</li>
            </ul>
            <p>Ledige rom: <?= htmlspecialchars($availableKingSuites) ?></p>
        </div>
    </a>
    <script src="../../public/assets/js/bookingScript.js"></script>
</div>
</body>
</html>
*/
?>
