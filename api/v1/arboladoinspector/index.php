<?php

use App\Controllers\Arbolado\Arb_InspectorController;

$arbInspectorController = new Arb_InspectorController();

/* Metodo GET */
if ($url['method'] == 'GET') {
	$action = $_GET['action'];
	unset($_GET['action']);

	switch ($action) {
		case '0':
			/* Mostramos todos los inspectores */
			Arb_InspectorController::index();
			break;

		case '1':
			/* Obtenemos una solicitud puntual */
			Arb_InspectorController::get(['id' => $_GET['id']]);
			break;

		default:
			$error = new ErrorException('El action no es valido');
			sendRes(null, $error->getMessage(), $_GET);
			exit;
			break;
	}
}

/* Metodo POST */
if ($url['method'] == 'POST') Arb_InspectorController::store();

/* Metodo PUT */
if ($url['method'] == 'PUT') Arb_InspectorController::update($_PUT, $url['id']);

/* Metodo DELETE */
if ($url['method'] == 'DELETE') Arb_InspectorController::delete($url['id']);

header("HTTP/1.1 200 Bad Request");

eClean();
