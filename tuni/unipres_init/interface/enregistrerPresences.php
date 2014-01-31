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
        <title>Enregistrer presences</title>
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

if (!array_key_exists("presences",$_REQUEST) ||
    !array_key_exists("deletes",$_REQUEST) ||
    !array_key_exists("annee",$_REQUEST))
  die("Comment êtes-vous arriver ici ?");

$presences=getParam("presences");
$annee=getParam("annee");
$deletes=getParam("deletes");


$conn=doConnection();

$createQuery="

CREATE TABLE IF NOT EXISTS `presences$annee` (
  `presenceCle` int(11) NOT NULL AUTO_INCREMENT,
  `etudRef` varchar(45) NOT NULL,
  `date` date NOT NULL,
  `debut` time NOT NULL,
  `duree` time NOT NULL,
  `typeInterventionRef` varchar(10) NOT NULL,
  `matiereRef` varchar(45) NOT NULL,
  PRIMARY KEY (`presenceCle`),
  KEY `index2` (`etudRef`),
  KEY `index3` (`date`),
  KEY `index4` (`matiereRef`),
  KEY `index5` (`typeInterventionRef`),
  KEY `index6` (`etudRef`,`date`,`matiereRef`,`typeInterventionRef`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ; 
";

$deleteQueries=array();
foreach ($deletes as $delete) {
  $deleteQueries[]="delete from presences".$annee." where ".$delete.";\n";
}
  
$insertQuery="insert into presences".$annee."(`etudRef`,`date`,`debut`,`duree`,`typeInterventionRef`,`matiereRef`) values ";
$values = "";
foreach ($presences as $presence) {
  $values.=",(".$presence.")";
}
$insertQuery.=substr($values,1).";";

//echo $createQuery;
//echo $deleteQueries;
//echo $insertQuery;


$res=true;

$queryString="set autocommit=0";
$res=$res && !(!mysql_query($queryString,$conn));
$queryString="begin ";
$res=$res && !(!mysql_query($queryString,$conn));

//echo mysql_errno()." - ".mysql_error()."<hr/>";


$res=$res && !(!mysql_query($createQuery,$conn));
//echo mysql_errno()." - ".mysql_error()."<hr/>";

if ($res!=false) {
  foreach ($deleteQueries as $deleteQuery) {
    if ($res==false) break;
    $res=$res && !(!mysql_query($deleteQuery,$conn));
  }
}

//echo mysql_errno()." - ".mysql_error()."<hr/>";


if ($res!=false) {
  $res=$res && !(!mysql_query($insertQuery,$conn));
}
//echo mysql_errno()." - ".mysql_error()."<hr/>";

if ($res!=false) {
  $queryString="commit; ";
  $res=$res && !(!mysql_query($queryString,$conn));
}
//echo mysql_errno()." - ".mysql_error()."<hr/>";

if ($res!=false) {
  echo "Insertion de ".count($presences)." presences reussie!";
} else {
  echo "Echec : <br/>" . mysql_error();
}

?>