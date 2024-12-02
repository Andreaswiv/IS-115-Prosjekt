<?php

session_start();

include realpath('../../src/resources/inc/db.php');
include realpath('../../src/models/Room.php');
include realpath('../../src/func/security.php');
include realpath('../../src/func/header.php');

use models\Room;

// Sørg for at brukeren er en admin
ensureAdmin();

// Hent datoene fra GET-parametere, med fallback til dagens dato
$start_date = $_GET['start_date'] ?? date('Y-m-d');
$end_date = $_GET['end_date'] ?? date('Y-m-d', strtotime('+1 day'));

// Validering av datoer
if ($start_date > $end_date) {
    $_SESSION['error_message'] = "Ankomstdato kan ikke være etter utreisedato.";
} elseif ($start_date == $end_date){
    $_SESSION['error_message'] = "Man må minst reservere et helt døgn.";
}

// Opprett databaseforbindelse
$database = new Database();
$db = $database->getConnection();

// Opprett en instans av Room-modellen
$roomModel = new Room($db);

// Hent tilgjengelige og opptatte rom for perioden
if (!isset($_SESSION['error_message'])) {
    $availableRooms = $roomModel->getAvailableRoomsForPeriod($start_date, $end_date);
    $occupiedRooms = $roomModel->getOccupiedRoomsForPeriod($start_date, $end_date);
} else {
    $availableRooms = [];
    $occupiedRooms = [];
}

// Funksjon for å gruppere rom etter romtype (lagt til tidligere)
function groupRoomsByType($rooms) {
    $groupedRooms = [];
    foreach ($rooms as $room) {
        $roomType = $room['room_type'];
        if (!isset($groupedRooms[$roomType])) {
            $groupedRooms[$roomType] = [];
        }
        $groupedRooms[$roomType][] = $room;
    }
    return $groupedRooms;
}

// Gruppér rommene etter romtype
$availableRoomsGrouped = groupRoomsByType($availableRooms);
$occupiedRoomsGrouped = groupRoomsByType($occupiedRooms);
?>


<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Romoversikt</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css?v1.0.1">
    <link rel="stylesheet" href="../../public/assets/css/roomStyle.css?v1.0.1">
</head>
<body>
    <!-- Skjema for å endre datoer -->
    <div class="search-container">
        <form method="GET" class="search-form">
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

    <!-- Feilmelding -->
    <?php if (isset($_SESSION['error_message'])): ?>
        <p style="color: red; font-weight: bold; text-align: center;">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
        </p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="container_roomOverview">
        <h1>Romoversikt</h1>

        <div class="rooms-overview-container">
            <!-- Ledige rom -->
            <div class="room-section">
                <h2>Ledige rom</h2>
                <?php if (empty($availableRoomsGrouped)): ?>
                    <p>Ingen ledige rom i denne perioden.</p>
                <?php else: ?>
                    <?php foreach ($availableRoomsGrouped as $roomType => $rooms): ?>
                        <h3><?php echo htmlspecialchars($roomType); ?></h3>
                        <table>
                            <thead>
                            <tr>
                                <th>Room ID</th>
                                <th>Romnummer</th>
                                <th>Kapasitet</th>
                                <th>Rediger</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($rooms as $room): ?>
                                <tr>
                                    <td><?= htmlspecialchars($room['id']) ?></td>
                                    <td><?= htmlspecialchars($room['room_name']) ?></td>
                                    <td><?= htmlspecialchars($room['capacity']) ?></td>
                                    <td>
                                        <form action="form_editRoomAdmin.php" method="get" style="display: inline;">
                                            <input type="hidden" name="room_id" value="<?= htmlspecialchars($room['id']); ?>">
                                            <button type="submit">Rediger</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Opptatte rom -->
            <div class="room-section">
                <h2>Opptatte rom</h2>
                <?php if (empty($occupiedRoomsGrouped)): ?>
                    <p>Ingen opptatte rom i denne perioden.</p>
                <?php else: ?>
                    <?php foreach ($occupiedRoomsGrouped as $roomType => $rooms): ?>
                        <h3><?php echo htmlspecialchars($roomType); ?></h3>
                        <table>
                            <thead>
                            <tr>
                                <th>Room ID</th>
                                <th>Romnummer</th>
                                <th>Kapasitet</th>
                                <th>Rediger</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($rooms as $room): ?>
                                <tr>
                                    <td><?= htmlspecialchars($room['id']) ?></td>
                                    <td><?= htmlspecialchars($room['room_name']) ?></td>
                                    <td><?= htmlspecialchars($room['capacity']) ?></td>
                                    <td>
                                        <form action="form_editRoomAdmin.php" method="get" style="display: inline;">
                                            <input type="hidden" name="room_id" value="<?= htmlspecialchars($room['id']); ?>">
                                            <button type="submit">Rediger</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
