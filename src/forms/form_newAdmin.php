<?php
include '../../src/resources/inc/setupdb/setup.php';
require_once '../func/header.php';
require_once '../func/security.php';
ensureAdmin(); // Ensure the user is an admin
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrer ny bruker eller administrator</title> <!-- Page title -->
    <link rel="stylesheet" href="../../public/assets/css/style.css"> <!-- Link to stylesheet -->
</head>
<body>
<div class="container">
    <h1>Registrer ny bruker eller administrator</h1> <!-- Page header -->

    <!-- Registration form for new users or administrators -->
    <form name="registerUserOrAdmin" action="../../src/controllers/registerController.php" method="POST" class="user-form">
        <label for="username">Brukernavn *</label>
        <input type="text" id="username" name="username" placeholder="Brukernavn" required> <!-- Username field -->

        <label for="password">Passord *</label>
        <input type="password" id="password" name="password" placeholder="Passord" required> <!-- Password field -->

        <label for="firstName">Fornavn *</label>
        <input type="text" id="firstName" name="firstName" placeholder="Fornavn" required> <!-- First name field -->

        <label for="lastName">Etternavn *</label>
        <input type="text" id="lastName" name="lastName" placeholder="Etternavn" required> <!-- Last name field -->

        <label for="email">E-post *</label>
        <input type="email" id="email" name="email" placeholder="E-post" required> <!-- Email field -->

        <label for="phone">Telefonnummer</label>
        <input type="number" id="phone" name="phone" placeholder="Telefonnummer (valgfritt)"> <!-- Optional phone number field -->

        <label for="address">Adresse *</label>
        <input type="text" id="address" name="address" placeholder="Gateadresse og nummer" required> <!-- Address field -->

        <label for="postalCode">Postnummer *</label>
        <input type="number" id="postalCode" name="postalCode" placeholder="Postnummer" required> <!-- Postal code field -->

        <label for="role">Rolle *</label>
        <select id="role" name="role" required> <!-- Role selection -->
            <option value="user">Bruker</option>
            <option value="admin">Administrator</option>
        </select>

        <button type="submit" name="register">Registrer</button> <!-- Submit button -->
    </form>
</div>
</body>
</html>
