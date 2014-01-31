
<?php

require_once "../dao/CalendarDaoFactory.class.php";
require_once "../dao/CatalogueDaoFactory.class.php";

$saisir=0;


//if (!isset($_SESSION["daoFactory"]))
  //$_SESSION["daoFactory"]=DaoFactory::createFactory();
//DaoFactory::createFactory();


$daoFactory=DaoFactory::createFactory();


echo "<form action='#' method='post'><font size='4'>Imprimer feuilles présences mois ( ";
echo "<input type='hidden' name='formation' value='".$formation0."'/>";
echo "<input type='hidden' name='etudRef' value='".$etudRef."'/>";


echo "<input type='hidden' name='moisPrecedent' value='".$moisPrecedent."'/>";
$moisSuivant=createMoisSelect($conn,"moisSuivant",+1,NULL,$moisSuivant,"javascript:submit();",$annee);
echo ")</font></form>";

$mois=(date("n")+$moisSuivant);
$mois=$annee==date("Y")?$mois:$mois+12;

$moisNom=$moisArr[($mois-1+12)%12];

$fullOutput="";
$fullOutputScreen="";
$etudsRef=explode(",",$etudRef);
if (count($etudsRef)==1) {
  $etudsNoms[0]=$etudNom;
  $etudsPrenoms[0]=$etudPrenom;
}

$cat=$daoFactory->createCatalogueEtud($conn,$formation,$annee,
    (strcmp($formation,"M1INFOFA")==0&&$mois>12)?$etudsRef[0]:null);

$gCal=$daoFactory->createGCalendarEtud($conn,$formation,$mois,$annee,
    (strcmp($formation,"M1INFOFA")==0&&$mois>12)?$etudsRef[0]:null);

$cles=$gCal->getSeancesCles();

sort($cles);
$countLines=0;


if ($saisir) {
  echo "<form method='post' action='enregistrerPresences.php'>";
  echo "<input type='hidden' name='annee' value='".$annee."'/>";
} else {
  echo "<form method='post' action='../others/filpdf/imprimerTable.php'>";
}

