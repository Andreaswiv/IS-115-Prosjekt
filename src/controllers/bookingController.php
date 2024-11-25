<?php
include '../../src/assets/inc/setupdb/setup.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture and sanitize POST data
    $userId = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
    $roomType = filter_input(INPUT_POST, 'room_type', FILTER_SANITIZE_STRING);
    $roomView = filter_input(INPUT_POST, 'room_view', FILTER_SANITIZE_STRING);
    $floor = filter_input(INPUT_POST, 'floor', FILTER_SANITIZE_NUMBER_INT);
    $nearElevator = filter_input(INPUT_POST, 'near_elevator', FILTER_SANITIZE_STRING);
    $startDate = filter_input(INPUT_POST, 'start_date', FILTER_SANITIZE_STRING);
    $endDate = filter_input(INPUT_POST, 'end_date', FILTER_SANITIZE_STRING);

    // Validate required fields
    if (!$userId || !$roomType || !$startDate || !$endDate || !$roomView || !$floor || !$nearElevator) {
        die("Error: All fields are required.");
    }

    // Validate date formats (expected format: YYYY-MM-DD)
    $startDateObject = DateTime::createFromFormat('Y-m-d', $startDate);
    $endDateObject = DateTime::createFromFormat('Y-m-d', $endDate);
    if (!$startDateObject || !$endDateObject) {
        die("Error: Invalid date format. Use YYYY-MM-DD.");
    }

    // Convert dates to the required format for storage or display
    $startDateFormatted = $startDateObject->format('Y-m-d'); // Stored in database as YYYY-MM-DD
    $endDateFormatted = $endDateObject->format('Y-m-d');     // Stored in database as YYYY-MM-DD

    // Validate date logic
    if ($startDateFormatted > $endDateFormatted) {
        die("Error: Start date cannot be later than end date.");
    }

    // Convert boolean-like values for database
    $nearElevatorBool = ($nearElevator === 'Yes') ? 1 : 0;
    $hasViewBool = (strtolower($roomView) === 'with view') ? 1 : 0;

    try {
        // Insert booking into the database
        $stmt = $conn->prepare("
            INSERT INTO bookings (user_id, room_type, floor, near_elevator, has_view, start_date, end_date) 
            VALUES (:userId, :roomType, :floor, :nearElevator, :hasView, :startDate, :endDate)
        ");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':roomType', $roomType, PDO::PARAM_STR);
        $stmt->bindParam(':floor', $floor, PDO::PARAM_INT);
        $stmt->bindParam(':nearElevator', $nearElevatorBool, PDO::PARAM_BOOL);
        $stmt->bindParam(':hasView', $hasViewBool, PDO::PARAM_BOOL);
        $stmt->bindParam(':startDate', $startDateFormatted, PDO::PARAM_STR);
        $stmt->bindParam(':endDate', $endDateFormatted, PDO::PARAM_STR);
        $stmt->execute();

        // Insert user preferences into the preferences table
        $preferences = [
            ['type' => 'room_type', 'value' => $roomType],
            ['type' => 'room_view', 'value' => $roomView],
            ['type' => 'floor', 'value' => "Floor $floor"],
            ['type' => 'near_elevator', 'value' => $nearElevator === 'Yes' ? 'Near Elevator' : 'Far from Elevator'],
        ];

        foreach ($preferences as $preference) {
            $stmt = $conn->prepare("
                INSERT INTO preferences (user_id, preference_type, preference_value) 
                VALUES (:userId, :preferenceType, :preferenceValue)
            ");
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':preferenceType', $preference['type'], PDO::PARAM_STR);
            $stmt->bindParam(':preferenceValue', $preference['value'], PDO::PARAM_STR);
            $stmt->execute();
        }

        // Success message
        echo "Booking and preferences saved successfully!";
    } catch (PDOException $e) {
        // Handle database errors gracefully
        error_log("Database Error: " . $e->getMessage());
        die("Error: Unable to process your booking. Please try again later.");
    }
}
