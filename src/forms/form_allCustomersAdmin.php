<?php
require_once '../../src/resources/inc/db.php';
require_once '../../src/models/User.php';
require_once '../func/security.php';
require_once '../../src/func/header.php';

// Sjekk at admin er logget inn
ensureAdmin();

$database = new Database();
$conn = $database->getConnection();
$userModel = new User($conn);

// Hent alle brukere med rollen "user"
$customers = $userModel->getUsersByRole('user');
?><!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Kundeoversikt</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css?v1.0.3">
</head>
<body>
<div class="allCustomers-container">
    <h1>Kundeoversikt</h1>
    <table>
        <thead>
        <tr>
            <th>Bruker-ID</th>
            <th>Navn</th>
            <th>Email</th>
            <th>Telefon</th>
            <th>Handling</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($customers as $customer): ?>
            <tr>
                <td><?php echo htmlspecialchars($customer['id']); ?></td>
                <td><?php echo htmlspecialchars($customer['firstName'] . ' ' . $customer['lastName']); ?></td>
                <td><?php echo htmlspecialchars($customer['email']); ?></td>
                <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                <td>
                    <form action="form_newBooking.php" method="post">
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($customer['id']); ?>">
                        <input type="hidden" name="firstName" value="<?php echo htmlspecialchars($customer['firstName']); ?>">
                        <input type="hidden" name="lastName" value="<?php echo htmlspecialchars($customer['lastName']); ?>">
                        <button type="submit">Lag en booking</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