for ($ie=0;$ie<min(50,count($etudsRef));$ie++) {

$etudNom=$etudsNoms[$ie];
$etudPrenom=$etudsPrenoms[$ie];

error_log("dealing with : ".$etudsRef[$ie]);

if (strcmp($formation,"M1INFOFA")==0&&$mois>12&&$ie>0) { //S2 pour les M1INFO
  $cat=$daoFactory->createCatalogueEtud($conn,$formation,$annee,$etudsRef[$ie]);
  $gCal=$daoFactory->createGCalendarEtud($conn,$formation,$mois,$annee,$etudsRef[$ie]);

  $cles=$gCal->getSeancesCles();

  sort($cles);
}

$header="";
$header.= "<table style='text-align:center' align='center'>
      <tr><td align='left'><img src='".INDEX_PAGE."/img/sudes.png' alt='sudes'/></td>
      <td align='center'> <h3>Feuille de présence - Contrat de professionalisation </h3></td>
      <td align='right'><img src='".INDEX_PAGE."/img/ulille1.png' alt='uLille1'/></td>
      </tr></table><br/>

      <table width='95%' border='1' style='border-collapse: collapse;border-style:solid;border-width:1px' align='center'>
      <tr><td style='border-style:none'> Année Universitaire : $annee/".($annee+1)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td style='border-style:none'>&nbsp;Mois : $moisNom ".($mois<=12?$annee:$annee+1)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
      <tr><td colspan='2'> Intitulé de la formation : $formationNom</td></tr>
      <tr><td style='border-style:none'> Nom : $etudNom</td><td style='border-style:none'>&nbsp;Prénom : $etudPrenom&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
		  </table>

      <br/>

      <h4>
      <ul style='margin-left:10%; font-size:10pt'>
        <li>Cette feuille de présence (signée par les enseignants et l'étudiant) sera remise par l'étudiant au secrétariat pédagogique FIL à la fin du mois.</li>
        <li>Calculez et notez en fin de fiche le décompte des heures de présence à valider par le secrétariat pédagogique.</li>
        <li>Précisez également les absences (ABS) et le motif dans les cases prévues pour les signatures.</li>
        <li>Pour des situations spéciales remplissez la case présence comme indiqué ci-dessous. Signez et, en fin de mois, le sécretariat pédagogique apposera un tampon ou une signature <br/> <ul>
        <li>NC - si votre groupe <b>N</b>'est pas <b>C</b>oncerné par le créneau respectif.</li>
        <li>EA - si l'<b>E</b>nseignant était <b>A</b>bsent à la séance. </li>
        <li>DI - si vous êtes <b>DI</b>spensé du cours par le responsable de la formation.</li>
        <li>REP - si l'enseignant a <b>REP</b>orté la séance.</li>
        </ul></li>
      </ul>
      </h4>
        ";
$header.= "";



  $tableHTMLCode= "<table border='1' style='border-collapse: collapse;' align='center'><thead><tr>";
  $tableHTMLCode.="<td>&nbsp;<b>Date</b>&nbsp;</td><td align='center'>&nbsp;<b>Module</b>&nbsp;</td>".
  "<td align='center'>&nbsp;<b>Horaires</b>&nbsp;</td><td td align='center'>&nbsp;<b>Heures</b>&nbsp;</td>".
    "<td align='center'><!--&nbsp;<b>P</b>/<b>A</b>bs/<b>NC</b><br>&nbsp;<b>Annulé</b>&nbsp;-->
                      &nbsp;<b>Présence</b>&nbsp;
    </td>".
    "<td align='center'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Signature&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>".
                       "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;étudiant&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>".
    "<td align='center'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Signature&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>".
                       "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;enseignant&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
  $tableHTMLCode.= "</tr></thead><tbody>";

$countLines=0;
for ($i=0;$i<count($cles);$i++)
{

  $jourCle=$cles[$i];


  $dates=array();

  $seances=$gCal->getSeanceParCle($jourCle);

  $durees=array();
  $matieres=array();
  $debuts=array();
  $fins=array();
  $matieres=array();
  $matieresNoms=array();
  $types=array();

  for ($j=0;$j<count($seances);$j++) {
    $debuts[]=$seances[$j]->getHDebut();
    $fins[]=$seances[$j]->getHFin();
    $dates[]=$seances[$j]->getDate();
    $durees[]=$seances[$j]->getDuree();
    $matieres[]=$seances[$j]->getCleMatiere();
    $nom1=$seances[$j]->getNomSeance();
    $nom2=$cat->getMatiereParCle($seances[$j]->getCleMatiere())->getNom();
    //error_log("type : ".$seances[$j]->getTypeSeance()." pour ".$nom1." cond ".($seances[$j]->getTypeSeance()==null?1:0));
    $matieresNoms[]=
      (($seances[$j]->getTypeSeance()==null)?"":($seances[$j]->getTypeSeance()." ")).
        (strlen($nom1)>strlen($nom2)?$nom1:$nom2);
    $types[]=$seances[$j]->getTypeSeance();
  }
  array_multisort($dates,$debuts,$fins,$durees,$matieres,$matieresNoms,$types);


  for ($j=0;$j<count($seances);$j++) {
    //if (strcmp(substr($dates[$j],8),17)==-1) continue;
    if ($ie==0) {
      mkdir("../seances/".$formation."/".$annee."/".$mois,0755,true);
      $file=fopen("../seances/".$formation."/".$annee."/".$mois."/".
        $dates[$j]."_".$debuts[$j]."_".$fins[$j]."_".
        str_replace('_','-',$matieres[$j])."_".
        str_replace('_','-',$matieresNoms[$j]),"w");
      fclose($file);
    }
    $fmtDate=substr($dates[$j],8)."/".substr($dates[$j],5,2)."/".substr($dates[$j],2,2);
    $tableHTMLCode.= "<tr style='border-style:solid;'><td align='right'>&nbsp;".
      $fmtDate."&nbsp;</td><td>&nbsp;".substr($matieresNoms[$j],0,min(40,strlen($matieresNoms[$j]))).(strlen($matieresNoms[$j])>40?"...":"")."&nbsp;</td>";
    $tableHTMLCode.="<td><font size='-1'>&nbsp;".$debuts[$j]."-".$fins[$j]."&nbsp;</font></td>".
      "<td align='center'>".$durees[$j]." h </td>";
    $tableHTMLCode.= "<td align='center'>&nbsp;".
          ($saisir?
              ( "<input type='hidden' name='deletes[]' value=\"".
                    "etudRef='".$etudsRef[$ie]."' and ".
                    "date='".$dates[$j]."' and ".
                    "debut='".$debuts[$j]."' and ".
                    "matiereRef='".$matieres[$j]."'".
                    "\"/>".
                "<input type='checkbox' name='presences[]' value=\"".
                "'".$etudsRef[$ie]."','".$dates[$j]."','".$debuts[$j]."','".$fins[$j]."','".$types[$j]."','".$matieres[$j]."'".
              "\" checked/>")
              :"<br/><br/>").
           "</td>";
    $tableHTMLCode.= "<td align='center'> &nbsp;</td>";
    $tableHTMLCode.= "<td align='center'> &nbsp;</td>";
    $tableHTMLCode.= "</tr>";
    $countLines++;
  }

}
$tableHTMLCode2="";
if (!$saisir) {
  $nbPagesSup=intval(($countLines+5+8)/31)+1;
  $countLines+=11;
  //$nbPagesSup=1;
  $limit=($nbPagesSup*31);
  error_log( "count :".$countLines." nbPages : ".$nbPagesSup." limit ".$limit);
  
  for (;$countLines<$limit;$countLines++) {
    $tableHTMLCode2.="<tr style='border-style:solid;'><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
      <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
  }
 $tableHTMLCode2.="<tr><td colspan='3' border='0' align='right'><br/>Total heures présences</td><td border='0'><br/>&nbsp; ______ h </td> <td colspan='3' border='0' align='left><br/>&nbsp;</td></tr>";
 //$tableHTMLCode2.="<tr><td colspan='3' border='0' align='right'><br/>Total heures absences</td><td border='0'><br/>&nbsp; ______ h </td> <td colspan='3' border='0' align='left><br/>&nbsp;______ absences</td></tr>";


}
$tableHTMLCode3= "</tbody></table>";


$fullOutput.="<page>".$header.$tableHTMLCode.$tableHTMLCode2.$tableHTMLCode3."</page>";
if ($nbPagesSup%2==1) $fullOutput.="<page>&nbsp;</page>";

  $fullOutputScreen.=$header."<hr width='50%' align='center'/>".$tableHTMLCode.$tableHTMLCode3."<hr/>";
}

if ($saisir) {
  if (hasRole(RESP_ROLE) || hasRole(SECR_ROLE))
    echo "<br/><center><input type='submit' value='Enregistrer'/></center>";

} else
{
  echo "<input type='hidden' name='tableContent' value='".str_replace("'",'"',$fullOutput)."'/>";
  echo "<input type='hidden' name='filename' value='".((strpos($etudRef,',')==FALSE)?$etudRef:$formation)."_".$mois."_".$annee.".pdf'/>";
  echo "<br/><center><input type='submit' value='Imprimer'/></center>";
}

echo $fullOutputScreen;

if ($saisir) {
  if (hasRole(RESP_ROLE) || hasRole(SECR_ROLE))
    echo "<br/><center><input type='submit' value='Enregistrer'/></center>";

} else
{
  echo "<input type='hidden' name='tableContent' value='".str_replace("'",'"',$fullOutput)."'/>";
  echo "<input type='hidden' name='filename' value='".((strpos($etudRef,',')==FALSE)?$etudRef:$formation)."_".$mois."_".$annee.".pdf'/>";
  echo "<br/><center><input type='submit' value='Imprimer'/></center>";
}
echo "</form>";
?>
 
  </body>
</html>
