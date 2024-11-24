<?php
require_once '../../src/assets/inc/db.php';

class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createUser($username, $firstName, $lastName, $email, $phone, $address, $postnummer, $password, $role)
    {
        try {
            // Check if username already exists
            if ($this->getUser($username)) {
                throw new Exception("Username already exists");
            }

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert query
            $query = "INSERT INTO " . $this->table . " (username, firstName, lastName, email, phone, address, postnummer, password, role)
                  VALUES (:username, :firstName, :lastName, :email, :phone, :address, :postnummer, :password, :role)";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':firstName', $firstName);
            $stmt->bindParam(':lastName', $lastName);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':postnummer', $postnummer);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':role', $role);

            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Database error");
            }
        } catch (Exception $e) {
            throw $e;
        }
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
