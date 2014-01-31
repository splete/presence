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
session_start();

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once "../dbmngt/connect.php";
require_once "../dbmngt/queries.php";
require_once "../html/utils.php";
require_once "../html/escaping.php";
require_once "../dao/DaoFactory.class.php";

//$connEtud=doConnection("fa_tutorat11");
$conn=doConnection();

$groupeRef=getParam("groupeRef","%");
$formation=getParam("formationRef","%");
//$interventionRef=getParam("interventionRef","%");
$seanceCle=getParam("seanceCle","%");
//$interventionTypeRef=getParam("interventionTypeRef","(sans)");
$offset=getParam("offset","0");
$matiereNom=getParam("matiereNom","(sans)");
$profCle=getParam("profRef","");
$profRef=$profCle==""?"________________":doGetNomProf($conn,getParam("profRef",""));
$horaire=getParam("horaire","(cf. détails séances)");
$salleRef=getParam("salleRef","(non précisée)");
$saisir=getParam("saisir",0);
$debutMois=getParam("debutMois");
$finMois=getParam("finMois");

$results=doQueryListeEtudiantsParGroupe($conn,$groupeRef);
$etuds=array();
$result=mysql_fetch_row($results);
while ($result) {
   $etuds[]=array($result[0],$result[1],$result[2]);
   //error_log("processing $result[0] ");
   $result=mysql_fetch_row($results);

}

$formationNom=doGetNomFormationFromGroupe($conn,$groupeRef);

$_SESSION["daoFactory"]=DaoFactory::createFactory();

$daoFactory=$_SESSION["daoFactory"];



$gCal=$daoFactory->createGCalendar($conn,$formation,date("Y-m-d", $debutMois),date("Y-m-d", $finMois));

//$cles=$gCal->getSeancesCles();


$ligneDates="";
$result=mysql_fetch_row($results);

$header="";

$annee=date("n",$debutMois)>9?date("Y",$debutMois):(date("Y",$debutMois)-1);
$anneeUniv=date("n",$debutMois)>9?(date("Y",$debutMois)."/".(date("Y",$debutMois)+1)):
                            ((date("Y",$debutMois)-1)."/".date("Y",$debutMois));

                            
$header.= "<h2 style='text-align:center'> Feuille d'emargement FA/FC - ".date("F y",$debutMois)."</h2>
              <h3 style='text-align:center'> $formationNom <br/>
                Année universitaire : $anneeUniv </h3>
		<table align='center'><tr>
		      <td align='left'><img align='left' src='http://stages.fil.univ-lille1.fr/presences/img/".$groupeRef.".jpg'/></td>
		                    <td>
        <table align='center'>
        <tr>
        <td>Cours :</td><td> $matiereNom ".(strlen($interventionTypeRef)>0?"- $interventionTypeRef":"")."
        </td></tr><tr><td>Enseignant :</td><td> $profRef
        </td></tr><tr><td>Horaire :</td><td> $horaire
        </td></tr><tr><td>Salle :</td><td> $salleRef
        </td></tr></table>
	</td>
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


$tableHTMLCode= "<table border='1' align='center'><thead><tr>";
$tableHTMLCode.= "<td/><td>Nom</td>";
$dates=array();

$seances=$gCal->getSeanceParCle($seanceCle);

$durees=array();
$matieres=array();


for ($i=0;$i<count($seances);$i++) {
  $debuts[]=$seances[$i]->getHDebut();
  $fins[]=$seances[$i]->getHFin();
  $dates[]=$seances[$i]->getDate();
  $durees[]=$seances[$i]->getDuree();
  $matieres[]=$seances[$i]->getCleMatiere();
  $types[]=$seances[$i]->getTypeSeance();
}
array_multisort($dates,$debuts,$fins,$durees,$matieres,$types);

for ($i=0;$i<count($dates);$i++) {
  $tableHTMLCode.= "<td align='center'>".$dates[$i]."<br/><font size='-1'>".$debuts[$i]."-".$fins[$i]."</font></td>";
}

$tableHTMLCode.= "</tr></thead><tbody>";
for ($i=0;$i<count($etuds);$i++) {
  $tableHTMLCode.= "<tr><td align='right'>".($i+1)."</td><td>".$etuds[$i][1]." ".$etuds[$i][2]."</td>";
  for ($j=0;$j<count($dates);$j++)
    $tableHTMLCode.= "<td align='center'>".
        ($saisir?
            ( "<input type='hidden' name='deletes[]' value=\"".
                  "etudRef='".$etuds[$i][0]."' and ".
                  "date='".$dates[$j]."' and ".
                  "debut='".$debuts[$j]."' and ".
                  "matiereRef='".$matieres[$j]."'".
                  "\"/>".
              "<input type='checkbox' name='presences[]' value=\"".
              "'".$etuds[$i][0]."','".$dates[$j]."','".$debuts[$j]."','".$fins[$j]."','".$types[$j]."','".$matieres[$j]."'".
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
