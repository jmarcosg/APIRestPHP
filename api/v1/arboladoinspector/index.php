<?php

use App\Controllers\Arbolado\Arb_InspectorController;
use App\Controllers\Arbolado\Arb_PodadorController;

$arbInspectorController = new Arb_InspectorController();
$arbPodadorController = new Arb_PodadorController();

/* Metodo GET */
if ($url['method'] == 'GET') {
	if (isset($_GET) && count($_GET) > 0 && isset($_GET['action'])) {
		$action = $_GET['action'];
		unset($_GET['action']);

		switch ($action) {
			case '0':
				/* Mostramos todos los inspectores */
				Arb_InspectorController::index();
				break;

			case '1':
				/* Obtenemos una solicitud puntual */
				$inspector = $arbInspectorController->get(['id' => $_GET['id']]);
				break;

			default:
				$inspector = new ErrorException('El action no es valido');
				break;
		}


		if (!$inspector instanceof ErrorException) {
			if ($inspector !== false) {
				sendRes($inspector);
			} else {
				sendRes(null, 'No se encontro la evaluación', $_GET);
			}
		} else {
			sendRes(null, $arbolado->getMessage(), $_GET);
		};
	}
	eClean();
}

/* Metodo POST */
if ($url['method'] == 'POST') {
	Arb_InspectorController::store($_POST);
}

/* Metodo PUT */
if ($url['method'] == 'PUT') {
	parse_str(file_get_contents('php://input'), $_PUT);
	$id = $url['id'];
	$arbolado = $arbPodadorController->update($_PUT, $id);

	if (!$arbolado instanceof ErrorException) {
		$_PUT['id'] = $id;
		sendRes($_PUT);
	} else {
		sendRes(null, $arbolado->getMessage(), ['id' => $id]);
	};
	eClean();
}

/* Metodo DELETE */
if ($url['method'] == 'DELETE') {
	Arb_InspectorController::delete($url['id']);
}

header("HTTP/1.1 200 Bad Request");

eClean();
