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
en POST : 
Login + date = liste des cours du prof a cette date
Login = cours en cours
*/

require_once "../dbmngt/connect.php";
require_once "../dbmngt/queries.php";

date_default_timezone_set('Europe/Paris');

$prof = $_GET['prof'];
if (strlen($prof) < 1) 
	die('Param requis');

$conn=doConnection();


$queryString = "SELECT * FROM seance WHERE profRef LIKE '" . $prof . "' ";
//print($queryString);
$results=mysql_query($queryString,$conn);

print('<table>');
while($result = mysql_fetch_assoc($results)){
	print('<tr>');
	print(
		'<td>' . $result['seanceCle'] . '</td>' . 
		'<td>' . $result['intervention_ref'] . '</td>' . 
		'<td>' . $result['profRef'] . '</td>' . 
		'<td>' . $result['jour'] . '</td>' . 
		'<td>' . $result['debut'] . '</td>'
	);
	print('</tr>');
} 
print('</table>');
die('Fin');


$results=doQueryListeEtudiantsParGroupe($conn,$groupeRef,  getCurrYear());

$result=mysql_fetch_row($results);


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

$prof = "bilasco"; // A recuperé en POST

?>

</body>
</html>