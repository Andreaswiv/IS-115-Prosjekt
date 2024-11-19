<?php
require_once 'db.php';
require_once './models/Booking.php';

$database = new Database();
$db = $database->getConnection();
$booking = new Booking($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $roomId = $_POST['room_id'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    if ($booking->createBooking($userId, $roomId, $startDate, $endDate)) {
        echo "Booking created successfully!";
    } else {
        echo "Failed to create booking.";
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];
    $userBookings = $booking->getUserBookings($userId);
    echo json_encode($userBookings);
}
?>
