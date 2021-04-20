<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
	<title> Intranet du Laboratoire Galaxy-Swiss Bourdin </title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />

<!-- CSS -->
	<link href="styles/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="styles/style.css" rel="stylesheet" type="text/css" />
	
	<link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico" />
</head>

<?php
	$repInclude = './include/';
	require($repInclude . "_init.inc.php");

	// page inaccessible si visiteur non connecté
	if ( !estVisiteurConnecte() ) 
	{
		header("Location: cSeConnecter.php");  
	}
	require($repInclude . "_entete.inc.html");
	require($repInclude . "_sommaire.inc.php");


    //Récupération des lignes de la table users
        $sqlListUser = $idConnexion->query("SELECT * FROM praticien");
        
    //Initialiser un tableau
        $listeUsers = array();
        
    //Récupérer les lignes
        while ( $row = $sqlListUser->fetch_assoc())  {
           $listeUsers[] = $row;
        }
		
	// Récupération des lignes de la table compte_rendu
		$sqlListCR = $idConnexion->query("SELECT CR.idCompRendu, CR.dateRapport, CR.bilan, P.nom, P.prenom, M.libMotif
										FROM compte_rendu CR
										INNER JOIN praticien P ON P.idPract = CR.idPract
										INNER JOIN motif M ON M.idMotif = CR.idMotif
										WHERE idVisi = 'a17'");
        
    //Initialiser un tableau
        $listeCR = array();
        
    //Récupérer les lignes
        while ( $row = $sqlListCR->fetch_assoc())  {
           $listeCR[] = $row;
        }
		
        $sqlPracti = "SELECT idPract, nom, prenom FROM praticien ORDER BY nom";

            $listePracti = mysqli_query($idConnexion, $sqlPracti);
            $donneesPracti = mysqli_fetch_row($listePracti);
            $resultP = $idConnexion->query($sqlPracti);
        
        
        $sqlMotif = "SELECT * FROM motif";

            $listeMotif = mysqli_query($idConnexion, $sqlMotif);
            $donneesMotif = mysqli_fetch_row($listeMotif);
            $resultM = $idConnexion->query($sqlMotif);

?>
   
	<!-- Division principale -->
<div id="contenu">
    <h2> Bienvenue sur l'application Web GSB </h2>

    <div class="container">
        <form id="formCR" class="mt-4" action="appUpdate.php" method="get">
            <div class="row mt-4">
                <div class="col-sm-3 my-auto">
                    <h6> Numéro du Rapport : </h6>
                </div>
                <div class="col">
                    <input type="text" readonly class="form-control mb-3" id="NumRapport" name="NumRapport">
                </div>
            </div>
    
            <div class="row mt-4">
                <div class="col-sm-3 my-auto pt-2">
                    <h6> Praticien : </h6>
                </div>
                <div class="col-sm-7">
					<input list="Practi" id="Practiciens" name="Practiciens" class="custom-select custom-select-sm">
					<input type="hidden" id="valIdPracti" name="valIdPracti" value="-1">
                    <datalist id="Practi">
<?php
if($resultP->num_rows > 0) {
    while($donneesPracti = $resultP->fetch_assoc()) {
        $userN = $donneesPracti["nom"];
        $userP = $donneesPracti["prenom"];
        $userID = $donneesPracti["idPract"];
?>    
						<option data-value="<?= $userID; ?>" value="<?= $userN ." ". $userP; ?>">
<?php
    }
} else {
    echo 'NO RESULTS';
}
?>  
                    </datalist>
                </div>
                <div class="col-sm-2">
					<button type="button" id="btnDetails" class="btn btn-primary p-0 height-30p pr-1 pl-1"> Détails </button>
                </div>
            </div>
                
            <div class="row mt-4">
                <div class="col-sm-3 my-auto pt-2">
                    <h6> Date Rapport : </h6>
                </div>
                <div class="col">
                    <input type="date" id="dateRapport" name="dateRapport" class="custom-select custom-select-sm" min="2000-01-01" max="2099-12-31">
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-sm-3 my-auto pt-2">
                    <h6> Motif Visite : </h6>
                </div>
                <div class="col">
                    <input list="MotifVisi" id="motifVisite" name="motifVisite" class="custom-select custom-select-sm">
					<input type="hidden" id="valMotifVisi" name="valMotifVisi" value="-1">
                    <datalist id="MotifVisi">
<?php
if($resultM->num_rows > 0) {
    while($donneesMotif = $resultM->fetch_assoc()) {
        $userIdM = $donneesMotif["idMotif"];
        $userLibM = $donneesMotif["libMotif"];
?>
                        <option data-value="<?= $userIdM; ?>" value="<?= $userLibM; ?>">
<?php
    }
} else {
    echo 'NO RESULTS';
}
?> 
                    </datalist>
                </div>
            </div>
                
            <div class="row mt-4">
                <div class="col-sm-3 my-auto pt-2">
                    <h6> Bilan : </h6>
                </div>
                <div class="col">
                    <textarea class="form-control" id="BilanTx" name="BilanTx" form="formCR" rows="3" style="height: 141px;"></textarea>
                </div>
            </div>

            <div class="row pt-4">
				<div class="col-sm">
					<button type="button" id="NPbut" class="btn btn-primary height-30p p-0 pr-1 pl-1 animation0_4s" 
                        onclick="tryPopulateDoc(-1);"> Précédent </button>
					<button type="button" id="NPbut2" class="btn btn-primary height-30p p-0 pr-1 pl-1 animation0_4s" 
                        onclick="tryPopulateDoc(1);"> Suivant </button>
				</div>

                <div class="col-sm-2">
					<button type="button" id="FgoBack" style="opacity:0;" class="btn btn-warning height-30p p-0 pr-1 pl-1 animation0_4s"
                        onclick="deleteNewDoc();"> Annuler </button>
				</div>
                <div class="col-sm-2 mr-2" id="saveForm1">
					<button type="button" class="btn btn-primary height-30p p-0 pr-1 pl-1"
                        onclick="tryPopulateDoc(0);"> Sauvegarder </button>
				</div>
                <div class="col-sm-2 mr-2" id="saveForm2" style="display:none;">
					<button type="button" class="btn btn-primary height-30p p-0 pr-1 pl-1"
                        onclick="saveNewDoc();"> Sauvegarder </button>
				</div>
				<div class="col-sm-2 mr-2">
					<button type="button" class="btn btn-primary height-30p p-0 pr-1 pl-1" 
                        onclick="generateNewDoc();"> Nouveau </button>
				</div>
			<!--	
				<div class="w-100"></div>
				<div class="col-sm offset-sm-10 mt-2">
					<button type="button" class="btn btn-primary height-30p p-0 pr-1 pl-1"> Fermer </button>
				</div> 
			-->
			</div>
        </form>
    </div>
</div>

<?php
    require($repInclude . "_pied.inc.html");
    require($repInclude . "_fin.inc.php");
?>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> 
	<script type="text/javascript" src="styles/js.js"></script>

<script>
    //Afficher le tableau au format JSON
	var jsUsers = <?= json_encode($listeUsers); ?>;
	var jsCR = <?= json_encode($listeCR); ?>;

	$(document).ready(function() {
		$('#btnDetails').popover(
			{
				title: "<h4>Détails du Praticien</h4>", 
				content: function() {
					try {
						var placementUser = getDataListSelectedOption('Practiciens','Practi')-1;
					}
					catch(err) {
						return 'Aucun Praticiens ne correspond!';
					}
					if (placementUser < 0) {
						return 'Aucun Praticiens ne correspond!';
					}
					else {
						return "Numéro :<code> "+ jsUsers[placementUser].idPract +" </code> 	<br/>"
							+"Nom : 	<code> "+ jsUsers[placementUser].nom +" </code>         <br/>"
							+"Prénom : 	<code> "+ jsUsers[placementUser].prenom +" </code>      <br/>"
							+"Adresse : <code> "+ jsUsers[placementUser].adresse +" </code>     <br/>" 
							+"Ville : 	<code> "+ jsUsers[placementUser].cp +" "+ jsUsers[placementUser].ville +" </code>   <br/>" 
							+"Coeff. Notoriété : 	<code> "+ jsUsers[placementUser].coeffNota +" </code>                   <br/>" 
							+"Lieu d'Exercice : 	<code> "+ jsUsers[placementUser].lieuExercise +" </code>                <br/>"
						}
					}, 
				html: true, 
				placement: "left",
				trigger:"focus" 
			}
        );
	});
</script>

    <script type="text/javascript" src="styles/jsAfter.js"></script>
<html>