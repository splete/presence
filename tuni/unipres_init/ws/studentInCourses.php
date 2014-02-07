<html>
    <head>
        <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="../styles/Css_autentification.css" rel="stylesheet" type="text/css" />
        <SCRIPT src="../js/toogleDivs.js" lang="javascript"></SCRIPT>
        <title>Liste presences</title>
    </head>
  <body>


<?php

require_once "../dbmngt/connect.php";

date_default_timezone_set('Europe/Paris');

$student = $_GET['student'];
if (strlen($student) < 1) 
	die('Param requis');

$conn=doConnection();

$queryString = "SELECT * FROM etudiant_groupe WHERE etudRef LIKE '" . $student . "' ";

print($queryString . '<br/>');

$results=mysql_query($queryString,$conn);

print('<table>');
while($result = mysql_fetch_assoc($results)){
	print('<tr>');
	print(
		'<td>' . $result['annee'] . '</td>' . 
		'<td>' . $result['groupeRef'] . '</td>' . 
		'<td>' . $result['etudRef'] . '</td>'
	);
	print('</tr>');
} 
print('</table>');
die('Fin');