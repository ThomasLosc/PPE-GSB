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
          FROM praticien";

 

 

 


$listeUser = mysqli_query($idConnexion, $req);
$donnees = mysqli_fetch_row($listeUser);
$result = $idConnexion->query($req);
?>

 

 
 

 

  <!-- Division principale -->
<div id="contenu">
    <h2> Bienvenue sur l'application Web GSB </h2>
    <div class="container">

 

 

 

    <center><h3>Liste des Praticiens</h3></center>

 

 

 

    <table id="tableaumedicament">
      <thead>
        <tr>
            <th scope="col">Nom</th>
            <th scope="col">Prenom</th>
            <th style="text-align: center" scope="col">Adresse</th>
            <th style="text-align: center" scope="col">CP</th>
            <th scope="col">Ville</th>
            <th scope="col">Coeff</th>
            <th scope="col">Lieu</th>
        </tr>
      </thead>

 

 

 

        <?php 
            if($result->num_rows > 0) {
                 while($donnees = $result->fetch_assoc()) {
                        $userN = $donnees["nom"];
                        $userP = $donnees["prenom"];
                        $userID = $donnees["idPract"];
                        $userA = $donnees["adresse"];
                        $userT = $donnees["cp"];
                        $userZ = $donnees["ville"];
                        $userE = $donnees["coeffNota"];
                        $userR = $donnees["lieuExercise"];
?>

 

 

 

  <tbody>
        <tr>
          <td><?php echo $userN; ?></td>
          <td><?php echo $userP; ?></td>
          <td><?php echo $userA; ?></td>
          <td><?php echo $userT; ?></td>
          <td><?php echo $userZ; ?></td>
          <td><?php echo $userE; ?></td>
           <td><?php echo $userR; ?></td>
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