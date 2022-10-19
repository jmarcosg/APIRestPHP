<?php

use App\Controllers\IdeasPropuestas\IdeasPropuestasController;

$dotenv = \Dotenv\Dotenv::createImmutable('./ideaspropuestas/');
$dotenv->load();

include './ideaspropuestas/config.php';

if ($url['method'] == 'GET') {
	$action = $_GET['action'];
	unset($_GET['action']);

	switch ($action) {

		case 'login':
			/*  */

		default:
			$error = new ErrorException('El action no es valido');
			sendRes(null, $error->getMessage(), $_GET);
			exit;
			break;
	}
}

if ($url['method'] == 'POST') {
	$action = $_POST['action'];
	unset($_POST['action']);

	switch ($action) {
		case 'login':
			IdeasPropuestasController::login();

		case 'saveContent':
			IdeasPropuestasController::saveContent();

		default:
			$error = new ErrorException('El action no es valido');
			sendRes(null, $error->getMessage(), $_GET);
			exit;
			break;
	}
}

header("HTTP/1.1 200 Bad Request");

eClean();
