<?php

use App\Controllers\WapUsuariosPerfilesController;

$wapUsuariosPerfilesController = new WapUsuariosPerfilesController();

/* Metodo GET */
if ($url['method'] == 'GET') {
	if (isset($_GET) && count($_GET) > 0) {
		$wapUsuPerfiles = $wapUsuariosPerfilesController->get($_GET);
		if (!$wapUsuPerfiles instanceof ErrorException) {
			if ($wapUsuPerfiles) {
				sendRes($wapUsuPerfiles);
			} else {
				sendRes(null, 'No se encontro el perfil', $_GET);
			}
		} else {
			sendRes(null, $wapUsuPerfiles->getMessage(), $_GET);
		};
	} else {
		$wapUsuPerfiles = $wapUsuariosPerfilesController->index(['TOP' => 10]);
		if (!$wapUsuPerfiles instanceof ErrorException) {
			sendRes($wapUsuPerfiles);
		} else {
			sendRes(null, $wapUsuPerfiles->getMessage(), $_GET);
		};
	}
	eClean();
}

/* Metodo POST */
if ($url['method'] == 'POST') {
	$wapUsuPerfiles = $wapUsuariosPerfilesController->store($_POST);
	if (!$wapUsuPerfiles instanceof ErrorException) {
		sendRes(['ReferenciaID' => $wapUsuPerfiles]);
	} else {
		sendRes(null, $wapUsuPerfiles->getMessage(), $_GET);
	};
	eClean();
}

/* Metodo PUT */
if ($url['method'] == 'PUT') {
	parse_str(file_get_contents('php://input'), $_PUT);
	$wapUsuPerfiles = $wapUsuariosPerfilesController->update($_PUT, $url['id']);
	if (!$wapUsuPerfiles instanceof ErrorException) {
		sendRes($_PUT);
	} else {
		sendRes(null, $wapUsuPerfiles->getMessage(), $_GET);
	};
	eClean();
}

/* Metodo DELETE */
if ($url['method'] == 'DELETE') {
	$wapUsuPerfiles = $wapUsuariosPerfilesController->delete($url['id']);
	if (!$wapUsuPerfiles instanceof ErrorException) {
		sendRes($url['id']);
	} else {
		sendRes(null, $wapUsuPerfiles->getMessage(), $url['id']);
	};
	eClean();
}

header("HTTP/1.1 200 Bad Request");

eClean();
