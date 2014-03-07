<?php


require_once "../dbmngt/connect.php";
require_once '../dbmngt/queries.php';

date_default_timezone_set('Europe/Paris');
header('Content-type: application/json');

$available_services = array (
	"presencefac" => "g5z8h6svaz4g8wcl7861"
);

$date = $_GET['date'];
// $date = $_POST['date'];

// http://localhost/tuni/unipres_init/ws/examOfDate.php?date=2014-03-07

if (strlen($date) < 10) {
	$exam = array();
	$exam["status"] = "0";
	$exam['date'] = "undefined";
	$exam['examens'] = array();
	print(json_encode($exam));
	die();
}


$conn=doConnection("fil_examens");

$examens = doQueryGetExamens($conn, $date);


$json = array();
$json["status"] = "1";
$json["date"] = $date;
$json["examens"] = array();

while($examen = mysql_fetch_array($examens, MYSQL_ASSOC)) {
	$exam = array();

	$exam["examen"] = $examen["exam_ref"];
	$exam["formation"] = $examen["formationRef"];
	$exam["matiere"] = $examen["matiere_ref"];
	$exam["matiere_libelle"] = $examen["nom"];
	$exam["date"] = $examen["date"];
	$exam["heure_debut"] = $examen["heure_debut"];
	$exam["heure_fin"] = $examen["heure_fin"];

	$json["examens"][] = $exam;
}

print json_encode($json);