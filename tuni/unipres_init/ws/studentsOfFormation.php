<?php


require_once "../dbmngt/connect.php";
require_once '../dbmngt/queries.php';

date_default_timezone_set('Europe/Paris');
header('Content-type: application/json');

$available_services = array (
	"presencefac" => "g5z8h6svaz4g8wcl7861"
);

// $formation = $_GET['formation'];
$formation = $_POST['formation'];
if (strlen($formation) < 5) {
	$stud = array();
	$stud["status"] = "0";
	$stud['formation'] = "undefined";
	$stud['date'] = "";
	$stud['students'] = array();
	print(json_encode($stud));
	die();
}

// $date = $_GET['date'];
$date = $_POST['date'];
if (strlen($date) < 10) {
	$stud = array();
	$stud["status"] = "0";
	$stud['formation'] = $formation;
	$stud['date'] = "undefined";
	$stud['students'] = array();
	print(json_encode($stud));
	die();
}


$dateSplit = split("-", $date);
$moisRef = intval($dateSplit[1]);
$anneeRef = intval($dateSplit[0]);

if ($moisRef < 9) {
	$moisRef = $moisRef+12;
	$anneeRef = $anneeRef-1;
}

$json = array();
$json["status"] = "1";
$json["formation"] = $formation;
$json["date"] = $date;
$json["students"] = array();

$conn=doConnection();
 	
$students = doQueryGetStudentsFromFormation($conn, $formation, $anneeRef);

while($student = mysql_fetch_array($students, MYSQL_ASSOC)) {
	if (strlen($student["etudRef"]) < 1 || strlen($student["nom"]) < 1 || strlen($student["prenom"]) < 1 ) 
		continue;
	$stud = array();
	$stud["etudRef"] = $student["etudRef"];
	$stud["nom"] = $student["nom"];
	$stud["prenom"] = $student["prenom"];
	$json["students"][] = $stud;
}

print(json_encode($json));
?>