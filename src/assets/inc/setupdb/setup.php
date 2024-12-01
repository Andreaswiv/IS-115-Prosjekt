<?php
$host = 'localhost';
$username = 'root';
$password = '';
$db_name = 'motell_booking';

try {
    // Connect to MySQL server
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the database if it doesn't exist
    $conn->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");
    $conn->exec("USE `$db_name`");

    // Create 'users' table if it doesn't exist
    $createUsersTable = "
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
    ";
    $conn->exec($createUsersTable);

    // Create 'bookings' table if it doesn't exist
    $createBookingsTable = "
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
    ";
    $conn->exec($createBookingsTable);

    // Create 'preferences' table if it doesn't exist
    $createPreferencesTable = "
    CREATE TABLE IF NOT EXISTS preferences (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        preference_type VARCHAR(100) NOT NULL,
        preference_value VARCHAR(100) NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );
    ";
    $conn->exec($createPreferencesTable);

    // Create 'rooms' table if it doesn't exist
    $createRoomsTable = "
    CREATE TABLE IF NOT EXISTS rooms (
        id INT AUTO_INCREMENT PRIMARY KEY,
        room_name VARCHAR(10) NOT NULL,
        room_type ENUM('Single', 'Double', 'King Suite') NOT NULL,
        capacity INT NOT NULL,
        is_available BOOLEAN NOT NULL DEFAULT 1
    );
    ";
    $conn->exec($createRoomsTable);

    // Insert initial data for rooms if the table is empty
    $roomCheck = $conn->query("SELECT COUNT(*) FROM rooms")->fetchColumn();
    if ($roomCheck == 0) {
        $roomInsertData = [
            ['100', 'Single', 1, 1],
            ['101', 'Single', 1, 0],
            ['102', 'Single', 1, 1],
            ['103', 'Single', 1, 0],
            ['104', 'Single', 1, 1],
            ['105', 'Single', 1, 1],
            ['106', 'Single', 1, 0],
            ['107', 'Single', 1, 0],
            ['108', 'Double', 2, 1],
            ['109', 'Double', 2, 0],
            ['110', 'Double', 2, 1],
            ['111', 'Double', 2, 0],
            ['112', 'Double', 2, 1],
            ['113', 'Double', 2, 1],
            ['114', 'Double', 2, 1],
            ['115', 'Double', 2, 1],
            ['201', 'Double', 2, 1],
            ['202', 'Double', 2, 0],
            ['203', 'Double', 2, 0],
            ['204', 'King Suite', 5, 1],
            ['205', 'King Suite', 5, 0],
            ['206', 'King Suite', 5, 1],
            ['207', 'King Suite', 5, 1],
            ['208', 'King Suite', 5, 1],
            ['209', 'King Suite', 5, 1]
        ];

        foreach ($roomInsertData as $room) {
            $stmt = $conn->prepare("
                INSERT INTO rooms (room_name, room_type, capacity, is_available)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute($room);
        }
    }

    // Insert initial users into the database if the table is empty
    $userCheck = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($userCheck == 0) {
        $userInsertData = [
            ['admin', password_hash('adminpassword', PASSWORD_DEFAULT), 'Admin', 'User', 'admin@example.com', '12345678', 'Admin Address', '1234', 'admin'],
            ['user1', password_hash('password1', PASSWORD_DEFAULT), 'John', 'Doe', 'john.doe@example.com', '12345678', '123 Street', '1000', 'user'],
            ['user2', password_hash('password2', PASSWORD_DEFAULT), 'Jane', 'Doe', 'jane.doe@example.com', '87654321', '456 Avenue', '2000', 'user'],
            ['user3', password_hash('password3', PASSWORD_DEFAULT), 'Alice', 'Smith', 'alice.smith@example.com', '23456789', '789 Boulevard', '3000', 'user'],
            ['user4', password_hash('password4', PASSWORD_DEFAULT), 'Bob', 'Johnson', 'bob.johnson@example.com', '34567890', '101 Highway', '4000', 'user'],
            ['user5', password_hash('password5', PASSWORD_DEFAULT), 'Charlie', 'Brown', 'charlie.brown@example.com', '45678901', '202 Lane', '5000', 'user'],
            ['user6', password_hash('password6', PASSWORD_DEFAULT), 'Diana', 'Prince', 'diana.prince@example.com', '56789012', '303 Street', '6000', 'user']
        ];

        foreach ($userInsertData as $user) {
            $stmt = $conn->prepare("
                INSERT INTO users (username, password, firstName, lastName, email, phone, address, postalCode, role)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute($user);
        }
    }

    // Insert preferences if the table is empty
    $preferenceCheck = $conn->query("SELECT COUNT(*) FROM preferences")->fetchColumn();
    if ($preferenceCheck == 0) {
        $insertPreferences = "
            INSERT INTO preferences (user_id, preference_type, preference_value)
            VALUES
            (2, 'room_type', 'Double Room'),
            (2, 'floor', '1st Floor'),
            (2, 'near_elevator', 'Near Elevator'),
            (2, 'has_view', 'Without view'),
            (3, 'room_type', 'Single Room'),
            (3, 'floor', '1st Floor'),
            (3, 'near_elevator', 'Far from Elevator'),
            (3, 'has_view', 'With view'),
            (4, 'room_type', 'King Suite'),
            (4, 'floor', '2nd Floor'),
            (4, 'near_elevator', 'Near Elevator'),
            (4, 'has_view', 'With view'),
            (5, 'room_type', 'Double Room'),
            (5, 'floor', '2nd Floor'),
            (5, 'near_elevator', 'Far from Elevator'),
            (5, 'has_view', 'Without view'),
            (6, 'room_type', 'Single Room'),
            (6, 'floor', '1st Floor'),
            (6, 'near_elevator', 'Far from Elevator'),
            (6, 'has_view', 'With view'),
            (7, 'room_type', 'King Suite'),
            (7, 'floor', '2nd Floor'),
            (7, 'near_elevator', 'Near Elevator'),
            (7, 'has_view', 'With view');
        ";
        $conn->exec($insertPreferences);
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
