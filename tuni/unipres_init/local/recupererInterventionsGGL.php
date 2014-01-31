
<?php

if (!isset($argv[1]) || strcmp($argv[1],"65gb314x.y")!=0)
  die("Unauthorized access!!!");

if (isset($argv[2]))
  $moisSuivant=$argv[2];
else
  $moisSuivant=1;

require_once "../dao/CalendarDaoFactory.class.php";
require_once "../dao/CatalogueDaoFactory.class.php";
require_once "../dbmngt/connect.php";
require_once "../dbmngt/queries.php";
require_once "../html/utils.php";
require_once "../html/dbutils.php";

$_SESSION="prevent session start";

require_once "../secure/auth.php";



$saisir=0;

$conn=doConnection();
$formationSet=getAllFormationsWithGGLCal($conn);



$daoFactory=DaoFactory::createFactory();

$annee=getCurrYear();

$mois=(date("n")+$moisSuivant);
$mois=$annee==date("Y")?$mois:$mois+12;

for ($formationRow=mysql_fetch_row($formationSet);
  $formationRow;
  $formationRow=mysql_fetch_row($formationSet)) {
  $formationKey=$formationRow[0];
  if (strcmp($formationKey,"M1INFOFA")==0 && $mois>12) {
    $etudsRef="";
    $groupeRef="M1INFOFA1,M1ESERVFA1,M1IAGLFA1,M1TIIRFA1,M1MOCADFA1,M1IVIFA1";
    $results=doQueryListeEtudiantsParGroupe($conn,$groupeRef);
    $result=mysql_fetch_row($results);
    while ($result) {
     $etudsRef.=",".$result[0];
     $result=mysql_fetch_row($results);
    }
    $etudsRef=substr($etudsRef,1);
  }
  else {
    $etudsRef=null;
  }

  
  if (!file_exists("../seances2/".$formationKey."/".$annee."/".$mois))
    mkdir("../seances2/".$formationKey."/".$annee."/".$mois,0755,true);
  else {
    echo exec("mkdir -p ../seancesOLD/".$formationKey."/".$annee."/".$mois);
    echo exec("mv ../seances2/".$formationKey."/".$annee."/".$mois."/* ../seancesOLD/".$formationKey."/".$annee."/".$mois."");
  }


  echo ("dealing with : ".$etudsRef." for ".$formationKey."\n");

  $cat=$daoFactory->createCatalogueEtud($conn,$formationKey,$annee,$etudsRef);
  $gCal=$daoFactory->createGCalendarEtud($conn,$formationKey,$mois,$annee,$etudsRef,1,1,$unique=false);
  $cles=$gCal->getSeancesCles();

  sort($cles);

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
$enseignants=array();

    for ($j=0;$j<count($seances);$j++) {
      $debuts[]=$seances[$j]->getHDebut();
      $fins[]=$seances[$j]->getHFin();
      $dates[]=$seances[$j]->getDate();
      $durees[]=$seances[$j]->getDuree();
      $matieres[]=$seances[$j]->getCleMatiere();
      $nom1=$seances[$j]->getNomSeance();
      $nom2=$cat->getMatiereParCle($seances[$j]->getCleMatiere())->getNom();
      $matieresNoms[]=
        (($seances[$j]->getTypeSeance()==null)?"":($seances[$j]->getTypeSeance()." ")).
          (strlen($nom1)>strlen($nom2)?$nom1:$nom2);
      $types[]=$seances[$j]->getTypeSeance();
      $enseignants[]=$seances[$j]->getEnseignants();
    }

    array_multisort($dates,$debuts,$fins,$durees,$matieres,$matieresNoms,$types,$enseignants);

    for ($j=0;$j<count($seances);$j++) {
    //if (strcmp(substr($dates[$j],8),17)==-1) continue;
      $fileName="../seances2/".$formationKey."/".$annee."/".$mois."/".
        $dates[$j]."_".$debuts[$j]."_".$fins[$j]."_".
        str_replace(array("_","/"),array('-','.'),$matieres[$j])."_".
        str_replace(array("_","/"),array('-','.'),$matieresNoms[$j])."_".
        str_replace(array(";"),array("+"),$enseignants[$j]);
      $file=fopen($fileName,"w");
      if ($file) 
	      fclose($file);
      else
	      echo "Unable to create $fileName\n";

    }
  }
 

}

?>

