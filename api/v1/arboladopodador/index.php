<?php

use App\Controllers\Arbolado\Arb_PodadorController;

$arbPodadorController = new Arb_PodadorController();

/* Metodo GET */
if ($url['method'] == 'GET') {
	if (isset($_GET) && count($_GET) > 0) {

		if (isset($_GET['list']) && $_GET['list'] == 'true') {
			unset($_GET['list']);
			if (isset($_GET['estado']) && $_GET['estado'] === 'todas') {
				/* Obtenemos todas las solicitudes */
				unset($_GET['estado']);
				$_GET['TOP'] = 1000;
				$podador = $arbPodadorController->index($_GET);
			} else {

				/* Obtenemos listado de solicitudes en funcion del estado */
				$podador = $arbPodadorController->index($_GET);
			}
		} else {
			/* Obtenemos una solicitud puntual */
			$podador = $arbPodadorController->get($_GET);
		}

		if (!$podador instanceof ErrorException) {
			if ($podador !== false) {
				sendRes($podador);
			} else {
				sendRes(null, 'No se encontro la solicitud', $_GET);
			}
		} else {
			sendRes(null, $arbolado->getMessage(), $_GET);
		};
	} else {
	}
	eClean();
}

/* Metodo POST */
if ($url['method'] == 'POST') {

	$file = $_FILES['certificado'];
	$nameFile = uniqid() . getExtFile($file);

	$_POST['certificado'] = $nameFile;
	/* Guardamos la solicitud */
	$id = $arbPodadorController->store($_POST);

	/* copiamos el archivo en la carpeta correspondiente */
	$path = getPathFile($file, "arbolado/podador/$id/", $nameFile);
	$copiado = copy($file['tmp_name'], $path);

	if (!$id instanceof ErrorException || !$copiado) {
		$arbPodadorController->delete($id);
		sendRes(['id' => $id]);
		exit;
	}
	sendRes(null, $id->getMessage(), $_GET);
	exit;
}

/* Metodo PUT */
if ($url['method'] == 'PUT') {
	parse_str(file_get_contents('php://input'), $_PUT);
	$id = $url['id'];
	$arbolado = $arbPodadorController->update($_PUT, $id);

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
	$arbolado = $arbPodadorController->delete($url['id']);
	if (!$arbolado instanceof ErrorException) {
		sendRes(['ReferenciaID' => $id]);
	} else {
		sendRes(null, $arbolado->getMessage(), ['ReferenciaID' => $id]);
	};
	eClean();
}

header("HTTP/1.1 200 Bad Request");

eClean();
