<?php

use App\Controllers\WlAplicacionController;

$wlAplicacionController = new WlAplicacionController();

/* Metodo GET */
if ($url['method'] == 'GET') {
	if (isset($_GET) && count($_GET) > 0) {
		$wlApp = $wlAplicacionController->get($_GET);
		if (!$wlApp instanceof ErrorException) {
			if ($wlApp) {
				sendRes($wlApp);
			} else {
				sendRes(null, 'No se encontro el usuario', $_GET);
			}
		} else {
			sendRes(null, $wlApp->getMessage(), $_GET);
		};
	} else {
		$wlApp = $wlAplicacionController->index(['TOP' => 10]);
		if (!$wlApp instanceof ErrorException) {
			sendRes($wlApp);
		} else {
			sendRes(null, $wlApp->getMessage(), $_GET);
		};
	}
	eClean();
}

/* Metodo POST */
if ($url['method'] == 'POST') {
	$wlApp = $wlAplicacionController->store($_POST);
	if (!$wlApp instanceof ErrorException) {
		sendRes(['ReferenciaID' => $wlApp]);
	} else {
		sendRes(null, $wlApp->getMessage(), $_GET);
	};
	eClean();
}

/* Metodo PUT */
if ($url['method'] == 'PUT') {
	parse_str(file_get_contents('php://input'), $_PUT);
	$wlApp = $wlAplicacionController->update($_PUT, $url['id']);
	if (!$wlApp instanceof ErrorException) {
		sendRes($_PUT);
	} else {
		sendRes(null, $wlApp->getMessage(), $_GET);
	};
	eClean();
}

/* Metodo DELETE */
if ($url['method'] == 'DELETE') {
	$wlApp = $wlAplicacionController->delete($url['id']);
	if (!$wlApp instanceof ErrorException) {
		sendRes($url['id']);
	} else {
		sendRes(null, $wlApp->getMessage(), $url['id']);
	};
	eClean();
}

header("HTTP/1.1 200 Bad Request");

eClean();
