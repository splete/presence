<?php


require_once "../dbmngt/connect.php";
require_once '../dbmngt/queries.php';

date_default_timezone_set('Europe/Paris');
header('Content-type: application/json');


$prof = $_GET['prof'];
//$prof = $_POST['prof'];
if (strlen($prof) < 1) {
	$cours = array();
	$cours["status"] = "0";
	$cours['prof'] = "undefined";
	$cours['date'] = "";
	$cours['cours'] = array();
	print(json_encode($cours));
	die();
}

$date = $_GET['date'];
//$date = $_POST['date'];
if (strlen($date) < 1) {
	$cours = array();
	$cours["status"] = "0";
	$cours['prof'] = $prof;
	$cours['date'] = "undefined";
	$cours['cours'] = array();
	print(json_encode($cours));
	die();
}


$conn=doConnection();

$formations = doQueryGetFAFormations($conn);


$dateSplit = split("-", $date);
$moisRef = intval($dateSplit[1]);
$anneeRef = intval($dateSplit[0]);

if ($moisRef < 9) {
	$moisRef = $moisRef+12;
	$anneeRef = $anneeRef-1;
}



$json = array();
$json["status"] = "1";
$json["prof"] = $prof;
$json["date"] = $date;
$json["cours"] = array();

while($formation = mysql_fetch_array($formations, MYSQL_ASSOC)['formationCle']) {

	$folder = "../seances2/$formation/$anneeRef/$moisRef/";
	if (!file_exists($folder))
		continue;

	$seanceFiles=scandir($folder);

	for ($i=0;$i<count($seanceFiles);$i++) {

		$seanceFileName=$seanceFiles[$i];
		$infos=explode("_",$seanceFileName);

		if ($seanceFileName[0]=='.') 
			continue;

		
		$chaineSplit = split(" ", $seanceFileName);

		$profFound = $chaineSplit[count($chaineSplit)-1];
        
        if ((strtolower($profFound) == $prof) && strpos($seanceFileName, $date) !== false) {
        	$cours = array();

        	$cours["formation"] = $formation;

        	$splitWithUnder = split("_", $seanceFileName);

			$cours["matiere"] = $splitWithUnder[3];
			$cours["libelle_matiere"] = $splitWithUnder[4];

			
			$cours["heure_debut"] = $splitWithUnder[1];
			$cours["heure_fin"] = $splitWithUnder[2];

			$json["cours"][] = $cours;
        }

	}
}

print(json_encode($json));

?>

