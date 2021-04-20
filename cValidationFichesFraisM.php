<?php
/** 
 * Script de contrôle et d'affichage du cas d'utilisation "Consulter une fiche de frais"
 * @package default
 * @todo  RAS
 */
  $repInclude = './include/';
  require($repInclude . "_init.inc.php");
  require($repInclude . "_entete.inc.html");
  require($repInclude . "_sommaire.inc.php");  
  
  // page inaccessible si visiteur non connecté
  if (! estVisiteurConnecte() || $_SESSION["numPoste"] != 1) {
    header("Location: cSeConnecter.php");  
  }

    $req = "SELECT id, nom, prenom 
                FROM visiteur WHERE numPoste=0
                ORDER BY nom";

    $listeUser = mysqli_query($idConnexion, $req);
    $donnees = mysqli_fetch_row($listeUser);
    $result = $idConnexion->query($req);

    if(isset($_POST['aRefuse'])) {
        $idFiche = $_POST['aRefuse'];
        $libelleHF = $_POST['libelleHF'];

        $newLibelle = "REFUSE : ". $libelleHF;
        $newLibelle = substr($newLibelle, 0, 99);

        $sqlR = "UPDATE lignefraishorsforfait SET isRefuse = '1', libelle = '". $newLibelle ."' WHERE id = ".$idFiche.";";

        mysqli_query($idConnexion, $sqlR); //Effectue la req
    }

    if(isset($_POST['fEtape']) && isset($_POST['fKilom']) && isset($_POST['nHotel']) && isset($_POST['rResto'])) {
        $fEtape = $_POST['fEtape'];
        $fKilom = $_POST['fKilom'];
        $nHotel = $_POST['nHotel'];
        $rResto = $_POST['rResto'];

        $idVisi2 = $_SESSION['idVisi'];
        $numMois2 = $_SESSION['idMois'];

        $sqlFE = "UPDATE lignefraisforfait SET quantite = '". $fEtape ."' 
                WHERE idVisiteur = '". $idVisi2 ."' AND mois = '". $numMois2 ."' AND idFraisForfait = 'ETP';";

        $u1 = mysqli_query($idConnexion, $sqlFE); //Effectue la req

        $sqlFK = "UPDATE lignefraisforfait SET quantite = '". $fKilom ."' 
                WHERE idVisiteur = '". $idVisi2 ."' AND mois = '". $numMois2 ."' AND idFraisForfait = 'KM';";

        $u2 = mysqli_query($idConnexion, $sqlFK); //Effectue la req

        $sqlNH = "UPDATE lignefraisforfait SET quantite = '". $nHotel ."' 
                WHERE idVisiteur = '". $idVisi2 ."' AND mois = '". $numMois2 ."' AND idFraisForfait = 'NUI';";

        $u3 = mysqli_query($idConnexion, $sqlNH); //Effectue la req

        $sqlRR = "UPDATE lignefraisforfait SET quantite = '". $rResto ."' 
                WHERE idVisiteur = '". $idVisi2 ."' AND mois = '". $numMois2 ."' AND idFraisForfait = 'REP';";

        $u4 = mysqli_query($idConnexion, $sqlRR); //Effectue la req
    }

?>
    <!-- Division principale -->
<div id="contenu">
    <h2> Suivi des frais </h2>

