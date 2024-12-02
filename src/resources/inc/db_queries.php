<?php
// src/resources/inc/db_queries.php

function fetchPreferences($conn) {
    $stmt = $conn->prepare("SELECT DISTINCT preference_value FROM preferences");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
function fetchUsers($conn, $sortBy, $orderDir, $viewType, $groupBy) {
    if ($groupBy && $groupBy !== "none") { // Grouping logic
        $stmt = $conn->prepare("
            SELECT u.*, p.preference_value 
            FROM users u
            LEFT JOIN preferences p ON u.id = p.user_id
            WHERE p.preference_value = :groupBy
            ORDER BY $sortBy $orderDir
        ");
        $stmt->bindParam(':groupBy', $groupBy, PDO::PARAM_STR);
    } elseif ($viewType === 'lastMonth') { // Last month view logic
        $oneMonthAgo = date('Y-m-d H:i:s', strtotime('-1 month'));
        $stmt = $conn->prepare("
            SELECT u.*
            FROM users u
            WHERE u.registrationDate >= :oneMonthAgo
            ORDER BY $sortBy $orderDir
        ");
        $stmt->bindParam(':oneMonthAgo', $oneMonthAgo, PDO::PARAM_STR);
    } else { // Default: Show all users
        $stmt = $conn->prepare("SELECT u.* FROM users u ORDER BY $sortBy $orderDir");
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetchUserById($conn, $userId) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateUser($conn, $userData) {
    try {
        $stmt = $conn->prepare("
            UPDATE users 
            SET firstName = :firstName, 
                lastName = :lastName, 
                email = :email, 
                phone = :phone, 
                address = :address, 
                postalCode = :postalCode, 
                role = :role
            WHERE id = :id
        ");
        $stmt->execute($userData);
        return true;
    } catch (PDOException $e) {
        error_log("Update User Error: " . $e->getMessage());
        throw new Exception("Could not update user. Please try again later.");
    }
}
// Get a random available room ID based on date range and room type
function getRandomAvailableRoomId($start_date, $end_date, $room_type, $db) {
    $query = "
        SELECT r.id
        FROM rooms r
        WHERE r.room_type = :room_type
          AND NOT EXISTS (
                SELECT 1
                FROM bookings b
                WHERE b.room_id = r.id
                  AND (
                      (:start_date BETWEEN b.start_date AND b.end_date)
                      OR (:end_date BETWEEN b.start_date AND b.end_date)
                      OR (b.start_date BETWEEN :start_date AND :end_date)
                      OR (b.end_date BETWEEN :start_date AND :end_date)
                  )
              )
        LIMIT 1
    ";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
    $stmt->bindParam(':room_type', $room_type, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchColumn(); // Return the first available room ID
}

// Create a booking
function createBooking($userId, $roomId, $roomType, $floor, $nearElevator, $hasView, $start_date, $end_date, $db) {
    $query = "
        INSERT INTO bookings (user_id, room_id, room_type, floor, near_elevator, has_view, start_date, end_date)
        VALUES (:user_id, :room_id, :room_type, :floor, :near_elevator, :has_view, :start_date, :end_date)
    ";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':room_id', $roomId, PDO::PARAM_INT);
    $stmt->bindParam(':room_type', $roomType, PDO::PARAM_STR);
    $stmt->bindParam(':floor', $floor, PDO::PARAM_INT);
    $stmt->bindParam(':near_elevator', $nearElevator, PDO::PARAM_INT);
    $stmt->bindParam(':has_view', $hasView, PDO::PARAM_INT);
    $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);

    return $stmt->execute(); // Returns true if the query is successful
}

?>