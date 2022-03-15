<?php

use App\Controllers\WapPersonaController;

$wapPersonaController = new WapPersonaController();

/* Metodo GET */
if ($url['method'] == 'GET') {
	if (isset($_GET) && count($_GET) > 0) {
		$wapPersona = $wapPersonaController->get($_GET);
		if (!$wapPersona instanceof ErrorException) {
			if ($wapPersona) {
				sendRes($wapPersona);
			} else {
				sendRes(null, 'No se encontro el usuario', $_GET);
			}
		} else {
			sendRes(null, $wapPersona->getMessage(), $_GET);
		};
	} else {
		$wapPersona = $wapPersonaController->index(['TOP' => 10]);
		if (!$wapPersona instanceof ErrorException) {
			sendRes($wapPersona);
		} else {
			sendRes(null, $wapPersona->getMessage(), $_GET);
		};
	}
	eClean();
}

/* Metodo POST */
if ($url['method'] == 'POST') {
	$wapPersona = $wapPersonaController->store($_POST);
	if (!$wapPersona instanceof ErrorException) {
		sendRes(['ReferenciaID' => $wapPersona]);
	} else {
		sendRes(null, $wapPersona->getMessage(), $_GET);
	};
	eClean();
}

/* Metodo PUT */
if ($url['method'] == 'PUT') {
	parse_str(file_get_contents('php://input'), $_PUT);
	$wapPersona = $wapPersonaController->update($_PUT, $url['id']);
	if (!$wapPersona instanceof ErrorException) {
		sendRes($_PUT);
	} else {
		sendRes(null, $wapPersona->getMessage(), $_GET);
	};
	eClean();
}

/* Metodo DELETE */
if ($url['method'] == 'DELETE') {
	$wapPersona = $wapPersonaController->delete($url['id']);
	if (!$wapPersona instanceof ErrorException) {
		sendRes($url['id']);
	} else {
		sendRes(null, $wapPersona->getMessage(), $url['id']);
	};
	eClean();
}

header("HTTP/1.1 200 Bad Request");

eClean();
