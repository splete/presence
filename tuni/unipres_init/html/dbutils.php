<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once ("../dbmngt/queries.php");
require_once ("../html/utils.php");

function createSelectFromRows($rows,$nomSelect,$current,$onChange=NULL) {
  $row=mysql_fetch_row($rows);
  $rowNo=0;
  $values=array();
  $keys=array();
  $i=0;
  while ($row)
  {
      $keys[$rowNo]=$row[0];
      $values[$rowNo]=array_key_exists(1,$row)?$row[1]:$row[0];
      if (strcmp($current,$row[0])==0)
          $i=$rowNo;
      $row=mysql_fetch_row($rows);
      $rowNo++;
  }
  createSelectWithOnChange($nomSelect,$keys,$values,$i,$onChange);
  return $keys[$i];
}

function createMatieresSelect($conn,$nomSelect,$formationRef,$debut,$fin,$currentMatiere=NULL,$onChange=NULL)
{

        return createSelectFromRows(doQueryListMatieres($conn,$formationRef,$debut,$fin),$nomSelect,$currentMatiere,$onChange);

}

function createAnneesSelect($conn,$nomSelect,$debut,$fin,$current=NULL,$onChange=NULL)
{
  $rows=doQueryListAnnees($conn,$debut,$fin);
  $row=mysql_fetch_row($rows);
  $rowNo=0;

  $i=0;
  while ($row)
  {
      $keys[$rowNo]=substr($row[0],strlen("presences"));
      $values[$rowNo]=substr($row[0],strlen("presences"));
      if (strcmp($current,$row[0])==0)
          $i=$rowNo;
      $row=mysql_fetch_row($rows);
      $rowNo++;
  }
  createSelectWithOnChange($nomSelect,$keys,$values,$i,$onChange);
  return $keys[$i];
}

function createFormationsSelect($conn,$nomSelect,$formation,$onChange=NULL,$unique=false,$all=false,$ft=null,$onlyM1INFO=false)
{

    $keysValues=constructGrantedGroupesKeys($unique,$all,$ft,$onlyM1INFO);
    $keys=$keysValues["keys"];
    $values=$keysValues["values"];
    $i=0;
    for (;($i<count($keys)) && ($keys[$i]!==$formation);$i++);
    if ($i==count($keys)) {
       $i=0;
       $formation=$keys[$i];
    }

    createSelectWithOnChange($nomSelect,$keys,$values,$i,"javascript:submit();");
    return $keys[$i];
}

function createMoisSelect($conn,$nomSelect,$increment,$mois=NULL,$offset=1,$onChange=NULL,$anneeDebut=null)
{
    $mois=$mois==NULL?mktime(0,0,0,date("n"),date("d"),date("Y")):$mois;
    $keys=array();$values=array();

    $anneeDebut=$anneeDebut==null?(date("n",$mois)>=9?date("Y",$mois):(date("Y",$mois)-1)):$anneeDebut;
    
    error_log("constructing mois select for annee $anneeDebut");
    
    $debut=mktime(0,0,0,9,1,$anneeDebut);
    $fin=mktime(0,0,0,7,30,$anneeDebut+1);
    $moisNo=date("n",$mois)+(date("n",$mois)>=9?0:12);
    
    error_log("constructing mois select starting from moisNo $moisNo");
    
    if ($increment==1) {
      for ($i=9;$i<12+10;$i++) {
        $keys[]=$i-$moisNo;
        $values[]=date("M Y",mktime(0,0,0,$i,1,$anneeDebut));
      }
      /*for ($i=$moisNo;$i<12+10;$i++) {
        $keys[]=$i-$moisNo;
        $values[]=date("M Y",mktime(0,0,0,$i,1,$anneeDebut));
      }*/
    } else {
      if ($moisNo<9) $moisNo+=12;
      for ($i=$moisNo;$i>=9;$i--) {
        $keys[]=$i-$moisNo;
        $values[]=date("M Y",mktime(0,0,0,$i,1,$anneeDebut));
      }
    } 

    //error_log("offset : ".$offset);

    $i=0;
    for (;($i<count($keys)) && ($keys[$i]!=$offset);$i++) {
      //error_log("comparing ".$keys[$i]." and ".$offset);
    }
    if ($i==count($keys)) {
       $i=0;
       $offset=$keys[$i];
    }

    //error_log("FOUND offset : ".$offset);

    createSelectWithOnChange($nomSelect,$keys,$values,$i,"javascript:submit();");
    return $offset;
}
?>
