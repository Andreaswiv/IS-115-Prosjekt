<?php
// Include database connection
include '../../src/assets/inc/setupdb/setup.php';

// Default sorting and filtering logic
$orderDir = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
$sortBy = isset($_GET['sortBy']) && $_GET['sortBy'] == 'registrationDate' ? 'registrationDate' : 'id';
$viewType = isset($_GET['view']) ? $_GET['view'] : 'all';
$isLastMonthView = ($viewType == 'lastMonth');
$groupBy = isset($_GET['groupBy']) && $_GET['groupBy'] !== 'none' ? $_GET['groupBy'] : null;

// Fetch all unique preferences for the dropdown
try {
    $stmt = $conn->prepare("SELECT DISTINCT preference_value FROM preferences");
    $stmt->execute();
    $preferences = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    die("Error fetching preferences: " . $e->getMessage());
}

// Fetch users based on filters, grouping, and sorting
try {
    if ($groupBy) {
        // Group users by preference
        $stmt = $conn->prepare("
            SELECT u.*, p.preference_value 
            FROM users u
            LEFT JOIN preferences p ON u.id = p.user_id
            WHERE p.preference_value = :groupBy
            ORDER BY $sortBy $orderDir
        ");
        $stmt->bindParam(':groupBy', $groupBy, PDO::PARAM_STR);
    } elseif ($isLastMonthView) {
        // Fetch users registered in the last month
        $oneMonthAgo = date('Y-m-d H:i:s', strtotime('-1 month'));
        $stmt = $conn->prepare("
            SELECT u.*
            FROM users u
            WHERE u.registrationDate >= :oneMonthAgo
            ORDER BY $sortBy $orderDir
        ");
        $stmt->bindParam(':oneMonthAgo', $oneMonthAgo, PDO::PARAM_STR);
    } else {
        // Fetch all users without grouping
        $stmt = $conn->prepare("SELECT u.* FROM users u ORDER BY $sortBy $orderDir");
    }
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
    $birthDate = htmlspecialchars($_POST['birthDate']);

    $errors = [];
    if (empty($firstName)) $errors[] = "First name is required.";
    if (empty($lastName)) $errors[] = "Last name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (empty($phone) || strlen($phone) != 8) $errors[] = "Phone number must be 8 digits.";
    if (empty($address)) $errors[] = "Address is required.";
    if (empty($postalCode) || strlen($postalCode) != 4) $errors[] = "Postal code must be 4 digits.";
    if (!empty($birthDate) && !DateTime::createFromFormat('Y-m-d', $birthDate)) $errors[] = "Birth date must be in YYYY-MM-DD format.";

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("
                UPDATE users 
                SET firstName = :firstName, 
                    lastName = :lastName, 
                    email = :email, 
                    phone = :phone, 
                    address = :address, 
                    postnummer = :postalCode,
                    birthDate = :birthDate
                WHERE id = :id
            ");
            $stmt->bindParam(':firstName', $firstName);
            $stmt->bindParam(':lastName', $lastName);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':postalCode', $postalCode);
            $stmt->bindParam(':birthDate', $birthDate);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $successMessage = "User updated successfully.";
            $isEditing = false;
        } catch (PDOException $e) {
            $errors[] = "Error updating user: " . $e->getMessage();
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
    <link rel="stylesheet" href="../../public/assets/css/exisUserStyles.css">
    <script>
        function toggleSortOrder() {
            const currentOrder = '<?php echo $orderDir; ?>';
            const newOrder = currentOrder === 'ASC' ? 'DESC' : 'ASC';
            const currentView = '<?php echo $viewType; ?>';
            const groupBy = '<?php echo $groupBy; ?>';
            window.location.href = `?sortBy=registrationDate&order=${newOrder}&view=${currentView}&groupBy=${groupBy}`;
        }
    </script>
</head>
<body>
<div class="container">
    <h1>User Management</h1>

    <div class="filters">
        <form method="get" class="filter-form">
            <!-- View Filter -->
            <div class="filter-group">
                <label for="viewType">View:</label>
                <select name="view" id="viewType" onchange="this.form.submit()">
                    <option value="all" <?php echo ($viewType == 'all') ? 'selected' : ''; ?>>All Users</option>
                    <option value="lastMonth" <?php echo ($viewType == 'lastMonth') ? 'selected' : ''; ?>>Last 30 Days</option>
                </select>
            </div>

            <!-- Group By Preference -->
            <div class="filter-group">
                <label for="groupBy">Group By Preference:</label>
                <select name="groupBy" id="groupBy" onchange="this.form.submit()">
                    <option value="none" <?php echo !$groupBy ? 'selected' : ''; ?>>None</option>
                    <?php foreach ($preferences as $preference): ?>
                        <option value="<?php echo htmlspecialchars($preference); ?>"
                            <?php echo ($groupBy == $preference) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($preference); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <?php if ($isEditing && $selectedUser) : ?>
        <h2>Edit User</h2>
        <form method="post">
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
            <input type="text" id="postalCode" name="postalCode" value="<?php echo htmlspecialchars($selectedUser['postnummer']); ?>"><br>

            <label for="birthDate">Birth Date:</label>
            <input type="date" id="birthDate" name="birthDate" value="<?php echo htmlspecialchars($selectedUser['birthDate']); ?>"><br>

            <button type="submit" name="updateUser">Update User</button>
        </form>
    <?php else : ?>
        <table>
            <thead>
            <tr>
                <th onclick="toggleSortOrder()">Registration Date<span class="sort-arrow"> <?php echo $orderDir === 'ASC' ? '▲' : '▼'; ?></th>
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
