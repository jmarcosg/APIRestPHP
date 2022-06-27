<?php

use App\Controllers\LicenciaComercial\Lc_SolicitudController;

$lcSolicitudController = new Lc_SolicitudController();

/* Metodo GET */
if ($url['method'] == 'GET') {
	$action = $_GET['action'];
	unset($_GET['action']);

	switch ($action) {
		case '0':
			/* Obtenemos todas las solicitudes, o funcion del estado */
			Lc_SolicitudController::index();
			break;

		case '1':
			/* Obtenemos una solicitud puntual */
			Lc_SolicitudController::get($_GET);
			break;

		case '2':
			/* Obtenemos la ultima solicitud */
			$_GET['TOP'] = 1;
			Lc_SolicitudController::get($_GET);
			break;

		default:
			$error = new ErrorException('El action no es valido');
			sendRes(null, $error->getMessage(), $_GET);
			exit;
			break;
	}
}

/* Metodo POST */
if ($url['method'] == 'POST') Lc_SolicitudController::store($_POST);

/* Metodo PUT */
if ($url['method'] == 'PUT') {
	parse_str(file_get_contents('php://input'), $_PUT);
	$id = $url['id'];

	$step = $_PUT['step'];
	unset($_PUT['step']);

	switch ($step) {
		case '1':
			Lc_SolicitudController::updateFirts($_PUT, $id);
			break;

		case '2':
			$lc = $lcSolicitudController->updateSec($_PUT, $id);
			break;

		default:
			# code...
			break;
	}


	if (!$lc instanceof ErrorException) {
		$_PUT['id'] = $id;
		sendRes($_PUT);
	} else {
		sendRes(null, $arbolado->getMessage(), ['id' => $id]);
	};
	eClean();
}

/* Metodo DELETE */
if ($url['method'] == 'DELETE') {
	$id = $url['id'];
	$arbolado = $arbSolicitudController->delete($url['id']);
	if (!$arbolado instanceof ErrorException) {
		sendRes(['ReferenciaID' => $id]);
	} else {
		sendRes(null, $arbolado->getMessage(), ['ReferenciaID' => $id]);
	};
	eClean();
}

header("HTTP/1.1 200 Bad Request");

eClean();
