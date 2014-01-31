
<?php
    require '../secure/auth.php';
    //error_log("accueilTuteur for ".getParam(CK_USER,"UNKOWNN"));

    if (!hasRole(RESP_ROLE) && !hasRole(SECR_ROLE) && !hasRole(STUD_ROLE))
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
require_once "../html/dbutils.php";

date_default_timezone_set('Europe/Paris');

$annee=  getCurrYear();//date("m")<9?date("Y")-1:date("Y");

$jours=array("Dimanche","Lundi","Mardi","Mercredi","Jeudi","Vendredi");
$moisArr = array("Janvier","Février","Mars","Avril","Mai","Juin","Juillet",
                "Août","Septembre","Octobre","Novembre","Décembre");
$conn=doConnection();

$offset=getParam("offset",0);
$moisSuivant=getParam("moisSuivant",(date("d")<15?0:1));
$moisPrecedent=getParam("moisPrecedent",-1);
$_SESSION["moisSuivant"]=$moisSuivant;
$maxLines=150;

$local=getParam("local",1);

if (hasRole(RESP_ROLE)||hasRole(SECR_ROLE)) {
  $formation0=getParam("formation","M1MIAGEFA");
  $etudRef=getParam("etudRef","aucun");
  $_SESSION["formation"]=$formation0;
  $_SESSION["etudRef"]=$etudRef;
  echo "<form method='post' action='#'>";
  echo "<h2>Choisir formation</h2>";
  echo "Formations :";
  $formation0=createFormationsSelect($conn,"formation",$formation0,"javascript:submit();",true,false,null,true);

  echo "</form>";
  if (strpos($formation0,',')==FALSE) {
    $groupeRef=$formation0."1";
    $formation=$formation0;
  } else {
      $groupeRef=str_replace(",","1,",$formation0)."1";
      $formation=substr($formation0,0,strpos($formation0,','));
      //error_log("retaining formation : $formation");
  }
  
  $results=doQueryListeEtudiantsParGroupe($conn,$groupeRef,  getCurrYear());
  $etuds=array();
  $result=mysql_fetch_row($results);
  
  if (strcmp($etudRef,"aucun")==0 && $result!=FALSE) {
    $etudRef=$result[0];
  }
  $etudsNoms=array();
  $etudsPrenoms=array();
  while ($result) {
     $etuds[]=array($result[0],$result[1],$result[2]);
     if (strcmp($result[0],$etudRef)==0) {
       $etudNom=$result[1];
       $etudPrenom=$result[2];
     }
     $etudsNoms[]=$result[1];
     $etudsPrenoms[]=$result[2];
     //error_log("processing $result[0] ");
     $result=mysql_fetch_row($results);
  }

  echo "<form method='post' action='#'>";
  echo "<h2>Choisir étudiant</h2>";
  echo "<select name='etudRef' onchange='javascript:submit();'>";
  $allEtuds="";
  for ($i=0;$i<count($etuds);$i++) {
    echo "<option value='".$etuds[$i][0]."' ".
          ($etuds[$i][0]==$etudRef?"selected='selected'":"").">".($etuds[$i][2]." ".$etuds[$i][1])."</option>";
    $allEtuds.=','.$etuds[$i][0];
  }
  echo "<option value='".substr($allEtuds,1)."' ".
          (substr($allEtuds,1)==$etudRef?"selected='selected'":"")."> === Tous === </option>";

  echo "</select>";
  echo "</form>";
} else if (hasRole(STUD_ROLE)) {
  $etudRef=$_SESSION[CK_USER];
  $res=getEtudInfos($conn,$etudRef,$annee);
  $etudNom=$res[0];
  $etudPrenom=$res[1];
  $formation=strcmp($res[2],"M1ESERVFA")==0 ||
             strcmp($res[2],"M1IAGLFA")==0 ||
             strcmp($res[2],"M1MOCADFA")==0 ||
             strcmp($res[2],"M1TIIRFA")==0 ||
             strcmp($res[2],"M1IVIFA")==0?"M1INFOFA":$res[2];
  $groupeRef=strcmp($res[3],"M1ESERVFA1")==0 ||
             strcmp($res[3],"M1IAGLFA1")==0 ||
             strcmp($res[3],"M1MOCADFA1")==0 ||
             strcmp($res[3],"M1TIIRFA1")==0 ||
             strcmp($res[3],"M1IVIFA1")==0?"M1INFOFA1":$res[3];
}

 

$formationNom=doGetNomFormationFromGroupe($conn,$groupeRef);

/*if (strcmp($formation,"M1MIAGEFA")==0 || strcmp($formation,"M2MIAGEFA")==0)
  include_once("listerInterventionsDB.php");
else*/
if ($local==1)
  include_once("listerInterventionsLocal.php");
else
  include_once("listerInterventionsGGL.php");
?>
