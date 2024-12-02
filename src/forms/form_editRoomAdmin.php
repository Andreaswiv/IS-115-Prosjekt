<?php
session_start();
require_once '../../src/resources/inc/setupdb/setup.php';
require_once '../func/security.php';
require_once '../../src/func/header.php';

ensureAdmin();

// Sjekk om rom-ID er sendt med
$roomId = isset($_GET['room_id']) ? intval($_GET['room_id']) : null;
if (!$roomId) {
    die("Feil: Rom-ID mangler.");
}

// Hent rominformasjon fra databasen
$query = "SELECT * FROM rooms WHERE id = :roomId";
$stmt = $conn->prepare($query);
$stmt->bindValue(':roomId', $roomId, PDO::PARAM_INT);
$stmt->execute();
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    die("Feil: Rommet finnes ikke.");
}

// Hent bookinginformasjon for dette rommet
$bookingQuery = "
    SELECT * FROM bookings
    WHERE room_id = :roomId
";
$bookingStmt = $conn->prepare($bookingQuery);
$bookingStmt->bindValue(':roomId', $roomId, PDO::PARAM_INT);
$bookingStmt->execute();
$bookings = $bookingStmt->fetchAll(PDO::FETCH_ASSOC);

// Håndter oppdatering av rommet når skjemaet sendes
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $roomName = htmlspecialchars($_POST['room_name']);
        $roomType = htmlspecialchars($_POST['room_type']);
        $capacity = intval($_POST['capacity']);
        $isAvailable = isset($_POST['is_available']) ? 1 : 0;
        $unavailableStart = $_POST['unavailable_start'] ?: null;
        $unavailableEnd = $_POST['unavailable_end'] ?: null;

        // Start en transaksjon
        $conn->beginTransaction();

        // Oppdater rommet i databasen
        $updateQuery = "
            UPDATE rooms
            SET room_name = :roomName,
                room_type = :roomType,
                capacity = :capacity,
                is_available = :isAvailable,
                unavailable_start = :unavailableStart,
                unavailable_end = :unavailableEnd
            WHERE id = :roomId
        ";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bindValue(':roomName', $roomName, PDO::PARAM_STR);
        $stmt->bindValue(':roomType', $roomType, PDO::PARAM_STR);
        $stmt->bindValue(':capacity', $capacity, PDO::PARAM_INT);
        $stmt->bindValue(':isAvailable', $isAvailable, PDO::PARAM_BOOL);
        $stmt->bindValue(':unavailableStart', $unavailableStart, PDO::PARAM_STR);
        $stmt->bindValue(':unavailableEnd', $unavailableEnd, PDO::PARAM_STR);
        $stmt->bindValue(':roomId', $roomId, PDO::PARAM_INT);
        $stmt->execute();

        // Hvis rommet settes som tilgjengelig, fjern relaterte bookinger
        if ($isAvailable) {
            $deleteBookingsQuery = "
                DELETE FROM bookings
                WHERE room_id = :roomId
                AND (
                    (start_date BETWEEN :start AND :end)
                    OR (end_date BETWEEN :start AND :end)
                )
            ";
            $deleteStmt = $conn->prepare($deleteBookingsQuery);
            $deleteStmt->bindValue(':roomId', $roomId, PDO::PARAM_INT);
            $deleteStmt->bindValue(':start', $unavailableStart ?? '1900-01-01', PDO::PARAM_STR);
            $deleteStmt->bindValue(':end', $unavailableEnd ?? '2100-12-31', PDO::PARAM_STR);
            $deleteStmt->execute();
        }

        // Fullfør transaksjonen
        $conn->commit();

        // Gå tilbake til romoversikten
        header("Location: form_roomOverview.php");
        exit();
    } catch (Exception $e) {
        // Rull tilbake ved feil
        $conn->rollBack();
        $errors[] = "Feil under oppdatering: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Rediger Rom</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
</head>
<body>
    <h1>Rediger Rom</h1>
    <div class="button"><a href="./form_roomOverview.php">Tilbake til Romoversikten</a></div>
    <div class= "container">
        <p>Rom-ID: <strong><?= htmlspecialchars($roomId); ?></strong></p>
        <?php if (!empty($errors)): ?>
            <ul style="color: red;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form action="" method="post">
            <label for="room_name">Romnavn:</label>
            <input type="text" id="room_name" name="room_name" value="<?= htmlspecialchars($room['room_name']); ?>" required>

            <label for="room_type">Romtype:</label>
            <input type="text" id="room_type" name="room_type" value="<?= htmlspecialchars($room['room_type']); ?>" required>

            <label for="capacity">Kapasitet:</label>
            <input type="number" id="capacity" name="capacity" value="<?= htmlspecialchars($room['capacity']); ?>" required>

            <label for="is_available">Tilgjengelig:</label>
            <input type="checkbox" id="is_available" name="is_available" <?= $room['is_available'] ? 'checked' : ''; ?>>

            <label for="unavailable_start">Utilgjengelig Fra:</label>
            <input type="datetime-local" id="unavailable_start" name="unavailable_start" 
                value="<?= htmlspecialchars($room['unavailable_start']); ?>">

            <label for="unavailable_end">Utilgjengelig Til:</label>
            <input type="datetime-local" id="unavailable_end" name="unavailable_end" 
                value="<?= htmlspecialchars($room['unavailable_end']); ?>">

            <button type="submit">Oppdater Rom</button>
        </form>

        <h2>Bookinger for dette rommet</h2>
        <?php if (empty($bookings)): ?>
            <p>Ingen bookinger funnet for dette rommet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Bruker-ID</th>
                        <th>Startdato</th>
                        <th>Sluttdato</th>
                        <th>Romtype</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?= htmlspecialchars($booking['user_id']); ?></td>
                            <td><?= htmlspecialchars($booking['start_date']); ?></td>
                            <td><?= htmlspecialchars($booking['end_date']); ?></td>
                            <td><?= htmlspecialchars($booking['room_type']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
