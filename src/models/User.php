<?php
require_once '../func/security.php';
class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Function to create a new user
    public function createUser($username, $firstName, $lastName, $email, $phone, $address, $postalCode, $password, $role) {
        $query = "
        INSERT INTO users (username, password, firstName, lastName, email, phone, address, postalCode, role)
        VALUES (:username, :password, :firstName, :lastName, :email, :phone, :address, :postalCode, :role)
    ";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':postalCode', $postalCode);
        $stmt->bindParam(':role', $role);

        if (!$stmt->execute()) {
            throw new Exception("Error creating user: " . implode(", ", $stmt->errorInfo()));
        }
    }

    // Function to retrieve a user by username
    public function getUser($username) {
        $query = "SELECT * FROM users WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
