<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function doQueryInterventionsOffsetDateCourante($conn,$offset)
{
    $queryString="
    select
          seance.seanceCle as seanceCle,
          matiere.matiereCle as matiereCle,
          matiere.nom as matiereNom,
          intervention.interventionCle as interventionCle,
          intervention.typeInterventionRef as type,
          groupeRef,
          profRef as prof,
          salleRef as salle,
          seance.debut as debut,
          ADDTIME(seance.debut,intervention.duree) as fin,
          periode.debut as date1ereseance,periode.fin as datederniereseance

      from seance inner join intervention on interventionRef=interventionCle
            inner join matiere on matiereCle=matiereRef inner join periode on periodeRef=periodeCle

where
  ADDDATE(LAST_DAY(CURRENT_DATE()),INTERVAL $offset MONTH) between periode.debut and periode.fin
  order by groupeRef,type,matiereNom

        ";

    //echo "querying ",$queryString;
    $result=mysql_query($queryString,$conn);
    return $result;
}

function doQueryInterventionsOffsetDateCouranteParFormation($conn,$offset,$formationRef)
{
    $queryString="
    select
          seance.seanceCle as seanceCle,
          matiere.matiereCle as matiereCle,
          matiere.nom as matiereNom,
          intervention.interventionCle as interventionCle,
          intervention.typeInterventionRef as type,
          groupeRef,
          profRef as prof,
          salleRef as salle,
          seance.debut as debut,
          ADDTIME(seance.debut,intervention.duree) as fin,
          periode.debut as date1ereseance,periode.fin as datederniereseance,
          seance.jour as jour

      from seance inner join intervention on interventionRef=interventionCle
            inner join matiere on matiereCle=matiereRef inner join periode on periodeRef=periodeCle

  where
((ADDDATE(LAST_DAY(CURRENT_DATE()),INTERVAL $offset MONTH) between periode.debut and periode.fin)
or (ADDDATE(DATE(concat(YEAR(CURRENT_DATE()),'-',MONTH(CURRENT_DATE()),'-1')),INTERVAL $offset MONTH) between periode.debut and periode.fin)
)and '".$formationRef."' like concat('%',formationRef,'%')
order by groupeRef,jour,debut,matiereNom

        ";

    //echo "querying ",$queryString;
    $result=mysql_query($queryString,$conn);
    return $result;
}

function doQuerySeancesOffsetDateCourante($conn,$offset)
{
    $queryString="
      SELECT ADDDATE(d1.date,INTERVAL d2.nbseance WEEK), d1.nomMatiere, d1.prof, d1.groupeRef, d1.salle,d1.debut,d1.fin FROM
      (select ADDDATE(
          d3.premierJourMois,
          INTERVAL (-DAYOFWEEK(d3.premierJourMois)+jour+1) DAY) as date,
          matiere.nom as nomMatiere,
          profRef as prof,
          salleRef as salle,
          seance.debut as debut,
          ADDTIME(seance.debut,intervention.duree) as fin,
          periode.debut as date1ereSeance,periode.fin as dateDerniereSeance,
          groupeRef
      from seance inner join intervention on interventionRef=interventionCle
            inner join matiere on matiereCle=matiereRef inner join periode on periodeRef=periodeCle,


          (select
            DATE(CONCAT(YEAR(CURRENT_DATE()),'-',MONTH(CURRENT_DATE())+$offset,'-01'))
                as premierJourMois ) d3
        ) d1,
        (select 0 as nbseance union select 1 as nbseance union select 2 as nbseance union select 3 as nbseance union select 4 as nbseance) d2
where (MONTH(ADDDATE(d1.date,INTERVAL d2.nbseance WEEK))=MONTH(CURRENT_DATE()) +$offset)
and ADDDATE(d1.date,INTERVAL d2.nbseance WEEK) between d1.date1ereSeance and d1.dateDerniereSeance

        ";

    //echo "querying ",$queryString;
    $result=mysql_query($queryString,$conn);
    return $result;
}

