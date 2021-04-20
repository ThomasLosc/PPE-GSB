<?php
/** 
 * Script de contrôle et d'affichage du cas d'utilisation "Consulter une fiche de frais"
 * @package default
 * @todo  RAS
 */
  $repInclude = './include/';
  require($repInclude . "_init.inc.php");

  // page inaccessible si visiteur non connecté
  if ( !estVisiteurConnecte() || $_SESSION["numPoste"] != 1) {
    header("Location: cSeConnecter.php");  
  }

  require($repInclude . "_entete.inc.html");
  require($repInclude . "_sommaire.inc.php");
  
    if(isset($_POST["etape"])){
        $etape=$_POST["etape"];
    }
    else{
        $etape="ARR";
    }
  
    if($etape=="validerConsult" || $etape=="remboursee"){
            $moisSaisi=$_POST["lstMois"];
            $unIdCli=$_POST["IdentifCli"];
    }
	  
	if($etape=="remboursee"){
		$unEtat="RB";
		modifierEtatFicheFrais($idConnexion, $moisSaisi, $unIdCli, $unEtat);
	}
  ?>
  
   <!-- Division principale -->
  <div id="contenu">
   
   <h2>Suivie des frais</h2>
<?php
        if($etape=="ARR"){
?>
        <!-- Choix visiteur -->
        <h3>Visiteur à sélectionner : </h3>
        <form action="cSuiviePaiement.php" method="post">
        <div class="corpsForm">
          
        <p>
            <label for="IdentifCli">Visiteur : </label>
            <select id="IdentifCli" name="IdentifCli" title="Sélectionnez le Visiteur souhaité pour la fiche de frais">
<?php
                $reqListeVisiteur="SELECT id, nom, prenom FROM visiteur WHERE numPoste = 0 ORDER BY nom;";
                $ListeVisiteur = mysqli_query($idConnexion,$reqListeVisiteur);  
                $ListeVisiteur2 = mysqli_fetch_row($ListeVisiteur);
                $result = $idConnexion->query($reqListeVisiteur); 
                
                if ( $result->num_rows>0){
                    while($ListeVisiteur2=$result->fetch_assoc()){
                        
                        $NomListedelaVisiteur = $ListeVisiteur2["nom"];
                        $PrenomListedelaVisiteur = $ListeVisiteur2["prenom"];
                        $IDListedelaVisiteur = $ListeVisiteur2["id"];
?>
                    <option value="<?php echo $IDListedelaVisiteur ?>"><?php echo $NomListedelaVisiteur. " / " . $PrenomListedelaVisiteur; ?> </option>
<?php
                    }
                }
?>
            </select>
        </p>
    </div>
    <div class="piedForm">
        <p>
            <input type="hidden" name="etape" value="ChoixVisiteur" />
            <input id="ok" type="submit" value="Valider" size="20"
                title="Demandez à consulter ce visiteur" />
            <input id="annuler" type="reset" value="Effacer" size="20" />
        </p> 
    </div>
        
    </form>
<?php
		}
		else if($etape=="ChoixVisiteur") {
		$unIdCli=$_POST["IdentifCli"];
?>
        <h3>mois à sélectionner : </h3>
		<form action="cSuiviePaiement.php" method="post">
            <div class="corpsForm">
                <p>
                <label for="mois">mois : </label>
                <select id="lstMois" name="lstMois" title="Sélectionnez le mois souhaité pour la fiche de frais">
                    <?php
                        // on propose tous les mois pour lesquels le visiteur a une fiche de frais
                        $req = "SELECT mois FROM FicheFrais 
                            WHERE idvisiteur ='". $unIdCli . "' AND idEtat = 'VA' 
                            ORDER BY mois desc;";
                    
                        $idJeuMois = mysqli_query($idConnexion,$req);
                        $lgMois = mysqli_fetch_assoc($idJeuMois);
                        $test=$idConnexion->query($req);
                        if($test->num_rows==0){
                            ?>    
                        <option> <?= "pas de fiche de frais pour ce visiteur"; ?> </option>
                            <?php
                        }
                        while ( is_array($lgMois) ) {
                            $mois = $lgMois["mois"];
                            $noMois = intval(substr($mois, 4, 2));
                            $annee = intval(substr($mois, 0, 4));
                            ?>    
                            <option value="<?= $mois; ?>"><?= obtenirLibelleMois($noMois) . " " . $annee; ?></option>
                            <?php
                            $lgMois = mysqli_fetch_assoc($idJeuMois);        
                        }
                        mysqli_free_result($idJeuMois);
                    ?>
                </select>
            </p>
		<input type="hidden" name="etape" value="validerConsult" />
		<input type="hidden" name="IdentifCli" value="<?php echo $unIdCli ?>"/>
		
		<div class="piedForm">
            <p>
            <input id="ok" type="submit" value="Valider" size="20"
                    title="Demandez à consulter ce visiteur" />
                <input id="annuler" type="reset" value="Effacer" size="20" />
	  
<?php 
	    if($test->num_rows==0){
?>
		        <input type="hidden" name="etape" value="ARR" />

                <?php } ?>
            </p>
        </div>
      
	    </form>
        <?php 
        }
    
	else if($etape=="validerConsult"||$etape=="remboursee") {
		
		$montantTOTAL=0;
		$tabFicheFrais = obtenirDetailFicheFrais($idConnexion, $moisSaisi, $unIdCli);
?>
    <h3>Fiche de frais du mois de <?php echo obtenirLibelleMois(intval(substr($moisSaisi,4,2))) . " " . substr($moisSaisi,0,4); ?> : 
    <em><?php echo $tabFicheFrais["libelleEtat"]; ?> </em>
    depuis le <em><?php echo $tabFicheFrais["dateModif"]; ?></em></br>
	</h3>
    
    <h2>
        Montant validé : <?php echo $tabFicheFrais["montantValide"]; echo "€"; ?>              
    </h2>
<?php          
    // demande de la requête pour obtenir la liste des éléments 
    // forfaitisés du visiteur connecté pour le mois demandé
    $req = obtenirReqEltsForfaitFicheFrais2($idConnexion,$moisSaisi,$unIdCli );
    $idJeuEltsFraisForfait = mysqli_query($idConnexion,$req);
    echo mysqli_error($idConnexion);
    $lgEltForfait = mysqli_fetch_assoc($idJeuEltsFraisForfait);
    // parcours des frais forfaitisés du visiteur connecté
    // le stockage intermédiaire dans un tableau est nécessaire
    // car chacune des lignes du jeu d'enregistrements doit être doit être
    // affichée au sein d'une colonne du tableau HTML
    $tabEltsFraisForfait = array();
    while ( is_array($lgEltForfait) ) {
        $tabEltsFraisForfait[$lgEltForfait["libelle"]] = $lgEltForfait["quantite"] *$lgEltForfait["montant"] ;
        $lgEltForfait = mysqli_fetch_assoc($idJeuEltsFraisForfait);
    }
    mysqli_free_result($idJeuEltsFraisForfait);
?>
  	<table class="listeLegere">
  	   <caption>Montant des éléments forfaitisés</caption>
        <tr>
            <?php
            // premier parcours du tableau des frais forfaitisés du visiteur connecté
            // pour afficher la ligne des libellés des frais forfaitisés
            foreach ( $tabEltsFraisForfait as $unLibelle => $montant ) {
            ?>
                <th><?php echo $unLibelle ; ?></th>
            <?php
            }
            ?>
        </tr>
        <tr>
            <?php
            // second parcours du tableau des frais forfaitisés du visiteur connecté
            // pour afficher la ligne des quantités des frais forfaitisés
            foreach ( $tabEltsFraisForfait as $unLibelle => $montant ) {
            ?>
                <td class="qteForfait"><?php echo "$montant €" ; ?></td>
            <?php
			$montantTOTAL=$montantTOTAL+$montant;
            }
            
            ?>
        </tr>
    </table>
	<?php 
	echo "<h2>Le total est de ". $montantTOTAL." € </h2>" ;
	?>
	
	<?php
	$req = obtenirReqEltsForfaitFicheFrais2($idConnexion,$moisSaisi,$unIdCli );
            $idJeuEltsFraisForfait2 = mysqli_query($idConnexion,$req);
            echo mysqli_error($idConnexion);
	$lgEltForfait2 = mysqli_fetch_assoc($idJeuEltsFraisForfait2);
            // parcours des frais forfaitisés du visiteur connecté
            // le stockage intermédiaire dans un tableau est nécessaire
            // car chacune des lignes du jeu d'enregistrements doit être doit être
            // affichée au sein d'une colonne du tableau HTML
            $tabEltsFraisForfait2 = array();
            while ( is_array($lgEltForfait2) ) {
                $tabEltsFraisForfait2[$lgEltForfait2["libelle"]] = $lgEltForfait2["quantite"]  ;
                $lgEltForfait2 = mysqli_fetch_assoc($idJeuEltsFraisForfait2);
            }
            mysqli_free_result($idJeuEltsFraisForfait2);
			?>
	<table class="listeLegere">
  	   <caption>Quantités des éléments forfaitisés</caption>
        <tr>
            <?php
            // premier parcours du tableau des frais forfaitisés du visiteur connecté
            // pour afficher la ligne des libellés des frais forfaitisés
            foreach ( $tabEltsFraisForfait2 as $unLibelle2 => $quantite2 ) {
            ?>
                <th><?php echo $unLibelle2 ; ?></th>
            <?php
            }
            ?>
        </tr>
        <tr>
            <?php
            // second parcours du tableau des frais forfaitisés du visiteur connecté
            // pour afficher la ligne des quantités des frais forfaitisés
            foreach ( $tabEltsFraisForfait2 as $unLibelle2 => $quantite2 ) {
            ?>
                <td class="qteForfait"><?php echo $quantite2 ; ?></td>
            <?php
            }
            
			
            ?>
               
            
        </tr>
    </table>
		
	<div style="float:left">
  	<table class="listeLegere">
  	   <caption>Descriptif des éléments hors forfait - <?php echo $tabFicheFrais["nbJustificatifs"]; ?> justificatifs reçus -
       </caption>
             <tr>
                <th class="date">Date</th>
                <th class="libelle">Libellé</th>
                <th class="montant">Montant</th> 
								
             </tr>
<?php          
            // demande de la requête pour obtenir la liste des éléments hors
            // forfait du visiteur connecté pour le mois demandé
            $req = obtenirReqEltsHorsForfaitFicheFrais($idConnexion,$moisSaisi, $unIdCli);
            $idJeuEltsHorsForfait = mysqli_query($idConnexion,$req);
            $lgEltHorsForfait = mysqli_fetch_assoc($idJeuEltsHorsForfait);
            
            // parcours des éléments hors forfait 
            while ( is_array($lgEltHorsForfait) ) {
            ?>
                <tr>
                   <td><?php echo $lgEltHorsForfait["date"] ; ?></td>
                   <td><?php echo filtrerChainePourNavig($lgEltHorsForfait["libelle"]) ; ?></td>
                   <td><?php echo $lgEltHorsForfait["montant"] ; 
				   
				   
				   ?></td>
				  
                </tr>
            <?php
                $lgEltHorsForfait = mysqli_fetch_assoc($idJeuEltsHorsForfait);
            }
            mysqli_free_result($idJeuEltsHorsForfait);
  ?>
    </table>
	</div>
	<div style="float:left; width:98%; ">
	</div>
	
			<?php 
				//boutton retour choix
			?>
			<div style="float:right">
			<form action="cSuiviePaiement.php" method="post">
				<input type="hidden" name="etape" value="ARR" />
				<input id="ok" type="submit" value="Retour" size="20"
				   title="Demandez à retourner choix du visiteur" />
			</form>
			</div>
			<?php //valider 
			
			?>
			
			
			<div style="float:right">
				
				<form action="cSuiviePaiement.php" method="post">
					
					<input type="hidden" name="IdentifCli" value="<?php echo $unIdCli ?>" />
					<input type="hidden" name="lstMois" value="<?php echo $moisSaisi ?>" />
					<input type="hidden" name="etape" value="remboursee" />
					
					<input id="ok" type="submit" value="remboursée" size="20"
					   title="Demandez valider fiche de frais" />
				</form>
			</div>
  
<?php
      
		}
		?>
	<!--------fin choix ---------------------------------------------------------------------------------------------------------------------------------->
		
  </div>

<?php        
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?> 