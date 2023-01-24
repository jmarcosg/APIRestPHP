<?php

use App\Controllers\Proveedor\Pro_SolicitudController;

$dotenv = \Dotenv\Dotenv::createImmutable('./proveedores/');
$dotenv->load();

include './proveedores/config.php';

if ($url['method'] == 'GET') {
	$action = $_GET['action'];
	unset($_GET['action']);

	switch ($action) {

		case 'getAllSolicitudes':
			/* IdeasPropuestasController::getAllSolicitudes(); */

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

		case 'saveSolicitud':
			Pro_SolicitudController::saveSolicitud();

		default:
			$error = new ErrorException('El action no es valido');
			sendRes(null, $error->getMessage(), $_GET);
	}
}

header("HTTP/1.1 200 Bad Request");

eClean();
