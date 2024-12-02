<?php

namespace models;

class Room
{
    private $conn;
    private $table = 'rooms';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Hent tilgjengelige rom basert på romtype
    public function getAvailableRooms($roomType)
    {
        $query = "
            SELECT * FROM " . $this->table . "
            WHERE room_type = :room_type
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':room_type', $roomType, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countAvailableRooms($start_date, $end_date, $guest_count, $room_type) {
        $query = "
        SELECT COUNT(*) AS available_count
        FROM rooms r
        WHERE r.capacity >= :guest_count
          AND r.room_type = :room_type
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
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':guest_count', $guest_count, \PDO::PARAM_INT);
        $stmt->bindParam(':room_type', $room_type, \PDO::PARAM_STR);
        $stmt->bindParam(':start_date', $start_date, \PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $end_date, \PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['available_count'] ?? 0;
    }
    // Hent alle rom
    public function getAllRooms() {
        $query = "SELECT * FROM rooms ORDER BY id ASC";
        $stmt = $this->conn->prepare($query); // Bruker korrekt PDO-forbindelse
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC); // Bruk global \PDO for konstant
    }
################################################################################################################
/*
    public function countAvailableRoomsForPeriod($start_date, $end_date) {
        $query = "
        SELECT COUNT(*) AS available_count
        FROM rooms r
        WHERE NOT EXISTS (
            SELECT 1
            FROM bookings b
            WHERE b.room_id = r.id
              AND (
                  (:start_date BETWEEN b.start_date AND b.end_date)
                  OR (:end_date BETWEEN b.start_date AND b.end_date)
                  OR (b.start_date BETWEEN :start_date AND :end_date)
                  OR (b.end_date BETWEEN :start_date AND :end_date)
                  OR (b.start_date <= :start_date AND b.end_date >= :end_date)
              )
        )
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $start_date, \PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $end_date, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['available_count'] ?? 0;
    }
    

    */

    public function countAvailableRoomsForPeriod($start_date, $end_date) {
        $query = "
        SELECT COUNT(*) AS available_count
        FROM rooms r
        WHERE NOT EXISTS (
            SELECT 1
            FROM bookings b
            WHERE b.room_id = r.id
              AND b.start_date < :end_date
              AND b.end_date > :start_date
        )
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $start_date, \PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $end_date, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['available_count'] ?? 0;
    }


    

public function getAvailableRoomsForPeriod($start_date, $end_date) {
    $query = "
    SELECT *
    FROM rooms r
    WHERE NOT EXISTS (
        SELECT 1
        FROM bookings b
        WHERE b.room_id = r.id
          AND b.start_date < :end_date
          AND b.end_date > :start_date
    )
    ORDER BY r.id ASC
    ";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':start_date', $start_date, \PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $end_date, \PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

public function getOccupiedRoomsForPeriod($start_date, $end_date) {
    $query = "
    SELECT DISTINCT r.*
    FROM rooms r
    INNER JOIN bookings b ON b.room_id = r.id
    WHERE b.start_date < :end_date
      AND b.end_date > :start_date
    ORDER BY r.id ASC
    ";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':start_date', $start_date, \PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $end_date, \PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

    
################################################################################################################

}
?>
