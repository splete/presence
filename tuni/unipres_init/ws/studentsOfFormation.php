<?php


require_once "../dbmngt/connect.php";
require_once '../dbmngt/queries.php';

date_default_timezone_set('Europe/Paris');
header('Content-type: application/json');

$available_services = array (
	"presencefac" => "g5z8h6svaz4g8wcl7861"
);

// http://localhost/tuni/unipres_init/ws/studentsOfFormation.php?formation=M2ESERVFA&date=2014-02-06&matiereref=13-m2eservfa-glihm-platine&hdebut=10:30:00&hfin=12:30:00

$formation = $_GET['formation'];
$date = $_GET['date'];
$matiereref = $_GET['matiereref'];
$hdebut = $_GET['hdebut'];
$hfin = $_GET['hfin'];

// $formation = $_POST['formation'];
// $date = $_POST['date'];
// $matiereref = $_POST['matiereref'];
// $hdebut = $_POST['hdebut'];
// $hfin = $_POST['hfin'];

if (strlen($formation) < 5) {
	$stud = array();
	$stud['status'] = 0;
	$stud['formation'] = "undefined";
	$stud['date'] = "";
	$stud["matiereref"] = "";
	$stud['students'] = array();
	print(json_encode($stud));
	die();
}
if (strlen($date) < 10) {
	$stud = array();
	$stud['status'] = 0;
	$stud['formation'] = $formation;
	$stud['date'] = "undefined";
	$stud["heure_debut"] = "";
	$stud["heure_fin"] = "";
	$stud["matiereref"] = "";
	$stud['students'] = array();
	print(json_encode($stud));
	die();
}
if (strlen($matiereref) < 1) {
	$stud = array();
	$stud['status'] = 0;
	$stud['formation'] = $formation;
	$stud['date'] = $date;
	$stud["heure_debut"] = "";
	$stud["heure_fin"] = "";
	$stud["matiereref"] = "unidefined";
	$stud['students'] = array();
	print(json_encode($stud));
	die();
}
if (strlen($date) < 1) {
	$stud = array();
	$stud['status'] = 0;
	$stud['formation'] = $formation;
	$stud['date'] = "";
	$stud["heure_debut"] = "";
	$stud["heure_fin"] = "undefined";
	$stud["matiereref"] = "";
	$stud['students'] = array();
	print(json_encode($stud));
	die();
}
if (strlen($hfin) < 1) {
	$stud = array();
	$stud['status'] = 0;
	$stud['formation'] = $formation;
	$stud['date'] = $date;
	$stud["heure_debut"] = $hdebut;
	$stud["heure_fin"] = "";
	$stud["matiereref"] = $matiereref;
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
$json["status"] = 1;
$json["formation"] = $formation;
$json["date"] = $date;
$json["heure_debut"] = $hdebut;
$json["heure_fin"] = $hfin;
$json["matiereref"] = $matiereref;
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

	$present = doQueryGetPresenceOfStudent($conn, $formation, $student['etudRef'], $matiereref, $date, $hdebut, $hfin);
	
	if ($statePresent = mysql_fetch_array($present, MYSQL_ASSOC))
		$stud['present'] = $statePresent['present'];
	else 
		$stud["present"] = "U";

	$json["students"][] = $stud;
}

print(json_encode($json));
?>