<?php

function sanitize($variable)
{
    $variable = strip_tags($variable);
    $variable = htmlentities($variable);
    $variable = trim($variable);
    return $variable;
}

function errorHandling()
{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}



function getAvailableRooms($roomType, $conn) {
    $query = "SELECT COUNT(*) as available_rooms FROM rooms WHERE room_type = :room_type AND is_available = 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':room_type', $roomType, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['available_rooms'] ?? 0;
}
// src/resources/inc/helpers.php

function validateUserData($data) {
    $errors = [];
    if (empty($data[':firstName'])) $errors[] = "First name is required.";
    if (empty($data[':lastName'])) $errors[] = "Last name is required.";
    if (empty($data[':email']) || !filter_var($data[':email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (empty($data[':phone']) || strlen($data[':phone']) != 8) $errors[] = "Phone number must be 8 digits.";
    if (empty($data[':address'])) $errors[] = "Address is required.";
    if (empty($data[':postalCode']) || strlen($data[':postalCode']) != 4) $errors[] = "Postal code must be 4 digits.";
    return $errors;
}


?>
