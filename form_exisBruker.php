<?php
/*
Lag et script som viser en brukerprofil basert på innholdet i en matrise. For å gjøre dette må du lage en
matrise med den nødvendige informasjonen i begynnelsen. Visningen skal gjøres i form av et skjema
som inneholder den eksisterende informasjonen om brukeren. Dersom skjemaet sendes, skal den nye
informasjonen lagres i matrisen. Løsningen er ekstra elegant dersom du først sjekker om det er gjort
noen endringer før informasjonen skrives til matrisen. Brukeren må få beskjed om at brukeroppføringen
er endret. Det er samme krav til validering, feilmeldingsjekk og feilmeldinger som i forrige oppgave

DENNE KODEN OPPDATERER INFORMASJONEN TIL EN EKSISTERENDE BRUKER, 
MATRISEN $brukerprofil MÅ ENDRES SÅ DEN HENTER INN DATA FRA ALLEREDE LAGRET BRUKER I DATABASE

*/





// Initialiser brukerprofilen --------------------------------------------- DENNE MÅ ENDRES, SE CAPS-CASE OVER
$brukerprofil = array(
    'fornavn' => "Anders",
    'etternavn' => "Eriksen",
    'epost' => "anders.eriksen@email.com",
    'telefonnr' => "12345678",
    'addresse' => "Lamåsen 12",
    'postnummer' => "1337"
);

function vask($variabel)
{
    $variabel = strip_tags($variabel);
    $variabel = htmlentities($variabel);
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

# Kontrollerer at requestmetoden er POST, og deretter vasker samt fjerner mellomrom på data fra skjema, lagres i variabler
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fornavn = trim(vask($_POST['fornavn']));
    $etternavn = trim(vask($_POST['etternavn']));
    $epost = trim(vask($_POST['epost']));
    $telefonnr = trim(vask($_POST['telefonnr']));
    $addresse = trim(vask($_POST['addresse']));
    $postnr = trim(vask($_POST['postnummer']));

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
            // Oppdater brukerprofilen med de nye verdiene
            $brukerprofil['fornavn'] = $fornavn_formatert;
            $brukerprofil['etternavn'] = $etternavn_formatert;
            $brukerprofil['epost'] = $epost;
            $brukerprofil['telefonnr'] = $telefonnr;
            $brukerprofil['addresse'] = $addresse_formatert;
            $brukerprofil['postnummer'] = $postnr;

            // Meld at oppføringen er endret
            $melding = 'Brukeroppføringen er endret.';
        } else {
            $melding = 'Ingen endringer ble gjort.';
        }

        // Vis den oppdaterte informasjonen
        $registreringsmelding = 'Den nye brukeren er registrert. Her er den oppdaterte informasjonen:';
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
    if (!empty($registreringsmelding)) {
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


