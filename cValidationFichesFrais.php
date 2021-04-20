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
?>
    <!-- Division principale -->
<div id="contenu">
    <h2>Suivi des frais</h2>

<?php
    if(isset($_POST['ValiderFF'])) {            // VALIDER LA FICHE
        $idVisiteur = $_POST['idVISI'];
        $idMoisFF = $_POST['idMOIS'];
        $cTotalFF = $_POST['cTOTAL'];

        $sqlValidationFF = "UPDATE fichefrais SET idEtat = 'VA', dateModif = now(), montantValide = '". $cTotalFF ."'
            WHERE idVisiteur = '". $idVisiteur ."' AND mois = '". $idMoisFF ."';";

        $u1 = mysqli_query($idConnexion, $sqlValidationFF); //Effectue la req

        echo "<h2> La fiche de frais à bien été validé! </h2>";
    }

    if(isset($_POST['aReporte'])) {            // REPORTER LA FICHE HF
        $idMoisFF = $_POST['idMoisFF'];
        $idVisiFF = $_POST['idVisiFF'];
        $idFF = $_POST['idFF'];

        $moisSaisiARepot = $idMoisFF+1;
        $NumMois = intval(substr($moisSaisiARepot,4,2));
        $NumAnnee = intval(substr($moisSaisiARepot,0,4));
        
        if ($NumMois == 13) {
            $NumAnnee += 1;
            $NumMois2 = 0;
            $NumMois = 1;
            $moisSaisiARepot = sprintf("%d%d%d", $NumAnnee, $NumMois2, $NumMois);
        }
        
        $existeFicheFrais = existeFicheFrais($idConnexion, $moisSaisiARepot, $idVisiFF);
        
        if(!$existeFicheFrais) {
            ajouterFicheFrais($idConnexion, $moisSaisiARepot, $idVisiFF);
        }
        
        $requete = "UPDATE lignefraishorsforfait SET mois = '". $moisSaisiARepot ."'
                WHERE id ='". $idFF ."';";

        mysqli_query($idConnexion, $requete);
    }

    if (empty($_POST['idVisi']) && empty($_POST['idMois'])) { //Faut <POST> de idVisi ET <POST> de idMois de vide
?>
<!-- CHOIX VISITEUR -->
    <h3>Choisissez le visiteur :</h3>
    
    <form action="cValidationFichesFrais.php" method="post">
    
    <div class="corpsForm">
        <p>
            <label> Visiteur : </label>
            <select id="idVisi" name="idVisi" title="Sélectionnez le visiteur souhaité">
<?php 
if($result->num_rows > 0) {
    while($donnees = $result->fetch_assoc()) {
        $userN = $donnees["nom"];
        $userP = $donnees["prenom"];
        $userID = $donnees["id"];
?>    
    <option value="<?php echo $userID; ?>"> <?php echo $userN ." ". $userP; ?> </option>
<?php
    }
} else {
    echo 'NO RESULTS';  
}
?>
            </select>
        </p>
    </div>
    <div class="piedForm">
        <p>
            <input id="ok" type="submit" value="Valider" size="20"
                    title="Demandez à consulter cette fiche de frais"/>
            <input id="annuler" type="reset" value="Effacer" size="20"/>
        </p> 
    </div>
    </form>
<!-- FIN CHOIX VISITEUR -->
<!-- CHOIX MOIS -->

<?php 
    }
    else if(isset($_POST['idVisi']) && empty($_POST['idMois'])) { //Faut que idVisi existe en <POST> ET idMois vide 

        $_SESSION['idVisi'] = $_POST['idVisi'];
        $idVisi = $_POST['idVisi'];

        $reqM = "SELECT * FROM fichefrais WHERE idVisiteur = '". $idVisi ."' AND idEtat != 'VA';"; //ICICIIIICICIICICICI

        $listeM = mysqli_query($idConnexion, $reqM); //Effectue la req
        $donneesM = mysqli_fetch_row($listeM);      // Récupère les résultats et les mets ligne par ligne

        $resultM = $idConnexion->query($reqM);
?>
<h3>Choisissez le mois :</h3>
    
    <form action="cValidationFichesFrais.php" method="post">
    
    <div class="corpsForm">
        <p>
            <label> Mois : </label>
            <select id="idMois" name="idMois" title="Sélectionnez le mois souhaité">
<?php 
if($resultM->num_rows > 0) {
    while($donneesM = $resultM->fetch_assoc()) {
        $mois = $donneesM["mois"];
        
        $noMois = intval(substr($mois, 4, 2));
        $annee = intval(substr($mois, 0, 4));
?>
    <option value="<?php echo $mois; ?>"> <?= obtenirLibelleMois($noMois). " " .$annee; ?> </option>
<?php
    }
} 
else {
    echo 'Pas de fiche de frais';
}
?>
            </select>
        </p>
    </div>
    <div class="piedForm">
        <p>
            <input id="ok" type="submit" value="Valider" size="20"
                    title="Demandez à consulter cette fiche de frais"/>
            <input id="annuler" type="reset" value="Effacer" size="20"/>
        </p> 
    </div>
    </form>
<!-- FIN CHOIX MOIS -->
<?php
    }
    else if(isset($_SESSION['idVisi']) && isset($_POST['idMois'])) { // Faut idVisi ET idMois d'existant.
        $_SESSION['idMois'] = $_POST['idMois'];
        
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

    echo "<h4>Le visiteur sélectionné est : ". $nomU ." ". $prenomU ."<br/>Le mois choisi est : ". obtenirLibelleMois($noMois). " ". $annee ."</h4>"; 
?>

<h2> Frais au forfait </h2>
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
		</tbody>
    </table>

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
			</tr>

<?php 
    if($resultHf->num_rows > 0) {
        while($donneesHf = $resultHf->fetch_assoc()) {
?>
            <tr>
                <td class="qteForfait"> <?= $donneesHf['date']; ?> </td>
                <td class="qteForfait"> 
                    <?php 
                        
                        if($donneesHf['isRefuse'] == 0) {
                            $total += $donneesHf['montant'];
                        }
                        echo $donneesHf['libelle']; 
                    ?> 
                </td>
                <td class="qteForfait"> <?= $donneesHf['montant']; ?> </td>
                <td class="qteForfait"> 
                    <form action="cValidationFichesFrais.php" method="post" style="margin-bottom: 0px">
                        <input name="idMoisFF" type="hidden" value="<?= $donneesHf['mois']; ?>">
                        <input name="idVisiFF" type="hidden" value="<?= $idVisi; ?>">
                        <input name="idFF" type="hidden" value="<?= $donneesHf['id']; ?>">
                        <input name="idMois" type="hidden" value="<?= $idMois; ?>">
                        <input name="aReporte" type="hidden" value="yes">
                        <input type="submit" value="REPORTER" size="20" title="Reporter la Fiche HF"/>
                    </form> 
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
        </tr>
<?php      
    }
?> 
			
		
        </tbody>
    </table>

<h2>
    Le Montant Total est : <?= $total; ?> €. 
</h2>

    <form action="cValidationFichesFraisM.php" method="post">
        <p> 
            <input type="submit" value="Modifier" title="Modifier les Fiches"/>
        </p>
    </form>

    <form action="cValidationFichesFrais.php" method="post">
        <p>
            <input type="submit" value="Retour" size="20" title="Retour au Choix de Visiteur"/>
        </p>
    </form>

<br/>

    <form action="cValidationFichesFrais.php" method="post">
        <h4>
            <input type="hidden" value="<?= $idVisi ?>" name="idVISI"/>
            <input type="hidden" value="<?= $idMois ?>" name="idMOIS"/>
            <input type="hidden" value="<?= $total ?>" name="cTOTAL"/>
            <input type="submit" name="ValiderFF" value="VALIDER" size="20" title="Valider la fiche de ce Visiteur"/>
        </h4>
    </form>
<!-- FIN VALIDATION FICHE FRAIS -->
<?php
    }
    else {}
?>

</div>
<?php        
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?> 
