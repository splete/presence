<?php


require_once "../dbmngt/connect.php";
require_once '../dbmngt/queries.php';

date_default_timezone_set('Europe/Paris');
header('Content-type: application/json');

$available_services = array (
	"presencefac" => "g5z8h6svaz4g8wcl7861"
);

// http://localhost/tuni/unipres_init/ws/studentsOfExam.php?formation=M2ESERVFA&date=2014-03-07&examen=13_glihm-platine_s1_ss1&hdebut=14:00:00&hfin=16:00:00

// $formation = $_GET['formation'];
// $date = $_GET['date'];
// $examen = $_GET['examen'];
// $hdebut = $_GET['hdebut'];
// $hfin = $_GET['hfin'];

$formation = $_POST['formation'];
$date = $_POST['date'];
$examen = $_POST['examen'];
$hdebut = $_POST['hdebut'];
$hfin = $_POST['hfin'];

if (strlen($formation) < 5) {
	$stud = array();
	$stud['status'] = 0;
	$stud['formation'] = "undefined";
	$stud["heure_debut"] = "";
	$stud["heure_fin"] = "";
	$stud['date'] = "";
	$stud["examen"] = "";
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
	$stud["examen"] = "";
	$stud['students'] = array();
	print(json_encode($stud));
	die();
}
if (strlen($examen) < 1) {
	$stud = array();
	$stud['status'] = 0;
	$stud['formation'] = $formation;
	$stud['date'] = $date;
	$stud["heure_debut"] = "";
	$stud["heure_fin"] = "";
	$stud["examen"] = "unidefined";
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
	$stud["examen"] = "";
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
	$stud["examen"] = $examen;
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

$conn=doConnection("fil_examens");

$students = doQueryGetStudentsFromFormation($conn, $formation, $anneeRef);

$json = array();
$json["status"] = 1;
$json["formation"] = $formation;
$json["date"] = $date;
$json["heure_debut"] = $hdebut;
$json["heure_fin"] = $hfin;
$json["examen"] = $examen;
$json["students"] = array();

while($student = mysql_fetch_array($students, MYSQL_ASSOC)) {
	if (strlen($student["etudRef"]) < 1 || strlen($student["nom"]) < 1 || strlen($student["prenom"]) < 1 ) 
		continue;

	$stud = array();
	$stud["etudRef"] = $student["etudRef"];
	$stud["nom"] = $student["nom"];
	$stud["prenom"] = $student["prenom"];

	$present = doQueryGetPresenceOfStudentInExam($conn, $formation, $student['etudRef'], $examen, $date, $hdebut, $hfin);

	if (isset($present) && $present != '' && $statePresent = mysql_fetch_array($present, MYSQL_ASSOC)) {
		$stud['present_entree'] = $statePresent['present_entree'];
		$stud['present_sortie'] = $statePresent['present_sortie'];
	} else {
		$stud["present_entee"] = "U";
		$stud["present_sortte"] = "U";
	}

	$json["students"][] = $stud;

}

print json_encode($json); 