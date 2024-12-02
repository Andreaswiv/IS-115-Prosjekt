<?php
include '../../src/resources/inc/setupdb/setup.php';
include '../../src/resources/inc/functions.php';
require_once '../../src/func/security.php';
require_once '../../src/func/header.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../public/login.php'); // Redirect to login page if not authenticated
    exit();
}

$userId = $_SESSION['user_id']; // Retrieve user ID from session

// Fetch all bookings for the logged-in user
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
$stmt->bindValue(':userId', $userId, PDO::PARAM_INT); // Bind the user ID to the query
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all results as an associative array
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dine Bookinger</title> <!-- Page title -->
    <link rel="stylesheet" href="../../public/assets/css/style.css?v1.0.2"> <!-- Link to stylesheet -->
</head>
<body>
<div class="container_roomOverview-two">
    <h1>Dine Bookinger</h1> <!-- Header for the page -->

    <?php if (empty($bookings)): ?>
        <!-- Message displayed if no bookings are found -->
        <p class="no-bookings">
            Du har ingen registrerte bookinger. <br>
            <a href="form_newBooking.php" style="text-decoration: none; color: #007bff;">Gå til booking</a> for å komme i gang!
        </p>
    <?php else: ?>
        <!-- Display table of user bookings -->
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
                    <th>Faktura</th> <!-- Column for downloading invoice -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['id']); ?></td> <!-- Booking ID -->
                        <td><?php echo htmlspecialchars($booking['room_name']); ?></td> <!-- Room name -->
                        <td><?php echo htmlspecialchars($booking['room_type']); ?></td> <!-- Room type -->
                        <td><?php echo htmlspecialchars($booking['start_date']); ?></td> <!-- Start date -->
                        <td><?php echo htmlspecialchars($booking['end_date']); ?></td> <!-- End date -->
                        <td><?php echo htmlspecialchars($booking['floor']); ?></td> <!-- Floor number -->
                        <td><?php echo htmlspecialchars($booking['near_elevator']); ?></td> <!-- Proximity to elevator -->
                        <td><?php echo htmlspecialchars($booking['has_view']); ?></td> <!-- Whether room has a view -->
                        <td>
                            <!-- Link to download the invoice -->
                            <a href="../../src/func/fakturaGenerator.php?booking_id=<?php echo urlencode($booking['id']); ?>" 
                               style="text-decoration: none; color: #007bff;">Last ned</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
