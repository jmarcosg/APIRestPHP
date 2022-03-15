<?php

use App\Controllers\WapUsuarioController;

$wapUsuarioController = new WapUsuarioController();

/* Metodo GET */
if ($url['method'] == 'GET') {
	if (isset($_GET) && count($_GET) > 0) {
		$wapUsuario = $wapUsuarioController->get($_GET);
		if (!$wapUsuario instanceof ErrorException) {
			if ($wapUsuario) {
				sendRes($wapUsuario);
			} else {
				sendRes(null, 'No se encontro el usuario', $_GET);
			}
		} else {
			sendRes(null, $wapUsuario->getMessage(), $_GET);
		};
	} else {
		$wapUsuario = $wapUsuarioController->index(['TOP' => 10]);
		if (!$wapUsuario instanceof ErrorException) {
			sendRes($wapUsuario);
		} else {
			sendRes(null, $wapUsuario->getMessage(), $_GET);
		};
	}
	eClean();
}

/* Metodo POST */
if ($url['method'] == 'POST') {
	$wapUsuario = $wapUsuarioController->store($_POST);
	if (!$wapUsuario instanceof ErrorException) {
		sendRes(['ReferenciaID' => $wapUsuario]);
	} else {
		sendRes(null, $wapUsuario->getMessage(), $_GET);
	};
	eClean();
}

/* Metodo PUT */
if ($url['method'] == 'PUT') {
	parse_str(file_get_contents('php://input'), $_PUT);
	$wapUsuario = $wapUsuarioController->update($_PUT, $url['id']);
	if (!$wapUsuario instanceof ErrorException) {
		sendRes($_PUT);
	} else {
		sendRes(null, $wapUsuario->getMessage(), $_GET);
	};
	eClean();
}

/* Metodo DELETE */
if ($url['method'] == 'DELETE') {
	$wapUsuario = $wapUsuarioController->delete($url['id']);
	if (!$wapUsuario instanceof ErrorException) {
		sendRes($url['id']);
	} else {
		sendRes(null, $wapUsuario->getMessage(), $url['id']);
	};
	eClean();
}

header("HTTP/1.1 200 Bad Request");

eClean();
