<?php
/*
THIS SCRIPT SAVES A NEW USER IN THE DATABASE
The user input is validated and stored in the "users" table in the database
*/

# Include database connection
include '../../src/assets/inc/setupdb/setup.php';
require_once '../func/security.php';
require_once '../../src/func/header.php';
# Include utility functions
include '../../src/assets/inc/functions.php';

# Validation of user data
$errorMessages = [];
$isConfirmationValid = true;

# Username
if (isset($_REQUEST['username']) && $_REQUEST['username'] !== null) {
    $username = sanitize($_REQUEST['username']);
    if ($username != "") {
        # Username is valid
        $usernameFormatted = mb_convert_case(mb_strtolower($username, 'UTF-8'), MB_CASE_LOWER, "UTF-8"); // Ensures lowercase username
    } else {
        $errorMessages[] = "Required field: Username cannot be empty.";
        $isConfirmationValid = false;
    }
} else {
    $errorMessages[] = "Required field: Username is missing.";
    $isConfirmationValid = false;
}

# Password
if (isset($_REQUEST['password']) && $_REQUEST['password'] !== null) {
    $password = sanitize($_REQUEST['password']);
    if (strlen($password) >= 8) {
        # Hash the password before saving
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    } else {
        $errorMessages[] = "Password must be at least 8 characters long.";
        $isConfirmationValid = false;
    }
} else {
    $errorMessages[] = "Required field: Password is missing.";
    $isConfirmationValid = false;
}

# First name
if (isset($_REQUEST['firstName']) && $_REQUEST['firstName'] !== null) {
    $firstName = sanitize($_REQUEST['firstName']);
    if ($firstName != "") {
        # First name is valid
        $firstNameFormatted = mb_convert_case(mb_strtolower($firstName, 'UTF-8'), MB_CASE_TITLE, "UTF-8"); // Ensures uppercase first letter
    } else {
        $errorMessages[] = "Required field: First name cannot be empty.";
        $isConfirmationValid = false;
    }
} else {
    $errorMessages[] = "Required field: First name is missing.";
    $isConfirmationValid = false;
}

# Last name
if (isset($_REQUEST['lastName']) && $_REQUEST['lastName'] !== null) {
    $lastName = sanitize($_REQUEST['lastName']);
    if ($lastName != "") {
        # Last name is valid
        $lastNameFormatted = mb_convert_case(mb_strtolower($lastName, 'UTF-8'), MB_CASE_TITLE, "UTF-8"); // Ensures uppercase first letter
    } else {
        $errorMessages[] = "Required field: Last name cannot be empty.";
        $isConfirmationValid = false;
    }
} else {
    $errorMessages[] = "Required field: Last name is missing.";
    $isConfirmationValid = false;
}

# Email validation
if (isset($_REQUEST['email']) && $_REQUEST['email'] !== null) {
    $email = sanitize($_REQUEST['email']);
    if (empty($email)) {
        $errorMessages[] = "Required field: Email cannot be empty.";
        $isConfirmationValid = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessages[] = "Invalid email address.";
        $isConfirmationValid = false;
    }
} else {
    $errorMessages[] = "Required field: Email is missing.";
    $isConfirmationValid = false;
}

# Phone number validation
$phone = isset($_REQUEST['phone']) ? sanitize($_REQUEST['phone']) : null;
if ($phone !== null) {
    if (strlen($phone) == 8) {
        # Phone number is properly formatted
        $phoneFormatted = $phone;
    } else {
        $errorMessages[] = "Phone number must be 8 digits long.";
        $isConfirmationValid = false;
    }
} else {
    $phoneFormatted = null;
}

