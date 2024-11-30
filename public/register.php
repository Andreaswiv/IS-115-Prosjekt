<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registrering</title>
</head>
<body>
<h2>Registrering</h2>
<link rel="stylesheet" href="assets/css/table.css">

<!-- Display Success and Error Messages -->
<?php
if (isset($_SESSION['register_errors'])) {
    echo '<ul style="color: red;">';
    foreach ($_SESSION['register_errors'] as $error) {
        echo '<li>' . htmlspecialchars($error) . '</li>';
    }
    echo '</ul>';
    unset($_SESSION['register_errors']);
}
?>

<!-- Registration form -->
<form action="../src/controllers/registerController.php" method="post">
    <label for="username">Brukernavn:</label>
    <input type="text" name="username" id="username" value="<?php echo isset($_SESSION['form_data']['username']) ? htmlspecialchars($_SESSION['form_data']['username']) : ''; ?>" required><br>

    <label for="password">Passord:</label>
    <input type="password" name="password" id="password" value="<?php echo isset($_SESSION['form_data']['password']) ? htmlspecialchars($_SESSION['form_data']['password']) : ''; ?>" required><br>

    <label for="firstName">Fornavn:</label>
    <input type="text" name="firstName" id="firstName" value="<?php echo isset($_SESSION['form_data']['firstName']) ? htmlspecialchars($_SESSION['form_data']['firstName']) : ''; ?>" required><br>

    <label for="lastName">Etternavn:</label>
    <input type="text" name="lastName" id="lastName" value="<?php echo isset($_SESSION['form_data']['lastName']) ? htmlspecialchars($_SESSION['form_data']['lastName']) : ''; ?>" required><br>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>" required><br>

    <label for="phone">Telefon:</label>
    <input type="text" name="phone" id="phone" value="<?php echo isset($_SESSION['form_data']['phone']) ? htmlspecialchars($_SESSION['form_data']['phone']) : ''; ?>" required><br>

    <label for="address">Addresse:</label>
    <input type="text" name="address" id="address" value="<?php echo isset($_SESSION['form_data']['address']) ? htmlspecialchars($_SESSION['form_data']['address']) : ''; ?>" required><br>

    <label for="postalCode">Postnummer:</label>
    <input type="text" name="postalCode" id="postalCode" value="<?php echo isset($_SESSION['form_data']['postalCode']) ? htmlspecialchars($_SESSION['form_data']['postalCode']) : ''; ?>" required><br>

    <!-- Hidden field for role -->
    <input type="hidden" name="role" value="user">

    <button type="submit">Registrer deg</button>
</form>
</body>
</html>
