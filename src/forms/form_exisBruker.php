<?php

// Initialize PDO connection
include '../../database/setupdb/setup.php';

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

// Sanitize function
function sanitize($variable)
{
    $variable = strip_tags($variable);
    $variable = htmlentities($variable);
    $variable = trim($variable);
    return $variable;
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
        $errorMessages[] = "Required field: First name cannot be empty.";
    }
    if (empty($lastName)) {
        $errorMessages[] = "Required field: Last name cannot be empty.";
    }
    if (empty($email)) {
        $errorMessages[] = "Required field: Email cannot be empty.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessages[] = "Invalid email address.";
    }
    if (empty($phone)) {
        $errorMessages[] = "Required field: Phone number cannot be empty.";
    } elseif (strlen($phone) != 8) {
        $errorMessages[] = "Phone number must be 8 digits long.";
    }
    if (empty($address)) {
        $errorMessages[] = "Required field: Address cannot be empty.";
    }
    if (empty($postalCode)) {
        $errorMessages[] = "Required field: Postal code cannot be empty.";
    } elseif (strlen($postalCode) != 4) {
        $errorMessages[] = "Postal code must be 4 digits long.";
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
                $message = 'User record updated in the database.';
            } catch (PDOException $e) {
                $errorMessages[] = "Error updating the database: " . $e->getMessage();
            }
        } else {
            $message = 'No changes were made.';
        }

        // Display the updated information
        $registrationMessage = 'Updated information has been saved:';
    }
}
?>