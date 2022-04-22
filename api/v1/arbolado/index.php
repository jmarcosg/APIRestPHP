<?php

use App\Controllers\Arbolado\Arb_SolicitudController;

$arbSolicitudController = new Arb_SolicitudController();

/* Metodo GET */
if ($url['method'] == 'GET') {
	if (isset($_GET) && count($_GET) > 0) {

		if ($_GET['list'] == 'true') {
			unset($_GET['list']);
			$arbolado = $arbSolicitudController->index($_GET);
		} else {
			$arbolado = $arbSolicitudController->get($_GET);
		}

		if (!$arbolado instanceof ErrorException) {
			if ($arbolado) {
				sendRes($arbolado);
			} else {
				sendRes(null, 'No se encontro la solicitud', $_GET);
			}
		} else {
			sendRes(null, $arbolado->getMessage(), $_GET);
		};
	} else {
		$arbolado = $arbSolicitudController->index(['TOP' => 10]);
		if (!$arbolado instanceof ErrorException) {
			sendRes($arbolado);
		} else {
			sendRes(null, $arbolado->getMessage(), $_GET);
		};
	}
	eClean();
}

/* Metodo POST */
if ($url['method'] == 'POST') {
	$arbolado = $arbSolicitudController->store($_POST);
	if (!$arbolado instanceof ErrorException) {
		sendRes(['id' => $arbolado]);
	} else {
		sendRes(null, $arbolado->getMessage(), $_GET);
	};
	eClean();
}

/* Metodo PUT */
if ($url['method'] == 'PUT') {
	parse_str(file_get_contents('php://input'), $_PUT);
	$id = $url['id'];
	$arbolado = $arbSolicitudController->update($_PUT, $id);

	if (!$arbolado instanceof ErrorException) {
		$_PUT['ReferenciaID'] = $id;
		sendRes($_PUT);
	} else {
		sendRes(null, $arbolado->getMessage(), ['ReferenciaID' => $id]);
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
