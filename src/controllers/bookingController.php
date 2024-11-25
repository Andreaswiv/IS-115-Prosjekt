<?php
include '../../src/assets/inc/setupdb/setup.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $roomType = $_POST['room_type'];
    $roomView = $_POST['room_view'];
    $floor = $_POST['floor'];
    $nearElevator = $_POST['near_elevator'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    try {
        // Insert booking
        $stmt = $conn->prepare("
            INSERT INTO bookings (user_id, room_type, floor, near_elevator, has_view, start_date, end_date) 
            VALUES (:userId, :roomType, :floor, :nearElevator, :hasView, :startDate, :endDate)
        ");
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':roomType', $roomType);
        $stmt->bindParam(':floor', $floor);
        $stmt->bindParam(':nearElevator', $nearElevator, PDO::PARAM_BOOL);
        $stmt->bindValue(':hasView', $roomView === 'With view', PDO::PARAM_BOOL);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->execute();

        // Insert preferences
        $preferences = [$roomType, $roomView, "Floor $floor", $nearElevator === 'Yes' ? 'Near Elevator' : 'Far from Elevator'];
        foreach ($preferences as $preference) {
            $stmt = $conn->prepare("
                INSERT INTO preferences (user_id, preference_type, preference_value) 
                VALUES (:userId, 'room_preference', :preferenceValue)
            ");
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':preferenceValue', $preference);
            $stmt->execute();
        }

        echo "Booking and preferences saved successfully!";
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
