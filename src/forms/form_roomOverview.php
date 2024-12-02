<?php
session_start();
include realpath('../../src/resources/inc/db.php');
include realpath('../../src/models/Room.php');
include realpath('../../src/func/security.php');
include realpath('../../src/func/header.php');

use models\Room;

// Sørg for at brukeren er en admin
ensureAdmin();

// Opprett databaseforbindelse
$database = new Database();
$db = $database->getConnection();

// Opprett en instans av Room-modellen
$roomModel = new Room($db);

// Hent alle rom
$rooms = $roomModel->getAllRooms();
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Romoversikt</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
</head>
<body>

    <div class="container_roomOverview">
        <h1>Romoversikt</h1>

        <?php if (empty($rooms)): ?>
            <p>Det er ingen registrerte rom i systemet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Room ID</th>
                        <th>Romnummer</th>
                        <th>Romtype</th>
                        <th>Kapasitet</th>
                        <th>Rediger</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rooms as $room): ?>
                        <tr>
                            <td><?= htmlspecialchars($room['id']) ?></td>
                            <td><?= htmlspecialchars($room['room_name']) ?></td>
                            <td><?= htmlspecialchars($room['room_type']) ?></td>
                            <td><?= htmlspecialchars($room['capacity']) ?></td>
                            <td>
                                <form action="form_editRoomAdmin.php" method="get" style="display: inline;">
                                    <input type="hidden" name="room_id" value="<?= htmlspecialchars($room['id']) ?>">
                                    <button type="submit">Rediger</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
