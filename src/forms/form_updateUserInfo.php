<?php
session_start();

require_once '../models/User.php';
require_once '../resources/inc/db.php';
require_once '../func/header.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../../public/login.php"); // Redirect to login page if not authenticated
    exit;
}

$username = $_SESSION['username']; // Retrieve the username from the session

// Create database connection and initialize user model
$db = new Database();
$conn = $db->getConnection();
$userModel = new User($conn);

// Fetch user data
$userData = $userModel->getUser($username);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure all required fields are filled
    $requiredFields = ['firstName', 'lastName', 'email', 'phone', 'address', 'postalCode'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            echo "<p>Feil: Feltet {$field} må fylles ut.</p>";
            exit;
        }
    }

    // Validate input fields
    $errors = [];
    if (strlen($_POST['username']) < 4) {
        $errors[] = "Brukernavnet må være minst 4 tegn.";
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Ugyldig e-postadresse.";
    }
    if (!empty($_POST['password']) && strlen($_POST['password']) < 6) {
        $errors[] = "Passordet må være minst 6 tegn.";
    }

    if ($errors) {
        foreach ($errors as $error) {
            echo "<p>Feil: {$error}</p>";
        }
        exit;
    }

    try {
        // Hash the password if provided
        $hashedPassword = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

        // Update the user data
        $userModel->updateUser(
            $userData['id'],         // User ID
            $_POST['username'],      // New username
            $_POST['firstName'],     // First name
            $_POST['lastName'],      // Last name
            $_POST['email'],         // Email address
            $_POST['phone'],         // Phone number
            $_POST['address'],       // Address
            $_POST['postalCode'],    // Postal code
            $hashedPassword          // New password (optional)
        );

        // Update session username if changed
        $_SESSION['username'] = $_POST['username'];

        // Success message
        echo "<p>Informasjonen ble oppdatert!</p>";

        // Refresh user data to reflect changes
        $userData = $userModel->getUser($_POST['username']);
    } catch (Exception $e) {
        echo "<p>Feil ved oppdatering: {$e->getMessage()}</p>"; // Display error if update fails
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Rediger Profil</title> <!-- Page title -->
    <link rel="stylesheet" href="../../public/assets/css/style.css"> <!-- Link to stylesheet -->
</head>
<body>
<div class="user-container">
    <h1>Rediger Profil</h1> <!-- Page header -->

    <!-- Form for updating user profile -->
    <form method="post">
        <label for="username">Brukernavn:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($userData['username']); ?>" required>

        <label for="password">Nytt Passord (la stå tomt hvis du ikke vil endre):</label>
        <input type="password" id="password" name="password">

        <label for="firstName">Fornavn:</label>
        <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($userData['firstName']); ?>" required>

        <label for="lastName">Etternavn:</label>
        <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($userData['lastName']); ?>" required>

        <label for="email">E-post:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>

        <label for="phone">Telefon:</label>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($userData['phone']); ?>" required>

        <label for="address">Adresse:</label>
        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($userData['address']); ?>" required>

        <label for="postalCode">Postnummer:</label>
        <input type="text" id="postalCode" name="postalCode" value="<?php echo htmlspecialchars($userData['postalCode']); ?>" required>

        <button type="submit">Oppdater informasjon</button> <!-- Submit button -->
    </form>

    <br>
    <a href="../../public/logout.php">Logg ut her</a> <!-- Logout link -->
</div>
</body>
</html>
