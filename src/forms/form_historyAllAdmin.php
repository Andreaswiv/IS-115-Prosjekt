<?php
// Oversikt over alle bestillinger som er blitt gjort

include '../../src/resources/inc/setupdb/setup.php';
include '../../src/resources/inc/functions.php';
require_once '../../src/func/security.php';
require_once '../../src/func/header.php';

// Ensure the user is logged in and has admin privileges
ensureAdmin();

// Fetch all bookings from the database
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
    ORDER BY b.id DESC
";
$stmt = $conn->prepare($query);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC); // Retrieve all bookings as an associative array
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alle Bookinger</title> <!-- Page title -->
    <link rel="stylesheet" href="../../public/assets/css/style.css?v1.0.1"> <!-- Link to stylesheet -->
</head>
<body>
<div class="container_roomOverview">
    <h1>Alle Bookinger</h1> <!-- Page header -->

    <?php if (empty($bookings)): ?>
        <p>Det er ingen registrerte bookinger.</p> <!-- Message if no bookings are found -->
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>Booking-ID</th> <!-- Booking ID column -->
                <th>Brukernavn</th> <!-- Username column -->
                <th>Fornavn</th> <!-- First name column -->
                <th>Etternavn</th> <!-- Last name column -->
                <th>Romnummer</th> <!-- Room number column -->
                <th>Romtype</th> <!-- Room type column -->
                <th>Startdato</th> <!-- Start date column -->
                <th>Sluttdato</th> <!-- End date column -->
                <th>Etasje</th> <!-- Floor column -->
                <th>Nær Heis</th> <!-- Near elevator column -->
                <th>Utsikt</th> <!-- View column -->
            </tr>
            </thead>
            <tbody>
            <?php foreach ($bookings as $booking): ?> <!-- Iterate over each booking -->
                <tr>
                    <td><?php echo htmlspecialchars($booking['id']); ?></td> <!-- Display booking ID -->
                    <td><?php echo htmlspecialchars($booking['user_name']); ?></td> <!-- Display username -->
                    <td><?php echo htmlspecialchars($booking['first_name']); ?></td> <!-- Display first name -->
                    <td><?php echo htmlspecialchars($booking['last_name']); ?></td> <!-- Display last name -->
                    <td><?php echo htmlspecialchars($booking['room_name']); ?></td> <!-- Display room number -->
                    <td><?php echo htmlspecialchars($booking['room_type']); ?></td> <!-- Display room type -->
                    <td><?php echo htmlspecialchars($booking['start_date']); ?></td> <!-- Display start date -->
                    <td><?php echo htmlspecialchars($booking['end_date']); ?></td> <!-- Display end date -->
                    <td><?php echo htmlspecialchars($booking['floor']); ?></td> <!-- Display floor -->
                    <td><?php echo htmlspecialchars($booking['near_elevator']); ?></td> <!-- Display near elevator status -->
                    <td><?php echo htmlspecialchars($booking['has_view']); ?></td> <!-- Display view status -->
                </tr>
            <?php endforeach; ?> <!-- End of booking iteration -->
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
