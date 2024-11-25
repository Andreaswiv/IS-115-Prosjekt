<?php
require_once '../../src/assets/inc/db.php';

class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createUser($username, $firstName, $lastName, $email, $phone, $address, $postalCode, $password, $role, $birthDate = null) {
        $query = "
        INSERT INTO users (username, firstName, lastName, email, phone, address, postnummer, password, role, birthDate)
        VALUES (:username, :firstName, :lastName, :email, :phone, :address, :postnummer, :password, :role, :birthDate)
    ";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':postnummer', $postalCode);
        $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':birthDate', $birthDate);

        if ($stmt->execute()) {
            return true;
        }

        throw new Exception("Failed to create user: " . implode(", ", $stmt->errorInfo()));
    }

    // Check if a user exists by username
    public function getUser($username)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
?>
