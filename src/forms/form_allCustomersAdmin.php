<?php
require_once '../../src/resources/inc/db.php';
require_once '../../src/models/User.php';
require_once '../func/security.php';
require_once '../../src/func/header.php';

// Ensure that an admin user is logged in
ensureAdmin();

$database = new Database();
$conn = $database->getConnection();
$userModel = new User($conn);

// Fetch all users with the role "user"
$customers = $userModel->getUsersByRole('user');
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Kundeoversikt</title> <!-- Page title -->
    <link rel="stylesheet" href="../../public/assets/css/style.css"> <!-- Link to CSS -->
</head>
<body>
<div class="container">
    <h1>Kundeoversikt</h1> <!-- Page header -->
    <table>
        <thead>
            <tr>
                <th>Bruker-ID</th> <!-- User ID column -->
                <th>Navn</th> <!-- Name column -->
                <th>Email</th> <!-- Email column -->
                <th>Telefon</th> <!-- Phone number column -->
                <th>Handling</th> <!-- Actions column -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $customer): ?> <!-- Loop through each customer -->
                <tr>
                    <td><?php echo htmlspecialchars($customer['id']); ?></td> <!-- Display user ID -->
                    <td><?php echo htmlspecialchars($customer['firstName'] . ' ' . $customer['lastName']); ?></td> <!-- Display full name -->
                    <td><?php echo htmlspecialchars($customer['email']); ?></td> <!-- Display email -->
                    <td><?php echo htmlspecialchars($customer['phone']); ?></td> <!-- Display phone -->
                    <td>
                        <form action="form_newBooking.php" method="post"> <!-- Form to create a booking -->
                            <!-- Pass user details via hidden inputs -->
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($customer['id']); ?>">
                            <input type="hidden" name="firstName" value="<?php echo htmlspecialchars($customer['firstName']); ?>">
                            <input type="hidden" name="lastName" value="<?php echo htmlspecialchars($customer['lastName']); ?>">
                            <button type="submit">Lag en booking</button> <!-- Button to create a booking -->
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?> <!-- End of loop -->
        </tbody>
    </table>
</div>
</body>
</html>