<?php
    if (isset($_SESSION['idVisi']) && isset($_SESSION['idMois'])) {
//
        
$idVisi = $_SESSION['idVisi'];
$numMois = $_SESSION['idMois'];

$reqVf = "SELECT * FROM lignefraisforfait L
        INNER JOIN fraisforfait F
        ON F.id = L.idFraisForfait
        WHERE L.idVisiteur = '". $idVisi ."' AND L.mois = ". $numMois .";";

$listeVf = mysqli_query($idConnexion, $reqVf); //Effectue la req
$donneesVf = mysqli_fetch_row($listeVf);      // Récupère les résultats et les mets ligne par ligne
$donneesVf2 = mysqli_fetch_row($listeVf);

$resultVf = $idConnexion->query($reqVf);
$resultVf2 = $idConnexion->query($reqVf);
?>

<!-- VALIDATION FICHE FRAIS -->
<h3> Validation Fiche de Frais : </h3>

<?php 
    $lgUser = obtenirDetailVisiteur($idConnexion, $idVisi);
    $nomU = $lgUser['nom'];
    $prenomU = $lgUser['prenom'];

    $noMois = intval(substr($numMois, 4, 2));
    $annee = intval(substr($numMois, 0, 4));

    echo "<h4>Le visiteur sélectionné est : ". $nomU ." ". $prenomU ."<br/>Le mois choisi est : ". obtenirLibelleMois($noMois) ." ". $annee ."</h4>"; 
?>

<h2> Frais au forfait </h2>

<form action="cValidationFichesFraisM.php" method="post">
    <table class="listeLegere">
    <tbody>
    <tr>
<?php 
if($resultVf->num_rows > 0) {
while($donneesVf = $resultVf->fetch_assoc()) {
?>    
<th> <?= $donneesVf['libelle']; ?> </th>
<?php
}
} else {
echo 'NO RESULTS';  
}
?>
        <th> Total </th>
    </tr>
    <tr>
<?php 
    $total = 0;
    if($resultVf2->num_rows > 0) {
        while($donneesVf = $resultVf2->fetch_assoc()) {
        $quantite =  $donneesVf['quantite'];
        $montant =  $donneesVf['montant'];

        $calculQxM = $quantite * $montant;

        $total += $calculQxM;
?>    
        <td> <?= $donneesVf['quantite']; ?> (<?= $calculQxM; ?> €) </td>
<?php
        }
    } else {
        echo 'NO RESULTS';  
    }
?>
        <td> <?= $total; ?> € </td>
    </tr>

    <tr>        
        <td class="qteForfait"> <input style="width: 100px;" type="number" id="fEtape" name="fEtape" required> </td>
        <td class="qteForfait"> <input style="width: 100px;" type="number" id="fKilom" name="fKilom" required> </td>
        <td class="qteForfait"> <input style="width: 100px;" type="number" id="nHotel" name="nHotel" required> </td>
        <td class="qteForfait"> <input style="width: 100px;" type="number" id="rResto" name="rResto" required> </td>
    </tr>
    </tbody>
    </table>
        <input type="submit" value="Valider les modifications">
</form>

<!-- Début Hors Forfait -->
<?php
    $idVisi = $_SESSION['idVisi'];
    $idMois = $_SESSION['idMois'];

    $sqlCount = "SELECT Max(nbJustificatifs) FROM fichefrais WHERE idVisiteur = '". $idVisi ."';";
    
    $listeCount = mysqli_query($idConnexion, $sqlCount); //Effectue la req
    $donneesCount = mysqli_fetch_row($listeCount);      // Récupère les résultats et les mets ligne par ligne
    $resultCount = $idConnexion->query($sqlCount);
    
    $sqlHf = "SELECT * FROM lignefraishorsforfait WHERE idVisiteur = '". $idVisi ."' AND mois = '". $idMois ."';";    

    $listeHf = mysqli_query($idConnexion, $sqlHf); //Effectue la req
    $donneesHf = mysqli_fetch_row($listeHf);      // Récupère les résultats et les mets ligne par ligne
    $resultHf = $idConnexion->query($sqlHf);
?>

<br/>
<h2> Hors Forfait </h2>
    <p> Descriptif des éléments hors forfait - 
<?php 
    if($resultCount->num_rows > 0) {
        while($donneesCount = $resultCount->fetch_assoc()) {
            echo $donneesCount['Max(nbJustificatifs)'];
        }
    }
?> 
    justificatifs reçus </p>

<table class="listeLegere">
    <tbody>
        <tr>
            <th> Date </th>		
            <th> Libellé </th>
            <th> Montant </th>
            <th> Refus </th>
        </tr>

<?php 
    if($resultHf->num_rows > 0) {
        while($donneesHf = $resultHf->fetch_assoc()) {
        ?>
            <tr>
                <td class="qteForfait"> <?= $donneesHf['date']; ?> </td>
                <td class="qteForfait"> <?= $donneesHf['libelle']; ?> </td>
                <td class="qteForfait"> <?= $donneesHf['montant']; ?> </td>
                <td class="qteForfait"> 
                    <?php 
                        if($donneesHf['isRefuse'] == 0) {
                            ?>
                                <form action="cValidationFichesFraisM.php" method="post" style="margin-bottom: 0px">
                                    <input name="aRefuse" type="hidden" value="<?= $donneesHf['id'] ?>">
                                    <input name="libelleHF" type="hidden" value="<?= $donneesHf['libelle'] ?>">
                                    <input id="ok" type="submit" value="REFUSER" size="20" title="Refuser la Fiche"/>
                                </form>
                            <?php
                        }
                        else {
                            echo "<div style='font-weight: bold; color: #0055e3;'> REFUSE </div>";
                        }
                    ?> 
                </td>
            </tr>
        <?php
        }
    }
    else {
?>
        <tr>
            <td class="qteForfait"> XXX </td>
            <td class="qteForfait"> XXX </td>
            <td class="qteForfait"> XXX </td>
            <td class="qteForfait"> XXX </td>
        </tr>
<?php      
    }
?> 
</tbody>
</table>

<form action="cValidationFichesFrais.php" method="post">
    <p> 
        <input type="hidden" name="idMois" value="<?= $numMois; ?>"/>
        <input id="ok" type="submit" value="Retour" size="20" title="Retour au Visiteur Choisi"/>
    </p>
</form>
<!-- FIN VALIDATION FICHE FRAIS -->
<?php
//
    }
    else {

    }
?>

</div>
<?php        
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?> 
