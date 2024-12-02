<?php
require_once '../src/func/header.php';
require_once '../src/func/security.php';
require_once '../src/models/Room.php'; // Include the Room class
require_once '../src/resources/inc/setupdb/setup.php'; // Include your DB setup

use models\Room;

// Create Room instance
global $conn;
$roomModel = new Room($conn);

// Handle form submission
$start_date = null;
$end_date = null;
$adult_count = 1;
$child_count = 0;
$guest_count = 0;
$roomTypeCounts = [];
$roomTypes = ['Single', 'Double', 'King Suite'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $adult_count = (int) ($_POST['adult_count'] ?? 1);
    $child_count = (int) ($_POST['child_count'] ?? 0);
    $guest_count = $adult_count + $child_count;

    if ($start_date && $end_date && $guest_count > 0) {
        // Fetch the available room counts for each room type
        foreach ($roomTypes as $roomType) {
            $roomTypeCounts[$roomType] = $roomModel->countAvailableRooms($start_date, $end_date, $guest_count, $roomType);
        }
    }
}

if (isset($_SESSION['logout_message'])) {
    echo '<p style="color: green; text-align: center;">' . htmlspecialchars($_SESSION['logout_message']) . '</p>';
    unset($_SESSION['logout_message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['start_date'] = $start_date;
    $_SESSION['end_date'] = $end_date;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Search</title>
    <link rel="stylesheet" href="../public/assets/css/homePageStyle.css?v1.0.8">
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
<br>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <div class="room-container">
        <h2>Ledige Rom</h2>
        <div class="results">
            <?php if (!empty($roomTypeCounts)): ?>
                <?php foreach ($roomTypeCounts as $roomType => $count): ?>
                    <?php if ($count > 0): ?>
                        <?php
                        // Set the price based on the room type
                        $pricePerNight = match ($roomType) {
                            'Single' => 1000,
                            'Double' => 1500,
                            'King Suite' => 2500,
                            default => 0,
                        };
                        ?>
                        <div class="room-card-wrapper">
                            <a href="<?php
                            $roomUrl = match ($roomType) {
                                'Single' => '../src/forms/singleRoomBooking.php',
                                'Double' => '../src/forms/doubleRoomBooking.php',
                                'King Suite' => '../src/forms/kingSuiteBooking.php',
                                default => '../src/forms/defaultRoomBooking.php',
                            };
                            echo $roomUrl . '?' .
                                'room_type=' . urlencode($roomType) .
                                '&start_date=' . urlencode($start_date) .
                                '&end_date=' . urlencode($end_date) .
                                '&adult_count=' . urlencode($adult_count) .
                                '&child_count=' . urlencode($child_count);
                            ?>" class="room-link">
                                <div class="room-card">
                                    <img src="<?php
                                    echo match ($roomType) {
                                        'Single' => '../public/assets/img/single_room.JPG',
                                        'Double' => '../public/assets/img/double_room.jpg',
                                        'King Suite' => '../public/assets/img/king_suite.jpeg',
                                        default => '../public/assets/img/default_room.jpg',
                                    };
                                    ?>" alt="Room Image">
                                    <div class="room-info">
                                        <h3>Romtype: <?= htmlspecialchars($roomType) ?></h3>
                                        <p><?= htmlspecialchars($count) ?> rom ledig</p>
                                    </div>
                                    <div class="room-extra">
                                        <p>Pris: <strong><?= htmlspecialchars($pricePerNight) ?> NOK per natt</strong></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Ingen ledige rom for de valgte datoene og antall gjester.</p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
</body>
</html>
