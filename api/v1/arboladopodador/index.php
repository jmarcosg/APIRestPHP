<?php

use App\Controllers\Arbolado\Arb_PodadorController;

$arbPodadorController = new Arb_PodadorController();

/* Metodo GET */
if ($url['method'] == 'GET') {
	$action = $_GET['action'];
	unset($_GET['action']);

	switch ($action) {
		case '0':
			/* Obtenemos todas las solicitudes, o funcion del estado */
			if ($_GET['estado'] == 'todas') {
				unset($_GET['estado']);
				Arb_PodadorController::index();
			} else {
				Arb_PodadorController::getNoDeshabilitados();
			}
			break;

		case '1':
			/* Obtenemos una solicitud puntual */
			Arb_PodadorController::get();
			break;

		case '2':
			/* Obtenemos el estado de la ultima solicitud enviada por el usuario */
			Arb_PodadorController::getEstadoSolicitudDetalle();
			break;

		case '3':
			/* Obtenemos todas las solicitudes deshabilitadas */
			Arb_PodadorController::getDeshabilitados();
			break;

		default:
			$error = new ErrorException('El action no es valido');
			sendRes(null, $error->getMessage(), $_GET);
			exit;
			break;
	}
}

/* Metodo POST */
if ($url['method'] == 'POST') Arb_PodadorController::store();

/* Metodo PUT */
if ($url['method'] == 'PUT') Arb_PodadorController::update($url['id']);

/* Metodo DELETE */
if ($url['method'] == 'DELETE') Arb_PodadorController::delete($url['id']);

header("HTTP/1.1 200 Bad Request");

exit();
