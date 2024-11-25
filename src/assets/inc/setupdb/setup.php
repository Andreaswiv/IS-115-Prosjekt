<?php
$host = 'localhost';
$username = 'root';
$password = '';
$db_name = 'motell_booking';

try {
    // Connect to MySQL server and select the database
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the database if it doesn't exist
    $conn->exec("CREATE DATABASE IF NOT EXISTS $db_name");
    $conn->exec("USE $db_name");

    // Create tables only if they don't exist
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
        postnummer VARCHAR(20),
        role VARCHAR(20) DEFAULT 'user',
        birthDate DATE NULL,
        registrationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
";
    $conn->exec($createUsersTable);

    $createBookingsTable = "
    CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        room_type VARCHAR(50) NOT NULL,
        floor INT NOT NULL,
        near_elevator BOOLEAN NOT NULL,
        has_view BOOLEAN NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );
";
    $conn->exec($createBookingsTable);

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

    // Insert initial data only if tables are empty
    $userCheck = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($userCheck == 0) {
        $insertUsers = "
        INSERT INTO users (username, password, firstName, lastName, email, phone, address, postnummer, role, birthDate, registrationDate)
        VALUES
        ('user1', 'password1', 'John', 'Doe', 'john.doe@example.com', '12345678', '123 Street', '1000', 'user', '1990-01-01', '2024-08-01'),
        ('user2', 'password2', 'Jane', 'Doe', 'jane.doe@example.com', '87654321', '456 Avenue', '2000', 'user', '1992-02-02', '2024-11-15'),
        ('user3', 'password3', 'Alice', 'Smith', 'alice.smith@example.com', '23456789', '789 Boulevard', '3000', 'user', '1994-03-03', '2024-10-20'),
        ('user4', 'password4', 'Bob', 'Johnson', 'bob.johnson@example.com', '34567890', '101 Highway', '4000', 'user', '1996-04-04', '2024-09-25'),
        ('user5', 'password5', 'Charlie', 'Brown', 'charlie.brown@example.com', '45678901', '202 Lane', '5000', 'user', '1998-05-05', '2024-11-10'),
        ('user6', 'password6', 'Diana', 'Prince', 'diana.prince@example.com', '56789012', '303 Street', '6000', 'user', '2000-06-06', '2024-12-05');
        ";
        $conn->exec($insertUsers);
    }

    $preferenceCheck = $conn->query("SELECT COUNT(*) FROM preferences")->fetchColumn();
    if ($preferenceCheck == 0) {
        $insertPreferences = "
        INSERT INTO preferences (user_id, preference_type, preference_value)
        VALUES
        (1, 'Room Preference', 'Double Room'),
        (2, 'Room Preference', 'Single Room'),
        (3, 'Room Preference', 'King Suite'),
        (4, 'Room Preference', 'Double Room'),
        (5, 'Room Preference', 'Single Room'),
        (6, 'Room Preference', 'King Suite');
        ";
        $conn->exec($insertPreferences);
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
