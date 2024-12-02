<?php
session_start();
require_once '../models/User.php';
require_once '../resources/inc/db.php';
require_once '../func/header.php';


// Sjekk om brukeren er logget inn
if (!isset($_SESSION['username'])) {
    header("Location: ../../public/login.php");
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

    // Validering
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
        // Hash passord hvis oppgitt
        if (!empty($_POST['password'])) {
            $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
        } else {
            $hashedPassword = null; // Indikerer at passordet ikke skal endres
        }
    
        // Oppdater brukeren med riktig rekkefølge på parametrene
        $userModel->updateUser(
            $userData['id'],         // $id
            $_POST['username'],      // $username
            $_POST['firstName'],     // $firstName
            $_POST['lastName'],      // $lastName
            $_POST['email'],         // $email
            $_POST['phone'],         // $phone
            $_POST['address'],       // $address
            $_POST['postalCode'],    // $postalCode
            $hashedPassword          // $password
        );
    
        // Oppdater sesjonsvariabler hvis brukernavn endret
        $_SESSION['username'] = $_POST['username'];
    
        // Suksessmelding
        echo "<p>Informasjonen ble oppdatert!</p>";
    
        // Oppdater brukerdata for å reflektere endringer i skjemaet
        $userData = $userModel->getUser($_POST['username']);
    } catch (Exception $e) {
        echo "<p>Feil ved oppdatering: {$e->getMessage()}</p>";
    }
    
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Rediger Profil</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
</head>
<body>
<div class="container">
    <h1>Rediger Profil</h1>
    <form method="post">
        <label for="username">Brukernavn:</label><br>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($userData['username']); ?>" required><br><br>

        <label for="password">Nytt Passord (la stå tomt hvis du ikke vil endre):</label><br>
        <input type="password" id="password" name="password"><br><br>

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
