<?php
session_start();
require_once '../../src/resources/inc/setupdb/setup.php';
require_once '../func/security.php';
require_once '../../src/func/header.php';

ensureAdmin(); // Ensure that the user has admin privileges

// Check if room ID is provided
$roomId = isset($_GET['room_id']) ? intval($_GET['room_id']) : null;
if (!$roomId) {
    die("Feil: Rom-ID mangler."); // Terminate if room ID is missing
}

// Fetch room information from the database
$query = "SELECT * FROM rooms WHERE id = :roomId";
$stmt = $conn->prepare($query);
$stmt->bindValue(':roomId', $roomId, PDO::PARAM_INT);
$stmt->execute();
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    die("Feil: Rommet finnes ikke."); // Terminate if the room does not exist
}

// Fetch booking information for the specified room
$bookingQuery = "
    SELECT * FROM bookings
    WHERE room_id = :roomId
";
$bookingStmt = $conn->prepare($bookingQuery);
$bookingStmt->bindValue(':roomId', $roomId, PDO::PARAM_INT);
$bookingStmt->execute();
$bookings = $bookingStmt->fetchAll(PDO::FETCH_ASSOC);

$errors = []; // Array to collect error messages
$successMessage = null; // Variable for success message
$transactionStarted = false; // Track if a database transaction has started

// Handle room update when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $newRoomId = isset($_POST['new_room_id']) ? intval($_POST['new_room_id']) : null; // New room ID
        $roomName = htmlspecialchars($_POST['room_name']); // Sanitize room name input
        $startDate = $_POST['start_date'] ?: null; // Start date for blocking
        $endDate = $_POST['end_date'] ?: null; // End date for blocking

        if (!$newRoomId || !$roomName) {
            throw new Exception("Både nytt ID og romnavn må fylles ut."); // Validate input
        }

        if ($startDate && $endDate) {
            if ($startDate >= $endDate) {
                throw new Exception("Startdato må være før sluttdato."); // Validate date range
            }

            // Check availability for the specified date range
            $availabilityQuery = "
                SELECT 1
                FROM bookings
                WHERE room_id = :roomId
                  AND start_date < :end_date
                  AND end_date > :start_date
                LIMIT 1
            ";
            $availabilityStmt = $conn->prepare($availabilityQuery);
            $availabilityStmt->bindValue(':roomId', $roomId, PDO::PARAM_INT);
            $availabilityStmt->bindValue(':start_date', $startDate, PDO::PARAM_STR);
            $availabilityStmt->bindValue(':end_date', $endDate, PDO::PARAM_STR);
            $availabilityStmt->execute();

            if ($availabilityStmt->fetch()) {
                throw new Exception("Rommet er allerede booket i den valgte perioden."); // Check for overlapping bookings
            }
        }

        // Start a database transaction
        $conn->beginTransaction();
        $transactionStarted = true;

        // Update room ID and name
        $updateQuery = "
            UPDATE rooms
            SET id = :newRoomId,
                room_name = :roomName
            WHERE id = :roomId
        ";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bindValue(':newRoomId', $newRoomId, PDO::PARAM_INT);
        $stmt->bindValue(':roomName', $roomName, PDO::PARAM_STR);
        $stmt->bindValue(':roomId', $roomId, PDO::PARAM_INT);
        $stmt->execute();

        // If start and end dates are provided, add a booking to block the room
        if ($startDate && $endDate) {
            $addBookingQuery = "
                INSERT INTO bookings (user_id, room_id, room_type, floor, near_elevator, has_view, start_date, end_date)
                VALUES (1, :newRoomId, :roomType, 0, 0, 0, :start_date, :end_date)
            ";
            $addBookingStmt = $conn->prepare($addBookingQuery);
            $addBookingStmt->bindValue(':newRoomId', $newRoomId, PDO::PARAM_INT);
            $addBookingStmt->bindValue(':roomType', $room['room_type'], PDO::PARAM_STR);
            $addBookingStmt->bindValue(':start_date', $startDate, PDO::PARAM_STR);
            $addBookingStmt->bindValue(':end_date', $endDate, PDO::PARAM_STR);
            $addBookingStmt->execute();
        }

        // Commit the transaction
        $conn->commit();
        $transactionStarted = false;

        // Set success message and refresh room data
        $successMessage = "Rom oppdatert";

        // Fetch the updated room data
        $query = "SELECT * FROM rooms WHERE id = :newRoomId";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':newRoomId', $newRoomId, PDO::PARAM_INT);
        $stmt->execute();
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        // Update bookings for the new room ID
        $bookingQuery = "
            SELECT * FROM bookings
            WHERE room_id = :newRoomId
        ";
        $bookingStmt = $conn->prepare($bookingQuery);
        $bookingStmt->bindValue(':newRoomId', $newRoomId, PDO::PARAM_INT);
        $bookingStmt->execute();
        $bookings = $bookingStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        if ($transactionStarted) {
            $conn->rollBack(); // Rollback the transaction in case of an error
        }
        $errors[] = "Feil under oppdatering: " . $e->getMessage(); // Add error message to the list
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Rediger Rom</title> <!-- Page title -->
    <link rel="stylesheet" href="../../public/assets/css/style.css"> <!-- Link to CSS -->
</head>
<body>
<br>
<h1 style="color:black;">Rediger Rom</h1> <!-- Page header -->
<div class="container">
    <?php if ($successMessage): ?>
        <p style="color: green;"><?= htmlspecialchars($successMessage); ?></p> <!-- Display success message -->
    <?php endif; ?>
    <p>Rom-ID: <strong><?= htmlspecialchars($roomId); ?></strong></p> <!-- Display room ID -->
    <p>Romnavn: <strong><?= htmlspecialchars($room['room_name']); ?></strong></p> <!-- Display room name -->
    <?php if (!empty($errors)): ?>
        <ul style="color: red;"> <!-- Display errors if any -->
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- Form for updating room details -->
    <form action="" method="post">
        <label for="new_room_id">Nytt Rom-ID:</label>
        <input type="number" id="new_room_id" name="new_room_id" value="<?= htmlspecialchars($roomId); ?>" required>

        <label for="room_name">Romnavn:</label>
        <input type="text" id="room_name" name="room_name" value="<?= htmlspecialchars($room['room_name']); ?>" required>

        <label for="start_date">Blokker Fra:</label>
        <input type="date" id="start_date" name="start_date">

        <label for="end_date">Blokker Til:</label>
        <input type="date" id="end_date" name="end_date">

        <button type="submit">Oppdater Rom</button> <!-- Submit button -->
    </form>

    <h2>Bookinger for dette rommet</h2>
    <?php if (empty($bookings)): ?>
        <p>Ingen bookinger funnet for dette rommet.</p> <!-- Message if no bookings are found -->
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>Startdato</th> <!-- Start date column -->
                <th>Sluttdato</th> <!-- End date column -->
                <th>Romtype</th> <!-- Room type column -->
            </tr>
            </thead>
            <tbody>
            <?php foreach ($bookings as $booking): ?> <!-- Loop through each booking -->
                <tr>
                    <td><?= htmlspecialchars($booking['start_date']); ?></td> <!-- Display start date -->
                    <td><?= htmlspecialchars($booking['end_date']); ?></td> <!-- Display end date -->
                    <td><?= htmlspecialchars($booking['room_type']); ?></td> <!-- Display room type -->
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
