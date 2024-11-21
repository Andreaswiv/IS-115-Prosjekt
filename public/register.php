<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>
<body>
<h2>Register</h2>

<!-- Registration form -->
<form action="../src/controllers/registerController.php" method="post">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" value="<?php echo isset($_SESSION['form_data']['username']) ? htmlspecialchars($_SESSION['form_data']['username']) : ''; ?>" required><br>

    <label for="firstName">First Name:</label>
    <input type="text" name="firstName" id="firstName" value="<?php echo isset($_SESSION['form_data']['firstName']) ? htmlspecialchars($_SESSION['form_data']['firstName']) : ''; ?>" required><br>

    <label for="lastName">Last Name:</label>
    <input type="text" name="lastName" id="lastName" value="<?php echo isset($_SESSION['form_data']['lastName']) ? htmlspecialchars($_SESSION['form_data']['lastName']) : ''; ?>" required><br>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>" required><br>

    <label for="phone">Phone:</label>
    <input type="text" name="phone" id="phone" value="<?php echo isset($_SESSION['form_data']['phone']) ? htmlspecialchars($_SESSION['form_data']['phone']) : ''; ?>" required><br>

    <label for="address">Address:</label>
    <input type="text" name="address" id="address" value="<?php echo isset($_SESSION['form_data']['address']) ? htmlspecialchars($_SESSION['form_data']['address']) : ''; ?>" required><br>

    <label for="postnummer">Postnummer:</label>
    <input type="text" name="postnummer" id="postnummer" value="<?php echo isset($_SESSION['form_data']['postnummer']) ? htmlspecialchars($_SESSION['form_data']['postnummer']) : ''; ?>" required><br>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" value="<?php echo isset($_SESSION['form_data']['password']) ? htmlspecialchars($_SESSION['form_data']['password']) : ''; ?>" required><br>

    <label for="role">Role:</label>
    <select name="role" id="role" required>
        <option value="user" <?php echo (isset($_SESSION['form_data']['role']) && $_SESSION['form_data']['role'] == 'user') ? 'selected' : ''; ?>>User</option>
        <option value="admin" <?php echo (isset($_SESSION['form_data']['role']) && $_SESSION['form_data']['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
    </select><br>

    <button type="submit">Register</button>
</form>

<?php
// Display validation errors if present
if (isset($_SESSION['register_errors'])) {
    echo '<ul style="color: red;">';
    foreach ($_SESSION['register_errors'] as $error) {
        echo '<li>' . htmlspecialchars($error) . '</li>';
    }
    echo '</ul>';
    unset($_SESSION['register_errors']);  // Clear error messages after displaying
}

// Clear form data after displaying
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>

</body>
</html>

