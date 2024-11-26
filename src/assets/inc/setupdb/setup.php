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
        postnummer VARCHAR(20),
        role ENUM('user', 'admin') DEFAULT 'user',
        birthDate DATE NULL,
        registrationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";
    $conn->exec($createUsersTable);

    // Create 'bookings' table if it doesn't exist
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

    // Insert initial users into the database if the table is empty
    $userCheck = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($userCheck == 0) {
        $userInsertData = [
            [
                'username' => 'admin',
                'password' => password_hash('adminpassword', PASSWORD_DEFAULT),
                'firstName' => 'Admin',
                'lastName' => 'User',
                'email' => 'admin@example.com',
                'phone' => '12345678',
                'address' => 'Admin Address',
                'postnummer' => '1234',
                'role' => 'admin',
                'birthDate' => null,
                'registrationDate' => '20-02-2024',
            ],
            [
                'username' => 'user1',
                'password' => password_hash('password1', PASSWORD_DEFAULT),
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '12345678',
                'address' => '123 Street',
                'postnummer' => '1000',
                'role' => 'user',
                'birthDate' => '01-01-1990',
                'registrationDate' => '12-01-2024',
            ],
            [
                'username' => 'user2',
                'password' => password_hash('password2', PASSWORD_DEFAULT),
                'firstName' => 'Jane',
                'lastName' => 'Doe',
                'email' => 'jane.doe@example.com',
                'phone' => '87654321',
                'address' => '456 Avenue',
                'postnummer' => '2000',
                'role' => 'user',
                'birthDate' => '02-02-1992',
                'registrationDate' => '02-04-2024',
            ],
            [
                'username' => 'user3',
                'password' => password_hash('password3', PASSWORD_DEFAULT),
                'firstName' => 'Alice',
                'lastName' => 'Smith',
                'email' => 'alice.smith@example.com',
                'phone' => '23456789',
                'address' => '789 Boulevard',
                'postnummer' => '3000',
                'role' => 'user',
                'birthDate' => '03-03-1994',
                'registrationDate' => '29-05-2024',
            ],
            [
                'username' => 'user4',
                'password' => password_hash('password4', PASSWORD_DEFAULT),
                'firstName' => 'Bob',
                'lastName' => 'Johnson',
                'email' => 'bob.johnson@example.com',
                'phone' => '34567890',
                'address' => '101 Highway',
                'postnummer' => '4000',
                'role' => 'user',
                'birthDate' => '04-04-1996',
                'registrationDate' => '20-03-2024',
            ],
            [
                'username' => 'user5',
                'password' => password_hash('password5', PASSWORD_DEFAULT),
                'firstName' => 'Charlie',
                'lastName' => 'Brown',
                'email' => 'charlie.brown@example.com',
                'phone' => '45678901',
                'address' => '202 Lane',
                'postnummer' => '5000',
                'role' => 'user',
                'birthDate' => '05-05-1998',
                'registrationDate' => '26-04-2024',
            ],
            [
                'username' => 'user6',
                'password' => password_hash('password6', PASSWORD_DEFAULT),
                'firstName' => 'Diana',
                'lastName' => 'Prince',
                'email' => 'diana.prince@example.com',
                'phone' => '56789012',
                'address' => '303 Street',
                'postnummer' => '6000',
                'role' => 'user',
                'birthDate' => '06-06-2000',
                'registrationDate' => '02-09-2024',
            ],
        ];

        foreach ($userInsertData as $userData) {
            $stmt = $conn->prepare("
                INSERT INTO users (username, password, firstName, lastName, email, phone, address, postnummer, role, birthDate, registrationDate)
                VALUES (:username, :password, :firstName, :lastName, :email, :phone, :address, :postnummer, :role, STR_TO_DATE(:birthDate, '%d-%m-%Y'), STR_TO_DATE(:registrationDate, '%d-%m-%Y'))
            ");
            $stmt->execute($userData);
        }
    }

    // Ensure users exist before inserting preferences
  $preferenceCheck = $conn->query("SELECT COUNT(*) FROM preferences")->fetchColumn();
    if ($preferenceCheck == 0) {
        $insertPreferences = "
        INSERT INTO preferences (user_id, preference_type, preference_value)
        VALUES
        (2, 'room_type', 'Double Room'),
        (2, 'room_type', 'Double Room'),
        (3, 'room_type', 'Single Room'),
        (4, 'room_type', 'King Suite'),
        (5, 'room_type', 'Double Room'),
        (6, 'room_type', 'Single Room'),
        (7, 'room_type', 'King Suite');
        ";
        $conn->exec($insertPreferences);
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
