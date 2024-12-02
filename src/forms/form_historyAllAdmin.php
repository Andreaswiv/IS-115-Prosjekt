<?php
// Oversikt over alle bestillinger som er blitt gjort

include '../../src/resources/inc/setupdb/setup.php';
include '../../src/resources/inc/functions.php';
require_once '../../src/func/security.php';
require_once '../../src/func/header.php';

// Sjekk at brukeren er logget inn og er admin
ensureAdmin();

// Hent alle bookinger fra databasen
$query = "
    SELECT b.id, u.username AS user_name, u.firstName AS first_name, u.lastName AS last_name, 
           r.room_name, b.room_type, b.start_date, b.end_date, b.floor, 
           CASE 
               WHEN b.near_elevator = 1 THEN 'Ja'
               ELSE 'Nei'
           END as near_elevator,
           CASE 
               WHEN b.has_view = 1 THEN 'Ja'
               ELSE 'Nei'
           END as has_view
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    JOIN users u ON b.user_id = u.id
    ORDER BY b.start_date DESC
";
$stmt = $conn->prepare($query);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alle Bookinger</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
</head>
<body>
<div class="container_roomOverview">
    <h1>Alle Bookinger</h1>

    <?php if (empty($bookings)): ?>
        <p>Det er ingen registrerte bookinger.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Booking-ID</th>
                    <th>Brukernavn</th>
                    <th>Fornavn</th>
                    <th>Etternavn</th>
                    <th>Romnummer</th>
                    <th>Romtype</th>
                    <th>Startdato</th>
                    <th>Sluttdato</th>
                    <th>Etasje</th>
                    <th>Nær Heis</th>
                    <th>Utsikt</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['id']); ?></td>
                        <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['room_type']); ?></td>
                        <td><?php echo htmlspecialchars($booking['start_date']); ?></td>
                        <td><?php echo htmlspecialchars($booking['end_date']); ?></td>
                        <td><?php echo htmlspecialchars($booking['floor']); ?></td>
                        <td><?php echo htmlspecialchars($booking['near_elevator']); ?></td>
                        <td><?php echo htmlspecialchars($booking['has_view']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
