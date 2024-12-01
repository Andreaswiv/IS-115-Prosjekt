<?php
namespace models;
class Room{
    private $conn;
    private $table = 'rooms';

    public function __construct($db)
    {
        if ($db instanceof \PDO) { // Use \PDO here
            $this->conn = $db;
        } else {
            throw new \InvalidArgumentException("Invalid database connection.");
        }
    }
    public function getAllRooms()
    {
        $query = "SELECT * FROM rooms ORDER BY id ASC";
        $stmt = $this->conn->prepare($query); // Corrected property usage
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Hent et spesifikt rom ved ID
    public function getRoomById($id)
    {
        $query = "SELECT * FROM rooms WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Oppdater et rom
    public function updateRoom($id, $name, $room_type, $capacity, $is_available)
    {
        $query = "UPDATE rooms SET 
                    name = :name, 
                    room_type = :room_type, 
                    capacity = :capacity, 
                    is_available = :is_available, 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':room_type', $room_type, PDO::PARAM_STR);
        $stmt->bindParam(':capacity', $capacity, PDO::PARAM_INT);
        $stmt->bindParam(':is_available', $is_available, PDO::PARAM_BOOL);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Legg til et nytt rom
    public function addRoom($name, $room_type, $capacity)
    {
        $query = "INSERT INTO rooms (name, room_type, capacity, is_available) 
                  VALUES (:name, :room_type, :capacity, 1)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':room_type', $room_type, PDO::PARAM_STR);
        $stmt->bindParam(':capacity', $capacity, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Slett et rom
    public function deleteRoom($id)
    {
        $query = "DELETE FROM rooms WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
