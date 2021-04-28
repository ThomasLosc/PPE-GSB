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


 $nomX = $_GET["nom"];
        $prenomX = $_GET["prenom"];
        $adresseX = $_GET["adresse"];
        $villeX = $_GET["ville"];
        $secteurX = $_GET["secteur"];
        $laboX = $_GET["labo"];


         <form method="post" name="form1">

           <input id="nom" name="nom" value=" <?= $listeVisiteur['id']; ?>">
           <input id="prenom" name="prenom" value" =<? echo $sqlVisiteur['prenom']; ?>">
        <br>
        <br>
           <input id="adresse" name="adresse" value="<? echo $sqlVisiteur['adresse']; ?>" >
        <br>
        <br>
           <input id="ville" name="ville" value="<? echo $sqlVisiteur['ville']; ?>">
        <br>
        <br>
           <input id="secteur" name="secteur" value="<? echo $sqlVisiteur['secteur']; ?>">
        <br>
        <br>
           <input id="labo" name="labo" value="<? echo $sqlVisiteur['labo']; ?>">
        </form>
        
        <br>
        <br>
        <button type="button" id="NPbut" onclick="tryPopulateDoc(-1);"> Précédent </button>
                    
        <button type="button" id="NPbut2" onclick="tryPopulateDoc(1);"> Suivant </button>
    </div>
</div>  