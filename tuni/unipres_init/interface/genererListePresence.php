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
require_once "../html/escaping.php";

//$connEtud=doConnection("fil_dept");
$conn=doConnection();


$groupeRef=getParam("groupeRef","%");
$interventionRef=getParam("interventionRef","%");
$seanceCle=getParam("seanceCle","%");
$interventionTypeRef=getParam("interventionTypeRef","(sans)");
$offset=getParam("offset","0");
$matiereNom=getParam("matiereNom","(sans)");
$matiereCle=getParam("matiereCle","");
$profCle=getParam("profRef","");
$profRef=doGetNomProf($conn,getParam("profRef",""));
$horaire=getParam("horaire","(sans)");
$salleRef=getParam("salleRef","(sans)");
$saisir=getParam("saisir",0);
$mois=getParam("mois");

$results=doQueryListeEtudiantsParGroupe($conn,$groupeRef);
$etuds=array();

$formationNom=doGetNomFormationFromGroupe($conn,$groupeRef);

$result=mysql_fetch_row($results);
while ($result) {
   $etuds[]=array($result[0],$result[1],$result[2]);
   //error_log("processing $result[0] ");
   $result=mysql_fetch_row($results);

}

//$results=doQuerySeanceOffsetDateCourante($conn,$seanceCle,$offset);
$results=doQuerySeanceOffsetDateCouranteParInterventionEtGroupe($conn,$interventionRef,$groupeRef,$offset);

$ligneDates="";
$result=mysql_fetch_row($results);

$header="";

$annee=date("n",$mois)>=9?date("Y",$mois):(date("Y",$mois)-1);
$anneeUniv=date("n",$mois)>=9?(date("Y",$mois)."/".(date("Y",$mois)+1)):
                            ((date("Y",$mois)-1)."/".date("Y",$mois));

$header.= "<h2 style='text-align:center'> Feuille d'emargement FA/FC - ".date("F y",$mois)."</h2>
            <h3 style='text-align:center'> $formationNom <br/>
              Année universitaire : $anneeUniv </h3>
	      <table align='center'><tr>
      <td align='left'><img align='left' src='http://stages.fil.univ-lille1.fr/presences/img/".$groupeRef.".jpg'/></td>
	      <td>
      <table align='center'>
      <tr>
      <td>Cours :</td><td> $matiereNom - $interventionTypeRef
      </td></tr><tr><td>Enseignant :</td><td> $profRef 
      </td></tr><tr><td>Horaire :</td><td> $horaire
      </td></tr><tr><td>Salle :</td><td> $salleRef
      </td></tr></table></td>
      </tr></table>
      ";
$header.= "";

echo $header;

if ($saisir) {
  echo "<form method='post' action='enregistrerPresences.php'>";
  echo "<input type='hidden' name='annee' value='".$annee."'/>";
} else {
  echo "<form method='post' action='../others/filpdf/imprimerTable.php'>";
}


$tableHTMLCode.= "<table border='1' align='center'><thead><tr>";
$tableHTMLCode.= "<td/><td>Nom</td>";
$dates=array();

while($result) {
  $tableHTMLCode.= "<td>$result[0]</td>";
  $dates[]=$result[0];
  $result=mysql_fetch_row($results);
}
$tableHTMLCode.= "</tr></thead><tbody>";

$df=split("-",$horaire);
for ($i=0;$i<count($etuds);$i++) {
  $tableHTMLCode.= "<tr><td align='right'>".($i+1)."</td><td>".$etuds[$i][1]." ".$etuds[$i][2]."</td>";
  for ($j=0;$j<count($dates);$j++)
    $tableHTMLCode.= "<td align='center'>".
        ($saisir?
            ("<input type='hidden' name='deletes[]' value=\"".
                  "etudRef='".$etuds[$i][0]."' and ".
                  "date='".$dates[$j]."' and ".
                  "debut='".$df[0]."' and ".
                  "matiereRef='".$matiereCle."'".
                  "\" />".
             "<input type='checkbox' name='presences[]' value=\"".
              "'".$etuds[$i][0]."','".$dates[$j]."','".$df[0]."','".$df[1]."','".$interventionTypeRef."','".$matiereCle."'".
            "\" checked/>")
            :"<br/><br/>").
         "</td>";
  $tableHTMLCode.= "</tr>";
}
$tableHTMLCode.= "</tbody></table>";

if (!$saisir)
 $tableHTMLCode.="<br/><br/><table align='center'><tr><td>".
                  "Date et signature enseignant : _________________________ "."
                  </td></tr></table>";

echo $tableHTMLCode."\n\n";

if ($saisir) {
  echo "<br/><center><input type='submit' value='Enregistrer'/></center>";
  echo "</form>";
} else
{
  echo "<input type='hidden' name='tableContent' value='".str_replace("'",'"',$header.$tableHTMLCode)."'/>";
  echo "<input type='hidden' name='filename' value='".$groupeRef."_".$matiereNom."_".$interventionTypeRef."_".$profCle.".pdf'/>";
  echo "<br/><center><input type='submit' value='Imprimer'/></center>";
} 
?>
  </body>
</html>
