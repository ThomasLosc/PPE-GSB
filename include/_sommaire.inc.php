<?php
/** 
 * Contient la division pour le sommaire, sujet à des variations suivant la 
 * connexion ou non d'un utilisateur, et dans l'avenir, suivant le type de cet utilisateur 
 * @todo  RAS
 */

?>
    <!-- Division pour le sommaire -->
    <div id="menuGauche">
     <div id="infosUtil">
    <?php      
      if (estVisiteurConnecte() ) {
          $idUser = obtenirIdUserConnecte() ;
          $lgUser = obtenirDetailVisiteur($idConnexion, $idUser);
          $nom = $lgUser['nom'];
          $prenom = $lgUser['prenom'];   
          $libelleU = $lgUser['libelle']; 

          $numP = $lgUser['numP']; 
          $_SESSION["numPoste"] = $numP;

    ?>
        <h2>
    <?php  
            echo $nom . " " . $prenom ;
    ?>
        </h2>
        <h3> <?php echo $libelleU; ?> </h3>        
    <?php
       } 
    ?>  
      </div>  
<?php      
  if (estVisiteurConnecte() ) {
     
?>
        <ul id="menuList">
           <li class="smenu">
              <a href="cAccueil.php" title="Page d'accueil">Accueil</a>
           </li>
           <li class="smenu">
              <a href="cSeDeconnecter.php" title="Se déconnecter">Se déconnecter</a>
           </li>

   <?php 
      if($_SESSION["numPoste"] == 0) { ?>
           <li class="smenu">
              <a href="cSaisieFicheFrais.php" title="Saisie fiche de frais du mois courant">Saisie fiche de frais</a>
           </li>
           <li class="smenu">
              <a href="cConsultFichesFrais.php" title="Consultation de mes fiches de frais">Mes fiches de frais</a>
           </li>
           <li class="smenu">
              <a href="appAcceuil.php" title="Saisie Compte-Rendus"> Saisie Compte-Rendus  </a>
           </li>
   <?php 
      }
      else if($_SESSION["numPoste"] == 1) { ?>
         <li class="smenu">
            <a href="cValidationFichesFrais.php" title="Validation Fiches de Frais">Validation Fiches de Frais</a>
         </li>
         <li class="smenu">
            <a href="cSuiviePaiement.php" title="Suivre Paiements Fiches de Frais"> Suivre Paiements Fiches de Frais </a>
         </li>
         <li class="smenu">
            <a href="cVoirComptesRendus.php" title="Voir Comptes-rendus">Voir Comptes-rendus</a>
         </li>
<?php }
          // affichage des éventuelles erreurs déjà détectées
            if ( nbErreurs($tabErreurs) > 0 ) {
               echo toStringErreurs($tabErreurs) ;
            }
         }
  
        ?>
        </ul>
    </div>
    