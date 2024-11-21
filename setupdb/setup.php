<?php
$host = 'localhost'; // EVENTUELT '127.0.0.1:3307' ??
$username = 'root';       // Update if different
$password = '';           // Update if different
$db_name = 'motell_booking';


// Connect to MySQL server
try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    ####### $conn = new PDO("mysql:unix_socket=/tmp/mysql.sock", $username, $password); ### ny fiks for spesifikk socket, funker ikke atm?

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the database if it doesn't exist
    $conn->exec("CREATE DATABASE IF NOT EXISTS $db_name");
    echo "Database '$db_name' created successfully or already exists.<br>";

    // Select the database
    $conn->exec("USE $db_name");

    // Create the `users` table ---------------- LA TIL FIRSTNAME OG LASTNAME
    $createUsersTable = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            firstName VARCHAR(100) NOT NULL,
            lastName VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            address VARCHAR(255) NOT NULL,
            postnummer VARCHAR(20) NOT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(20) DEFAULT 'user'
        );
    ";
    $conn->exec($createUsersTable);
    echo "Table 'users' created successfully or already exists.<br>";

    // Create the `bookings` table
    $createBookingsTable = "
        CREATE TABLE IF NOT EXISTS bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            room_id INT NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
    ";
    $conn->exec($createBookingsTable);
    echo "Table 'bookings' created successfully or already exists.<br>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn = null;
?>

