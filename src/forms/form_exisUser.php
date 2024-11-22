<?php
// Include database setup
include '../../src/assets/inc/setupdb/setup.php';

// Fetch user data
try {
    $user_id = 1; // Replace with dynamic user ID logic if available
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        throw new Exception("Ingen bruker funnet med ID $user_id.");
    }

    // Populate user profile for the form
    $userProfile = [
        'firstName' => $user['firstName'],
        'lastName' => $user['lastName'],
        'email' => $user['email'],
        'phone' => $user['phone'],
        'address' => $user['address'],
        'postalCode' => $user['postnummer'],
    ];
} catch (Exception $e) {
    die("Feil: " . $e->getMessage());
}

// Function to sanitize user input
function sanitize($variable)
{
    return htmlspecialchars(trim($variable));
}

$errorMessages = []; // Array for storing error messages
$successMessage = ""; // Message for successful updates
$noChangesMessage = "Ingen endringer gjort."; // Message when no changes are made

// Assign initial values for the form
$firstName = $userProfile['firstName'];
$lastName = $userProfile['lastName'];
$email = $userProfile['email'];
$phone = $userProfile['phone'];
$address = $userProfile['address'];
$postalCode = $userProfile['postalCode'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = sanitize($_POST['firstName']);
    $lastName = sanitize($_POST['lastName']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $postalCode = sanitize($_POST['postalCode']);

    // Validate input fields
    if (empty($firstName)) {
        $errorMessages[] = "Fornavn kan ikke være tomt.";
    }
    if (empty($lastName)) {
        $errorMessages[] = "Etternavn kan ikke være tomt.";
    }
    if (empty($email)) {
        $errorMessages[] = "E-post kan ikke være tom.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessages[] = "Ugyldig e-postadresse.";
    }
    if (empty($phone)) {
        $errorMessages[] = "Telefonnummer kan ikke være tomt.";
    } elseif (strlen($phone) != 8) {
        $errorMessages[] = "Telefonnummer må være 8 sifre.";
    }
    if (empty($address)) {
        $errorMessages[] = "Adresse kan ikke være tom.";
    }
    if (empty($postalCode)) {
        $errorMessages[] = "Postnummer kan ikke være tomt.";
    } elseif (strlen($postalCode) != 4) {
        $errorMessages[] = "Postnummer må være 4 sifre.";
    }

    // Check for changes
    $changesMade = false;
    foreach (['firstName', 'lastName', 'email', 'phone', 'address', 'postalCode'] as $field) {
        if ($userProfile[$field] !== $$field) {
            $changesMade = true;
            break;
        }
    }

    // Proceed with updating if there are changes and no validation errors
    if (empty($errorMessages) && $changesMade) {
        try {
            $stmt = $conn->prepare("
                UPDATE users SET 
                    firstName = :firstName, 
                    lastName = :lastName, 
                    email = :email, 
                    phone = :phone,
                    address = :address, 
                    postnummer = :postalCode 
                WHERE id = :id
            ");
            $stmt->bindParam(':firstName', $firstName);
            $stmt->bindParam(':lastName', $lastName);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':postalCode', $postalCode);
            $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch the updated user data
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $userProfile = [
                'firstName' => $user['firstName'],
                'lastName' => $user['lastName'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'address' => $user['address'],
                'postalCode' => $user['postnummer'],
            ];

            $successMessage = "Din profil har blitt oppdatert.";
        } catch (Exception $e) {
            $errorMessages[] = "Feil ved oppdatering av databasen: " . $e->getMessage();
        }
    } elseif (empty($errorMessages)) {
        $successMessage = $noChangesMessage;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Brukerprofil</title>
</head>
<body>
    <h1>Brukerprofil</h1>

    <?php if (!empty($errorMessages)) : ?>
        <ul>
            <?php foreach ($errorMessages as $error) : ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (!empty($successMessage)) : ?>
        <p><?php echo htmlspecialchars($successMessage); ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="firstName">Fornavn:</label><br>
        <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>"><br><br>

        <label for="lastName">Etternavn:</label><br>
        <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>"><br><br>

        <label for="email">E-post:</label><br>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>"><br><br>

        <label for="phone">Telefonnummer:</label><br>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>"><br><br>

        <label for="address">Adresse:</label><br>
        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>"><br><br>

        <label for="postalCode">Postnummer:</label><br>
        <input type="text" id="postalCode" name="postalCode" value="<?php echo htmlspecialchars($postalCode); ?>"><br><br>

        <button type="submit">Oppdater profil</button>
    </form>
</body>
</html>
