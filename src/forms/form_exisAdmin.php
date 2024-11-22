<?php

// Include database connection
include '../../src/assets/inc/setupdb/setup.php';

try {
    // Fetch all users from the database
    $stmt = $conn->prepare("SELECT id, username, firstName, lastName, email, phone, address, postnummer AS postalCode, role FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$users) {
        throw new Exception("No users found in the database.");
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!-- list all users in table List of Users -->
<!DOCTYPE html>
<html>
<head>
    <title>Existing Admin/User Records</title>
</head>
<body>
    <h1>List of Users</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Postal Code</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?> <!-- fill table with data from database table Users -->
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['firstName']); ?></td>
                    <td><?php echo htmlspecialchars($user['lastName']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                    <td><?php echo htmlspecialchars($user['address']); ?></td>
                    <td><?php echo htmlspecialchars($user['postalCode']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a href="form_nyAdmin.php">Registrer en ny bruker</a>
</body>
</html>
