<?php
/*
DETTE SCRIPTET LAGRER EN NY BRUKER I EN MATRISE
--------------- DETTE MÅ ENDRES -------------- SÅNN AT DATAEN OM NY BRUKER LAGRES I DATABASE ISTEDENFOR
*/

# VIDERE SKAL DETTE FORMET LEDE TIL form_bruker_index.php



# oppretter en funksjon for å vaske input fra bruker
function vask($variabel)
{
    $variabel = strip_tags($variabel);
    $variabel = htmlentities($variabel);

    return $variabel;
}

# validering av brukerdata
$feilmeldinger = [];
$brukerinfo = [];
$bekreftelseGodkjent = true;

# SJEKK AT FELTENE IKKE ER TOMME

#fornavn
$fNavn = vask($_REQUEST['fNavn']);
if($fNavn != ""){
    #fornavn godkjent
    $brukerinfo[] = mb_convert_case(mb_strtolower($fNavn, 'UTF-8'), MB_CASE_TITLE, "UTF-8"); //sikrer stor forbokstav etterfulgt av små bokstaver
    $bekreftelseGodkjent = true;
} else{
    #fornavn ikke godkjent
    $feilmeldinger[] = "Påkrevet felt: Fornavn kan ikke være tomt.";
    $bekreftelseGodkjent = false;
}


#etternavn
$eNavn = vask($_REQUEST['eNavn']);
if($eNavn != ""){
    #etternavn godkjent
    $brukerinfo[] = mb_convert_case(mb_strtolower($eNavn, 'UTF-8'), MB_CASE_TITLE, "UTF-8"); //sikrer stor forbokstav etterfulgt av små bokstaver
    $bekreftelseGodkjent = true;
} else{
    #etternavn ikke godkjent
    $feilmeldinger[] = "Påkrevet felt: Etternavn kan ikke være tomt.";
    $bekreftelseGodkjent = false;
}


# validering formatering email
$email = vask($_REQUEST['email']);
if(empty($email)){
    #epost er tom
    $feilmeldinger[] = "Påkrevet felt: Epost kan ikke være tomt.";
    $bekreftelseGodkjent = false;

} else if(filter_var($email, FILTER_VALIDATE_EMAIL)){
    # epost formattert riktig
    $brukerinfo[] = $email;
    $bekreftelseGodkjent = true;
} else {
    #epost ikke formattert riktig
    $feilmeldinger[] = "Epost ikke formattert riktig.";
    $bekreftelseGodkjent = false;
}


# validering formatering tlfnummer
$tlf = vask($_REQUEST['tlf']);
if($tlf == ""){
    # ikke påkrevet felt tlfnummer ikke registrert
    $brukerinfo[] = "";
    $bekreftelseGodkjent = true;
}
else if(strlen($tlf) == 8){
    # mobilnummer formatert riktig
    $brukerinfo[] = $tlf;
    $bekreftelseGodkjent = true;
} else {
    # mobilnummer ikke formatert riktig
    $feilmeldinger[] = "Telefonnummer må være 8 siffer langt.";
    $bekreftelseGodkjent = false;
}


#addresse
$addr = vask($_REQUEST['addresse']);
if($addr != ""){
    #addresse godkjent
    $brukerinfo[] = mb_convert_case(mb_strtolower($addr, 'UTF-8'), MB_CASE_TITLE, "UTF-8"); //sikrer stor forbokstav etterfulgt av små bokstaver
    $bekreftelseGodkjent = true;
} else{
    #addresse ikke godkjent
    $feilmeldinger[] = "Påkrevet felt: Addresse kan ikke være tomt.";
    $bekreftelseGodkjent = false;
}

#postnummer
$postnr = (int)vask($_REQUEST['zip']);
if(strlen($postnr) == 4){
    #postnummer godkjent
    $brukerinfo[] = $postnr;
    $bekreftelseGodkjent = true;

} else{
    #postnummer ikke godkjent
    $feilmeldinger[] = "Påkrevet felt: Postnummer må bestå av 4 tall.";
    $bekreftelseGodkjent = false;
}





?>


<html>
    <head>

    </head>
    <body>
        <form name="oppg2" action="<?php echo $_SERVER['PHP_SELF']?>">
            <input type="text" name="fNavn" placeholder="Fornavn">*påkrevet<br>
            <input type="text" name="eNavn" placeholder="Etternavn">*påkrevet<br>
            <input type="text" name="email" placeholder="E-post">*påkrevet<br>
            <input type="number" name="tlf" placeholder="Telefonnummer"><br>
            <input type="text" name="addresse" placeholder="Gatenavn og -nummer">*påkrevet<br>
            <input type="number" name="zip" placeholder="Postnummer">*påkrevet<br>
            <input type="submit" name="registrer" value="Registrer"><br>
        </form>
    </body>
</html>




<?php

if($bekreftelseGodkjent === true)
{
    echo "<strong> opprettelsen av bruker er bekreftet</strong>";
    echo "<pre>";
    print_r($brukerinfo);
    echo "</pre>";
} else if($bekreftelseGodkjent === false){
    foreach($feilmeldinger as $melding){
        echo $melding . "<br>";
    }
}
?>
?>