<?php
// Include database connection
include '../../src/assets/inc/setupdb/setup.php';

// Fetch all users from the database
try {
    $stmt = $conn->prepare("SELECT * FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error fetching users: " . $e->getMessage());
}

// Check if editing is requested
$isEditing = false;
$selectedUser = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editUserId'])) {
    $editUserId = (int)$_POST['editUserId'];

    // Fetch the selected user's data
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $editUserId, PDO::PARAM_INT);
        $stmt->execute();
        $selectedUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($selectedUser) {
            $isEditing = true;
        } else {
            throw new Exception("User not found.");
        }
    } catch (Exception $e) {
        die("Error fetching user: " . $e->getMessage());
    }
}

// Handle form submission for updating user data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateUser'])) {
    $id = (int)$_POST['userId'];
    $firstName = htmlspecialchars($_POST['firstName']);
    $lastName = htmlspecialchars($_POST['lastName']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $address = htmlspecialchars($_POST['address']);
    $postalCode = htmlspecialchars($_POST['postalCode']);

    // Validate input
    $errors = [];
    if (empty($firstName)) $errors[] = "Fornavn er påkrevd.";
    if (empty($lastName)) $errors[] = "Etternavn er påkrevd.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Gyldig e-post er påkrevd.";
    if (empty($phone) || strlen($phone) != 8) $errors[] = "Telefonnummer må være 8 siffer.";
    if (empty($address)) $errors[] = "Adresse er påkrevd.";
    if (empty($postalCode) || strlen($postalCode) != 4) $errors[] = "Postnummer må være 4 siffer.";

    if (empty($errors)) {
        // Update the user in the database
        try {
            $stmt = $conn->prepare("
                UPDATE users 
                SET firstName = :firstName, 
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
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $successMessage = "Brukeren ble oppdatert.";
            $isEditing = false;

            // Refresh user list
            $stmt = $conn->prepare("SELECT * FROM users");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $errors[] = "Feil ved oppdatering av bruker: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brukeradministrasjon</title>
    <link rel="stylesheet" href="../../public/assets/css/table.css">
</head>
<body>
<h1>Brukeradministrasjon</h1>

<?php if (!empty($errors)) : ?>
    <div class="error">
        <ul>
            <?php foreach ($errors as $error) : ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (isset($successMessage)) : ?>
    <div class="success">
        <p><?php echo htmlspecialchars($successMessage); ?></p>
    </div>
<?php endif; ?>

<?php if ($isEditing && $selectedUser) : ?>
    <h2>Rediger Bruker</h2>
    <form method="post">
        <input type="hidden" name="userId" value="<?php echo htmlspecialchars($selectedUser['id']); ?>">
        <label for="firstName">Fornavn:</label><br>
        <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($selectedUser['firstName']); ?>"><br><br>

        <label for="lastName">Etternavn:</label><br>
        <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($selectedUser['lastName']); ?>"><br><br>

        <label for="email">E-post:</label><br>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($selectedUser['email']); ?>"><br><br>

        <label for="phone">Telefonnummer:</label><br>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($selectedUser['phone']); ?>"><br><br>

        <label for="address">Adresse:</label><br>
        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($selectedUser['address']); ?>"><br><br>

        <label for="postalCode">Postnummer:</label><br>
        <input type="text" id="postalCode" name="postalCode" value="<?php echo htmlspecialchars($selectedUser['postnummer']); ?>"><br><br>

        <button type="submit" name="updateUser">Oppdater</button>
    </form>
<?php else : ?>
    <h2>Profil</h2>
    <table>
        <thead>
        <tr>
            <th>Fornavn</th>
            <th>Etternavn</th>
            <th>E-post</th>
            <th>Telefonnummer</th>
            <th>Handling</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user) : ?>
            <tr>
                <td><?php echo htmlspecialchars($user['firstName']); ?></td>
                <td><?php echo htmlspecialchars($user['lastName']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                <td>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="editUserId" value="<?php echo htmlspecialchars($user['id']); ?>">
                        <button type="submit">Rediger</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</body>
</html>
