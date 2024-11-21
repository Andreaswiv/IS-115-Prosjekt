<?php

// Initialiser PDO-forbindelse
include 'inc/db_connect.php';

// Hent brukerdata fra databasen (bruker ID som eksempel)
$bruker_id = 1; // Definer hvordan bruker-ID skal identifiseres
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $bruker_id, PDO::PARAM_INT);
$stmt->execute();

$bruker = $stmt->fetch(PDO::FETCH_ASSOC);
if ($bruker) {
    $brukerprofil = array(
        'fornavn' => $bruker['firstName'],
        'etternavn' => $bruker['lastName'],
        'epost' => $bruker['email'],
        'telefonnr' => $bruker['phone'],
        'addresse' => $bruker['address'],
        'postnummer' => $bruker['postnummer']
    );
} else {
    echo "Ingen bruker funnet med ID $bruker_id.";
    $brukerprofil = array(
        'fornavn' => "",
        'etternavn' => "",
        'epost' => "",
        'telefonnr' => "",
        'addresse' => "",
        'postnummer' => ""
    );
}



function vask($variabel)
{
    $variabel = strip_tags($variabel);
    $variabel = htmlentities($variabel);
    $variabel = trim($variabel);
    return $variabel;
}

$feilmeldinger = []; // Definerer en tom matrise for feilmeldinger til bruker
$endringer = []; // Definerer en tom matrise for endringsmeldinger til bruker
$melding = ""; 
$registreringsmelding = "";

# Definerer variablene for skjemaet
$fornavn = $brukerprofil['fornavn'];
$etternavn = $brukerprofil['etternavn'];
$epost = $brukerprofil['epost'];
$telefonnr = $brukerprofil['telefonnr'];
$addresse = $brukerprofil['addresse'];
$postnr = $brukerprofil['postnummer'];

