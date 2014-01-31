<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function getAgendaUserForFormation($conn,$formationRef)
{
    $queryString="
    select
          agendaUser
      from gcals
    where
      formationRef like '".$formationRef."';

        ";

    //echo "querying ",$queryString;
    $result=mysql_query($queryString,$conn);
    $aus=mysql_fetch_row($result);
    return $aus[0];
}

function getCatalogueRows($conn,$formationRef,$annee) {
  $queryString="
    select
          nom,
          matiereCle
      from matiere
    where
      formationRef like '".$formationRef."' and anneeReference<=$annee and obsolete=0;

        ";

    //echo "querying ",$queryString;
    $result=mysql_query($queryString,$conn);
    
    return $result;
}

function getSpecificCatalogueRowsForEtud($conn,$formationRef,$annee,$etudRef) {
  $queryString="
    select distinct nomMatiere,optGroupeRef from
      matiere_opt natural join options
    where  '".$etudRef."' like concat('%',etudRef,'%') order by optGroupeRef;
    ";

    //echo "querying ",$queryString;
    $result=mysql_query($queryString,$conn);

    return $result;
}

function getAllFormationsWithGGLCal($conn)
{
  $queryString="select
          formationRef
      from gcals order by formationRef;";
  $result=mysql_query($queryString,$conn);
  return $result;
}
?>
