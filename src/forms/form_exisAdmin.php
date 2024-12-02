<?php
include '../../src/resources/inc/setupdb/setup.php';
include '../../src/resources/inc/db_queries.php';
include '../../src/resources/inc/functions.php';
require_once '../func/security.php';
require_once '../../src/func/header.php';
ensureAdmin(); // Ensure that the user is an admin

// Default sorting and filtering logic
$orderDir = $_GET['order'] ?? 'ASC'; // Sorting direction: ascending or descending
$sortBy = $_GET['sortBy'] ?? 'id'; // Column to sort by
$viewType = $_GET['view'] ?? 'all'; // View filter: all users or recent
$groupBy = $_GET['groupBy'] ?? 'none'; // Grouping preference
$isLastMonthView = ($viewType == 'lastMonth'); // Check if last month view is selected

try {
    $preferences = fetchPreferences($conn); // Fetch grouping preferences
    $users = fetchUsers($conn, $sortBy, $orderDir, $viewType, $groupBy); // Fetch user list based on filters

    // Fetch roles for the roles dropdown
    $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'role'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    preg_match("/^enum\((.*)\)$/", $result['Type'], $matches);
    $roles = array_map(function ($value) {
        return trim($value, "'"); // Extract role names from ENUM type
    }, explode(',', $matches[1]));
} catch (Exception $e) {
    die("Error: " . $e->getMessage()); // Handle and display errors
}

// Handle editing and updating users
$isEditing = false; // Track if editing mode is active
$selectedUser = null; // Store the selected user for editing

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['editUserId'])) { // Handle edit request
        $selectedUser = fetchUserById($conn, (int)$_POST['editUserId']);
        $isEditing = !empty($selectedUser); // Enable editing mode if user is found
    }

    if (isset($_POST['updateUser'])) { // Handle update request
        $userData = [
            ':id' => (int)$_POST['userId'], // User ID
            ':firstName' => htmlspecialchars($_POST['firstName']), // First name
            ':lastName' => htmlspecialchars($_POST['lastName']), // Last name
            ':email' => htmlspecialchars($_POST['email']), // Email address
            ':phone' => htmlspecialchars($_POST['phone']), // Phone number
            ':address' => htmlspecialchars($_POST['address']), // Address
            ':postalCode' => htmlspecialchars($_POST['postalCode']), // Postal code
            ':role' => htmlspecialchars($_POST['role']), // User role
        ];

        $errors = validateUserData($userData); // Validate input data
        if (empty($errors)) { // Proceed if no validation errors
            try {
                updateUser($conn, $userData); // Update user information in the database
                $successMessage = "User updated successfully."; // Success message
                $isEditing = false; // Exit editing mode
            } catch (Exception $e) {
                $errors[] = "Error updating user: " . $e->getMessage(); // Handle update errors
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
    <title>User Management</title> <!-- Page title -->
    <link rel="stylesheet" href="../../public/assets/css/style.css?v1.0.3"> <!-- Link to stylesheet -->
    <script>
        // Pass PHP variables to JavaScript for role change confirmation
        const phpVars = {
            orderDir: '<?= $orderDir; ?>',
            viewType: '<?= $viewType; ?>',
            groupBy: '<?= $groupBy; ?>',
            originalRole: '<?= htmlspecialchars($selectedUser['role'] ?? ''); ?>'
        };
    </script>
    <script src="../../public/assets/js/user-management.js?v1.0.1"></script> <!-- Link to JS script -->
</head>
<body>
<div class="behandling-container">
    <h1>Bruker Behandling</h1> <!-- Page header -->
    <div class="filters">
        <?php if (!$isEditing): // Display filters only if not editing ?>
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
            <button type="submit" name="updateUser">Update User</button> <!-- Submit button for updating -->
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
                            <button type="submit" name="editUserId" value="<?php echo htmlspecialchars($user['id']); ?>">Edit</button> <!-- Button to edit user -->
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
