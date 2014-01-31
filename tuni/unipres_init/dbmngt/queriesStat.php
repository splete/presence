<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function doQueryPresencesParDateEtMatiere($conn,$debut,$fin,$matiereRef) {
  $anneeDebut=date("n",$debut)>9?date("Y",$debut):(date("Y",$debut)-1);
  $anneeFin=date("n",$fin)>9?date("Y",$fin):(date("Y",$fin)-1);
  //$queryString="union ";
  for ($annee=$anneeDebut;$annee<=$anneeFin;$annee++)
    $queryString="union (
                    select date,count(*) as nb from presences$annee
                      where matiereRef='".$matiereRef."'
                      and date<='".date("Y-n-d",$fin)."'
                      and date>='".date("Y-n-d",$debut)."'
                     group by date) ";
  $queryString=substr($queryString,strlen("union "));
  //echo "querying ".$queryString;

  $result=mysql_query($queryString,$conn);
  return $result;
}
?>