# Address
if (isset($_REQUEST['address']) && $_REQUEST['address'] !== null) {
    $address = sanitize($_REQUEST['address']);
    if ($address != "") {
        # Address is valid
        $addressFormatted = mb_convert_case(mb_strtolower($address, 'UTF-8'), MB_CASE_TITLE, "UTF-8"); // Ensures uppercase first letter
    } else {
        $errorMessages[] = "Required field: Address cannot be empty.";
        $isConfirmationValid = false;
    }
} else {
    $errorMessages[] = "Required field: Address is missing.";
    $isConfirmationValid = false;
}

# Postal code
if (isset($_REQUEST['postalCode']) && $_REQUEST['postalCode'] !== null) {
    $postalCode = sanitize($_REQUEST['postalCode']);
    if (strlen($postalCode) != 4) {
        $errorMessages[] = "Postal code must consist of 4 digits.";
        $isConfirmationValid = false;
    }
} else {
    $errorMessages[] = "Required field: Postal code is missing.";
    $isConfirmationValid = false;
}

$birthDate = isset($_REQUEST['birthDate']) ? sanitize($_REQUEST['birthDate']) : null;
if ($birthDate !== null) {
    if (!DateTime::createFromFormat('Y-m-d', $birthDate)) {
        $errorMessages[] = "Invalid birth date format. Please use the YYYY-MM-DD format.";
        $isConfirmationValid = false;
    } else {
        $birthDateFormatted = $birthDate;
    }
} else {
    $birthDateFormatted = null;
}

?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrer ny bruker</title>
    <link rel="stylesheet" href="../../public/assets/css/table.css">
</head>
<body>
<div class="container">
    <h1>Registrer ny bruker</h1>

    <?php
    # Display error messages only if there are errors
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isConfirmationValid) {
        echo '<div class="error-messages">';
        foreach ($errorMessages as $message) {
            echo "<p>$message</p>";
        }
        echo '</div>';
    }

    # Display success message if registration is successful
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isConfirmationValid) {
        echo '<div class="success-message">';
        echo "<p><strong>Brukeren er registrert.</strong></p>";
        echo '</div>';
    }
    ?>

    <form name="registerUser" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="user-form">
        <label for="username">Brukernavn *</label>
        <input type="text" id="username" name="username" placeholder="Brukernavn" required>

        <label for="password">Passord *</label>
        <input type="password" id="password" name="password" placeholder="Passord" required>

        <label for="firstName">Fornavn *</label>
        <input type="text" id="firstName" name="firstName" placeholder="Fornavn" required>

        <label for="lastName">Etternavn *</label>
        <input type="text" id="lastName" name="lastName" placeholder="Etternavn" required>

        <label for="email">E-post *</label>
        <input type="email" id="email" name="email" placeholder="E-post" required>

        <label for="phone">Telefonnummer</label>
        <input type="number" id="phone" name="phone" placeholder="Telefonnummer (valgfritt)">

        <label for="address">Adresse *</label>
        <input type="text" id="address" name="address" placeholder="Gateadresse og nummer" required>

        <label for="postalCode">Postnummer *</label>
        <input type="number" id="postalCode" name="postalCode" placeholder="Postnummer" required>

        <label for="birthDate">Fødselsdato</label>
        <input type="date" name="birthDate" placeholder="Birth Date (DD-MM-YYYYD)"><br>


        <button type="submit" name="register">Registrer</button>
    </form>
</div>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isConfirmationValid) {
    try {
        // Insert the validated data into the database
        $stmt = $conn->prepare("
            INSERT INTO users (username, firstName, lastName, email, phone, address, postnummer, password, birthDate)
            VALUES (:username, :firstName, :lastName, :email, :phone, :address, :postalCode, :password, :birthDate)
        ");
        $stmt->bindParam(':username', $usernameFormatted);
        $stmt->bindParam(':firstName', $firstNameFormatted);
        $stmt->bindParam(':lastName', $lastNameFormatted);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phoneFormatted);
        $stmt->bindParam(':address', $addressFormatted);
        $stmt->bindParam(':postalCode', $postalCode);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':birthDate', $birthDateFormatted);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "<p class='error-message'>Feil ved lagring av bruker: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>
