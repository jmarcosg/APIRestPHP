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
	$id = $url['id'];
	$wapPersona = $wapPersonaController->update($_PUT, $id);

	if (!$wapPersona instanceof ErrorException) {
		$_PUT['ReferenciaID'] = $id;
		sendRes($_PUT);
	} else {
		sendRes(null, $wapPersona->getMessage(), ['ReferenciaID' => $id]);
	};
	eClean();
}

/* Metodo DELETE */
if ($url['method'] == 'DELETE') {
	$id = $url['id'];
	$wapPersona = $wapPersonaController->delete($url['id']);
	if (!$wapPersona instanceof ErrorException) {
		sendRes(['ReferenciaID' => $id]);
	} else {
		sendRes(null, $wapPersona->getMessage(), ['ReferenciaID' => $id]);
	};
	eClean();
}

header("HTTP/1.1 200 Bad Request");

eClean();
