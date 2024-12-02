<?php
namespace models;
require_once 'src/resources/inc/db.php';
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
        $stmt->bindParam(':floor', $floor);
        $stmt->bindParam(':nearElevator', $nearElevator, PDO::PARAM_BOOL);
        $stmt->bindParam(':hasView', $hasView, PDO::PARAM_BOOL);
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>
