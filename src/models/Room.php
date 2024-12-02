<?php
/*
namespace models;

class Room {
    private $conn;
    private $table = 'rooms';

    public function __construct($db) {
        if ($db instanceof \PDO) { // Bruk global \PDO
            $this->conn = $db;
        } else {
            throw new \InvalidArgumentException("Invalid database connection.");
        }
    }

    // Hent alle rom
    public function getAllRooms() {
        $query = "SELECT * FROM rooms ORDER BY id ASC";
        $stmt = $this->conn->prepare($query); // Bruker korrekt PDO-forbindelse
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC); // Bruk global \PDO for konstant
    }

    // Hent et spesifikt rom ved ID
    public function getRoomById($id) {
        $query = "SELECT * FROM rooms WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT); // Bruk global \PDO for konstant
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC); // Bruk global \PDO for konstant
    }

    // Oppdater et rom
    public function updateRoom($id, $name, $room_type, $capacity, $is_available) {
        $query = "UPDATE rooms SET 
                    name = :name, 
                    room_type = :room_type, 
                    capacity = :capacity, 
                    is_available = :is_available
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->bindParam(':room_type', $room_type, \PDO::PARAM_STR);
        $stmt->bindParam(':capacity', $capacity, \PDO::PARAM_INT);
        $stmt->bindParam(':is_available', $is_available, \PDO::PARAM_BOOL);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Legg til et nytt rom
    public function addRoom($name, $room_type, $capacity) {
        $query = "INSERT INTO rooms (name, room_type, capacity, is_available) 
                  VALUES (:name, :room_type, :capacity, 1)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->bindParam(':room_type', $room_type, \PDO::PARAM_STR);
        $stmt->bindParam(':capacity', $capacity, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Slett et rom
    public function deleteRoom($id) {
        $query = "DELETE FROM rooms WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }
}
*/
############################################################## NY GPT MED OPPDATERT DB-KOBLING ######################################################################
/*

namespace models;

class Room {
    private $conn;
    private $table = 'rooms';

    public function __construct($db) {
        if ($db instanceof \PDO) {
            $this->conn = $db;
        } else {
            throw new \InvalidArgumentException("Invalid database connection.");
        }
    }

    // Hent alle rom
    public function getAllRooms() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Hent et spesifikt rom ved ID
    public function getRoomById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Oppdater et rom
    public function updateRoom($id, $room_name, $room_type, $capacity, $is_available, $unavailable_start = null, $unavailable_end = null) {
        $query = "
            UPDATE " . $this->table . " 
            SET room_name = :room_name, 
                room_type = :room_type, 
                capacity = :capacity, 
                is_available = :is_available, 
                unavailable_start = :unavailable_start, 
                unavailable_end = :unavailable_end
            WHERE id = :id
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':room_name', $room_name, \PDO::PARAM_STR);
        $stmt->bindParam(':room_type', $room_type, \PDO::PARAM_STR);
        $stmt->bindParam(':capacity', $capacity, \PDO::PARAM_INT);
        $stmt->bindParam(':is_available', $is_available, \PDO::PARAM_BOOL);
        $stmt->bindParam(':unavailable_start', $unavailable_start, \PDO::PARAM_STR);
        $stmt->bindParam(':unavailable_end', $unavailable_end, \PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Legg til et nytt rom
    public function addRoom($room_name, $room_type, $capacity) {
        $query = "
            INSERT INTO " . $this->table . " (room_name, room_type, capacity, is_available) 
            VALUES (:room_name, :room_type, :capacity, 1)
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':room_name', $room_name, \PDO::PARAM_STR);
        $stmt->bindParam(':room_type', $room_type, \PDO::PARAM_STR);
        $stmt->bindParam(':capacity', $capacity, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Slett et rom
    public function deleteRoom($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Hent tilgjengelige rom basert på type og dato
    public function getAvailableRoomsByType($room_type, $start_date, $end_date) {
        $query = "
            SELECT * FROM " . $this->table . " 
            WHERE room_type = :room_type 
              AND is_available = 1 
              AND (
                  unavailable_start IS NULL OR unavailable_end IS NULL OR 
                  (:start_date NOT BETWEEN unavailable_start AND unavailable_end AND 
                   :end_date NOT BETWEEN unavailable_start AND unavailable_end)
              )
              AND id NOT IN (
                  SELECT room_id FROM bookings 
                  WHERE (:start_date BETWEEN start_date AND end_date)
                     OR (:end_date BETWEEN start_date AND end_date)
                     OR (start_date BETWEEN :start_date AND :end_date)
                     OR (end_date BETWEEN :start_date AND :end_date)
              )
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':room_type', $room_type, \PDO::PARAM_STR);
        $stmt->bindParam(':start_date', $start_date, \PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $end_date, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Sjekk om et rom er tilgjengelig for en gitt dato
    public function isRoomAvailable($room_id, $start_date, $end_date) {
        $query = "
            SELECT COUNT(*) as count FROM " . $this->table . " r
            WHERE r.id = :room_id 
              AND r.is_available = 1
              AND (
                  r.unavailable_start IS NULL OR r.unavailable_end IS NULL OR 
                  (:start_date NOT BETWEEN r.unavailable_start AND r.unavailable_end AND 
                   :end_date NOT BETWEEN r.unavailable_start AND r.unavailable_end)
              )
              AND NOT EXISTS (
                  SELECT 1 FROM bookings b
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
        $stmt->bindParam(':room_id', $room_id, \PDO::PARAM_INT);
        $stmt->bindParam(':start_date', $start_date, \PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $end_date, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}


*/
#################################################### NY GPT MED GAMMEL FUNKSJONALITET + NY DB-KONTAKT ##############################################################################


