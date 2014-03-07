<?php


require_once "../dbmngt/connect.php";
require_once '../dbmngt/queries.php';


date_default_timezone_set('Europe/Paris');
header('Content-type: application/json');

$available_services = array (
	"presencefac" => "g5z8h6svaz4g8wcl7861"
);


// http://localhost/tuni/unipres_init/ws/setStudentsPresenceExam.php?formation=M2ESERVFA&date=2014-03-07&examen=13_glihm-platine_s1_ss1&hdebut=14:00:00&hfin=16:00:00&present=P&students=nathanael.martin&type=entree

// $formation = $_GET['formation'];
// $date = $_GET['date'];
// $examen = $_GET['examen'];
// $hdebut = $_GET['hdebut'];
// $hfin = $_GET['hfin'];
// $students = $_GET['students'];
// $present = $_GET['present'];
// $type = $_GET['type'];

$formation = $_POST['formation'];
$date = $_POST['date'];
$examen = $_POST['examen'];
$hdebut = $_POST['hdebut'];
$hfin = $_POST['hfin'];
$students = $_POST['students'];
$present = $_POST['present'];
$type = $_POST['type'];


if (strlen($formation) < 5) {
	$stud = array();
	$stud['status'] = 0;
	$stud['formation'] = "undefined";
	$stud['date'] = "";
	$stud["heure_debut"] = "";
	$stud["heure_fin"] = "";
	$stud["examen"] = "";
	$stud['present'] = "";
	$stud['type'] = "";
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
	$stud['present'] = "";
	$stud['type'] = "";
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
	$stud['present'] = "";
	$stud['type'] = "";
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
	$stud['present'] = "";
	$stud['type'] = "";
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
	$stud['present'] = "";
	$stud['type'] = "";
	$stud['students'] = array();
	print(json_encode($stud));
	die();
}
if (strlen($present) < 1) {
	$stud = array();
	$stud['status'] = 0;
	$stud['formation'] = $formation;
	$stud['date'] = $date;
	$stud["heure_debut"] = $hdebut;
	$stud["heure_fin"] = $hfin;
	$stud["examen"] = $examen;
	$stud['present'] = "undefined";
	$stud['type'] = "";
	$stud['students'] = array();
	print(json_encode($stud));
	die();
}
if (strlen($type) < 1) {
	$stud = array();
	$stud['status'] = 0;
	$stud['formation'] = $formation;
	$stud['date'] = $date;
	$stud["heure_debut"] = $hdebut;
	$stud["heure_fin"] = $hfin;
	$stud["examen"] = $examen;
	$stud['present'] = $present;
	$stud['type'] = "undefined";
	$stud['students'] = array();
	$stud['students'][] = array("no students");
	print(json_encode($stud));
	die();
}
if (strlen($students) < 1) {
	$stud = array();
	$stud['status'] = 0;
	$stud['formation'] = $formation;
	$stud['date'] = $date;
	$stud["heure_debut"] = $hdebut;
	$stud["heure_fin"] = $hfin;
	$stud["examen"] = $examen;
	$stud['present'] = $present;
	$stud['type'] = "";
	$stud['students'] = array();
	$stud['students'][] = array("no students");
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
$json["examen"] = $examen;
$json['present'] = $present;
$json['type'] = $type;
$json["students"] = array();

// On vérifie que l'etudint existe
// On marque sa presence

$stud = array();
$stud["etudRef"] = $students;
$stud["present"] = $present;


$conn=doConnection("fil_examens");

$existsInBase = doQueryGetPresenceOfStudentInExam($conn, $formation, $students, $examen, $date, $hdebut, $hfin);

if (isset($existsInBase) && mysql_fetch_array($existsInBase, MYSQL_ASSOC)) {

	$stud["maj"] = 1;
	doQuerySetStudentsPresenceInExam($conn, $formation, $students, $examen, $date, $hdebut, $hfin, $present, $type, TRUE);
} else {
	$stud["maj"] = 0;
	doQuerySetStudentsPresenceInExam($conn, $formation, $students, $examen, $date, $hdebut, $hfin, $present, $type);
}

$json["students"][] = $stud;


print json_encode($json);