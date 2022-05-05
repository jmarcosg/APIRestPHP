<?php

use App\Controllers\Arbolado\Arb_SolicitudController;
use App\Controllers\Arbolado\Arb_ArchivoController;

$arbSolicitudController = new Arb_SolicitudController();

/* Metodo GET */
if ($url['method'] == 'GET') {
	if (isset($_GET) && count($_GET) > 0) {

		if (isset($_GET['list']) && $_GET['list'] == 'true') {
			unset($_GET['list']);
			if (isset($_GET['estado']) && $_GET['estado'] === 'todas') {
				/* Obtenemos todas las solicitudes */
				unset($_GET['estado']);
				$_GET['TOP'] = 1000;
				$arbolado = $arbSolicitudController->index($_GET);
			} else {

				/* Obtenemos listado de solicitudes en funcion del estado */
				$arbolado = $arbSolicitudController->index($_GET);
			}
		} else {
			/* Obtenemos una solicitud puntual */
			$arbolado = $arbSolicitudController->get($_GET);
		}

		if (!$arbolado instanceof ErrorException) {
			if ($arbolado !== false) {
				sendRes($arbolado);
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

	/* Guardamos la solicitud */
	$id = $arbSolicitudController->store($_POST);
	if (!$id instanceof ErrorException) {
		$arbArchivoController = new Arb_ArchivoController();

		foreach ($_FILES as $key => $file) {
			/* Generamos un nombre unico para el archivo */
			$nameFile = uniqid() . getExtFile($file);

			/* Guardamos el nombre del archivo en la tabla */
			$req = ['id_solicitud' => $id, 'name' => $nameFile];
			$archivo = $arbArchivoController->store($req);

			/* copiamos el archivo en la carpeta correspondiente */
			$path = getPathFile($file, "arbolado/solicitud_poda/$id/", $nameFile);
			$copiado = copy($file['tmp_name'], $path);

			if ($archivo instanceof ErrorException || !$copiado) {
				/* Si hubo un error en algun archivo */
				$arbSolicitudController->delete($id);
				sendRes(null, $archivo, $_GET);
				exit;
			}
		}

		sendRes(['id' => $id]);
	} else {
		sendRes(null, $id->getMessage(), $_GET);
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
