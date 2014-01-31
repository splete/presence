
<?php
    require '../secure/auth.php';
    //error_log("accueilTuteur for ".getParam(CK_USER,"UNKOWNN"));

    if (!hasRole(RESP_ROLE) && !hasRole(SECR_ROLE))
        redirectAuth(null);
?>
<html>
    <head>
        <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="../styles/Css_autentification.css" rel="stylesheet" type="text/css" />
        <SCRIPT src="../js/toogleDivs.js" lang="javascript"></SCRIPT>
        <title>Liste presences</title>
    </head>
  <body>
<?php


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once "../dbmngt/connect.php";
require_once "../dbmngt/queries.php";
require_once "../html/utils.php";
require_once "../dao/CalendarDaoFactory.class.php";
require_once "../dao/CatalogueDaoFactory.class.php";

$conn=doConnection();

$formation=getParam("formation","M2ESERVFA");
$offset=getParam("offset",1);

constructFormationSelect($formation);



date_default_timezone_set('Europe/Paris');
$debutMois=mktime(0, 0, 0, date("m")+$offset  , 1, date("Y"));
$finMois=mktime(0,0,0, date("m")+$offset  , 31, date("Y"));
$annee=date("m")<9?date("Y")-1:date("Y");

$_SESSION["daoFactory"]=DaoFactory::createFactory();


$daoFactory=$_SESSION["daoFactory"];


$cat=$daoFactory->createCatalogue($conn,$formation,$annee);

error_log($formation);

if (strcmp($formation,"M1MIAGEFA")==0 || strcmp($formation,"M2MIAGEFA")==0)
  $gCal=$daoFactory->createCalendar($conn,$formation,$offset);
else
  $gCal=$daoFactory->createGCalendar($conn,$formation,date("Y-m-d", $debutMois),date("Y-m-d", $finMois));


$cles=$gCal->getSeancesCles();

sort($cles);

echo "<h1>Imprimer feuilles présences mois prochain (".date("F y",$debutMois).")</h1>";
echo "<table>";
for ($i=0;$i<count($cles);$i++)
{

  $seanceCle=$cles[$i];
  $seanceNom=$cat->getMatiereParCle($cles[$i])->getNom();


  echo "<form id='".$seanceCle."' action='genererListePresenceCal.php' method='POST'>";
  echo "<input type='hidden' name='seanceCle' value=\"".$seanceCle."\"/>";
  echo "<input type='hidden' name='matiereNom' value=\"".$seanceNom."\"/>";
  echo "<input type='hidden' name='offset' value=\"".$offset."\"/>";
  echo "<input type='hidden' name='formationRef' value=\"".$formation."\"/>";
  echo "<input type='hidden' name='groupeRef' value=\"".$formation."1\"/>";
  echo "<input type='hidden' name='debutMois' value=\"".$debutMois."\"/>";
  echo "<input type='hidden' name='finMois' value=\"".$finMois."\"/>";
  

  echo "<tr><td>";
  

  
  echo "<td>$i.</td>";
  echo "<td>$seanceNom</td>";
  echo "<td>";
  
  echo "<input type='submit' name='lister' value='Lister'/>";
  echo "</td></tr>";
   echo "</form>";
}
echo "</table>";

$debutMoisPrecedent=mktime(0, 0, 0, date("m")-$offset  , date("d"), date("Y"));
$finMoisPrecedent=mktime(0,0,0, date("m")-$offset  , 31, date("Y"));


if (strcmp($formation,"M1MIAGEFA")==0 || strcmp($formation,"M2MIAGEFA")==0)
  $gCal=$daoFactory->createCalendar($conn,$formation,-$offset);
else
  $gCal=$daoFactory->createGCalendar($conn,$formation,date("Y-m-d", $debutMoisPrecedent),date("Y-m-d", $finMoisPrecedent));

$cles=$gCal->getSeancesCles();

sort($cles);

echo "<h1>Saisir présences mois dernier (".date("F y",$debutMoisPrecedent).")</h1>";
echo "<table>";
for ($i=0;$i<count($cles);$i++)
{

  $seanceCle=$cles[$i];
  $seanceNom=$cat->getMatiereParCle($cles[$i])->getNom();


  echo "<form id='".$seanceCle."' action='genererListePresenceCal.php' method='POST'>";
  echo "<input type='hidden' name='seanceCle' value=\"".$seanceCle."\"/>";
  echo "<input type='hidden' name='matiereNom' value=\"".$seanceNom."\"/>";
  echo "<input type='hidden' name='offset' value=\"".$offset."\"/>";
  echo "<input type='hidden' name='formationRef' value=\"".$formation."\"/>";
  echo "<input type='hidden' name='groupeRef' value=\"".$formation."1\"/>";
  echo "<input type='hidden' name='debutMois' value=\"".$debutMoisPrecedent."\"/>";
  echo "<input type='hidden' name='finMois' value=\"".$finMoisPrecedent."\"/>";


  echo "<tr><td>";



  echo "<td>$i.</td>";
  echo "<td>$seanceNom</td>";
  echo "<td>";

  echo "<input type='submit' name='saisir' value='Saisir'/>";
  echo "</td></tr>";
   echo "</form>";
}
echo "</table>";

?>
  </body>
</html>