function doQuerySeanceOffsetDateCourante($conn,$seanceCle,$offset)
{
    $queryString="
      SELECT ADDDATE(d1.date,INTERVAL d2.nbseance WEEK) FROM
      (select ADDDATE(
          d3.premierJourMois,
          INTERVAL (-DAYOFWEEK(d3.premierJourMois)+jour+1) DAY) as date,
          matiere.nom as nomMatiere,
          profRef as prof,
          salleRef as salle,
          seance.debut as debut,
          ADDTIME(seance.debut,intervention.duree) as fin,
          periode.debut as date1ereSeance,periode.fin as dateDerniereSeance,
          groupeRef,
          matiere.formationRef as formationRef
      from seance inner join intervention on interventionRef=interventionCle
            inner join matiere on matiereCle=matiereRef inner join periode on periodeRef=periodeCle,


          (select
            DATE(CONCAT(YEAR(ADDDATE(CURRENT_DATE(),INTERVAL $offset MONTH)),'-',MONTH(ADDDATE(CURRENT_DATE(),INTERVAL $offset MONTH)),'-01'))
                as premierJourMois ) d3
         where seanceCle like '".$seanceCle."'
        ) d1,
        (select 0 as nbseance union select 1 as nbseance union select 2 as nbseance union select 3 as nbseance union select 4 as nbseance) d2
where (MONTH(ADDDATE(d1.date,INTERVAL d2.nbseance WEEK))=MONTH(ADDDATE(CURRENT_DATE(),INTERVAL $offset MONTH)))
and ADDDATE(d1.date,INTERVAL d2.nbseance WEEK) between d1.date1ereseance and d1.dateDerniereSeance
and not exists (select * from exceptions where
                  (ADDDATE(d1.date,INTERVAL d2.nbseance WEEK) between exceptions.debut and exceptions.fin)
                  and d1.formationRef like exceptions.formationRef
               )
        ";

    //echo "querying ",$queryString;
    $result=mysql_query($queryString,$conn);
    return $result;
}

function doQuerySeanceOffsetDateCouranteParInterventionEtGroupe($conn,$interventionRef,$groupeRef,$offset)
{
    $queryString="
      SELECT  distinct ADDDATE(d1.date,INTERVAL d2.nbseance WEEK) as dates FROM
      (select ADDDATE(
          d3.premierJourMois,
          INTERVAL (-DAYOFWEEK(d3.premierJourMois)+jour+1) DAY) as date,
          matiere.nom as nomMatiere,
          profRef as prof,
          salleRef as salle,
          seance.debut as debut,
          ADDTIME(seance.debut,intervention.duree) as fin,
          periode.debut as date1ereSeance,periode.fin as dateDerniereSeance,
          groupeRef,
          matiere.formationRef as formationRef
      from seance inner join intervention on interventionRef=interventionCle
            inner join matiere on matiereCle=matiereRef inner join periode on periodeRef=periodeCle,


          (select
            DATE(CONCAT(YEAR(ADDDATE(CURRENT_DATE(),INTERVAL $offset MONTH)),'-',MONTH(ADDDATE(CURRENT_DATE(),INTERVAL $offset MONTH)),'-01'))
                as premierJourMois ) d3
         where interventionRef like '".$interventionRef."' and groupeRef like '".$groupeRef."'
        ) d1,
        (select 0 as nbseance union select 1 as nbseance union select 2 as nbseance union select 3 as nbseance union select 4 as nbseance) d2
where (MONTH(ADDDATE(d1.date,INTERVAL d2.nbseance WEEK))=MONTH(ADDDATE(CURRENT_DATE(),INTERVAL $offset MONTH)))
and ADDDATE(d1.date,INTERVAL d2.nbseance WEEK) between d1.date1ereseance and d1.dateDerniereSeance
and not exists (select * from exceptions where
                  (ADDDATE(d1.date,INTERVAL d2.nbseance WEEK) between exceptions.debut and exceptions.fin)
                  and d1.formationRef like exceptions.formationRef
               )

        order by dates";

    //echo "querying ",$queryString;
    $result=mysql_query($queryString,$conn);
    return $result;
}

function doQueryListeEtudiantsParGroupe($conn,$groupeRef,$anneeRef=null) {
  $anneeRef=($anneeRef==null)?(date("n")<9?date("Y")-1:date("Y")):$anneeRef;

  $queryString="
      select etudCle,nom,prenom,groupeRef from
        etudiant inner join etudiant_groupe on etudRef=etudCle 
         where '$groupeRef' like concat ('%',groupeRef,'%')  and annee='".$anneeRef."'
     order by nom, prenom";

    $result=mysql_query($queryString,$conn);
    if (mysql_errno()!=0) error_log($queryString." - ".mysql_error());
    return $result;
}


