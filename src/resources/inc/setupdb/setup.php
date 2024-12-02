<?php
/*
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

    // Create 'users' table
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

    // Create 'rooms' table
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


    ####################################################################################  
    /* veldig sikker på at disse trengs for å løse 
    "En administrator må kunne navngi og beskrive rom og romtyper, samt gjøre enkelte rom utilgjengelige for perioder"
    *//*
    $alterRoomsTable = "
    ALTER TABLE rooms 
    ADD COLUMN IF NOT EXISTS unavailable_start DATETIME DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS unavailable_end DATETIME DEFAULT NULL;
";
$conn->exec($alterRoomsTable);
    ####################################################################################  

    // Create 'bookings' table
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

    // Create 'preferences' table
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

    // Insert initial data for rooms
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
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute($room);
        }
    }

    // Insert initial data for users
    $userCheck = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($userCheck == 0) {
        $userInsertData = [
            ['admin', password_hash('adminpassword', PASSWORD_DEFAULT), 'Admin', 'User', 'admin@example.com', '12345678', 'Admin Address', '1234', 'admin'],
            ['user1', password_hash('password1', PASSWORD_DEFAULT), 'John', 'Doe', 'john.doe@example.com', '12345678', '123 Street', '1000', 'user'],
            ['user2', password_hash('password2', PASSWORD_DEFAULT), 'Jane', 'Doe', 'jane.doe@example.com', '87654321', '456 Avenue', '2000', 'user'],
        ];

        foreach ($userInsertData as $user) {
            $stmt = $conn->prepare("
                INSERT INTO users (username, password, firstName, lastName, email, phone, address, postalCode, role)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute($user);
        }
    }

    // Insert preferences if empty
    $preferenceCheck = $conn->query("SELECT COUNT(*) FROM preferences")->fetchColumn();
    if ($preferenceCheck == 0) {
        $insertPreferences = "
            INSERT INTO preferences (user_id, preference_type, preference_value)
            VALUES
            (2, 'room_type', 'Double Room'),
            (3, 'room_type', 'Single Room')
        ";
        $conn->exec($insertPreferences);
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
*/





require_once realpath(__DIR__ . '/../db.php');


$database = new Database();
$conn = $database->getConnection();


global $conn; ############################## MENER DENNE KAN FJERNES ##############################



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