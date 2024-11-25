<?php
include '../../src/assets/inc/setupdb/setup.php';
include '../../src/assets/inc/functions.php';
require_once '../func/security.php';
require_once '../../src/func/header.php';

$roomTypes = [
    'Double Room' => ['Room with view', 'Room without view'],
    'Single Room' => ['Room with view', 'Room without view'],
    'King Suite' => [],
];

$floors = [1, 2];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Room</title>
    <link rel="stylesheet" href="../../public/assets/css/table.css">
</head>
<body>
<div class="container">
    <h1>Book a Room</h1>

    <form name="newBooking" action="../controllers/bookingController.php" method="POST" class="booking-form">
        <label for="user_id">User ID *</label>
        <input type="text" id="user_id" name="user_id" placeholder="User ID" required>

        <label for="room_type">Room Type *</label>
        <select id="room_type" name="room_type" required>
            <option value="Double Room">Double Room</option>
            <option value="Single Room">Single Room</option>
            <option value="King Suite">King Suite</option>
        </select>

        <label for="room_view">View *</label>
        <select id="room_view" name="room_view" required>
            <option value="With view">With view</option>
            <option value="Without view">Without view</option>
        </select>

        <label for="floor">Floor *</label>
        <select id="floor" name="floor" required>
            <option value="1">1st Floor</option>
            <option value="2">2nd Floor</option>
        </select>

        <label for="near_elevator">Near Elevator *</label>
        <select id="near_elevator" name="near_elevator" required>
            <option value="Yes">Yes</option>
            <option value="No">No</option>
        </select>

        <label for="start_date">Start Date *</label>
        <input type="date" id="start_date" name="start_date" required>

        <label for="end_date">End Date *</label>
        <input type="date" id="end_date" name="end_date" required>

        <button type="submit" name="submitBooking">Book Room</button>
    </form>
</div>
</body>
</html>
