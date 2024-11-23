<?php
/*
THIS SCRIPT SAVES IS ONLY VISIBLE TO ADMINS, AND SAVES A NEW ADMIN OR USER IN THE DATABASE
The user input is validated and stored in the "users" table in the database, with the addition of a ROLE column
*/

# Include database connection
include '../../src/assets/inc/setupdb/setup.php';

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
    $phoneFormatted = null; // Optional field
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

# Role validation
if (isset($_REQUEST['role']) && $_REQUEST['role'] !== null) {
    $role = sanitize($_REQUEST['role']);
    if ($role == "User" || $role == "Admin") {
        # Role is valid
    } else {
        $errorMessages[] = "Role must be either 'User' or 'Admin'.";
        $isConfirmationValid = false;
    }
} else {
    $errorMessages[] = "Required field: Role is missing.";
    $isConfirmationValid = false;
}
?>

<html>
<head>
    <title>Admin/User Registration</title>
</head>
<body>
    <form name="registerAdmin" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <input type="text" name="username" placeholder="Username">*required<br>
        <input type="text" name="firstName" placeholder="First Name">*required<br>
        <input type="text" name="lastName" placeholder="Last Name">*required<br>
        <input type="text" name="email" placeholder="Email">*required<br>
        <input type="number" name="phone" placeholder="Phone Number"><br>
        <input type="text" name="address" placeholder="Street Name and Number">*required<br>
        <input type="number" name="postalCode" placeholder="Postal Code">*required<br>
        <input type="password" name="password" placeholder="Password">*required<br>
        <select name="role">
            <option value="User">User</option>
            <option value="Admin">Admin</option>
        </select>*required<br>
        <input type="submit" name="register" value="Register"><br>
    </form>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($isConfirmationValid) {
        try {
            // Insert the validated data into the database
            $stmt = $conn->prepare("
                INSERT INTO users (username, firstName, lastName, email, phone, address, postnummer, password, role)
                VALUES (:username, :firstName, :lastName, :email, :phone, :address, :postalCode, :password, :role)
            ");
            $stmt->bindParam(':username', $usernameFormatted);
            $stmt->bindParam(':firstName', $firstNameFormatted);
            $stmt->bindParam(':lastName', $lastNameFormatted);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phoneFormatted);
            $stmt->bindParam(':address', $addressFormatted);
            $stmt->bindParam(':postalCode', $postalCode);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':role', $role);
            $stmt->execute();

            echo "<strong>Admin/User has been successfully registered.</strong>";

        } catch (PDOException $e) {
            echo "Error saving admin/user to the database: " . $e->getMessage();
        }
    } else {
        # Display error messages
        foreach ($errorMessages as $message) {
            echo $message . "<br>";
        }
    }
}
?>
