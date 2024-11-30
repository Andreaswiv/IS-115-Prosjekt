<?php
session_start();

include '../../src/assets/inc/db.php'; // Database connection
include '../../src/models/Room.php';   // Room model
include '../../src/func/security.php';
include '../../src/func/header.php';// Optional security functions
ensureAdmin();

// Create a database connection instance
$database = new Database();
$db = $database->getConnection();

// Create an instance of the Room model
$roomModel = new Room($db);

// Fetch all rooms
$rooms = $roomModel->getAllRooms();
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Romoversikt</title>
    <link rel="stylesheet" href="../../public/assets/css/overviewStyle.css">
</head>
<body>
<h1>Romoversikt</h1>
<p><a href="../AdminIndex.php">Tilbake til Admin Panel</a></p>

<?php if (empty($rooms)): ?>
    <p>Du har ingen rom.</p>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>Romnummer</th>
            <th>Navn</th>
            <th>Romtype</th>
            <th>Kapasitet</th>
            <th>Tilgjengelig</th>
            <th>Utilgjengelig Fra</th>
            <th>Utilgjengelig Til</th>
            <th>Rediger</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rooms as $room): ?>
            <tr>
                <td><?php echo htmlspecialchars($room['id']); ?></td>
                <td><?php echo htmlspecialchars($room['name']); ?></td>
                <td><?php echo htmlspecialchars($room['room_type']); ?></td>
                <td><?php echo htmlspecialchars($room['capacity']); ?></td>
                <td><?php echo $room['is_available'] ? 'Ja' : 'Nei'; ?></td>
                <td><?php echo htmlspecialchars($room['unavailable_start']) ?: '-'; ?></td>
                <td><?php echo htmlspecialchars($room['unavailable_end']) ?: '-'; ?></td>
                <td>
                    <form action="roomEdit.php" method="get" style="display: inline;">
                        <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room['id']); ?>">
                        <button type="submit">Rediger</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</body>
</html>
