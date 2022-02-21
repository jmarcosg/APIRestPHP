<?php

use App\Controllers\WapPersonaController;

$wapPersonaController = new WapPersonaController();

/* Metodo GET */
if ($token == USUARIO_KEY && $url['method'] == 'GET') {
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
	exit();
}

/* Metodo POST */
if ($token == USUARIO_KEY && $url['method'] == 'POST') {
	$wapPersona = $wapPersonaController->store($_POST);
	if (!$wapPersona instanceof ErrorException) {
		sendRes(['ReferenciaID' => $wapPersona]);
	} else {
		sendRes(null, $wapPersona->getMessage(), $_GET);
	};
	exit();
}

/* Metodo PUT */
if ($token == USUARIO_KEY && $url['method'] == 'PUT') {
	parse_str(file_get_contents('php://input'), $_PUT);
	$wapPersona = $wapPersonaController->update($_PUT, $url['id']);
	if (!$wapPersona instanceof ErrorException) {
		sendRes($_PUT);
	} else {
		sendRes(null, $wapPersona->getMessage(), $_GET);
	};
	exit();
}

/* Metodo DELETE */
if ($token == USUARIO_KEY && $url['method'] == 'DELETE') {
	$wapPersona = $wapPersonaController->delete($url['id']);
	if (!$wapPersona instanceof ErrorException) {
		sendRes($url['id']);
	} else {
		sendRes(null, $wapPersona->getMessage(), $url['id']);
	};
	exit();
}

if ($token != USUARIO_KEY) {
	header("HTTP/1.1 401 Unauthorized");
} else {
	header("HTTP/1.1 200 Bad Request");
}
exit();
