<?php
/*
namespace models;

require_once '../../src/resources/inc/db.php';
require_once '../func/security.php';

class Booking
{
    private $conn;
    private $table = "bookings";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createBooking($userId, $roomId, $roomType, $floor, $nearElevator, $hasView, $startDate, $endDate)
    {
        $query = "INSERT INTO " . $this->table . " (user_id, room_id, room_type, floor, near_elevator, has_view, start_date, end_date) 
              VALUES (:userId, :roomId, :roomType, :floor, :nearElevator, :hasView, :startDate, :endDate)";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':roomId', $roomId);
        $stmt->bindParam(':roomType', $roomType);
        $stmt->bindParam(':floor', $floor, $floor !== null ? \PDO::PARAM_INT : \PDO::PARAM_NULL);
        $stmt->bindParam(':nearElevator', $nearElevator, \PDO::PARAM_BOOL);
        $stmt->bindParam(':hasView', $hasView, \PDO::PARAM_BOOL);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);

        return $stmt->execute();
    }


    public function getUserBookings($userId)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :userId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC); // Use \PDO to specify global namespace
    }
}


?>
*/  ####################################### enda mere nytt tøys ########################################
/*
namespace models;

require_once '../../src/resources/inc/db.php';
require_once '../func/security.php';

class Booking
{
    private $conn;
    private $table = "bookings";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /*
     * Create a new booking.
     *//*
    public function createBooking($userId, $roomId, $roomType, $floor, $nearElevator, $hasView, $startDate, $endDate)
    {
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, room_id, room_type, floor, near_elevator, has_view, start_date, end_date) 
                  VALUES (:userId, :roomId, :roomType, :floor, :nearElevator, :hasView, :startDate, :endDate)";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':roomId', $roomId);
        $stmt->bindParam(':roomType', $roomType);
        $stmt->bindParam(':floor', $floor, $floor !== null ? \PDO::PARAM_INT : \PDO::PARAM_NULL);
        $stmt->bindParam(':nearElevator', $nearElevator, \PDO::PARAM_BOOL);
        $stmt->bindParam(':hasView', $hasView, \PDO::PARAM_BOOL);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);

        return $stmt->execute();
    }

    /**
     * Get all bookings for a specific user.
     *//*
    public function getUserBookings($userId)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :userId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get all bookings for a specific room.
     *//*
    public function getRoomBookings($roomId)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE room_id = :roomId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':roomId', $roomId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Check if a room is available within a specific date range.
     *//*
    public function isRoomAvailable($roomId, $startDate, $endDate)
    {
        $query = "SELECT COUNT(*) FROM " . $this->table . " 
                  WHERE room_id = :roomId 
                  AND (
                      (:startDate BETWEEN start_date AND end_date) 
                      OR (:endDate BETWEEN start_date AND end_date) 
                      OR (start_date BETWEEN :startDate AND :endDate) 
                      OR (end_date BETWEEN :startDate AND :endDate)
                  )";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':roomId', $roomId, \PDO::PARAM_INT);
        $stmt->bindParam(':startDate', $startDate, \PDO::PARAM_STR);
        $stmt->bindParam(':endDate', $endDate, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() == 0;
    }

    /**
     * Delete a booking by its ID.
     *//*
    public function deleteBooking($bookingId)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :bookingId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':bookingId', $bookingId, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Get a booking by its ID.
     *//*
    public function getBookingById($bookingId)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :bookingId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':bookingId', $bookingId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Get all bookings.
     *//*
    public function getAllBookings()
    {
        $query = "SELECT * FROM " . $this->table . " ORDER BY start_date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}


*/


// src/models/Booking.php

namespace models;

class Booking {
    private $conn;
    private $table = 'bookings';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Opprett en ny booking
    public function createBooking($user_id, $room_id, $room_type, $floor, $near_elevator, $has_view, $start_date, $end_date) {
        $query = "
            INSERT INTO " . $this->table . " 
            (user_id, room_id, room_type, floor, near_elevator, has_view, start_date, end_date) 
            VALUES 
            (:user_id, :room_id, :room_type, :floor, :near_elevator, :has_view, :start_date, :end_date)
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->bindParam(':room_id', $room_id, \PDO::PARAM_INT);
        $stmt->bindParam(':room_type', $room_type, \PDO::PARAM_STR);
        $stmt->bindParam(':floor', $floor, \PDO::PARAM_INT);
        $stmt->bindParam(':near_elevator', $near_elevator, \PDO::PARAM_BOOL);
        $stmt->bindParam(':has_view', $has_view, \PDO::PARAM_BOOL);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);

        if (!$stmt->execute()) {
            throw new \Exception("Feil ved opprettelse av booking: " . implode(", ", $stmt->errorInfo()));
        }
    }
}
?>
