<?php
session_start();
require_once '../models/User.php'; // Path to your User model
require_once '../assets/inc/db.php'; // Database connection
require_once '../../src/func/header.php';

// Sjekk om brukeren er logget inn
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Opprett databaseforbindelse
$db = new Database();
$conn = $db->getConnection();
$userModel = new User($conn);

// Hent brukerdata
$userData = $userModel->getUser($username);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure all required fields are provided
    $requiredFields = ['firstName', 'lastName', 'email', 'phone', 'address', 'postalCode'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            echo "<p>Feil: Feltet {$field} må fylles ut.</p>";
            exit;
        }
    }

    try {
        // Update the user using the correct `updateUser` method
        $userModel->updateUser(
            $userData['id'], // Use the correct ID from the fetched user data
            $_POST['firstName'],
            $_POST['lastName'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['address'],
            $_POST['postalCode'],
            $userData['role'] // Preserve the existing role
        );

        // Success message
        echo "<p>Informasjonen ble oppdatert!</p>";

        // Refresh user data to reflect changes in the form
        $userData = $userModel->getUser($username);
    } catch (Exception $e) {
        echo "<p>Feil ved oppdatering: {$e->getMessage()}</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Din profil</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
</head>
<body>
<h3>Hei <?php echo htmlspecialchars($userData['firstName']); ?>!</h3>
<p>Her kan du se og oppdatere din informasjon:</p>
<div class="container">
<form method="post">
    <label for="firstName">Fornavn:</label><br>
    <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($userData['firstName']); ?>" required><br><br>

    <label for="lastName">Etternavn:</label><br>
    <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($userData['lastName']); ?>" required><br><br>

    <label for="email">E-post:</label><br>
    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required><br><br>

    <label for="phone">Telefon:</label><br>
    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($userData['phone']); ?>" required><br><br>

    <label for="address">Adresse:</label><br>
    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($userData['address']); ?>" required><br><br>

    <label for="postalCode">Postnummer:</label><br>
    <input type="text" id="postalCode" name="postalCode" value="<?php echo htmlspecialchars($userData['postalCode']); ?>" required><br><br>

    <button type="submit">Oppdater informasjon</button>
</form>

<br>
<a href="../../public/logout.php">Logg ut her</a>
</div>
</body>
</html>
