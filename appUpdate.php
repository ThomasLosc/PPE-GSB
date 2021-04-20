<?php
$repInclude = './include/';
require($repInclude . "_init.inc.php");

// &NumRapport=1
// &Practiciens=Forax+Thomas
// &valIdPracti=1
// &dateRapport=2020-12-13
// &motifVisite=Sollicitation+de+la+part+des+Médecins
// &valMotifVisi=4
// &BilanTx=Pas+terrible%2C+voire+Inutbyyile%21

    // ID du compte-rendu
$numRapport = $_GET["NumRapport"];

    // ID du praticien
$valIdPracti = $_GET["valIdPracti"];
    
    // ID du compte-rendu
$dateRapport = $_GET["dateRapport"];

    // Text du motif visite + Son ID
$motifVisite = $_GET["motifVisite"];
$valMotifVisi = $_GET["valMotifVisi"];

    // Text du Textarea Bilan
$bilanTx = $_GET["BilanTx"];

$reqUpd = "UPDATE compte_rendu SET dateRapport = '". $dateRapport ."', 
                            bilan = '". $bilanTx ."', 
                            idPract = ". $valIdPracti .",
                            idMotif = ". $valMotifVisi ."
            WHERE idCompRendu = ". $numRapport .";";

mysqli_query($idConnexion, $reqUpd);

header("Location: appAcceuil.php");

?>