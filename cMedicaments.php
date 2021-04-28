<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
  <title> Intranet du Laboratoire Galaxy-Swiss Bourdin </title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  
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


  $req = "SELECT *
          FROM medicament";


$listeUser = mysqli_query($idConnexion, $req);
$donnees = mysqli_fetch_row($listeUser);
$result = $idConnexion->query($req);
?>

  <!-- Division principale -->
<div id="contenu">
    <h2> Bienvenue sur l'application Web GSB </h2>
    <div class="container">

    <center><h4">Liste des Médicaments</h4></center>
    <br>
    <br>
   

          <table id="tableaumedicament">
            <thead>
    <tr>
      <th scope="col">Code</th>
      <th scope="col">Nom Commercial</th>
      <th scope="col">Famille</th>
      <th scope="col">Composition</th>
      <th scope="col">Effet indésirable</th>
      <th scope="col">Contre Indication</th>
      <th scope="col">Prix</th>
    </tr>
  </thead>

<?php 
            if($result->num_rows > 0) {
                 while($donnees = $result->fetch_assoc()) {
                        $userN = $donnees["codeM"];
                        $userP = $donnees["nomCommercial"];
                        $userID = $donnees["famille"];
                        $userA = $donnees["compo"];
                        $userT = $donnees["effetIndesi"];
                        $userZ = $donnees["contreIndic"];
                        $userE = $donnees["prix"];
?>

  <tbody>
        <tr>
          <td><center><?php echo $userN; ?><center></td>
          <td><?php echo $userP; ?></td>
          <td><?php echo $userID; ?></td>
          <td><?php echo $userA; ?></td>
          <td><?php echo $userT; ?></td>
          <td><?php echo $userZ; ?></td>
          <td><?php echo $userE; ?>€</td>
        </tr>
  </tbody>

  <?php
    }
} else {
    echo 'NO RESULTS';  
}
?>
</table>

    <br>
    <br>
    <button><a href="cAccueil.php" title="Page d'accueil">FERMER</a></button>
    </div>
</div>

<?php
    require($repInclude . "_pied.inc.html");
    require($repInclude . "_fin.inc.php");
?>