function doGetNomProf($conn,$profCle)
{
  $profCle="('".str_replace(",","','",$profCle)."')";
  $queryString="select concat(prenom,' ',nom) from prof where profCle in ".$profCle.";";

  //echo $profCle;
  //echo $queryString;

  $result=mysql_query($queryString,$conn);

  $profNoms="";

  while ($prof=mysql_fetch_row($result)) {
    $profNoms.=",".$prof[0];
  }

  return substr($profNoms,1);
}

function doGetNomFormationFromGroupe($conn,$groupeRef)
{

  /*if (strpos($groupeRef,",")>0 &&
      strpos($groupeRef,"M1")==0 &&
      strpos($groupeRef,"FA1")==strlen($groupeRef)-3)
    return "M1 INFO FA";*/
  if (strcmp(substr($groupeRef,0,strlen("M1INFOFA1")),"M1INFOFA1")==0)
    //return "Master 1 Informatique";
    $groupeRef="M1INFOFA1";
    
  $queryString="select nom from formation
              where exists (
                  select * from groupe ".
                  "where groupeCle like '".$groupeRef."' and formationRef=formationCle);";
                  //"where '".$groupeRef."' like concat('%',groupeCle,'%') and formationRef=formationCle);";
  
  $result=mysql_query($queryString,$conn);

  $formation=mysql_fetch_row($result);

  //if (mysql_error($conn)) 
  error_log("querying : $queryString - ".mysql_error($conn)." - ".$formation[0]);

  return $formation[0];
}

function doQueryListMatieres($conn,$formationRef,$debut,$fin) {
  $queryString="
      (select matiereCle,nom from matiere
        where '".$formationRef."' like concat('%',formationRef,'%')
              and (anneeReference<='".date("Y",$debut)."')
              and ((obsolete=0) or
                   (exists (select * from matiere where '".$formationRef."' like concat('%',formationRef,'%')
                        and anneeReference>='".date("Y",$fin)."' )
                   )
                  )
     order by nom)
    ";
  //echo "querying ".$queryString;
  $result=mysql_query($queryString,$conn);
  return $result;
}

function doQueryListAnnees($conn,$debut,$fin) {
  $queryString=" show tables  where tables_in_fil_presences like 'presences%'
        and substr(tables_in_fil_presences,length('presences')+1) between ".
          date("Y",$debut)." and ".date("Y",$fin)." ;";
  //echo ("querying ".$queryString);
  $result=mysql_query($queryString,$conn);
  return $result;
}

function getEtudInfos($conn,$etudRef,$anneeRef=null){
  $anneeRef=($anneeRef==null)?(date("n")<9?date("Y")-1:date("Y")):$anneeRef;

  $queryString="select nom,prenom,formationRef,groupeRef from
    etudiant inner join etudiant_groupe on etudRef=etudCle inner join groupe on groupeRef=groupeCle where
    etudCle='$etudRef' and annee=$anneeRef";
  $result=mysql_query($queryString,$conn);
  $res=mysql_fetch_row($result);
  return $res;
}

function doQueryGetFAFormations($conn) {
  $queryString = "SELECT formationCle 
                  FROM formation
                  WHERE right(nom, 3) LIKE '%FA%' ";
  $result = mysql_query($queryString);
  return $result;
}

function doQueryGetStudentsFromFormation($conn, $formation, $anneeRef) {
  $queryString = "SELECT etudiant_groupe.etudRef AS etudRef, etudiant.nom AS nom, etudiant.prenom AS prenom
                  FROM etudiant_groupe 
                  LEFT JOIN etudiant ON etudiant.etudCle LIKE etudiant_groupe.etudRef
                  WHERE etudiant_groupe.annee=$anneeRef
                  AND etudiant_groupe.groupeRef LIKE '%".$formation."%' 
                  ORDER BY etudiant.nom ASC ";
  $result = mysql_query($queryString);
  return $result;
}
?>
