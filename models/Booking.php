<?php
require_once 'db.php';

class Booking {
    private $conn;
    private $table = "bookings";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createBooking($userId, $roomId, $startDate, $endDate) {
        $query = "INSERT INTO " . $this->table . " (user_id, room_id, start_date, end_date) VALUES (:userId, :roomId, :startDate, :endDate)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':roomId', $roomId);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        return $stmt->execute();
    }

    public function getUserBookings($userId) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :userId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
