<?php
require_once realpath(__DIR__ . '/../db.php');

$database = new Database();
$conn = $database->getConnection();

global $conn;

try {
    $conn->exec("CREATE DATABASE IF NOT EXISTS motell_booking");
    $conn->exec("USE motell_booking");

    $conn->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            firstName VARCHAR(100) NOT NULL,
            lastName VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            phone VARCHAR(20),
            address VARCHAR(255),
            postalCode VARCHAR(20),
            role ENUM('user', 'admin') DEFAULT 'user',
            registrationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    $conn->exec("
        CREATE TABLE IF NOT EXISTS rooms (
            id INT AUTO_INCREMENT PRIMARY KEY,
            room_name VARCHAR(10) NOT NULL,
            room_type ENUM('Single', 'Double', 'King Suite') NOT NULL
        );
    ");

    $conn->exec("
        CREATE TABLE IF NOT EXISTS bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            room_id INT NOT NULL,
            room_type VARCHAR(50) NOT NULL,
            floor INT NOT NULL,
            near_elevator BOOLEAN NOT NULL,
            has_view BOOLEAN NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
        );
    ");
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>