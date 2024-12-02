<?php
include '../../src/resources/inc/setupdb/setup.php';
include '../../src/resources/inc/functions.php';
require_once '../../src/func/security.php';
require_once '../../src/func/header.php';

// Sjekk at brukeren er logget inn
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../public/login.php');
    exit();
}

// Hent brukerens ID fra sesjonen
$userId = $_SESSION['user_id'];

// Hent alle bestillinger for den innloggede brukeren
$query = "
    SELECT b.id, r.room_name, b.room_type, b.start_date, b.end_date, b.floor, 
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
    WHERE b.user_id = :userId
    ORDER BY b.start_date DESC
";
$stmt = $conn->prepare($query);
$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dine Bookinger</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
</head>
<body>
<div class="container_roomOverview">
    <h1>Dine Bookinger</h1>

    <?php if (empty($bookings)): ?>
        <p>Du har ingen registrerte bookinger.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Booking-ID</th>
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
