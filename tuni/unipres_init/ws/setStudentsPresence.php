<?php


require_once "../dbmngt/connect.php";
require_once '../dbmngt/queries.php';

date_default_timezone_set('Europe/Paris');
header('Content-type: application/json');

$available_services = array (
	"presencefac" => "g5z8h6svaz4g8wcl7861"
);

// http://localhost/tuni/unipres_init/ws/setStudentsPresence.php?formation=M2ESERVFA&date=2014-02-06&matiereref=13-m2eservfa-glihm-platine&hdebut=10:30:00&hfin=12:30:00&present=P&students=nathanael.martin

// $formation = $_GET['formation'];
// $date = $_GET['date'];
// $matiereref = $_GET['matiereref'];
// $hdebut = $_GET['hdebut'];
// $hfin = $_GET['hfin'];
// $students = $_GET['students'];
// $present = $_GET['present'];

$formation = $_POST['formation'];
$date = $_POST['date'];
$matiereref = $_POST['matiereref'];
$hdebut = $_POST['hdebut'];
$hfin = $_POST['hfin'];
$etudRef = $_POST['etudRef'];
$presence = $_POST['presence'];

if (strlen($formation) < 5) {
	$stud = array();
	$stud['status'] = 0;
	$stud['formation'] = "undefined";
	$stud['date'] = "";
	$stud["heure_debut"] = "";
	$stud["heure_fin"] = "";
	$stud["matiereref"] = "";
	$stud['present'] = "";
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
	$stud['present'] = "";
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
	$stud['present'] = "";
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
	$stud['present'] = "";
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
	$stud['present'] = "";
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
	$stud["heure_fin"] = $present;
	$stud["matiereref"] = $matiereref;
	$stud['present'] = "undefined";
	$stud['students'] = array();
	print(json_encode($stud));
	die();
}
if (strlen($students) < 1) {
	$stud = array();
	$stud['status'] = 0;
	$stud['formation'] = $formation;
	$stud['date'] = $date;
	$stud["heure_debut"] = $hdebut;
	$stud["heure_fin"] = $hsin;
	$stud["matiereref"] = $matiereref;
	$stud['present'] = $present;
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
$json["matiereref"] = $matiereref;
$json['present'] = $present;
$json["students"] = array();

// On vérifie que l'etudint existe
// On marque sa presence

$stud = array();
$stud["etudRef"] = $students;
$stud["present"] = $present;

$conn=doConnection();

$existsInBase = doQueryGetPresenceOfStudent($conn, $formation, $students, $matiereref, $date, $hdebut, $hfin);
if (mysql_fetch_array($existsInBase, MYSQL_ASSOC)['present']) {
	$stud["maj"] = 0;
	doQuerySetStudentsPresence($conn, $formation, $students, $matiereref, $date, $hdebut, $hfin, $present, TRUE);
} else {
	$stud["maj"] = 1;
	doQuerySetStudentsPresence($conn, $formation, $students, $matiereref, $date, $hdebut, $hfin, $present);
}

$json["students"][] = $stud;


print(json_encode($json));