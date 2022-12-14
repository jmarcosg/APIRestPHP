<?php

use App\Controllers\Weblogin\WlFotoPerfilController;

$dotenv = \Dotenv\Dotenv::createImmutable('./wlfotoperfil/');
$dotenv->load();

include './wlfotoperfil/config.php';

if ($url['method'] == 'GET') {
	$action = $_GET['action'];
	unset($_GET['action']);

	switch ($action) {
		case 'getFotoById':
			WlFotoPerfilController::getFotoById();

		case 'getPersonasSinVerificar':
			WlFotoPerfilController::getPersonasSinVerificar();

		default:
			$error = new ErrorException('El action no es valido');
			sendRes(null, $error->getMessage(), $_GET);
			exit;
	}
}

if ($url['method'] == 'POST') {
	$action = $_POST['action'];
	unset($_POST['action']);

	switch ($action) {
		case 'addFoto':
			WlFotoPerfilController::saveFoto();

		case 'getLastFotos':
			WlFotoPerfilController::getLastFotos();

		case 'editFotoByUser':
			WlFotoPerfilController::editFotoByUser();

		case 'changeEstado':
			WlFotoPerfilController::changeEstado();

		default:
			$error = new ErrorException('El action no es valido');
			sendRes(null, $error->getMessage(), $_GET);
			exit;
	}
}

header("HTTP/1.1 200 Bad Request");

eClean();