// src/models/Room.php
/*
namespace models;

class Room {
    private $conn;
    private $table = 'rooms';

    public function __construct($db) {
        if ($db instanceof \PDO) {
            $this->conn = $db;
        } else {
            throw new \InvalidArgumentException("Invalid database connection.");
        }
    }

    // Hent alle rom
    public function getAllRooms() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Hent et spesifikt rom ved ID
    public function getRoomById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Oppdater et rom
    public function updateRoom($id, $room_name, $room_type, $capacity, $is_available, $unavailable_start = null, $unavailable_end = null) {
        $query = "
            UPDATE " . $this->table . " 
            SET room_name = :room_name, 
                room_type = :room_type, 
                capacity = :capacity, 
                is_available = :is_available, 
                unavailable_start = :unavailable_start, 
                unavailable_end = :unavailable_end
            WHERE id = :id
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':room_name', $room_name, \PDO::PARAM_STR);
        $stmt->bindParam(':room_type', $room_type, \PDO::PARAM_STR);
        $stmt->bindParam(':capacity', $capacity, \PDO::PARAM_INT);
        $stmt->bindParam(':is_available', $is_available, \PDO::PARAM_INT);
        $stmt->bindParam(':unavailable_start', $unavailable_start);
        $stmt->bindParam(':unavailable_end', $unavailable_end);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Legg til et nytt rom
    public function addRoom($room_name, $room_type, $capacity) {
        $query = "
            INSERT INTO " . $this->table . " (room_name, room_type, capacity, is_available) 
            VALUES (:room_name, :room_type, :capacity, 1)
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':room_name', $room_name, \PDO::PARAM_STR);
        $stmt->bindParam(':room_type', $room_type, \PDO::PARAM_STR);
        $stmt->bindParam(':capacity', $capacity, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Slett et rom
    public function deleteRoom($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Hent tilgjengelige rom basert på type og dato
    public function getAvailableRoomsByType($room_type, $start_date, $end_date) {
        $query = "
            SELECT r.* FROM " . $this->table . " r
            WHERE r.room_type = :room_type
              AND r.is_available = 1
              AND r.id NOT IN (
                  SELECT b.room_id FROM bookings b
                  WHERE (
                      :start_date < b.end_date AND :end_date > b.start_date
                  )
              )
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':room_type', $room_type, \PDO::PARAM_STR);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Sjekk om et rom er tilgjengelig for en gitt dato
    public function isRoomAvailable($room_id, $start_date, $end_date) {
        $query = "
            SELECT COUNT(*) as count FROM " . $this->table . " r
            WHERE r.id = :room_id 
              AND r.is_available = 1
              AND NOT EXISTS (
                  SELECT 1 FROM bookings b
                  WHERE b.room_id = r.id 
                    AND (
                        :start_date < b.end_date AND :end_date > b.start_date
                    )
              )
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':room_id', $room_id, \PDO::PARAM_INT);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Søk etter tilgjengelige rom basert på datoer og antall gjester
    public function searchAvailableRooms($startDate, $endDate, $guests) {
        $roomTypes = ['Single Room', 'Double Room', 'King Suite'];
        $availableRooms = [];

        foreach ($roomTypes as $type) {
            $capacity = $this->getRoomCapacity($type);
            if ($capacity >= $guests) {
                $rooms = $this->getAvailableRoomsByType($type, $startDate, $endDate);
                if (!empty($rooms)) {
                    $availableRooms[$type] = $rooms;
                }
            }
        }

        return $availableRooms;
    }

    // Hent romkapasitet basert på romtype
    private function getRoomCapacity($roomType) {
        $capacities = [
            'Single Room' => 1,
            'Double Room' => 2,
            'King Suite' => 4,
        ];
        return $capacities[$roomType] ?? 0;
    }
}

*/
namespace models;

class Room {
    private $conn;
    private $table = 'rooms';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Hent tilgjengelige rom basert på romtype
    public function getAvailableRooms($roomType) {
        $query = "
            SELECT * FROM " . $this->table . "
            WHERE room_type = :room_type
              AND is_available = 1
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':room_type', $roomType, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>
