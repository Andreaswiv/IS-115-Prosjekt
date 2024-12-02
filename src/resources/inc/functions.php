<?php
// functions.php

/**
 * Sanitize a variable by stripping tags, converting to HTML entities, and trimming whitespace.
 *
 * @param string $variable The input variable to sanitize.
 * @return string The sanitized variable.
 */
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
?>
