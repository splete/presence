<?php


require_once "../dbmngt/connect.php";
require_once '../dbmngt/queries.php';

date_default_timezone_set('Europe/Paris');
header('Content-type: application/json');

// $login = $_GET['login'];
$login = $_POST['login'];
if (strlen($login) < 1) {
	$resp = array();
	$resp['status'] = 0;
	$resp['login'] = "undefined";
	$resp['authorised'] = 0;
	print(json_encode($res));
	die();
}

// $pass = $_GET['password'];
$pass = $_POST['pass'];
if (strlen($pass) < 1	) {
	$resp = array();
	$resp['status'] = 0;
	$resp['login'] = $login;
	$resp['authorised'] = 0;
	print(json_encode($resp));
	die();
}

$resp = array();
$resp["status"] = 1;
$resp['login'] = $login;
$resp['authorised'] = 1;

print(json_encode($resp));
?>