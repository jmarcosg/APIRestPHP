<?php

use App\Controllers\Arbolado\Arb_SolicitudController;
use App\Controllers\Arbolado\Arb_ArchivoController;

$arbSolicitudController = new Arb_SolicitudController();

/* Metodo GET */
if ($url['method'] == 'GET') {
	if (isset($_GET) && count($_GET) > 0 && isset($_GET['action'])) {
		$action = $_GET['action'];
		unset($_GET['action']);

		switch ($action) {
			case '0':
				/* Obtenemos todas las solicitudes, o funcion del estado */
				$_GET['TOP'] = 1000;
				$arbolado = $arbSolicitudController->index($_GET, ['order' => ' ORDER BY id DESC ']);
				break;

			case '1':
				/* Obtenemos una solicitud puntual */
				$arbolado = $arbSolicitudController->get($_GET);
				break;

			default:
				$arbolado = new ErrorException('El action no es valido');
				break;
		}

		/* Envio del mensaje */
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

		/* Enviamos el correo electronico */
		$data  = [
			'email' => $_POST['email'],
			'tipo' => $_POST['tipo'],
			'solicita' => $_POST['solicita'],
			'ubicacion' => $_POST['ubicacion'],
			'motivo' => $_POST['motivo'],
			'cantidad' => $_POST['cantidad']
		];
		$arbSolicitudController->sendEmailSolicitud($id, 'envioSolicitud', $data);

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

	/* Extraemos el contacto y el email  */
	$contacto = $_PUT['contacto'];
	$email = $_PUT['email'];
	unset($_PUT['contacto']);
	unset($_PUT['email']);

	$arbolado = $arbSolicitudController->update($_PUT, $id);

	if (!$arbolado instanceof ErrorException) {
		/* Enviamos el correo electronico */
		$data = [
			'id' => $id,
			'email' => $email,
			'contacto' => $contacto,
			'observacion' => $_PUT['observacion']
		];
		$arbSolicitudController->sendEmailSolicitud($id, $_PUT['estado'], $data);
		$_PUT['id'] = $id;
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