# Kontrollerer at requestmetoden er POST, og deretter renser data med func vask()
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fornavn = vask($_POST['fornavn']);
    $etternavn = vask($_POST['etternavn']);
    $epost = vask($_POST['epost']);
    $telefonnr = vask($_POST['telefonnr']);
    $addresse = vask($_POST['addresse']);
    $postnr = vask($_POST['postnummer']);

    // Validerer input, legger evt melding inn i $feilmeldinger ved behov
    if (empty($fornavn)) {
        $feilmeldinger[] = "Påkrevet felt: Fornavn kan ikke være tomt.";
    }
    if (empty($etternavn)) {
        $feilmeldinger[] = "Påkrevet felt: Etternavn kan ikke være tomt.";
    }
    if (empty($epost)) {
        $feilmeldinger[] = "Påkrevet felt: E-post kan ikke være tom.";
    } elseif (!filter_var($epost, FILTER_VALIDATE_EMAIL)) {
        $feilmeldinger[] = 'Ugyldig e-postadresse.';
    }
    if (empty($telefonnr)) {
        $feilmeldinger[] = "Påkrevet felt: Telefonnummer kan ikke være tomt.";
    } elseif (strlen($telefonnr) != 8) {
        $feilmeldinger[] = "Telefonnummer må være 8 siffer langt.";
    }
    if (empty($addresse)){
        $feilmeldinger[] = "Påkrevet felt: Addresse kan ikke være tomt.";
    }
    if (empty($postnr)){
        $feilmeldinger[] = "Påkrevet felt: Postnummer kan ikke være tomt.";
    }else if (strlen($postnr) != 4){
        $feilmeldinger[] = "Postnummer må være 4 siffer langt.";
    }

    // Hvis ingen valideringsfeilmeldinger, fortsett
    if (empty($feilmeldinger)) {
        $endringer_gjort = false;

        // Formater fornavn, etternavn og adresse
        $fornavn_formatert = mb_convert_case(mb_strtolower($fornavn, 'UTF-8'), MB_CASE_TITLE, "UTF-8");
        $etternavn_formatert = mb_convert_case(mb_strtolower($etternavn, 'UTF-8'), MB_CASE_TITLE, "UTF-8");
        $addresse_formatert = mb_convert_case(mb_strtolower($addresse, 'UTF-8'), MB_CASE_TITLE, "UTF-8");

        // Sammenlign hvert felt og registrer endringer
        if ($fornavn_formatert != $brukerprofil['fornavn']) {
            $endringer['Fornavn'] = array('gammel' => $brukerprofil['fornavn'], 'ny' => $fornavn_formatert);
            $endringer_gjort = true;
        }
        if ($etternavn_formatert != $brukerprofil['etternavn']) {
            $endringer['Etternavn'] = array('gammel' => $brukerprofil['etternavn'], 'ny' => $etternavn_formatert);
            $endringer_gjort = true;
        }
        if ($epost != $brukerprofil['epost']) {
            $endringer['E-post'] = array('gammel' => $brukerprofil['epost'], 'ny' => $epost);
            $endringer_gjort = true;
        }
        if ($telefonnr != $brukerprofil['telefonnr']) {
            $endringer['Telefonnummer'] = array('gammel' => $brukerprofil['telefonnr'], 'ny' => $telefonnr);
            $endringer_gjort = true;
        }
        if ($addresse_formatert != $brukerprofil['addresse']) {
            $endringer['Adresse'] = array('gammel' => $brukerprofil['addresse'], 'ny' => $addresse_formatert);
            $endringer_gjort = true;
        }
        if ($postnr != $brukerprofil['postnummer']) {
            $endringer['Postnummer'] = array('gammel' => $brukerprofil['postnummer'], 'ny' => $postnr);
            $endringer_gjort = true;
        }

        if ($endringer_gjort) {
            // Oppdater databasen med de nye verdiene
            try {
                $updateStmt = $conn->prepare("
                    UPDATE users SET 
                        firstName = :firstName, 
                        lastName = :lastName, 
                        email = :email, 
                        phone = :phone, 
                        address = :address, 
                        postnummer = :postnummer 
                    WHERE id = :id
                ");
                $updateStmt->bindParam(':firstName', $fornavn_formatert);
                $updateStmt->bindParam(':lastName', $etternavn_formatert);
                $updateStmt->bindParam(':email', $epost);
                $updateStmt->bindParam(':phone', $telefonnr);
                $updateStmt->bindParam(':address', $addresse_formatert);
                $updateStmt->bindParam(':postnummer', $postnr);
                $updateStmt->bindParam(':id', $bruker_id, PDO::PARAM_INT);
                $updateStmt->execute();

                  // Hent oppdatert informasjon
                $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
                $stmt->bindParam(':id', $bruker_id, PDO::PARAM_INT);
                $stmt->execute();
                $bruker = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($bruker) {
                  $brukerprofil = array(
                  'fornavn' => $bruker['firstName'],
                  'etternavn' => $bruker['lastName'],
                  'epost' => $bruker['email'],
                  'telefonnr' => $bruker['phone'],
                  'addresse' => $bruker['address'],
                  'postnummer' => $bruker['postnummer']
                     );
                 }
        
                // Meld at oppføringen er endret
                $melding = 'Brukeroppføringen er oppdatert i databasen.';
            } catch (PDOException $e) {
                $feilmeldinger[] = "Feil ved oppdatering av databasen: " . $e->getMessage();
            }
        } else {
            $melding = 'Ingen endringer ble gjort.';
            $endringer_gjort = false;
        }

        // Vis den oppdaterte informasjonen
        $registreringsmelding = 'Oppdatert informasjon er registrert:';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Brukerprofil</title>
</head>
<body>
    <h1>Brukerprofil</h1>

    <?php
    // Vis feilmeldinger
    if (!empty($feilmeldinger)) {
        echo '<ul>';
        foreach ($feilmeldinger as $feil) {
            echo '<li>' . htmlspecialchars($feil) . '</li>';
        }
        echo '</ul>';
    }

    // Vis melding til brukeren
    if (!empty($melding)) {
        echo '<p">' . htmlspecialchars($melding) . '</p>';
    }

    // Vis oppdatert brukerprofil hvis registrert
    if (!empty($registreringsmelding) && $endringer_gjort) {
        echo '<p>' . htmlspecialchars($registreringsmelding) . '</p>';
        echo '<ul>';
        foreach ($brukerprofil as $key => $value) {
            echo '<li>' . htmlspecialchars(ucfirst($key)) . ': ' . htmlspecialchars($value) . '</li>';
        }
        echo '</ul>';
    }
    ?>

    <form method="post" action="">
        <label for="fornavn">Fornavn:</label><br>
        <input type="text" id="fornavn" name="fornavn" value="<?php echo htmlspecialchars($fornavn); ?>"><br><br>

        <label for="etternavn">Etternavn:</label><br>
        <input type="text" id="etternavn" name="etternavn" value="<?php echo htmlspecialchars($etternavn); ?>"><br><br>

        <label for="epost">E-post:</label><br>
        <input type="text" id="epost" name="epost" value="<?php echo htmlspecialchars($epost); ?>"><br><br>

        <label for="telefonnr">Telefonnummer:</label><br>
        <input type="text" id="telefonnr" name="telefonnr" value="<?php echo htmlspecialchars($telefonnr); ?>"><br><br>

        <label for="addresse">Adresse:</label><br>
        <input type="text" id="addresse" name="addresse" value="<?php echo htmlspecialchars($addresse); ?>"><br><br>

        <label for="postnummer">Postnummer:</label><br>
        <input type="text" id="postnummer" name="postnummer" value="<?php echo htmlspecialchars($postnr); ?>"><br><br>

        <input type="submit" value="Oppdater">
    </form>
</body>
</html>



