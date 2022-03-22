<?php

use App\Controllers\DeportesUsuarioController;

$deportesUsuarioController = new DeportesUsuarioController();

/* Metodo GET */
if ($url['method'] == 'GET') {
	$id = $url['id'];
	if (isset($_GET) && $id !== null && $id !== '') {
		$params = ['id' => $id];
		$deportesUsuario = $deportesUsuarioController->get($params);
		if (!$deportesUsuario instanceof ErrorException) {
			if ($deportesUsuario) {
				sendRes($deportesUsuario);
			} else {
				sendRes(null, 'No se encontro el usuario', $params);
			}
		} else {
			sendRes(null, $deportesUsuario->getMessage(), $params);
		};
	} else {
		$deportesUsuario = $deportesUsuarioController->index(['TOP' => 10]);
		if (!$deportesUsuario instanceof ErrorException) {
			sendRes($deportesUsuario);
		} else {
			sendRes(null, $deportesUsuario->getMessage(), $_GET);
		};
	}
	eClean();
}

/* Metodo POST */
if ($url['method'] == 'POST') {
	$deportesUsuario = $deportesUsuarioController->store($_POST);
	if (!$deportesUsuario instanceof ErrorException) {
		sendRes(['ReferenciaID' => $deportesUsuario]);
	} else {
		sendRes(null, $deportesUsuario->getMessage(), $_GET);
	};
	eClean();
}

/* Metodo PUT */
if ($url['method'] == 'PUT') {
	parse_str(file_get_contents('php://input'), $_PUT);
	$deportesUsuario = $deportesUsuarioController->update($_PUT, $url['id']);
	if (!$deportesUsuario instanceof ErrorException) {
		sendRes($_PUT);
	} else {
		sendRes(null, $deportesUsuario->getMessage(), $_GET);
	};
	eClean();
}

/* Metodo DELETE */
if ($url['method'] == 'DELETE') {
	$deportesUsuario = $deportesUsuarioController->delete($url['id']);
	if (!$deportesUsuario instanceof ErrorException) {
		sendRes($url['id']);
	} else {
		sendRes(null, $deportesUsuario->getMessage(), ['id' => $url['id']]);
	};
	eClean();
}

header("HTTP/1.1 200 Bad Request");

eClean();
