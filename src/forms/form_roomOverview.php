<?php

session_start();

include realpath('../../src/resources/inc/db.php');
include realpath('../../src/models/Room.php');
include realpath('../../src/func/security.php');
include realpath('../../src/func/header.php');

use models\Room;

// Ensure the user is an admin
ensureAdmin();

// Retrieve dates from GET parameters, defaulting to today and tomorrow if not set
$start_date = $_GET['start_date'] ?? date('Y-m-d');
$end_date = $_GET['end_date'] ?? date('Y-m-d', strtotime('+1 day'));

// Validate date input
if ($start_date > $end_date) {
    $_SESSION['error_message'] = "Ankomstdato kan ikke være etter utreisedato.";
} elseif ($start_date == $end_date) {
    $_SESSION['error_message'] = "Man må minst reservere et helt døgn.";
}

// Create a database connection
$database = new Database();
$db = $database->getConnection();

// Initialize the Room model
$roomModel = new Room($db);

// Fetch available and occupied rooms for the selected period
if (!isset($_SESSION['error_message'])) {
    $availableRooms = $roomModel->getAvailableRoomsForPeriod($start_date, $end_date);
    $occupiedRooms = $roomModel->getOccupiedRoomsForPeriod($start_date, $end_date);
} else {
    $availableRooms = [];
    $occupiedRooms = [];
}

// Function to group rooms by type
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

// Group available and occupied rooms by type
$availableRoomsGrouped = groupRoomsByType($availableRooms);
$occupiedRoomsGrouped = groupRoomsByType($occupiedRooms);
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Romoversikt</title> <!-- Page title -->
    <link rel="stylesheet" href="../../public/assets/css/style.css?v1.0.1"> <!-- Main stylesheet -->
    <link rel="stylesheet" href="../../public/assets/css/roomStyle.css?v1.0.1"> <!-- Room-specific styles -->
</head>
<body>
    <!-- Form to change date range -->
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

    <!-- Error message -->
    <?php if (isset($_SESSION['error_message'])): ?>
        <p style="color: red; font-weight: bold; text-align: center;">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
        </p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="container_roomOverview">
        <h1>Romoversikt</h1> <!-- Page header -->

        <div class="rooms-overview-container">
            <!-- Available rooms -->
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
                                    <td><?= htmlspecialchars($room['id']) ?></td> <!-- Room ID -->
                                    <td><?= htmlspecialchars($room['room_name']) ?></td> <!-- Room number -->
                                    <td><?= htmlspecialchars($room['capacity']) ?></td> <!-- Room capacity -->
                                    <td>
                                        <form action="form_editRoomAdmin.php" method="get" style="display: inline;">
                                            <input type="hidden" name="room_id" value="<?= htmlspecialchars($room['id']); ?>">
                                            <button type="submit">Rediger</button> <!-- Edit button -->
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Occupied rooms -->
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
                                    <td><?= htmlspecialchars($room['id']) ?></td> <!-- Room ID -->
                                    <td><?= htmlspecialchars($room['room_name']) ?></td> <!-- Room number -->
                                    <td><?= htmlspecialchars($room['capacity']) ?></td> <!-- Room capacity -->
                                    <td>
                                        <form action="form_editRoomAdmin.php" method="get" style="display: inline;">
                                            <input type="hidden" name="room_id" value="<?= htmlspecialchars($room['id']); ?>">
                                            <button type="submit">Rediger</button> <!-- Edit button -->
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
