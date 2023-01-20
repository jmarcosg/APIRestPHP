<?php

use App\Controllers\Weblogin\LoginController;
use App\Controllers\Weblogin\WapAppsRecientesController;
use App\Controllers\Weblogin\WlAppController;

$dotenv = \Dotenv\Dotenv::createImmutable('./weblogin/');
$dotenv->load();

include './weblogin/config.php';

if ($url['method'] == 'GET') {
	$action = $_GET['action'];
	unset($_GET['action']);

	switch ($action) {

		case 'aplicaciones':
			WlAppController::getApps();

		case 'getLicComercialHistorial':
			LoginController::getLicComercialHistorial();

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
		case 'getAllData':
			LoginController::getAllData();

		case 'getIntoApp':
			WapAppsRecientesController::getIntoApp();

		case 'getLicComercialInfo':
			LoginController::getLicComercialInfo();

		default:
			$error = new ErrorException('El action no es valido');
			sendRes(null, $error->getMessage(), $_GET);
			exit;
	}
}

header("HTTP/1.1 200 Bad Request");

eClean();
