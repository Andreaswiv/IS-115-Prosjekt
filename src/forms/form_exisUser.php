<?php

# Include database connection
include '../../src/assets/inc/setupdb/setup.php';

# Include utility functions
include '../../src/assets/inc/functions.php';


try {
    // Fetch user data by ID
    $user_id = 1; // Example user ID
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        throw new Exception("No user found with ID $user_id.");
    }

    // Populate user profile
    $userProfile = [
        'firstName' => $user['firstName'],
        'lastName' => $user['lastName'],
        'email' => $user['email'],
        'phone' => $user['phone'],
        'address' => $user['address'],
        'postalCode' => $user['postnummer'],
    ];

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

$errorMessages = []; // Define an empty array for error messages
$updates = []; // Define an empty array for change notifications
$message = "";
$registrationMessage = "";

// Define variables for the form
$firstName = $userProfile['firstName'];
$lastName = $userProfile['lastName'];
$email = $userProfile['email'];
$phone = $userProfile['phone'];
$address = $userProfile['address'];
$postalCode = $userProfile['postalCode'];

// Check if the request method is POST, then sanitize the input data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = sanitize($_POST['firstName']);
    $lastName = sanitize($_POST['lastName']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $postalCode = sanitize($_POST['postalCode']);

    // Validate input, add error messages to $errorMessages if necessary
    if (empty($firstName)) {
        $errorMessages[] = "Påkrevet felt: fornavn kan ikke være tomt.";
    }
    if (empty($lastName)) {
        $errorMessages[] = "Påkrevet felt: etternavn kan ikke være tomt.";
    }
    if (empty($email)) {
        $errorMessages[] = "Påkrevet felt: epost kan ikke være tomt.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessages[] = "Ugyldig epost-addresse.";
    }
    if (empty($phone)) {
        $errorMessages[] = "Påkrevet felt: telefonnummer kan ikke være tomt.";
    } elseif (strlen($phone) != 8) {
        $errorMessages[] = "Telefonnummer må være 8 siffer.";
    }
    if (empty($address)) {
        $errorMessages[] = "Påkrevet felt: addresse kan ikke være tomt.";
    }
    if (empty($postalCode)) {
        $errorMessages[] = "Påkrevet felt: postnummer kan ikke være tomt.";
    } elseif (strlen($postalCode) != 4) {
        $errorMessages[] = "Postnummer må være 4 siffer.";
    }

    // If no validation errors, proceed
    if (empty($errorMessages)) {
        $changesMade = false;

        // Format first name, last name, and address
        $formattedFirstName = mb_convert_case(mb_strtolower($firstName, 'UTF-8'), MB_CASE_TITLE, "UTF-8");
        $formattedLastName = mb_convert_case(mb_strtolower($lastName, 'UTF-8'), MB_CASE_TITLE, "UTF-8");
        $formattedAddress = mb_convert_case(mb_strtolower($address, 'UTF-8'), MB_CASE_TITLE, "UTF-8");

        // Compare each field and record changes
        if ($formattedFirstName != $userProfile['firstName']) {
            $updates['First Name'] = ['old' => $userProfile['firstName'], 'new' => $formattedFirstName];
            $changesMade = true;
        }
        if ($formattedLastName != $userProfile['lastName']) {
            $updates['Last Name'] = ['old' => $userProfile['lastName'], 'new' => $formattedLastName];
            $changesMade = true;
        }
        if ($email != $userProfile['email']) {
            $updates['Email'] = ['old' => $userProfile['email'], 'new' => $email];
            $changesMade = true;
        }
        if ($phone != $userProfile['phone']) {
            $updates['Phone'] = ['old' => $userProfile['phone'], 'new' => $phone];
            $changesMade = true;
        }
        if ($formattedAddress != $userProfile['address']) {
            $updates['Address'] = ['old' => $userProfile['address'], 'new' => $formattedAddress];
            $changesMade = true;
        }
        if ($postalCode != $userProfile['postalCode']) {
            $updates['Postal Code'] = ['old' => $userProfile['postalCode'], 'new' => $postalCode];
            $changesMade = true;
        }

        if ($changesMade) {
            // Update database with the new values
            try {
                $updateStmt = $conn->prepare("
                    UPDATE users SET 
                        firstName = :firstName, 
                        lastName = :lastName, 
                        email = :email, 
                        phone = :phone, 
                        address = :address, 
                        postnummer = :postnummer 
                    WHERE id = :id
                ");
                $updateStmt->bindParam(':firstName', $formattedFirstName);
                $updateStmt->bindParam(':lastName', $formattedLastName);
                $updateStmt->bindParam(':email', $email);
                $updateStmt->bindParam(':phone', $phone);
                $updateStmt->bindParam(':address', $formattedAddress);
                $updateStmt->bindParam(':postnummer', $postalCode);
                $updateStmt->bindParam(':id', $user_id, PDO::PARAM_INT);
                $updateStmt->execute();

                // Retrieve updated information
                $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
                $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    $userProfile = [
                        'firstName' => $user['firstName'],
                        'lastName' => $user['lastName'],
                        'email' => $user['email'],
                        'phone' => $user['phone'],
                        'address' => $user['address'],
                        'postalCode' => $user['postnummer']
                    ];
                }

                // Notify that the entry was updated
                $message = 'Brukerinformasjon oppdatert.';
            } catch (PDOException $e) {
                $errorMessages[] = "Feil i oppdatering av brukerinformasjon: " . $e->getMessage();
            }
        } else {
            $message = 'Ingen endringer gjort.';
        }

        // Display the updated information
        $registrationMessage = 'Oppdatert informasjon er lagret:';
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
