<?php
include '../../src/resources/inc/setupdb/setup.php';
include '../../src/resources/inc/db_queries.php';
include '../../src/resources/inc/functions.php';
require_once '../func/security.php';
require_once '../../src/func/header.php';
ensureAdmin();

// Default sorting and filtering logic
$orderDir = $_GET['order'] ?? 'ASC';
$sortBy = $_GET['sortBy'] ?? 'id';
$viewType = $_GET['view'] ?? 'all';
$groupBy = $_GET['groupBy'] ?? 'none';
$isLastMonthView = ($viewType == 'lastMonth');

try {
    $preferences = fetchPreferences($conn);
    $users = fetchUsers($conn, $sortBy, $orderDir, $viewType, $groupBy);

    // Fetch roles for the roles dropdown
    $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'role'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    preg_match("/^enum\((.*)\)$/", $result['Type'], $matches);
    $roles = array_map(function ($value) {
        return trim($value, "'");
    }, explode(',', $matches[1]));
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Handle editing and updating
$isEditing = false;
$selectedUser = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['editUserId'])) {
        $selectedUser = fetchUserById($conn, (int)$_POST['editUserId']);
        $isEditing = !empty($selectedUser);
    }

    if (isset($_POST['updateUser'])) {
        $userData = [
            ':id' => (int)$_POST['userId'],
            ':firstName' => htmlspecialchars($_POST['firstName']),
            ':lastName' => htmlspecialchars($_POST['lastName']),
            ':email' => htmlspecialchars($_POST['email']),
            ':phone' => htmlspecialchars($_POST['phone']),
            ':address' => htmlspecialchars($_POST['address']),
            ':postalCode' => htmlspecialchars($_POST['postalCode']),
            ':role' => htmlspecialchars($_POST['role']),
        ];

        $errors = validateUserData($userData);
        if (empty($errors)) {
            try {
                updateUser($conn, $userData);
                $successMessage = "User updated successfully.";
                $isEditing = false;
            } catch (Exception $e) {
                $errors[] = "Error updating user: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css?v1.0.3">
    <script>
        const phpVars = {
            orderDir: '<?= $orderDir; ?>',
            viewType: '<?= $viewType; ?>',
            groupBy: '<?= $groupBy; ?>',
            originalRole: '<?= htmlspecialchars($selectedUser['role'] ?? ''); ?>'
        };
    </script>
    <script src="../../public/assets/js/user-management.js?v1.0.1"></script>
</head>
<body>
<div class="behandling-container">
    <h1>Bruker Behandling</h1>
    <div class="filters">
        <?php if (!$isEditing): // Only show this section if not editing a user ?>
            <form method="get" class="filter-form">
                <div class="filter-group-container">
                    <!-- View Filter -->
                    <div class="filter-group">
                        <label for="viewType">Visning:</label>
                        <select name="view" id="viewType" onchange="this.form.submit()">
                            <option value="all" <?php echo ($viewType == 'all') ? 'selected' : ''; ?>>Alle brukere</option>
                            <option value="lastMonth" <?php echo ($viewType == 'lastMonth') ? 'selected' : ''; ?>>Siste 30 dager</option>
                        </select>
                    </div>
                    <!-- Group By Preference -->
                    <div class="filter-group">
                        <label for="groupBy">Gruppering:</label>
                        <select name="groupBy" id="groupBy" onchange="this.form.submit()">
                            <option value="none" <?php echo (!$groupBy || $groupBy === 'none') ? 'selected' : ''; ?>>Ingen</option>
                            <?php foreach ($preferences as $preference): ?>
                                <option value="<?php echo htmlspecialchars($preference); ?>"
                                    <?php echo ($groupBy === $preference) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($preference); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <?php if ($isEditing && $selectedUser) : ?>
        <!-- Editing Form -->
        <h2>Edit User</h2>
        <form method="post" onsubmit="return confirmRoleChange()">
            <input type="hidden" name="userId" value="<?php echo htmlspecialchars($selectedUser['id']); ?>">
            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($selectedUser['firstName']); ?>"><br>
            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($selectedUser['lastName']); ?>"><br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($selectedUser['email']); ?>"><br>
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($selectedUser['phone']); ?>"><br>
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($selectedUser['address']); ?>"><br>
            <label for="postalCode">Postal Code:</label>
            <input type="text" id="postalCode" name="postalCode" value="<?php echo htmlspecialchars($selectedUser['postalCode']); ?>"><br>
            <label for="role">Role:</label>
            <select id="role" name="role">
                <?php foreach ($roles as $role): ?>
                    <option value="<?php echo htmlspecialchars($role); ?>"
                        <?php echo ($selectedUser['role'] === $role) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars(ucfirst($role)); ?>
                    </option>
                <?php endforeach; ?>
            </select><br>
            <button type="submit" name="updateUser">Update User</button>
        </form>
    <?php else : ?>
        <!-- User Table -->
        <table>
            <thead>
            <tr>
                <th onclick="toggleSortOrder()">Registration Date<span class="sort-arrow"> <?php echo $orderDir === 'ASC' ? '▲' : '▼'; ?></span></th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['registrationDate']); ?></td>
                    <td><?php echo htmlspecialchars($user['firstName']); ?></td>
                    <td><?php echo htmlspecialchars($user['lastName']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <form method="post">
                            <button type="submit" name="editUserId" value="<?php echo htmlspecialchars($user['id']); ?>">Edit</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>