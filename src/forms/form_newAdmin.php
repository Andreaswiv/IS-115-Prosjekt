<?php
include '../../src/assets/inc/setupdb/setup.php';
require_once '../func/header.php';

?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrer ny bruker eller administrator</title>
    <link rel="stylesheet" href="../../public/assets/css/table.css">
</head>
<body>
<div class="container">
    <h1>Registrer ny bruker eller administrator</h1>

    <form name="registerUserOrAdmin" action="../../src/controllers/registerController.php" method="POST" class="user-form">
        <label for="username">Brukernavn *</label>
        <input type="text" id="username" name="username" placeholder="Brukernavn" required>

        <label for="password">Passord *</label>
        <input type="password" id="password" name="password" placeholder="Passord" required>

        <label for="firstName">Fornavn *</label>
        <input type="text" id="firstName" name="firstName" placeholder="Fornavn" required>

        <label for="lastName">Etternavn *</label>
        <input type="text" id="lastName" name="lastName" placeholder="Etternavn" required>

        <label for="email">E-post *</label>
        <input type="email" id="email" name="email" placeholder="E-post" required>

        <label for="phone">Telefonnummer</label>
        <input type="number" id="phone" name="phone" placeholder="Telefonnummer (valgfritt)">

        <label for="address">Adresse *</label>
        <input type="text" id="address" name="address" placeholder="Gateadresse og nummer" required>

        <label for="postalCode">Postnummer *</label>
        <input type="number" id="postalCode" name="postalCode" placeholder="Postnummer" required>

        <label for="role">Rolle *</label>
        <select id="role" name="role" required>
            <option value="user">Bruker</option>
            <option value="admin">Administrator</option>
        </select>

        <button type="submit" name="register">Registrer</button>
    </form>
</div>
</body>
</html>
